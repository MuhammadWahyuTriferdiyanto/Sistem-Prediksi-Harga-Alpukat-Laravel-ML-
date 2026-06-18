@extends('layouts.app')
@section('title','Data Riwayat Harga')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h5 class="fw-bold mb-0">Data Riwayat Harga (Data Training)</h5>
  <div class="d-flex gap-2">
    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importModal">
      <i class="fas fa-file-csv me-1"></i>Import CSV
    </button>
    <a href="{{ route('riwayat-harga.create') }}" class="btn btn-primary-ml"><i class="fas fa-plus me-1"></i>Tambah Data</a>
  </div>
</div>

<div class="alert alert-warning small">
  <i class="fas fa-info-circle me-1"></i>
  Setelah menambah/mengubah data secara signifikan, jangan lupa <a href="{{ route('model.index') }}">latih ulang model</a> agar prediksi mencerminkan data terbaru.
</div>

<div class="card-kampung card mb-4">
  <div class="card-body p-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-4">
        <select name="jenis_alpukat" class="form-select form-select-sm">
          <option value="">Semua Jenis</option>
          @foreach($jenisAlpukat as $j)
          <option value="{{ $j }}" @selected(request('jenis_alpukat')==$j)>{{ $j }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <input type="number" name="cari_tahun" class="form-control form-control-sm" placeholder="Tahun" value="{{ request('cari_tahun') }}">
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary-ml btn-sm w-100">Filter</button>
      </div>
    </form>
  </div>
</div>

<div class="card-kampung card">
  <div class="table-responsive">
    <table class="table table-ml mb-0">
      <thead><tr><th>Tanggal</th><th>Jenis</th><th>Harga/kg</th><th>Curah Hujan</th><th>Pasokan</th><th>Musim</th><th>Aksi</th></tr></thead>
      <tbody>
        @forelse($riwayatHarga as $r)
        <tr>
          <td class="small">{{ $r->tanggal->format('d/m/Y') }}</td>
          <td class="fw-600">{{ $r->jenis_alpukat }}</td>
          <td class="fw-bold text-primary">Rp {{ number_format($r->harga_per_kg,0,',','.') }}</td>
          <td class="small">{{ $r->curah_hujan_mm ?? '-' }} mm</td>
          <td class="small">{{ $r->pasokan_kg ?? '-' }} kg</td>
          <td><span class="badge {{ $r->musim_panen ? 'badge-musim-panen' : 'badge-musim-normal' }}">{{ $r->musim_panen ? 'Panen' : 'Normal' }}</span></td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('riwayat-harga.edit',$r->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
              <form method="POST" action="{{ route('riwayat-harga.destroy',$r->id) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" data-confirm="Hapus data ini?"><i class="fas fa-trash"></i></button></form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data riwayat harga</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer bg-white">{{ $riwayatHarga->links() }}</div>
</div>

<!-- MODAL IMPORT CSV -->
<div class="modal fade" id="importModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('riwayat-harga.import') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h6 class="modal-title fw-bold">Import Data dari CSV</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="small text-muted">Format kolom CSV: <code>tanggal,jenis_alpukat,bulan,tahun,curah_hujan_mm,pasokan_kg,musim_panen,harga_per_kg</code></p>
          <input type="file" name="file_csv" class="form-control" accept=".csv" required>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary-ml">Import</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
