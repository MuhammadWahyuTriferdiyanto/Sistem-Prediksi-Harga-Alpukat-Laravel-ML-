<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Sistem Prediksi Harga Alpukat</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
</head>
<body>
<div class="login-wrapper">
  <div class="login-card">
    <div class="login-logo">🥑📈</div>
    <h4 class="text-center fw-bold mb-1" style="color:#312e81">Prediksi Harga Alpukat</h4>
    <p class="text-center text-muted small mb-4">Sistem Machine Learning - Random Forest</p>

    <?php if($errors->any()): ?>
    <div class="alert alert-danger small"><i class="fas fa-exclamation-circle me-1"></i><?php echo e($errors->first()); ?></div>
    <?php endif; ?>
    <?php if(session('success')): ?>
    <div class="alert alert-success small"><i class="fas fa-check me-1"></i><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('auth.login')); ?>">
      <?php echo csrf_field(); ?>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" placeholder="admin@prediksialpukat.id" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="input-group">
          <input type="password" name="password" class="form-control" placeholder="••••••••" required>
          <button class="btn btn-outline-secondary" type="button" data-toggle-password><i class="fas fa-eye"></i></button>
        </div>
      </div>
      <div class="mb-4 form-check">
        <input type="checkbox" name="remember" class="form-check-input" id="remember">
        <label class="form-check-label small" for="remember">Ingat saya</label>
      </div>
      <button type="submit" class="btn btn-primary-ml w-100 py-2 fw-bold">
        <i class="fas fa-sign-in-alt me-2"></i>Masuk
      </button>
    </form>
    <div class="mt-3 p-3 rounded" style="background:#eef2ff">
      <p class="small text-muted mb-1"><strong>Demo Login:</strong></p>
      <p class="small mb-0">Email: <code>admin@prediksialpukat.id</code></p>
      <p class="small mb-0">Password: <code>admin123</code></p>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo e(asset('js/app.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\laragon\www\ml-service\resources\views/auth/login.blade.php ENDPATH**/ ?>