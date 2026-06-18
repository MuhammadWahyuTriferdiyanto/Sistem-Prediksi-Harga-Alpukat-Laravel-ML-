<?php

namespace App\Http\Controllers;

use App\Services\MLService;
use Exception;

class ModelController extends Controller
{
    public function __construct(protected MLService $ml) {}

    public function index()
    {
        $status = $this->ml->health();
        $metrics = $this->ml->getMetrics();
        $jenisAlpukat = $this->ml->getJenisAlpukat();

        return view('model.index', compact('status', 'metrics', 'jenisAlpukat'));
    }

    public function latihUlang()
    {
        try {
            $hasil = $this->ml->latihUlang();
            return back()->with('success', 'Model berhasil dilatih ulang dengan data terbaru!')
                ->with('metrics_baru', $hasil['metrics'] ?? null);
        } catch (Exception $e) {
            return back()->with('error', 'Gagal melatih ulang model: ' . $e->getMessage());
        }
    }
}
