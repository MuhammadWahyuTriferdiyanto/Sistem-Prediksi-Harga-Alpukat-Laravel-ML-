# Sistem Prediksi Harga Alpukat

Sistem prediksi harga jual alpukat menggunakan **Laravel** (aplikasi web) yang terintegrasi dengan **Machine Learning Service** berbasis **Python FastAPI** dan algoritma **Random Forest Regressor**.

## Tech Stack

- **Web App:** Laravel 10, Bootstrap 5, Chart.js
- **ML Service:** Python, FastAPI, scikit-learn (Random Forest Regressor)
- **Database:** MySQL
- **Komunikasi:** REST API (Laravel → FastAPI via HTTP)

## Fitur

- Prediksi harga alpukat untuk 1 titik waktu tertentu, lengkap dengan rentang confidence
- Forecasting harga untuk beberapa bulan ke depan (grafik tren)
- CRUD data riwayat harga sebagai data training, termasuk import dari CSV
- Dashboard statistik dan grafik tren harga historis
- Halaman status model: lihat akurasi (R², MAPE, MAE) dan latih ulang model langsung dari Laravel

## Struktur

```
prediksi-harga-alpukat/
├── ml-service/      → Service Python (FastAPI + Random Forest)
└── laravel-app/     → Aplikasi web Laravel
```

## Instalasi

Lihat panduan lengkap di [`PANDUAN_INSTALASI.md`](PANDUAN_INSTALASI.md).

Ringkasan cepat:
```bash
# 1. Jalankan ML Service
cd ml-service
pip install -r requirements.txt
uvicorn main:app --reload --port 8001

# 2. Jalankan Laravel App (terminal baru)
cd laravel-app
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Akses di `http://127.0.0.1:8000`, login dengan:
```
Email    : admin@prediksialpukat.id
Password : admin123
```
