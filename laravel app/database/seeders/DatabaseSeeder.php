<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun admin default
        DB::table('users')->insert([
            'name' => 'Administrator',
            'email' => 'admin@prediksialpukat.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Import data harga historis dummy dari CSV (data yang sama dipakai untuk training model ML)
        $this->call(RiwayatHargaSeeder::class);
    }
}
