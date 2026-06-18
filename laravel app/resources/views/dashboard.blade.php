@extends('layouts.app')
@section('title','Dashboard')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h5 class="fw-bold mb-0">Dashboard Prediksi Harga Alpukat</h5>
  <span class="ml-status {{ $status['online'] ? 'online' : 'offline' }}">
    <span class="dot"></span> ML Service {{ $status['online'] ? 'Online' : 'Offline' }}
    @if($status['online'] && !$status['model_ready']) (Model belum dilatih) @endif
  </span>
</div>

@if(!$status['online'])
<div class="alert alert-warning">
  <i class="fas fa-exclamation-triangle me-2"></i>
  ML Service tidak terhubung. Pastikan service FastAPI sudah dijalankan (<code>uvicorn main:app --port 8001</code>) agar fitur prediksi dapat digunakan.
</div>
@endif

<div class="row g-4 mb-4">
  <div class="col-md-3 col-6">
    <div class="stat-card" style="background:linear-gradient(135deg,#4338ca,#6366f1)">
      <div class="d-flex justify-content-between"><div><div class="stat-val">{{ $totalDataHarga }}</div><div class="stat-lbl">Data Riwayat Harga</div></div><div class="stat-icon">📊</div></div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="stat-card" style="background:linear-gradient(135deg,#0891b2,#22d3ee)">
      <div class="d-flex justify-content-between"><div><div class="stat-val">{{ $totalPrediksi }}</div><div class="stat-lbl">Total Prediksi Dibuat</div></div><div class="stat-icon">🔮</div></div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="stat-card" style="background:linear-gradient(135deg,#16a34a,#4ade80)">
      <div class="d-flex justify-content-between"><div><div class="stat-val">{{ $jenisAlpukat->count() }}</div><div class="stat-lbl">Jenis Alpukat</div></div><div class="stat-icon">🥑</div></div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="stat-card" style="background:linear-gradient(135deg,#d97706,#fbbf24)">
      <div class="d-flex justify-content-between"><div><div class="stat-val">{{ $metrics['r2_score'] ?? '-' }}</div><div class="stat-lbl">Akurasi Model (R²)</div></div><div class="stat-icon">🎯</div></div>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card-kampung card">
      <div class="card-header"><i class="fas fa-chart-line me-2"></i>Tren Harga {{ $jenisUtama }} (Data Terakhir)</div>
      <div class="card-body">
        <canvas id="trenChart" height="220"></canvas>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card-kampung card">
      <div class="card-header"><i class="fas fa-list me-2"></i>Rata-Rata Harga per Jenis</div>
      <div class="card-body p-0">
        <table class="table table-ml mb-0">
          <thead><tr><th>Jenis</th><th>Rata-rata</th><th>Tertinggi</th></tr></thead>
          <tbody>
            @foreach($rataRataPerJenis as $r)
            <tr>
              <td class="fw-600">{{ $r->jenis_alpukat }}</td>
              <td>Rp {{ number_format($r->rata_rata,0,',','.') }}</td>
              <td class="text-success">Rp {{ number_format($r->tertinggi,0,',','.') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mt-1">
  <div class="col-12">
    <div class="card-kampung card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-history me-2"></i>Prediksi Terbaru</span>
        <a href="{{ route('prediksi.index') }}" class="btn btn-sm btn-light">Buat Prediksi Baru</a>
      </div>
      <div class="card-body p-0">
        <table class="table table-ml mb-0">
          <thead><tr><th>Jenis</th><th>Periode</th><th>Hasil Prediksi</th><th>Musim</th><th>Dibuat Oleh</th><th>Waktu</th></tr></thead>
          <tbody>
            @forelse($prediksiTerbaru as $p)
            <tr>
              <td class="fw-600">{{ $p->jenis_alpukat }}</td>
              <td>{{ $p->nama_bulan }} {{ $p->tahun_prediksi }}</td>
              <td class="text-primary fw-bold">Rp {{ number_format($p->hasil_prediksi,0,',','.') }}</td>
              <td><span class="badge {{ $p->musim_panen ? 'badge-musim-panen' : 'badge-musim-normal' }}">{{ $p->musim_panen ? 'Musim Panen' : 'Bukan Musim' }}</span></td>
              <td class="small text-muted">{{ $p->user->name ?? '-' }}</td>
              <td class="small text-muted">{{ $p->created_at->diffForHumans() }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada prediksi yang dibuat</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
const ctx = document.getElementById('trenChart');
new Chart(ctx, {
  type: 'line',
  data: {
    labels: {!! json_encode($trenHarga->pluck('tanggal')->map(fn($t) => \Carbon\Carbon::parse($t)->format('d M'))) !!},
    datasets: [{
      label: 'Harga per Kg (Rp)',
      data: {!! json_encode($trenHarga->pluck('harga_per_kg')) !!},
      borderColor: '#4338ca',
      backgroundColor: 'rgba(67,56,202,0.1)',
      tension: 0.3,
      fill: true,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: { y: { ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID') } } }
  }
});
</script>
@endpush
