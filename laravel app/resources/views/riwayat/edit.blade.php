@extends('layouts.app')
@section('title','Edit Data Harga')
@section('content')

<div class="d-flex justify-content-between mb-4">
  <h5 class="fw-bold mb-0">Edit Data Riwayat Harga</h5>
  <a href="{{ route('riwayat-harga.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
</div>

<div class="card-kampung card">
  <div class="card-body p-4">
    <form method="POST" action="{{ route('riwayat-harga.update',$riwayatHarga->id) }}">
      @csrf @method('PUT')
      <div class="row g-4">
        <div class="col-md-6">
          <label class="form-label">Tanggal *</label>
          <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal',$riwayatHarga->tanggal->format('Y-m-d')) }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Jenis Alpukat *</label>
          <input type="text" name="jenis_alpukat" class="form-control" value="{{ old('jenis_alpukat',$riwayatHarga->jenis_alpukat) }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Harga per Kg (Rp) *</label>
          <input type="number" name="harga_per_kg" class="form-control" value="{{ old('harga_per_kg',$riwayatHarga->harga_per_kg) }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Curah Hujan (mm)</label>
          <input type="number" name="curah_hujan_mm" class="form-control" step="0.1" value="{{ old('curah_hujan_mm',$riwayatHarga->curah_hujan_mm) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Estimasi Pasokan (kg)</label>
          <input type="number" name="pasokan_kg" class="form-control" step="0.1" value="{{ old('pasokan_kg',$riwayatHarga->pasokan_kg) }}">
        </div>
        <div class="col-12">
          <label class="form-label">Catatan</label>
          <textarea name="catatan" class="form-control" rows="2">{{ old('catatan',$riwayatHarga->catatan) }}</textarea>
        </div>
        <div class="col-12 d-flex gap-3">
          <button type="submit" class="btn btn-primary-ml px-5"><i class="fas fa-save me-2"></i>Perbarui</button>
          <a href="{{ route('riwayat-harga.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
