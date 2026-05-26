<header id="header" class="header fixed-top">
    {{-- CSS Custom Tetap Dipasang --}}
    <link href="{{ asset('assets-user/css/custom-style.css') }}" rel="stylesheet">

    <style>
        /* 🌟 LOGIKA HEADER TRANSPARAN 🌟 */
        #header {
            background: transparent;
            transition: all 0.5s ease-in-out;
            z-index: 9999 !important;
            padding: 15px 0;
        }

        #header.header-scrolled {
            background: rgba(0, 0, 0, 0.85) !important;
            backdrop-filter: blur(10px);
            padding: 10px 0;
            box-shadow: 0px 2px 15px rgba(0, 0, 0, 0.1);
        }

        .sitename, .navmenu a {
            color: #ffffff !important;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }

        /* Update ke Deep Gold agar senada dengan Paket */
        .navmenu a.active, .navmenu a:hover, .text-gold-custom {
            color: #b8860b !important;
        }

        /* 🌟 TOMBOL LOGIN MODERN 🌟 */
        .btn-login-modern {
            color: #ffffff !important;
            background: transparent;
            border: 2px solid #b8860b;
            padding: 6px 18px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-login-modern:hover {
            background: #b8860b;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(184, 134, 11, 0.4);
        }

        /* Style Akun Saya saat Login */
        .btn-account-auth {
            background: rgba(184, 134, 11, 0.1);
            border: 1px solid #b8860b;
            color: #ffffff !important;
            padding: 6px 15px;
            border-radius: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .cart-link i {
            font-size: 22px;
            color: #ffffff;
        }

        .nav-wrap { background: transparent; }
    </style>

    <div class="container-fluid container-xl">
        <div class="top-row d-flex align-items-center justify-content-between">

            <a href="{{ route('landing') }}" class="logo d-flex align-items-center text-decoration-none">
                <img src="{{ asset('assets-admin/img/logo.png') }}" alt="Logo">
                <h1 class="sitename m-0 ms-2">GriyaRiasAsmara</h1>
            </a>

            <div class="header-actions d-flex align-items-center">
                @auth
                    {{-- Keranjang HANYA muncul jika sudah Login --}}
                    <a href="{{ route('user.keranjang') }}" class="cart-link position-relative me-4">
                        <i class="bi bi-cart3"></i>
                        <span id="cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow" style="font-size: 10px;">
                            {{ session('cart') ? count(session('cart')) : 0 }}
                        </span>
                    </a>

                    <div class="dropdown dropdown-profile">
                        <a class="btn-account-auth dropdown-toggle text-decoration-none" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-2"></i>
                            <span>Akun Saya</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="{{ route('user.profile') }}"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
                            <li><a class="dropdown-item" href="{{ route('user.riwayat') }}"><i class="bi bi-clock-history me-2"></i> Pesanan Saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger w-100 border-0 bg-transparent text-start">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    {{-- Tampilan saat Guest: Keranjang Hilang, Tombol Login Bagus --}}
                    <a href="{{ route('login') }}" class="btn-login-modern text-decoration-none">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Masuk</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <div class="nav-wrap mt-2">
        <div class="container d-flex justify-content-center">
            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="{{ route('landing') }}#hero" class="nav-link active">Home</a></li>
                    <li><a href="{{ route('landing') }}#about" class="nav-link">Tentang</a></li>
                    <li><a href="{{ route('landing') }}#features" class="nav-link">Layanan</a></li>
                    <li><a href="{{ route('landing') }}#gallery" class="nav-link">Galeri</a></li>
                    <li><a href="{{ route('landing') }}#paket" class="nav-link">Paket</a></li>
                    <li><a href="{{ route('landing') }}#portfolio-unggulan" class="nav-link">Portofolio</a></li>
                    <li><a href="{{ route('landing') }}#testimonials" class="nav-link">Testimoni</a></li>
                    <li><a href="{{ route('landing') }}#contact" class="nav-link">Kontak</a></li>
                    @auth
                        <li><a href="{{ route('user.dashboard') }}" class="fw-bold text-gold-custom">DASHBOARD</a></li>
                    @endauth
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>
        </div>
    </div>
</header>

<script>
    document.addEventListener('scroll', () => {
        const header = document.querySelector('#header');
        if (window.scrollY > 50) {
            header.classList.add('header-scrolled');
        } else {
            header.classList.remove('header-scrolled');
        }
    });
</script>