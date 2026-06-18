<?php

namespace App\Http\Controllers;

use App\Models\RiwayatHarga;
use App\Models\PrediksiLog;
use App\Services\MLService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(protected MLService $ml) {}

    public function index()
    {
        $status = $this->ml->health();

        $totalDataHarga = RiwayatHarga::count();
        $totalPrediksi = PrediksiLog::count();
        $jenisAlpukat = RiwayatHarga::daftarJenis();

        // Harga rata-rata per jenis alpukat (untuk grafik ringkasan)
        $rataRataPerJenis = RiwayatHarga::selectRaw('jenis_alpukat, AVG(harga_per_kg) as rata_rata, MAX(harga_per_kg) as tertinggi, MIN(harga_per_kg) as terendah')
            ->groupBy('jenis_alpukat')
            ->get();

        // Tren harga 12 data terakhir (per jenis terpopuler / pertama)
        $jenisUtama = $jenisAlpukat->first() ?? 'Miki';
        $trenHarga = RiwayatHarga::jenis($jenisUtama)
            ->orderByDesc('tanggal')
            ->take(12)
            ->get()
            ->sortBy('tanggal')
            ->values();

        $prediksiTerbaru = PrediksiLog::with('user')->latest()->take(5)->get();

        $metrics = $this->ml->getMetrics();

        return view('dashboard', compact(
            'status', 'totalDataHarga', 'totalPrediksi', 'jenisAlpukat',
            'rataRataPerJenis', 'trenHarga', 'jenisUtama', 'prediksiTerbaru', 'metrics'
        ));
    }
}
