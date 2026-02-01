<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | SysGRA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { width: 250px; height: 100vh; position: fixed; background: #1a1d20; color: white; transition: 0.3s; }
        .sidebar-link { padding: 15px 25px; display: block; color: #adb5bd; text-decoration: none; }
        .sidebar-link:hover, .sidebar-link.active { background: #e84545; color: white; }
        .content { margin-left: 250px; padding: 30px; }
    </style>
</head>
<body>
    <div class="sidebar shadow">
        <div class="p-4"><h4 class="fw-bold text-white">SysGRA <span class="text-danger">User</span></h4></div>
        <nav class="mt-2">
            <a href="{{ route('user.dashboard') }}" class="sidebar-link {{ request()->is('user/dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-fill me-2"></i> Dashboard
            </a>
            <a href="{{ route('pesanan.index') }}" class="sidebar-link {{ request()->is('pesanan-saya*') ? 'active' : '' }}">
                <i class="bi bi-cart-check-fill me-2"></i> Pesanan Saya
            </a>
            <a href="{{ route('profile') }}" class="sidebar-link">
                <i class="bi bi-person-fill me-2"></i> Profil
            </a>
            <hr class="border-secondary mx-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-link bg-transparent border-0 w-100 text-start">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>
        </nav>
    </div>

    <div class="content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>