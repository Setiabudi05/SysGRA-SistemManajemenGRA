<header id="header" class="header fixed-top">
    {{-- CSS Custom Tetap Dipasang --}}
    <link href="{{ asset('assets-user/css/custom-style.css') }}" rel="stylesheet">

    <div class="container-fluid container-xl">
        <div class="top-row d-flex align-items-center justify-content-between">
            
            <a href="{{ url('/') }}" class="logo d-flex align-items-center">
                <img src="{{ asset('assets-admin/img/logo.png') }}" alt="Logo">
                <h1 class="sitename m-0">GriyaRiasAsmara</h1>
            </a>

            <div class="header-actions d-flex align-items-center">
                @auth
                    {{-- DROPDOWN AKUN SAYA (Setelah Login) --}}
                    <div class="dropdown dropdown-profile">
                        <a class="btn-account dropdown-toggle shadow-sm" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-2"></i>
                            <span>Akun Saya</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
                            <li><a class="dropdown-item" href="{{ url('/pesanan-saya') }}"><i class="bi bi-clock-history me-2"></i> Pesanan Saya</a></li>
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
                    {{-- TOMBOL LOGIN (Sebelum Login) --}}
                    <a href="{{ route('login') }}" class="btn-account text-decoration-none">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </a>
                @endauth

                {{-- IKON KERANJANG (Selalu Ada) --}}
                <a href="{{ url('/keranjang-pesanan') }}" class="cart-link position-relative ms-3"> 
                    <i class="bi bi-cart"></i>
                    <span id="cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow" style="font-size: 10px;">
                        {{ session('cart') ? count(session('cart')) : 0 }}
                    </span>
                </a>
            </div>
        </div>
    </div>

    {{-- NAVIGASI UTAMA DINAMIS --}}
    <div class="nav-wrap mt-2">
        <div class="container d-flex justify-content-center">
            <nav id="navmenu" class="navmenu">
                <ul>
                    {{-- Menu Dasar yang Selalu Tampil --}}
                    <li><a href="{{ url('/') }}#hero">Home</a></li>
                    <li><a href="{{ url('/') }}#features">Layanan</a></li>
                    <li><a href="{{ url('/') }}#pricing">Paket</a></li>

                    @guest
                        {{-- MENU MARKETING (Hanya tampil saat BELUM Login) --}}
                        <li><a href="{{ url('/') }}#about">Tentang Kami</a></li>
                        <li><a href="{{ url('/') }}#gallery">Galeri</a></li>
                        <li><a href="{{ url('/') }}#portfolio-unggulan">Portofolio</a></li>
                        <li><a href="{{ url('/') }}#testimonials">Testimoni</a></li>
                        <li><a href="{{ url('/') }}#contact">Kontak</a></li>
                    @endguest

                    @auth
                        {{-- MENU TRANSAKSI (Hanya tampil saat SUDAH Login) --}}
                        <li><a href="{{ url('/pesanan-saya') }}" class="text-danger fw-bold">Status Booking</a></li>
                    @endauth
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>
        </div>
    </div>
</header>