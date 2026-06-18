<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PrediksiLog extends Model {
    protected $table = 'prediksi_log';
    protected $fillable = [
        'jenis_alpukat','bulan_prediksi','tahun_prediksi','hasil_prediksi',
        'confidence_low','confidence_high','musim_panen','parameter_input','user_id',
    ];
    protected $casts = [
        'musim_panen' => 'boolean',
        'parameter_input' => 'array',
    ];
    public function user() { return $this->belongsTo(User::class); }

    public function getNamaBulanAttribute() {
        $bulanIndo = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                      7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        return $bulanIndo[$this->bulan_prediksi] ?? $this->bulan_prediksi;
    }
}
