<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SysGRA | @yield('title')</title>

    {{-- Murni Menggunakan Google Fonts Resmi --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    {{-- CSS Template Mazer --}}
    <link rel="stylesheet" href="{{ asset('assets-template/css/main/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-template/css/main/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-template/css/shared/iconly.css') }}">

    {{-- Flatpickr CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    {{-- Master CSS User --}}
    <link rel="stylesheet" href="{{ asset('assets-user/css/style-user.css') }}">

    {{-- Icons Set --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="shortcut icon" href="{{ asset('assets-admin/img/logo.png') }}" type="image/x-icon">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        /* PENGATURAN FONT GLOBAL */
        body,
        html,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        span,
        p,
        table,
        th,
        td,
        button,
        input {
            font-family: 'Nunito', sans-serif !important;
        }

        /* PERBAIKAN NAVIGASI UTAMA: Main Navbar Horizontal */
        .main-navbar {
            background-color: #435ebe !important;
            padding: 0 !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        }

        .main-navbar .container {
            padding: 0 15px !important;
        }

        .main-navbar ul {
            padding-left: 0 !important;
            margin-bottom: 0 !important;
            list-style: none;
            display: flex !important;
            flex-direction: row;
        }

        .main-navbar .menu-item {
            position: relative;
        }

        .main-navbar .menu-item .menu-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 600 !important;
            font-size: 0.9rem !important;
            padding: 14px 20px !important;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
        }

        .main-navbar .menu-item .menu-link:hover {
            color: #fff !important;
            background-color: rgba(255, 255, 255, 0.12) !important;
        }

        .main-navbar .menu-item.active {
            background-color: rgba(0, 0, 0, 0.15) !important;
        }

        .main-navbar .menu-item.active .menu-link {
            color: #fff !important;
            font-weight: 700 !important;
        }

        .main-navbar .menu-item.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: #ffca28;
        }

        .main-navbar .menu-link i {
            font-size: 1.1rem !important;
            line-height: 0 !important;
        }

        /* RESPONSIVE MOBILE VIEW (NAVBAR VERTICAL CONVERSION) */
        @media (max-width: 1199.98px) {
            .main-navbar ul {
                flex-direction: column !important;
                display: none !important;
                background-color: #435ebe;
                padding: 10px 0 !important;
            }

            .main-navbar.active ul {
                display: flex !important;
            }

            .main-navbar .menu-item .menu-link {
                padding: 12px 20px !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            }

            .main-navbar .menu-item.active::after {
                display: none;
            }
        }
    </style>
    @stack('css')
</head>

<body>
    <div id="app">
        <div id="main" class="layout-horizontal">
            <header class="mb-3">
                {{-- TOPBAR: Logo & User Dropdown Section --}}
                <div class="header-top">
                    <div class="container d-flex justify-content-between align-items-center">
                        {{-- SISI KIRI: BRANDING LOGO --}}
                        <div class="logo">
                            <a href="{{ route('user.dashboard') }}">
                                <img src="{{ asset('assets-admin/img/logo.png') }}" alt="Logo SysGRA" style="height: 40px;">
                            </a>
                        </div>

                        {{-- SISI KANAN: PROFIL DROPDOWN & BURGER MOBILE --}}
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
                                        <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="confirmLogout()">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            
                            {{-- Burger Toggle Button for Mobile Devices --}}
                            <a href="#" class="burger-btn d-block d-xl-none ms-4" id="mobileMenuToggle">
                                <i class="bi bi-justify fs-2"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- NAVBAR HORIZONTAL: Menu Utama Pelanggan --}}
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

            {{-- MAIN VIEW SLOT ENGINE CONTENT --}}
            <div class="content-wrapper container">
                @yield('content')
            </div>

            {{-- APPLICATION FOOTER AREA --}}
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

    {{-- Form Logout Tersembunyi --}}
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

    {{-- JavaScript Core Vendors Template Mazer --}}
    <script src="{{ asset('assets-template/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets-template/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Script Tambahan --}}
    <script>
        // Navbar Toggle
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

        // SweetAlert Logout
        function confirmLogout() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Sesi Anda akan berakhir dan Anda harus login kembali.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#435ebe',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Logout!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            })
        }
    </script>

    @stack('js')
</body>
</html>