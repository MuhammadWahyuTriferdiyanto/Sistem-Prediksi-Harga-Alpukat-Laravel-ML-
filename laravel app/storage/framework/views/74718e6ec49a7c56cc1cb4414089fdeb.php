<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $__env->yieldContent('title','Dashboard'); ?> - Prediksi Harga Alpukat</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
<?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>

<div class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <h4>🥑 Prediksi Harga</h4>
    <p>Sistem ML - Kampung Alpukat</p>
  </div>
  <nav class="mt-3 px-2">
    <a href="<?php echo e(route('dashboard')); ?>" class="nav-link <?php if(request()->routeIs('dashboard')): ?> active <?php endif; ?>">
      <i class="fas fa-chart-line"></i> Dashboard
    </a>
    <a href="<?php echo e(route('prediksi.index')); ?>" class="nav-link <?php if(request()->routeIs('prediksi.*')): ?> active <?php endif; ?>">
      <i class="fas fa-robot"></i> Prediksi Harga
    </a>
    <a href="<?php echo e(route('riwayat-harga.index')); ?>" class="nav-link <?php if(request()->routeIs('riwayat-harga.*')): ?> active <?php endif; ?>">
      <i class="fas fa-database"></i> Data Riwayat Harga
    </a>
    <a href="<?php echo e(route('model.index')); ?>" class="nav-link <?php if(request()->routeIs('model.*')): ?> active <?php endif; ?>">
      <i class="fas fa-cogs"></i> Status Model ML
    </a>
    <div class="mt-4 px-3">
      <form method="POST" action="<?php echo e(route('auth.logout')); ?>">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-sm btn-outline-light w-100"><i class="fas fa-sign-out-alt me-1"></i>Logout</button>
      </form>
    </div>
  </nav>
</div>

<div class="content">
  <div class="topbar">
    <div class="d-flex align-items-center gap-3">
      <button id="sidebarToggle" class="btn btn-sm btn-outline-secondary d-md-none"><i class="fas fa-bars"></i></button>
      <h6 class="mb-0 fw-bold"><?php echo $__env->yieldContent('title','Dashboard'); ?></h6>
    </div>
    <span class="text-muted small"><i class="fas fa-user-circle me-1"></i><?php echo e(auth()->user()->name ?? ''); ?></span>
  </div>

  <div class="main">
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle me-2"></i><?php echo e(session('error')); ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo e(asset('js/app.js')); ?>"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\ml-service\resources\views/layouts/app.blade.php ENDPATH**/ ?>