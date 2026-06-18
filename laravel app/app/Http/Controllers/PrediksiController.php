<?php

namespace App\Http\Controllers;

use App\Models\PrediksiLog;
use App\Models\RiwayatHarga;
use App\Services\MLService;
use Illuminate\Http\Request;
use Exception;

class PrediksiController extends Controller
{
    public function __construct(protected MLService $ml) {}

    public function index()
    {
        $status = $this->ml->health();
        $jenisAlpukat = $this->ml->getJenisAlpukat();

        // Fallback ke data riwayat jika ML service sedang offline
        if (empty($jenisAlpukat)) {
            $jenisAlpukat = RiwayatHarga::daftarJenis()->toArray();
        }

        $bulanIndo = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];

        return view('prediksi.index', compact('status', 'jenisAlpukat', 'bulanIndo'));
    }

    /**
     * Prediksi harga untuk satu titik waktu tertentu.
     */
    public function predict(Request $request)
    {
        $request->validate([
            'jenis_alpukat' => 'required|string',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2100',
            'curah_hujan_mm' => 'nullable|numeric|min:0',
            'pasokan_kg' => 'nullable|numeric|min:0',
        ], [
            'jenis_alpukat.required' => 'Pilih jenis alpukat terlebih dahulu.',
            'bulan.required' => 'Bulan wajib diisi.',
            'tahun.required' => 'Tahun wajib diisi.',
        ]);

        try {
            $hasil = $this->ml->predict([
                'jenis_alpukat' => $request->jenis_alpukat,
                'bulan' => (int) $request->bulan,
                'tahun' => (int) $request->tahun,
                'curah_hujan_mm' => (float) ($request->curah_hujan_mm ?? 180),
                'pasokan_kg' => (float) ($request->pasokan_kg ?? 800),
            ]);

            // Simpan log prediksi untuk riwayat/audit
            PrediksiLog::create([
                'jenis_alpukat' => $hasil['jenis_alpukat'],
                'bulan_prediksi' => $hasil['bulan'],
                'tahun_prediksi' => $hasil['tahun'],
                'hasil_prediksi' => $hasil['prediksi_harga_per_kg'],
                'confidence_low' => $hasil['confidence_range_low'],
                'confidence_high' => $hasil['confidence_range_high'],
                'musim_panen' => $hasil['musim_panen'],
                'parameter_input' => $request->only('curah_hujan_mm', 'pasokan_kg'),
                'user_id' => auth()->id(),
            ]);

            return back()->with('hasil_prediksi', $hasil)->with('success', 'Prediksi berhasil dihitung!');

        } catch (Exception $e) {
            return back()->with('error', 'Gagal melakukan prediksi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Prediksi harga untuk beberapa bulan ke depan (forecasting), dipakai untuk grafik tren.
     */
    public function forecast(Request $request)
    {
        $request->validate([
            'jenis_alpukat' => 'required|string',
            'bulan_mulai' => 'required|integer|min:1|max:12',
            'tahun_mulai' => 'required|integer|min:2020|max:2100',
            'jumlah_bulan' => 'required|integer|min:1|max:24',
        ]);

        try {
            $hasil = $this->ml->predictBatch([
                'jenis_alpukat' => $request->jenis_alpukat,
                'bulan_mulai' => (int) $request->bulan_mulai,
                'tahun_mulai' => (int) $request->tahun_mulai,
                'jumlah_bulan' => (int) $request->jumlah_bulan,
                'curah_hujan_mm' => (float) ($request->curah_hujan_mm ?? 180),
                'pasokan_kg' => (float) ($request->pasokan_kg ?? 800),
            ]);

            return response()->json(['success' => true, 'data' => $hasil]);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
