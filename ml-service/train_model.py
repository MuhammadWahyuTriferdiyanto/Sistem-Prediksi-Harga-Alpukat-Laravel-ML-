"""
train_model.py
Melatih model Random Forest Regressor untuk memprediksi harga alpukat per kg
berdasarkan fitur: bulan, jenis alpukat, curah hujan, pasokan, dan status musim panen.
"""
import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestRegressor
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import LabelEncoder
from sklearn.metrics import mean_absolute_error, mean_absolute_percentage_error, r2_score
import joblib
import json
import os

DATA_PATH = "data/harga_alpukat_dummy.csv"
MODEL_PATH = "models/model_harga_alpukat.joblib"
ENCODER_PATH = "models/label_encoder_jenis.joblib"
METRICS_PATH = "models/metrics.json"


def main():
    if not os.path.exists(DATA_PATH):
        raise FileNotFoundError(
            f"File data tidak ditemukan: {DATA_PATH}. "
            "Jalankan 'python generate_data.py' dahulu."
        )

    print("📥 Memuat data...")
    df = pd.read_csv(DATA_PATH)

    # Encode jenis_alpukat (kategorikal) jadi angka
    le = LabelEncoder()
    df["jenis_alpukat_encoded"] = le.fit_transform(df["jenis_alpukat"])

    features = [
        "jenis_alpukat_encoded",
        "bulan",
        "tahun",
        "curah_hujan_mm",
        "pasokan_kg",
        "musim_panen",
    ]
    target = "harga_per_kg"

    X = df[features]
    y = df[target]

    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.2, random_state=42
    )

    print("🌲 Melatih model Random Forest Regressor...")
    model = RandomForestRegressor(
        n_estimators=200,
        max_depth=12,
        min_samples_split=4,
        min_samples_leaf=2,
        random_state=42,
        n_jobs=-1,
    )
    model.fit(X_train, y_train)

    print("📊 Evaluasi model...")
    y_pred = model.predict(X_test)
    mae = mean_absolute_error(y_test, y_pred)
    mape = mean_absolute_percentage_error(y_test, y_pred) * 100
    r2 = r2_score(y_test, y_pred)

    metrics = {
        "mae": round(float(mae), 2),
        "mape_percent": round(float(mape), 2),
        "r2_score": round(float(r2), 4),
        "n_train": len(X_train),
        "n_test": len(X_test),
        "features": features,
        "jenis_alpukat_classes": le.classes_.tolist(),
    }

    print(json.dumps(metrics, indent=2))

    os.makedirs("models", exist_ok=True)
    joblib.dump(model, MODEL_PATH)
    joblib.dump(le, ENCODER_PATH)
    with open(METRICS_PATH, "w") as f:
        json.dump(metrics, f, indent=2)

    print(f"\n✅ Model disimpan di: {MODEL_PATH}")
    print(f"✅ Label encoder disimpan di: {ENCODER_PATH}")
    print(f"✅ Metrics disimpan di: {METRICS_PATH}")


if __name__ == "__main__":
    main()
