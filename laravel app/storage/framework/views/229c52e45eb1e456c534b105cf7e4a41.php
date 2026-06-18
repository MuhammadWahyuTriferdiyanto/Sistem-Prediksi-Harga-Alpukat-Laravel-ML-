<?php $__env->startSection('title','Prediksi Harga'); ?>
<?php $__env->startSection('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h5 class="fw-bold mb-0">Prediksi Harga Alpukat</h5>
  <span class="ml-status <?php echo e($status['online'] ? 'online' : 'offline'); ?>">
    <span class="dot"></span> ML Service <?php echo e($status['online'] ? 'Online' : 'Offline'); ?>

  </span>
</div>

<?php if(!$status['online']): ?>
<div class="alert alert-danger">
  <i class="fas fa-exclamation-triangle me-2"></i>
  ML Service tidak terhubung. Jalankan service Python (FastAPI) terlebih dahulu sebelum melakukan prediksi.
</div>
<?php endif; ?>

<div class="row g-4">
  <!-- FORM PREDIKSI SATU TITIK WAKTU -->
  <div class="col-lg-5">
    <div class="card-kampung card">
      <div class="card-header"><i class="fas fa-robot me-2"></i>Prediksi Satu Periode</div>
      <div class="card-body p-4">
        <form method="POST" action="<?php echo e(route('prediksi.predict')); ?>">
          <?php echo csrf_field(); ?>
          <div class="mb-3">
            <label class="form-label">Jenis Alpukat *</label>
            <select name="jenis_alpukat" class="form-select" required>
              <option value="">Pilih Jenis</option>
              <?php $__currentLoopData = $jenisAlpukat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($j); ?>" <?php if(old('jenis_alpukat')==$j): echo 'selected'; endif; ?>><?php echo e($j); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label">Bulan *</label>
              <select name="bulan" class="form-select" required>
                <?php $__currentLoopData = $bulanIndo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $nama): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($num); ?>" <?php if(old('bulan', date('n'))==$num): echo 'selected'; endif; ?>><?php echo e($nama); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label">Tahun *</label>
              <input type="number" name="tahun" class="form-control" value="<?php echo e(old('tahun', date('Y'))); ?>" min="2020" max="2100" required>
            </div>
          </div>
          <div class="row g-2 mb-4">
            <div class="col-6">
              <label class="form-label">Curah Hujan (mm)</label>
              <input type="number" name="curah_hujan_mm" class="form-control" value="<?php echo e(old('curah_hujan_mm', 180)); ?>" step="0.1" min="0">
            </div>
            <div class="col-6">
              <label class="form-label">Estimasi Pasokan (kg)</label>
              <input type="number" name="pasokan_kg" class="form-control" value="<?php echo e(old('pasokan_kg', 800)); ?>" step="0.1" min="0">
            </div>
          </div>
          <button type="submit" class="btn btn-primary-ml w-100 py-2 fw-bold" <?php echo e(!$status['online'] ? 'disabled' : ''); ?>>
            <i class="fas fa-magic me-2"></i>Prediksi Harga
          </button>
        </form>
      </div>
    </div>

    <?php if(session('hasil_prediksi')): ?>
    <?php $h = session('hasil_prediksi'); ?>
    <div class="hasil-prediksi-box mt-4">
      <p class="mb-1 opacity-75 small">Prediksi Harga <?php echo e($h['jenis_alpukat']); ?> - Bulan <?php echo e($bulanIndo[$h['bulan']]); ?> <?php echo e($h['tahun']); ?></p>
      <div class="harga-utama">Rp <?php echo e(number_format($h['prediksi_harga_per_kg'],0,',','.')); ?></div>
      <p class="mb-2 opacity-75">per kilogram</p>
      <span class="badge <?php echo e($h['musim_panen'] ? 'badge-musim-panen' : 'badge-musim-normal'); ?>">
        <?php echo e($h['musim_panen'] ? '🌾 Musim Panen Raya' : '📈 Bukan Musim Panen'); ?>

      </span>
      <div class="confidence-range mt-2">
        <small>Rentang estimasi: Rp <?php echo e(number_format($h['confidence_range_low'],0,',','.')); ?> - Rp <?php echo e(number_format($h['confidence_range_high'],0,',','.')); ?></small>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- FORECAST BEBERAPA BULAN KE DEPAN -->
  <div class="col-lg-7">
    <div class="card-kampung card">
      <div class="card-header"><i class="fas fa-chart-area me-2"></i>Forecast Beberapa Bulan ke Depan</div>
      <div class="card-body p-4">
        <div class="row g-2 mb-3">
          <div class="col-md-4">
            <label class="form-label small">Jenis Alpukat</label>
            <select id="fc_jenis" class="form-select form-select-sm">
              <?php $__currentLoopData = $jenisAlpukat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($j); ?>"><?php echo e($j); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small">Mulai Bulan</label>
            <select id="fc_bulan" class="form-select form-select-sm">
              <?php $__currentLoopData = $bulanIndo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $nama): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($num); ?>" <?php if(date('n')==$num): echo 'selected'; endif; ?>><?php echo e($nama); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small">Tahun</label>
            <input type="number" id="fc_tahun" class="form-control form-control-sm" value="<?php echo e(date('Y')); ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label small">Jumlah Bulan</label>
            <select id="fc_jumlah" class="form-select form-select-sm">
              <option value="3">3 Bulan</option>
              <option value="6" selected>6 Bulan</option>
              <option value="12">12 Bulan</option>
            </select>
          </div>
        </div>
        <button id="btnForecast" class="btn btn-accent w-100 mb-3" <?php echo e(!$status['online'] ? 'disabled' : ''); ?>>
          <i class="fas fa-chart-line me-2"></i>Tampilkan Forecast
        </button>
        <div id="forecastLoading" class="text-center text-muted py-3" style="display:none">
          <i class="fas fa-spinner fa-spin me-2"></i>Menghitung prediksi...
        </div>
        <canvas id="forecastChart" height="220"></canvas>
        <div id="forecastError" class="alert alert-danger mt-3" style="display:none"></div>
      </div>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let forecastChartInstance = null;

