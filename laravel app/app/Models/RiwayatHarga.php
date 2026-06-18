<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class RiwayatHarga extends Model {
    protected $table = 'riwayat_harga';
    protected $fillable = [
        'tanggal','jenis_alpukat','harga_per_kg','curah_hujan_mm',
        'pasokan_kg','musim_panen','catatan','user_id',
    ];
    protected $casts = [
        'tanggal' => 'date',
        'musim_panen' => 'boolean',
    ];
    public function user() { return $this->belongsTo(User::class); }

    public function scopeJenis($q, $jenis) { return $q->where('jenis_alpukat', $jenis); }

    public static function daftarJenis() {
        return static::distinct()->pluck('jenis_alpukat')->sort()->values();
    }
}
