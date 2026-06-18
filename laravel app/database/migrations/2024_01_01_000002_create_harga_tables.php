<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Data harga historis - dipakai sebagai referensi & bisa diexport untuk training ulang model
        Schema::create('riwayat_harga', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('jenis_alpukat');
            $table->integer('harga_per_kg');
            $table->decimal('curah_hujan_mm', 6, 1)->nullable();
            $table->decimal('pasokan_kg', 8, 1)->nullable();
            $table->boolean('musim_panen')->default(false);
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });

        // Log setiap kali user melakukan prediksi, untuk riwayat/audit
        Schema::create('prediksi_log', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_alpukat');
            $table->integer('bulan_prediksi');
            $table->integer('tahun_prediksi');
            $table->integer('hasil_prediksi');
            $table->integer('confidence_low')->nullable();
            $table->integer('confidence_high')->nullable();
            $table->boolean('musim_panen')->default(false);
            $table->json('parameter_input')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('prediksi_log');
        Schema::dropIfExists('riwayat_harga');
    }
};
