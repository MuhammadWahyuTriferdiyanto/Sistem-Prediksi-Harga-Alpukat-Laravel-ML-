<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * MLService
 *
 * Kelas penghubung antara Laravel dan ML Service (FastAPI + Random Forest).
 * Semua komunikasi HTTP ke service Python dipusatkan di sini agar
 * controller tidak perlu tahu detail teknis pemanggilan API eksternal.
 */
class MLService
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.ml.url', env('ML_SERVICE_URL', 'http://127.0.0.1:8001')), '/');
        $this->timeout = 15;
    }

    /**
     * Cek apakah ML service hidup dan modelnya sudah siap dipakai.
     */
    public function health(): array
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/");
            if ($response->successful()) {
                return [
                    'online' => true,
                    'model_ready' => $response->json('model_ready', false),
                ];
            }
            return ['online' => false, 'model_ready' => false];
        } catch (Exception $e) {
            Log::warning('ML Service health check gagal: ' . $e->getMessage());
            return ['online' => false, 'model_ready' => false];
        }
    }

    /**
     * Ambil daftar jenis alpukat yang dikenali model.
     */
    public function getJenisAlpukat(): array
    {
        try {
            $response = Http::timeout($this->timeout)->get("{$this->baseUrl}/jenis-alpukat");
            if ($response->successful()) {
                return $response->json('jenis_alpukat', []);
            }
        } catch (Exception $e) {
            Log::warning('Gagal mengambil daftar jenis alpukat: ' . $e->getMessage());
        }
        return [];
    }

    /**
     * Ambil metrik akurasi model (MAE, MAPE, R2 Score).
     */
    public function getMetrics(): ?array
    {
        try {
            $response = Http::timeout($this->timeout)->get("{$this->baseUrl}/metrics");
            if ($response->successful()) {
                return $response->json();
            }
        } catch (Exception $e) {
            Log::warning('Gagal mengambil metrics model: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * Prediksi harga untuk satu titik waktu.
     *
     * @throws Exception jika service tidak bisa dihubungi atau mengembalikan error
     */
    public function predict(array $params): array
    {
        $response = Http::timeout($this->timeout)->post("{$this->baseUrl}/predict", $params);

        if ($response->failed()) {
            $detail = $response->json('detail', 'ML service tidak dapat memproses permintaan.');
            throw new Exception($detail);
        }

        return $response->json();
    }

    /**
     * Prediksi harga untuk beberapa bulan ke depan (forecasting).
     *
     * @throws Exception jika service tidak bisa dihubungi atau mengembalikan error
     */
    public function predictBatch(array $params): array
    {
        $response = Http::timeout($this->timeout)->post("{$this->baseUrl}/predict-batch", $params);

        if ($response->failed()) {
            $detail = $response->json('detail', 'ML service tidak dapat memproses permintaan.');
            throw new Exception($detail);
        }

        return $response->json();
    }

    /**
     * Memicu proses generate data + training ulang model dari sisi Laravel.
     *
     * @throws Exception jika training gagal
     */
    public function latihUlang(): array
    {
        $response = Http::timeout(120)->post("{$this->baseUrl}/train");

        if ($response->failed()) {
            $detail = $response->json('detail', 'Gagal melatih ulang model.');
            throw new Exception($detail);
        }

        return $response->json();
    }
}
