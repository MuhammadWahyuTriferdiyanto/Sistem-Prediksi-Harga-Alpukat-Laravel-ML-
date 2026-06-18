# ML Service - Prediksi Harga Alpukat

Service Machine Learning berbasis **FastAPI** dan **Random Forest Regressor** (scikit-learn) untuk memprediksi harga jual alpukat per kilogram. Service ini berjalan terpisah dari aplikasi Laravel dan diakses melalui REST API.

## Fitur

- Prediksi harga untuk 1 titik waktu tertentu (`/predict`)
- Prediksi harga untuk beberapa bulan ke depan / forecasting (`/predict-batch`)
- Latih ulang model langsung dari API (`/train`)
- Lihat akurasi model (MAE, MAPE, R² Score) via `/metrics`
- Rentang confidence (low-high) di setiap prediksi, dihitung dari variasi pohon-pohon dalam Random Forest

## Fitur yang Digunakan Model

| Fitur | Keterangan |
|---|---|
| `jenis_alpukat` | Jenis alpukat (Miki, Mentega, Wina, Aligator, Kendil) |
| `bulan` | Bulan (1-12), menangkap pola musiman |
| `tahun` | Tahun, menangkap tren kenaikan harga jangka panjang |
| `curah_hujan_mm` | Curah hujan, mempengaruhi hasil panen |
| `pasokan_kg` | Estimasi pasokan/stok di pasar |
| `musim_panen` | 1 jika bulan termasuk musim panen raya, 0 jika tidak |

## Instalasi & Menjalankan

1. Buat virtual environment (opsional tapi disarankan)
   ```bash
   python -m venv venv
   source venv/bin/activate   # Mac/Linux
   venv\Scripts\activate      # Windows
   ```

2. Install dependency
   ```bash
   pip install -r requirements.txt
   ```

3. Generate data dummy & latih model (skip langkah ini jika folder `models/` sudah berisi file `.joblib`)
   ```bash
   python generate_data.py
   python train_model.py
   ```

4. Jalankan server FastAPI
   ```bash
   uvicorn main:app --reload --port 8001
   ```

5. Service siap diakses di `http://127.0.0.1:8001`
   - Dokumentasi interaktif (Swagger UI): `http://127.0.0.1:8001/docs`

## Contoh Request

**Prediksi 1 titik waktu**
```bash
curl -X POST http://127.0.0.1:8001/predict \
  -H "Content-Type: application/json" \
  -d '{"jenis_alpukat": "Miki", "bulan": 8, "tahun": 2026, "curah_hujan_mm": 180, "pasokan_kg": 800}'
```

**Prediksi 6 bulan ke depan (forecasting)**
```bash
curl -X POST http://127.0.0.1:8001/predict-batch \
  -H "Content-Type: application/json" \
  -d '{"jenis_alpukat": "Miki", "bulan_mulai": 7, "tahun_mulai": 2026, "jumlah_bulan": 6}'
```

## Catatan tentang Data

Data yang digunakan untuk training adalah **data dummy/sintetis** yang dibuat dengan mempertimbangkan pola musim panen alpukat di Indonesia (Februari-April dan Agustus-Oktober adalah musim panen raya, harga cenderung turun) ditambah tren kenaikan harga jangka panjang dan noise acak agar terlihat realistis. Untuk implementasi produksi, ganti file `data/harga_alpukat_dummy.csv` dengan data harga historis riil, lalu jalankan ulang `train_model.py`.
