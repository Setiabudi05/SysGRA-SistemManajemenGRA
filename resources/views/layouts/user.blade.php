<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SysGRA | @yield('title')</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    {{-- CSS Template Mazer --}}
    <link rel="stylesheet" href="{{ asset('assets-template/css/main/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-template/css/main/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-template/css/shared/iconly.css') }}">
    
    {{-- Flatpickr CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    {{-- Master CSS User --}}
    <link rel="stylesheet" href="{{ asset('assets-user/css/style-user.css') }}">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="shortcut icon" href="{{ asset('assets-admin/img/logo.png') }}" type="image/x-icon">

    @stack('css')
</head>

<body>
    <div id="app">
        <div id="main" class="layout-horizontal">
            <header class="mb-3">
                {{-- Bagian Atas: Logo (Kiri) & User Profil (Kanan) sejajar --}}
                <div class="header-top">
                    <div class="container d-flex justify-content-between align-items-center">
                        {{-- LOGO DI SISI KIRI --}}
                        <div class="logo">
                            <a href="{{ route('user.dashboard') }}">
                                <img src="{{ asset('assets-admin/img/logo.png') }}" alt="Logo SysGRA">
                            </a>
                        </div>

                        {{-- GROUP KANAN: PROFIL & BURGER --}}
                        <div class="header-top-right d-flex align-items-center">
                            <div class="dropdown">
                                <a href="#" id="topbarUserDropdown"
                                    class="user-dropdown d-flex align-items-center dropend dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="avatar avatar-md2">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=435ee0&color=fff&bold=true"
                                            alt="Avatar">
                                    </div>
                                    <div class="text text-start ms-3 d-none d-sm-block">
                                        <h6 class="user-dropdown-name mb-0">{{ Auth::user()->name }}</h6>
                                        <p class="user-dropdown-status text-sm text-muted mb-0">Pelanggan</p>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="topbarUserDropdown">
                                    <li>
                                        <a class="dropdown-item {{ Request::is('user/profile*') ? 'active' : '' }}" href="{{ route('user.profile.index') }}">
                                            <i class="bi bi-person-circle me-2"></i> Akun Saya
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                    </li>
                                </ul>
                            </div>
                            
                            {{-- Burger Menu Mobile --}}
                            <a href="#" class="burger-btn d-block d-xl-none ms-4" id="mobileMenuToggle">
                                <i class="bi bi-justify fs-2"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Navigasi Utama --}}
                <nav class="main-navbar" id="mainNavbar">
                    <div class="container">
                        <ul class="d-flex align-items-center">
                            <li class="menu-item {{ Request::is('user/dashboard*') ? 'active' : '' }}">
                                <a href="{{ route('user.dashboard') }}" class='menu-link'>
                                    <i class="bi bi-grid-fill"></i><span>Dashboard</span>
                                </a>
                            </li>
                            <li class="menu-item {{ Request::is('user/booking*') ? 'active' : '' }}">
                                <a href="{{ route('user.booking') }}" class="menu-link">
                                    <i class="bi bi-calendar-check-fill"></i><span>Booking Rias</span>
                                </a>
                            </li>
                            <li class="menu-item {{ Request::is('user/keranjang*') ? 'active' : '' }}">
                                <a href="{{ route('user.keranjang') }}" class='menu-link'>
                                    <i class="bi bi-cart-fill"></i><span>Keranjang</span>
                                </a>
                            </li>
                            <li class="menu-item {{ Request::is('user/pembayaran*') ? 'active' : '' }}">
                                <a href="{{ route('user.pembayaran') }}" class='menu-link'>
                                    <i class="bi bi-cash-stack"></i><span>Pembayaran</span>
                                </a>
                            </li>
                            <li class="menu-item {{ Request::is('user/riwayat*') ? 'active' : '' }}">
                                <a href="{{ route('user.riwayat') }}" class='menu-link'>
                                    <i class="bi bi-clock-history"></i><span>Riwayat</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>

            <div class="content-wrapper container">
                @yield('content')
            </div>

            <footer>
                <div class="container text-start small text-muted">
                    <div class="footer clearfix mb-0">
                        <div class="float-start">
                            <p>2026 &copy; Griya Rias Asmara</p>
                        </div>
                        <div class="float-end d-none d-sm-block">
                            <p>Sistem Manajemen Booking Wedding</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('assets-template/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets-template/js/app.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('mobileMenuToggle');
            const navbar = document.getElementById('mainNavbar');

            if(toggleBtn && navbar) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    navbar.classList.toggle('active');
                });
            }
        });
    </script>

    @stack('js')
</body>
</html>