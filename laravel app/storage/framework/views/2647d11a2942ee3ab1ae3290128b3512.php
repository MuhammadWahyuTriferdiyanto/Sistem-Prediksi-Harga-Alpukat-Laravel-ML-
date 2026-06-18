<?php $__env->startSection('title','Status Model ML'); ?>
<?php $__env->startSection('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h5 class="fw-bold mb-0">Status Model Machine Learning</h5>
  <span class="ml-status <?php echo e($status['online'] ? 'online' : 'offline'); ?>">
    <span class="dot"></span> Service <?php echo e($status['online'] ? 'Online' : 'Offline'); ?>

  </span>
</div>

<?php if(session('metrics_baru')): ?>
<div class="alert alert-success">
  <i class="fas fa-check-circle me-2"></i>Model baru selesai dilatih. Akurasi (R²): <strong><?php echo e(session('metrics_baru')['r2_score'] ?? '-'); ?></strong>
</div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="card-kampung card">
      <div class="card-header"><i class="fas fa-tachometer-alt me-2"></i>Performa Model (Random Forest Regressor)</div>
      <div class="card-body p-4">
        <?php if($metrics): ?>
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <div class="p-3 rounded text-center" style="background:#eef2ff">
              <p class="small text-muted mb-1">R² Score</p>
              <h4 class="fw-bold text-primary mb-0"><?php echo e($metrics['r2_score']); ?></h4>
              <p class="small text-muted mb-0">Semakin mendekati 1, semakin baik</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 rounded text-center" style="background:#fef3c7">
              <p class="small text-muted mb-1">MAPE</p>
              <h4 class="fw-bold mb-0" style="color:#92400e"><?php echo e($metrics['mape_percent']); ?>%</h4>
              <p class="small text-muted mb-0">Rata-rata persentase error</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 rounded text-center" style="background:#dcfce7">
              <p class="small text-muted mb-1">MAE</p>
              <h4 class="fw-bold text-success mb-0">Rp <?php echo e(number_format($metrics['mae'],0,',','.')); ?></h4>
              <p class="small text-muted mb-0">Rata-rata selisih prediksi</p>
            </div>
          </div>
        </div>

        <h6 class="fw-bold mb-2">Detail Training</h6>
        <table class="table table-sm">
          <tr><td class="text-muted" style="width:200px">Jumlah data training</td><td><?php echo e($metrics['n_train']); ?> baris</td></tr>
          <tr><td class="text-muted">Jumlah data testing</td><td><?php echo e($metrics['n_test']); ?> baris</td></tr>
          <tr><td class="text-muted">Fitur yang digunakan</td><td><code><?php echo e(implode(', ', $metrics['features'])); ?></code></td></tr>
          <tr><td class="text-muted">Jenis alpukat dikenali</td><td><?php echo e(implode(', ', $metrics['jenis_alpukat_classes'])); ?></td></tr>
        </table>
        <?php else: ?>
        <p class="text-muted text-center py-4">Metrics belum tersedia. Model mungkin belum pernah dilatih.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card-kampung card">
      <div class="card-header"><i class="fas fa-redo me-2"></i>Latih Ulang Model</div>
      <div class="card-body p-4">
        <p class="small text-muted">Proses ini akan generate ulang data dummy & melatih ulang model Random Forest dari awal. Gunakan setelah menambahkan banyak data riwayat harga baru.</p>
        <form method="POST" action="<?php echo e(route('model.latih-ulang')); ?>" onsubmit="return confirm('Latih ulang model? Proses ini mungkin memakan waktu beberapa saat.')">
          <?php echo csrf_field(); ?>
          <button type="submit" class="btn btn-accent w-100" <?php echo e(!$status['online'] ? 'disabled' : ''); ?>>
            <i class="fas fa-sync me-2"></i>Latih Ulang Sekarang
          </button>
        </form>
        <?php if(!$status['online']): ?>
        <p class="small text-danger mt-2 mb-0"><i class="fas fa-exclamation-circle me-1"></i>Service ML sedang offline.</p>
        <?php endif; ?>
      </div>
    </div>

    <div class="card-kampung card mt-4">
      <div class="card-header"><i class="fas fa-info-circle me-2"></i>Jenis Alpukat Dikenali</div>
      <div class="card-body p-4">
        <?php if(count($jenisAlpukat)): ?>
        <div class="d-flex flex-wrap gap-2">
          <?php $__currentLoopData = $jenisAlpukat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <span class="badge bg-secondary"><?php echo e($j); ?></span>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <p class="text-muted small mb-0">Tidak ada data, service mungkin offline.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ml-service\resources\views/model/index.blade.php ENDPATH**/ ?>