document.getElementById('btnForecast').addEventListener('click', async function() {
  const jenis = document.getElementById('fc_jenis').value;
  const bulan = document.getElementById('fc_bulan').value;
  const tahun = document.getElementById('fc_tahun').value;
  const jumlah = document.getElementById('fc_jumlah').value;

  const loading = document.getElementById('forecastLoading');
  const errorBox = document.getElementById('forecastError');
  errorBox.style.display = 'none';
  loading.style.display = 'block';

  try {
    const response = await fetch('<?php echo e(route("prediksi.forecast")); ?>', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
      },
      body: JSON.stringify({
        jenis_alpukat: jenis,
        bulan_mulai: parseInt(bulan),
        tahun_mulai: parseInt(tahun),
        jumlah_bulan: parseInt(jumlah),
      }),
    });
    const result = await response.json();
    loading.style.display = 'none';

    if (!result.success) {
      errorBox.textContent = result.message || 'Gagal mengambil data forecast.';
      errorBox.style.display = 'block';
      return;
    }

    const labels = result.data.forecast.map(f => `${f.bulan}/${f.tahun}`);
    const harga = result.data.forecast.map(f => f.prediksi_harga_per_kg);
    const low = result.data.forecast.map(f => f.confidence_range_low);
    const high = result.data.forecast.map(f => f.confidence_range_high);

    if (forecastChartInstance) forecastChartInstance.destroy();

    const ctx = document.getElementById('forecastChart');
    forecastChartInstance = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          { label: 'Prediksi Harga', data: harga, borderColor: '#4338ca', backgroundColor: 'rgba(67,56,202,0.1)', tension: 0.3, fill: true },
          { label: 'Batas Atas', data: high, borderColor: '#94a3b8', borderDash: [5,5], pointRadius: 0, fill: false },
          { label: 'Batas Bawah', data: low, borderColor: '#94a3b8', borderDash: [5,5], pointRadius: 0, fill: false },
        ]
      },
      options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID') } } }
      }
    });
  } catch (err) {
    loading.style.display = 'none';
    errorBox.textContent = 'Terjadi kesalahan: ' + err.message;
    errorBox.style.display = 'block';
  }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ml-service\resources\views/prediksi/index.blade.php ENDPATH**/ ?>