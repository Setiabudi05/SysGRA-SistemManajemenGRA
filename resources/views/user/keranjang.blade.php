<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <title>Keranjang Pesanan - Griya Rias Asmara</title>
    
    <meta name="description" content="Jasa makeup, dekorasi, dan katering pernikahan profesional.">
    <meta name="keywords" content="wedding organizer, rias pengantin, catering pernikahan, dekorasi pernikahan">

    <link href="{{ asset('assets-admin/img/logo.png') }}" rel="icon">
    <link href="{{ asset('assets-admin/img/logo.png') }}" rel="apple-touch-icon">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">

    <link href="{{ asset('assets-admin/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets-admin/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets-admin/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('assets-admin/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets-admin/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="{{ asset('assets-admin/css/main.css') }}" rel="stylesheet">
</head>

<body class="index-page" data-cart-count="{{ count(Session::get('cart', [])) }}">

    <header id="header" class="header fixed-top">
        <div class="container-fluid container-xl position-relative">
            <div class="top-row d-flex align-items-center justify-content-between">
                <a href="{{ url('/') }}" class="logo d-flex align-items-center">
                    <img src="{{ asset('assets-admin/img/logo.png') }}" alt="Logo Griya Rias Asmara" class="logo-img">
                    <h1 class="sitename">GriyaRiasAsmara</h1>
                </a>
                <div class="d-flex align-items-center">
                    
                    <a href="{{ url('/keranjang-pesanan') }}" class="cart-icon position-relative">
                        <i class="bi bi-cart"></i>
                        <span id="cart-badge" 
                              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                              style="display: none; font-size: 10px; padding: 4px 6px;">
                            0
                        </span>
                    </a>
                    
                    <div class="social-links">
                        <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="nav-wrap">
            <div class="container d-flex justify-content-center position-relative">
                <nav id="navmenu" class="navmenu">
                    <ul>
                        <li><a href="{{ url('/user/index') }}#hero">Home</a></li>
                        <li><a href="{{ url('/user/index') }}#about">Tentang Kami</a></li>
                        <li><a href="{{ url('/user/index') }}#features">Layanan Kami</a></li>
                        <li><a href="{{ url('/user/index') }}#gallery">Galeri</a></li>
                        <li><a href="{{ url('/user/index') }}#pricing">Paket</a></li>
                        <li><a href="{{ url('/user/index') }}#portfolio-unggulan">Portofolio</a></li>
                        <li><a href="{{ url('/user/index') }}#vendor-partners">Mitra Vendor</a></li>
                        <li><a href="{{ url('/user/index') }}#testimonials">Testimoni</a></li>
                        <li><a href="{{ url('/user/index') }}#contact">Kontak</a></li>
                    </ul>
                    <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
                </nav>
            </div>
        </div>
    </header>

    <main class="main">

        <section id="keranjang" class="keranjang section" style="padding-top: 150px;">
            <div class="container" data-aos="fade-up">

                <div class="container section-title">
                    <span class="subtitle">Checkout</span>
                    <h2>Keranjang Pesanan Anda</h2>
                    <p>Periksa kembali pesanan Anda sebelum melanjutkan ke pembayaran DP.</p>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @forelse($cartItems as $item)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h4 class="card-title">{{ $item['package_name'] }}</h4>
                            <h5 class="text-danger mt-2 mb-3">{{ $item['package_price'] }}</h5>
                            
                            <hr>
                            
                            <h5>Data Pemesan:</h5>
                            <p class="mb-1"><strong>Pemesan:</strong> {{ $item['customer_name'] }}</p>
                            <p class="mb-1"><strong>No. Telp/WA:</strong> {{ $item['whatsapp_number'] }}</p>

                            {{-- Data Opsional Pemesan (Hanya tampil jika diisi) --}}
                            @if(!empty($item['bride_groom_name']))
                                <p class="mb-1"><strong>Nama Pengantin:</strong> {{ $item['bride_groom_name'] }}</p>
                            @endif
                            @if(!empty($item['parent_name']))
                                <p class="mb-1"><strong>Nama Orang Tua:</strong> {{ $item['parent_name'] }}</p>
                            @endif
                            @if(!empty($item['facebook_name']))
                                <p class="mb-1"><strong>Facebook:</strong> {{ $item['facebook_name'] }}</p>
                            @endif
                            @if(!empty($item['instagram_name']))
                                <p class="mb-1"><strong>Instagram:</strong> {{ $item['instagram_name'] }}</p>
                            @endif

                            <h5 class="mt-4">Detail Acara:</h5>
                            <p class="mb-1"><strong>Tanggal Acara:</strong> {{ \Carbon\Carbon::parse($item['event_date'])->format('d F Y') }}</p>
                            <p class="mb-1"><strong>Alamat Acara:</strong> {{ $item['event_address'] }}</p>
                            <p class="mb-1"><strong>Durasi Acara:</strong> {{ $item['event_duration'] }}</p>
                            
                            {{-- Data Opsional Catatan (Hanya tampil jika diisi) --}}
                            
                            @if(!empty($item['add_ons']))
                                <h5 class="mt-4">Paket Tambahan (Add-Ons):</h5>
                                <p class="mb-1 p-2 bg-light rounded" style="white-space: pre-wrap;">{{ $item['add_ons'] }}</p>
                            @endif

                            @if(!empty($item['gown_notes']))
                                <h5 class="mt-4">Catatan Kebaya/Gown:</h5>
                                <p class="mb-1 p-2 bg-light rounded" style="white-space: pre-wrap;">{{ $item['gown_notes'] }}</p>
                            @endif

                            @if(!empty($item['other_notes']))
                                <h5 class="mt-4">Catatan Lainnya:</h5>
                                <p class="mb-1 p-2 bg-light rounded" style="white-space: pre-wrap;">{{ $item['other_notes'] }}</p>
                            @endif

                        </div>
                    </div>
                @empty
                    <div class="alert alert-info">
                        Keranjang Anda masih kosong.
                    </div>
                @endforelse


                @if(!empty($cartItems))
                    <hr>
                    <h3>Total Pesanan: Rp {{ number_format($totalPrice, 0, ',', '.') }}</h3>
                    
                    <div class="card mt-4">
                        <div class="card-body">
                            <h4>Lanjutkan Pembayaran</h4>
                            <p>Silakan lakukan pembayaran DP (minimal 50%) untuk mengamankan tanggal Anda.</p>
                            
                            <form action="{{ route('checkout.process') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="down_payment" class="form-label">Masukkan Nominal DP</label>
                                    
                                    <input type="number" name="down_payment" class="form-control @error('down_payment') is-invalid @enderror" 
                                           placeholder="Contoh: {{ $minDp }}" required>
                                           
                                    @error('down_payment')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <button type="submit" class="btn btn-primary" style="background-color: #c85716; border-color: #c85716;">
                                    Konfirmasi & Lanjutkan Pembayaran
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </section>
        </main>

    <footer id="footer" class="footer dark-background">
        <div class="container">
            <div class="row gy-5">
                <div class="col-lg-4">
                    <div class="footer-content">
                        <a href="{{ url('/') }}" class="logo d-flex align-items-center mb-4">
                            <span class="sitename">GriyaRiasAsmara</span>
                        </a>
                        <p class="mb-4">Spesialisasi kami dalam mewujudkan pernikahan impian Anda, mulai dari riasan
                            flawless,
                            busana adat & modern, hingga dekorasi pelaminan yang elegan.</p>
                        <div class="newsletter-form">
                            <h5>Dapatkan Inspirasi Terbaru</h5>
                            <form action="" method="post">
                                <div class="input-group">
                                    <input type="email" name="email" class="form-control"
                                        placeholder="Masukkan email Anda" required="">
                                    <button type="submit" class="btn-subscribe">
                                        <i class="bi bi-send"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="footer-links">
                        <h4>Tentang Kami</h4>
                        <ul>
                            <li><a href="{{ url('/user/index') }}#about"><i class="bi bi-chevron-right"></i> Filosofi Kami</a></li>
                            <li><a href="#contact"><i class="bi bi-chevron-right"></i> Hubungi Kami</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="footer-links">
                        <h4>Layanan Utama</h4>
                        <ul>
                            <li><a href="{{ url('/user/index') }}#features"><i class="bi bi-chevron-right"></i> Rias Pengantin</a></li>
                            <li><a href="{{ url('/user/index') }}#gallery"><i class="bi bi-chevron-right"></i> Dekorasi</a></li>
                            <li><a href="{{ url('/user/index') }}#pricing"><i class="bi bi-chevron-right"></i> Paket Lengkap</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="footer-contact">
                        <h4>Lokasi & Kontak</h4>
                        <div class="contact-item">
                            <div class="contact-icon"><i class="bi bi-geo-alt"></i></div>
                            <div class="contact-info">
                                <p>Dukuh Kendaga, RT.02/RW.11, Kendaga, Larangan, Kec. Larangan, Kabupaten Brebes, Jawa
                                    Tengah 52262</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon"><i class="bi bi-telephone"></i></div>
                            <div class="contact-info"><p>085866659930</p></div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon"><i class="bi bi-envelope"></i></div>
                            <div class="contact-info"><p>info@griyariasasmara.com</p></div>
                        </div>
                        <div class="social-links">
                            <a href="#"><i class="bi bi-facebook"></i></a>
                            <a href="#"><i class="bi bi-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="copyright">
                            <p>© <span>Hak Cipta</span> <strong class="px-1 sitename">GriyaRiasAsmara</strong>
                                <span>Semua Hak Dilindungi</span>
                            </p>
                        </div>
                    </div>
                    <div class_lg="col-lg-6">
                        <div class="footer-bottom-links">
                            <a href="#">Kebijakan Privasi</a>
                            <a href="#">Ketentuan Layanan</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>
    <div id="preloader"></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets-admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/purecounter/purecounter_vanilla.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('assets-admin/js/main.js') }}"></script>

    {{-- 
    =========================================================
    INI ADALAH BAGIAN SCRIPT YANG DIPERBAIKI
    =========================================================
    --}}
    <script>
        $(document).ready(function() {
            
            function updateCartBadgeOnLoad() {
                
                // 1. Baca jumlah dari 'data-cart-count' di tag <body>
                let cartCount = parseInt($('body').attr('data-cart-count')) || 0;
                
                // 2. Tampilkan/sembunyikan badge berdasarkan jumlah
                if (cartCount > 0) {
                    $('#cart-badge')
                        .text(cartCount)
                        .show();
                } else {
                    $('#cart-badge').hide();
                }
            }

            // Panggil fungsi saat halaman siap
            // Ini adalah perbaikannya (dipindahkan ke dalam 'document.ready')
            updateCartBadgeOnLoad();

        });
    </script>
</body>
</html>