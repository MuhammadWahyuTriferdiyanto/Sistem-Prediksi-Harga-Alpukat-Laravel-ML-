<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RiwayatHargaSeeder extends Seeder
{
    /**
     * Mengimpor data harga alpukat dummy dari file CSV.
     * File CSV ini adalah data yang sama persis dengan yang dipakai
     * untuk melatih model Random Forest di ML service, sehingga
     * riwayat yang tampil di Laravel konsisten dengan data training model.
     */
    public function run(): void
    {
        $csvPath = __DIR__ . '/harga_alpukat_dummy.csv';

        if (!file_exists($csvPath)) {
            $this->command->warn('File harga_alpukat_dummy.csv tidak ditemukan, seeder riwayat harga dilewati.');
            return;
        }

        $file = fopen($csvPath, 'r');
        $header = fgetcsv($file); // baca baris header: tanggal,jenis_alpukat,bulan,tahun,curah_hujan_mm,pasokan_kg,musim_panen,harga_per_kg

        $batch = [];
        $batchSize = 200;
        $total = 0;

        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);

            $batch[] = [
                'tanggal' => $data['tanggal'],
                'jenis_alpukat' => $data['jenis_alpukat'],
                'harga_per_kg' => (int) $data['harga_per_kg'],
                'curah_hujan_mm' => (float) $data['curah_hujan_mm'],
                'pasokan_kg' => (float) $data['pasokan_kg'],
                'musim_panen' => (bool) $data['musim_panen'],
                'catatan' => 'Data dummy hasil generate otomatis (training set ML)',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $batchSize) {
                DB::table('riwayat_harga')->insert($batch);
                $total += count($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('riwayat_harga')->insert($batch);
            $total += count($batch);
        }

        fclose($file);

        $this->command->info("Berhasil mengimpor {$total} baris data riwayat harga.");
    }
}
