<header id="header" class="header fixed-top">
    <div class="container-fluid container-xl position-relative">

        <div class="top-row d-flex align-items-center justify-content-between">
            
            {{-- Bagian Kiri: LOGO --}}
            <a href="{{ url('/') }}" class="logo d-flex align-items-center">
                <img src="{{ asset('assets-admin/img/logo.png') }}" alt="Logo Griya Rias Asmara" class="logo-img">
                <h1 class="sitename">GriyaRiasAsmara</h1>
            </a>

            {{-- Bagian Kanan: AKSI PENTING (PROFIL, LOGIN, KERANJANG) --}}
            <div class="d-flex align-items-center">

                @auth
                    {{-- 1A. JIKA SUDAH LOGIN: Tautan Profil Pengguna --}}
                    <a href="{{ route('profile') }}" class="btn-login me-3">Profil</a> 

                    {{-- 1B. JIKA SUDAH LOGIN: Tombol Logout --}}
                    <form method="POST" action="{{ route('logout') }}" class="m-0 me-3"> 
                        @csrf
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); this.closest('form').submit();" class="btn-login">
                            Logout
                        </a>
                    </form>
                @else
                    {{-- 1C. JIKA BELUM LOGIN: Tombol Login --}}
                    <a href="{{ route('login') }}" class="btn-login me-3">Login</a>
                @endauth

                {{-- 2. Ikon KERANJANG BELANJA --}}
                <a href="{{ url('/keranjang-pesanan') }}" class="cart-icon position-relative"> 
                    <i class="bi bi-cart"></i>
                    <span id="cart-badge"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="display: none; font-size: 10px; padding: 4px 6px;">
                        0
                    </span>
                </a>
            </div>
        </div>
    </div>

    {{-- NAVIGASI UTAMA --}}
    <div class="nav-wrap">
        <div class="container d-flex justify-content-center position-relative">
            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="{{ url('/') }}#hero">Home</a></li>
                    <li><a href="{{ url('/') }}#about">Tentang Kami</a></li>
                    <li><a href="{{ url('/') }}#features">Layanan Kami</a></li>
                    <li><a href="{{ url('/') }}#gallery">Galeri</a></li>
                    <li><a href="{{ url('/') }}#pricing">Paket</a></li>
                    <li><a href="{{ url('/') }}#portfolio-unggulan">Portofolio</a></li>
                    <li><a href="{{ url('/') }}#vendor-partners">Mitra Vendor</a></li>
                    <li><a href="{{ url('/') }}#testimonials">Testimoni</a></li>
                    <li><a href="{{ url('/') }}#contact">Kontak</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>
        </div>
    </div>
</header>