<?php

namespace App\Http\Controllers;

use App\Models\RiwayatHarga;
use Illuminate\Http\Request;

class RiwayatHargaController extends Controller
{
    public function index(Request $request)
    {
        $query = RiwayatHarga::latest('tanggal');

        if ($request->jenis_alpukat) {
            $query->where('jenis_alpukat', $request->jenis_alpukat);
        }
        if ($request->cari_tahun) {
            $query->whereYear('tanggal', $request->cari_tahun);
        }

        $riwayatHarga = $query->paginate(20)->withQueryString();
        $jenisAlpukat = RiwayatHarga::daftarJenis();

        return view('riwayat.index', compact('riwayatHarga', 'jenisAlpukat'));
    }

    public function create()
    {
        return view('riwayat.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_alpukat' => 'required|string|max:50',
            'harga_per_kg' => 'required|integer|min:0',
            'curah_hujan_mm' => 'nullable|numeric|min:0',
            'pasokan_kg' => 'nullable|numeric|min:0',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'jenis_alpukat.required' => 'Jenis alpukat wajib diisi.',
            'harga_per_kg.required' => 'Harga wajib diisi.',
        ]);

        $bulan = (int) date('n', strtotime($request->tanggal));
        $musimPanen = in_array($bulan, [2, 3, 4, 8, 9, 10]);

        RiwayatHarga::create([
            'tanggal' => $request->tanggal,
            'jenis_alpukat' => $request->jenis_alpukat,
            'harga_per_kg' => $request->harga_per_kg,
            'curah_hujan_mm' => $request->curah_hujan_mm,
            'pasokan_kg' => $request->pasokan_kg,
            'musim_panen' => $musimPanen,
            'catatan' => $request->catatan,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('riwayat-harga.index')
            ->with('success', 'Data harga berhasil ditambahkan. Jangan lupa latih ulang model agar prediksi memakai data terbaru.');
    }

    public function edit(RiwayatHarga $riwayatHarga)
    {
        return view('riwayat.edit', compact('riwayatHarga'));
    }

    public function update(Request $request, RiwayatHarga $riwayatHarga)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_alpukat' => 'required|string|max:50',
            'harga_per_kg' => 'required|integer|min:0',
        ]);

        $bulan = (int) date('n', strtotime($request->tanggal));
        $musimPanen = in_array($bulan, [2, 3, 4, 8, 9, 10]);

        $riwayatHarga->update([
            'tanggal' => $request->tanggal,
            'jenis_alpukat' => $request->jenis_alpukat,
            'harga_per_kg' => $request->harga_per_kg,
            'curah_hujan_mm' => $request->curah_hujan_mm,
            'pasokan_kg' => $request->pasokan_kg,
            'musim_panen' => $musimPanen,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('riwayat-harga.index')->with('success', 'Data harga berhasil diperbarui.');
    }

    public function destroy(RiwayatHarga $riwayatHarga)
    {
        $riwayatHarga->delete();
        return redirect()->route('riwayat-harga.index')->with('success', 'Data harga berhasil dihapus.');
    }

    /**
     * Import data dari file CSV yang diupload (format sama dengan data dummy ML service).
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt|max:5120',
        ], [
            'file_csv.required' => 'Pilih file CSV terlebih dahulu.',
            'file_csv.mimes' => 'File harus berformat CSV.',
        ]);

        $file = fopen($request->file('file_csv')->getRealPath(), 'r');
        $header = fgetcsv($file);
        $count = 0;

        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);
            if (!isset($data['tanggal'], $data['jenis_alpukat'], $data['harga_per_kg'])) {
                continue;
            }

            RiwayatHarga::create([
                'tanggal' => $data['tanggal'],
                'jenis_alpukat' => $data['jenis_alpukat'],
                'harga_per_kg' => (int) $data['harga_per_kg'],
                'curah_hujan_mm' => $data['curah_hujan_mm'] ?? null,
                'pasokan_kg' => $data['pasokan_kg'] ?? null,
                'musim_panen' => (bool) ($data['musim_panen'] ?? false),
                'catatan' => 'Diimpor dari CSV',
                'user_id' => auth()->id(),
            ]);
            $count++;
        }
        fclose($file);

        return redirect()->route('riwayat-harga.index')
            ->with('success', "Berhasil mengimpor {$count} baris data dari CSV.");
    }
}
