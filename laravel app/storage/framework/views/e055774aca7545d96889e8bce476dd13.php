<?php $__env->startSection('title','Data Riwayat Harga'); ?>
<?php $__env->startSection('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h5 class="fw-bold mb-0">Data Riwayat Harga (Data Training)</h5>
  <div class="d-flex gap-2">
    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importModal">
      <i class="fas fa-file-csv me-1"></i>Import CSV
    </button>
    <a href="<?php echo e(route('riwayat-harga.create')); ?>" class="btn btn-primary-ml"><i class="fas fa-plus me-1"></i>Tambah Data</a>
  </div>
</div>

<div class="alert alert-warning small">
  <i class="fas fa-info-circle me-1"></i>
  Setelah menambah/mengubah data secara signifikan, jangan lupa <a href="<?php echo e(route('model.index')); ?>">latih ulang model</a> agar prediksi mencerminkan data terbaru.
</div>

<div class="card-kampung card mb-4">
  <div class="card-body p-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-4">
        <select name="jenis_alpukat" class="form-select form-select-sm">
          <option value="">Semua Jenis</option>
          <?php $__currentLoopData = $jenisAlpukat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($j); ?>" <?php if(request('jenis_alpukat')==$j): echo 'selected'; endif; ?>><?php echo e($j); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-md-3">
        <input type="number" name="cari_tahun" class="form-control form-control-sm" placeholder="Tahun" value="<?php echo e(request('cari_tahun')); ?>">
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
        <?php $__empty_1 = true; $__currentLoopData = $riwayatHarga; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td class="small"><?php echo e($r->tanggal->format('d/m/Y')); ?></td>
          <td class="fw-600"><?php echo e($r->jenis_alpukat); ?></td>
          <td class="fw-bold text-primary">Rp <?php echo e(number_format($r->harga_per_kg,0,',','.')); ?></td>
          <td class="small"><?php echo e($r->curah_hujan_mm ?? '-'); ?> mm</td>
          <td class="small"><?php echo e($r->pasokan_kg ?? '-'); ?> kg</td>
          <td><span class="badge <?php echo e($r->musim_panen ? 'badge-musim-panen' : 'badge-musim-normal'); ?>"><?php echo e($r->musim_panen ? 'Panen' : 'Normal'); ?></span></td>
          <td>
            <div class="d-flex gap-1">
              <a href="<?php echo e(route('riwayat-harga.edit',$r->id)); ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
              <form method="POST" action="<?php echo e(route('riwayat-harga.destroy',$r->id)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-outline-danger" data-confirm="Hapus data ini?"><i class="fas fa-trash"></i></button></form>
            </div>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data riwayat harga</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer bg-white"><?php echo e($riwayatHarga->links()); ?></div>
</div>

<!-- MODAL IMPORT CSV -->
<div class="modal fade" id="importModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="<?php echo e(route('riwayat-harga.import')); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ml-service\resources\views/riwayat/index.blade.php ENDPATH**/ ?>