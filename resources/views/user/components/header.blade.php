<header id="header" class="header fixed-top">
    {{-- Memastikan CSS Custom Terpasang --}}
    <link href="{{ asset('assets-user/css/custom-style.css') }}" rel="stylesheet">

    <div class="container-fluid container-xl">
        <div class="top-row d-flex align-items-center justify-content-between">
            
            {{-- Bagian Kiri: LOGO --}}
            <a href="{{ url('/') }}" class="logo d-flex align-items-center">
                <img src="{{ asset('assets-admin/img/logo.png') }}" alt="Logo">
                <h1 class="sitename m-0">GriyaRiasAsmara</h1>
            </a>

            {{-- Bagian Kanan: AKSI PENTING --}}
            <div class="header-actions d-flex align-items-center">

                @auth
                    {{-- Dropdown Akun Saya --}}
                    <div class="dropdown dropdown-profile">
                        <a class="btn-account dropdown-toggle shadow-sm" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-2" style="font-size: 18px;"></i>
                            <span>Akun Saya</span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="bi bi-person"></i> Profil Saya
                                </a>
                            </li>
                            <li>
                                {{-- Fitur utama untuk cek detail booking & bayar bank --}}
                                <a class="dropdown-item" href="{{ url('/pesanan-saya') }}">
                                    <i class="bi bi-clock-history"></i> Pesanan Saya
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger w-100 border-0 bg-transparent" style="text-align: left;">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn-account">Login</a>
                @endauth

                {{-- Ikon KERANJANG --}}
                <a href="{{ url('/keranjang-pesanan') }}" class="cart-link position-relative"> 
                    <i class="bi bi-cart"></i>
                    {{-- Badge merah akan muncul otomatis jika ada pesanan --}}
                    <span id="cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow">
                        0
                    </span>
                </a>
            </div>

        </div>
    </div>

    {{-- NAVIGASI UTAMA --}}
    <div class="nav-wrap mt-2">
        <div class="container d-flex justify-content-center">
            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="{{ url('/') }}#hero">Home</a></li>
                    <li><a href="{{ url('/') }}#about">Tentang Kami</a></li>
                    <li><a href="{{ url('/') }}#features">Layanan</a></li>
                    <li><a href="{{ url('/') }}#gallery">Galeri</a></li>
                    <li><a href="{{ url('/') }}#pricing">Paket</a></li>
                    <li><a href="{{ url('/') }}#portfolio-unggulan">Portofolio</a></li>
                    <li><a href="{{ url('/') }}#vendor-partners">Mitra</a></li>
                    <li><a href="{{ url('/') }}#testimonials">Testimoni</a></li>
                    <li><a href="{{ url('/') }}#contact">Kontak</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>
        </div>
    </div>
</header>