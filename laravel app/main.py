"""
main.py
FastAPI service untuk prediksi harga alpukat menggunakan model Random Forest.
Service ini dipanggil oleh aplikasi Laravel melalui HTTP request (REST API).
"""
from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field
from typing import List, Optional
import pandas as pd
import numpy as np
import joblib
import json
import os
import subprocess
from datetime import datetime

app = FastAPI(
    title="ML Service - Prediksi Harga Alpukat",
    description="Service Machine Learning (Random Forest) untuk memprediksi harga jual alpukat per kg",
    version="1.0.0",
)

# Izinkan Laravel (origin berbeda) mengakses API ini
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

MODEL_PATH = "models/model_harga_alpukat.joblib"
ENCODER_PATH = "models/label_encoder_jenis.joblib"
METRICS_PATH = "models/metrics.json"
DATA_PATH = "data/harga_alpukat_dummy.csv"

_model = None
_label_encoder = None


def load_model():
    """Load model & encoder ke memory (lazy loading)."""
    global _model, _label_encoder
    if _model is None or _label_encoder is None:
        if not os.path.exists(MODEL_PATH):
            raise HTTPException(
                status_code=503,
                detail="Model belum dilatih. Jalankan endpoint /train terlebih dahulu, "
                       "atau jalankan 'python train_model.py' secara manual.",
            )
        _model = joblib.load(MODEL_PATH)
        _label_encoder = joblib.load(ENCODER_PATH)
    return _model, _label_encoder


# ===================== SCHEMAS =====================

class PrediksiRequest(BaseModel):
    jenis_alpukat: str = Field(..., examples=["Miki"])
    bulan: int = Field(..., ge=1, le=12, examples=[8])
    tahun: int = Field(..., ge=2020, le=2100, examples=[2026])
    curah_hujan_mm: float = Field(180.0, ge=0, examples=[180.0])
    pasokan_kg: float = Field(800.0, ge=0, examples=[800.0])


class PrediksiResponse(BaseModel):
    jenis_alpukat: str
    bulan: int
    tahun: int
    prediksi_harga_per_kg: int
    musim_panen: bool
    confidence_range_low: int
    confidence_range_high: int


class PrediksiBatchRequest(BaseModel):
    jenis_alpukat: str
    bulan_mulai: int = Field(..., ge=1, le=12)
    tahun_mulai: int = Field(..., ge=2020, le=2100)
    jumlah_bulan: int = Field(6, ge=1, le=24, description="Berapa bulan ke depan yang diprediksi")
    curah_hujan_mm: float = 180.0
    pasokan_kg: float = 800.0


# ===================== HELPERS =====================

def is_musim_panen(bulan: int) -> int:
    return 1 if bulan in [2, 3, 4, 8, 9, 10] else 0


def predict_single(model, le, jenis_alpukat, bulan, tahun, curah_hujan_mm, pasokan_kg):
    if jenis_alpukat not in le.classes_:
        raise HTTPException(
            status_code=400,
            detail=f"Jenis alpukat '{jenis_alpukat}' tidak dikenali. "
                   f"Pilihan yang tersedia: {le.classes_.tolist()}",
        )

    jenis_encoded = le.transform([jenis_alpukat])[0]
    musim = is_musim_panen(bulan)

    X = pd.DataFrame([{
        "jenis_alpukat_encoded": jenis_encoded,
        "bulan": bulan,
        "tahun": tahun,
        "curah_hujan_mm": curah_hujan_mm,
        "pasokan_kg": pasokan_kg,
        "musim_panen": musim,
    }])

    # Prediksi dari setiap tree di forest untuk dapatkan rentang confidence
    all_preds = np.array([tree.predict(X)[0] for tree in model.estimators_])
    harga_prediksi = float(np.mean(all_preds))
    harga_low = float(np.percentile(all_preds, 10))
    harga_high = float(np.percentile(all_preds, 90))

    return {
        "jenis_alpukat": jenis_alpukat,
        "bulan": bulan,
        "tahun": tahun,
        "prediksi_harga_per_kg": int(round(harga_prediksi / 100) * 100),
        "musim_panen": bool(musim),
        "confidence_range_low": int(round(harga_low / 100) * 100),
        "confidence_range_high": int(round(harga_high / 100) * 100),
    }


# ===================== ENDPOINTS =====================

@app.get("/")
def root():
    return {
        "service": "ML Service - Prediksi Harga Alpukat",
        "status": "running",
        "model_ready": os.path.exists(MODEL_PATH),
        "endpoints": ["/health", "/predict", "/predict-batch", "/train", "/metrics", "/jenis-alpukat"],
    }


@app.get("/health")
def health():
    return {"status": "ok", "timestamp": datetime.now().isoformat()}


@app.get("/jenis-alpukat")
def get_jenis_alpukat():
    """Daftar jenis alpukat yang dikenali oleh model."""
    _, le = load_model()
    return {"jenis_alpukat": le.classes_.tolist()}


@app.get("/metrics")
def get_metrics():
    """Lihat akurasi/performa model hasil training terakhir."""
    if not os.path.exists(METRICS_PATH):
        raise HTTPException(status_code=404, detail="Metrics belum tersedia. Latih model terlebih dahulu.")
    with open(METRICS_PATH) as f:
        return json.load(f)


@app.post("/predict", response_model=PrediksiResponse)
def predict(req: PrediksiRequest):
    """Prediksi harga alpukat untuk satu titik waktu tertentu."""
    model, le = load_model()
    result = predict_single(
        model, le, req.jenis_alpukat, req.bulan, req.tahun,
        req.curah_hujan_mm, req.pasokan_kg,
    )
    return result


@app.post("/predict-batch")
def predict_batch(req: PrediksiBatchRequest):
    """Prediksi harga alpukat untuk beberapa bulan ke depan sekaligus (forecasting)."""
    model, le = load_model()
    results = []
    bulan, tahun = req.bulan_mulai, req.tahun_mulai

    for _ in range(req.jumlah_bulan):
        result = predict_single(
            model, le, req.jenis_alpukat, bulan, tahun,
            req.curah_hujan_mm, req.pasokan_kg,
        )
        results.append(result)
        bulan += 1
        if bulan > 12:
            bulan = 1
            tahun += 1

    return {"jenis_alpukat": req.jenis_alpukat, "forecast": results}


@app.post("/train")
def train_model():
    """
    Memicu ulang proses generate data dummy + training model dari awal.
    Berguna jika ingin refresh model tanpa masuk ke terminal server.
    """
    try:
        subprocess.run(["python", "generate_data.py"], check=True, capture_output=True, text=True)
        result = subprocess.run(["python", "train_model.py"], check=True, capture_output=True, text=True)

        global _model, _label_encoder
        _model = None
        _label_encoder = None

        with open(METRICS_PATH) as f:
            metrics = json.load(f)

        return {"status": "success", "message": "Model berhasil dilatih ulang", "metrics": metrics}
    except subprocess.CalledProcessError as e:
        raise HTTPException(status_code=500, detail=f"Gagal melatih model: {e.stderr}")
