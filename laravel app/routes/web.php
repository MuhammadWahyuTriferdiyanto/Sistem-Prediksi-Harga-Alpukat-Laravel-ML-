<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PrediksiController;
use App\Http\Controllers\RiwayatHargaController;
use App\Http\Controllers\ModelController;

// AUTH
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// SEMUA ROUTE BERIKUT WAJIB LOGIN (sistem internal admin/petani pengelola)
Route::middleware(['auth'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Prediksi harga
    Route::get('/prediksi', [PrediksiController::class, 'index'])->name('prediksi.index');
    Route::post('/prediksi', [PrediksiController::class, 'predict'])->name('prediksi.predict');
    Route::post('/prediksi/forecast', [PrediksiController::class, 'forecast'])->name('prediksi.forecast');

    // Riwayat data harga (data training)
    Route::resource('/riwayat-harga', RiwayatHargaController::class)
        ->parameters(['riwayat-harga' => 'riwayatHarga']);
    Route::post('/riwayat-harga/import', [RiwayatHargaController::class, 'import'])->name('riwayat-harga.import');

    // Pengaturan & status model ML
    Route::get('/model', [ModelController::class, 'index'])->name('model.index');
    Route::post('/model/latih-ulang', [ModelController::class, 'latihUlang'])->name('model.latih-ulang');
});
