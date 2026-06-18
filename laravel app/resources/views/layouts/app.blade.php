<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title','Dashboard') - Prediksi Harga Alpukat</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@stack('styles')
</head>
<body>

<div class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <h4>🥑 Prediksi Harga</h4>
    <p>Sistem ML - Kampung Alpukat</p>
  </div>
  <nav class="mt-3 px-2">
    <a href="{{ route('dashboard') }}" class="nav-link @if(request()->routeIs('dashboard')) active @endif">
      <i class="fas fa-chart-line"></i> Dashboard
    </a>
    <a href="{{ route('prediksi.index') }}" class="nav-link @if(request()->routeIs('prediksi.*')) active @endif">
      <i class="fas fa-robot"></i> Prediksi Harga
    </a>
    <a href="{{ route('riwayat-harga.index') }}" class="nav-link @if(request()->routeIs('riwayat-harga.*')) active @endif">
      <i class="fas fa-database"></i> Data Riwayat Harga
    </a>
    <a href="{{ route('model.index') }}" class="nav-link @if(request()->routeIs('model.*')) active @endif">
      <i class="fas fa-cogs"></i> Status Model ML
    </a>
    <div class="mt-4 px-3">
      <form method="POST" action="{{ route('auth.logout') }}">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-light w-100"><i class="fas fa-sign-out-alt me-1"></i>Logout</button>
      </form>
    </div>
  </nav>
</div>

<div class="content">
  <div class="topbar">
    <div class="d-flex align-items-center gap-3">
      <button id="sidebarToggle" class="btn btn-sm btn-outline-secondary d-md-none"><i class="fas fa-bars"></i></button>
      <h6 class="mb-0 fw-bold">@yield('title','Dashboard')</h6>
    </div>
    <span class="text-muted small"><i class="fas fa-user-circle me-1"></i>{{ auth()->user()->name ?? '' }}</span>
  </div>

  <div class="main">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @yield('content')
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
