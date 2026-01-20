<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SysGRA | Keranjang Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('assets-user/css/custom-style.css') }}" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body>

{{-- HEADER / NAVBAR --}}
<header class="header fixed-top py-3" style="background: rgba(0,0,0,0.9); border-bottom: 1px solid rgba(255,255,255,0.1);">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="{{ url('/') }}" class="text-decoration-none">
            <h2 class="m-0 text-white fw-bold">GriyaRiasAsmara</h2>
        </a>
        <div class="header-actions d-flex align-items-center">
            @auth
                <div class="dropdown">
                    <a class="btn btn-outline-light rounded-pill px-3 py-1 dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i> Akun Saya
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a class="dropdown-item" href="{{ route('profile') }}">Profil</a></li>
                        <li><a class="dropdown-item" href="{{ url('/pesanan-saya') }}">Pesanan Saya</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger border-0 bg-transparent">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </div>
</header>

<main style="margin-top: 130px; padding-bottom: 80px;">
    <div class="container">
        
        {{-- TOMBOL KEMBALI --}}
        <div class="mb-4">
            <a href="{{ url('/') }}" class="back-link">
                <i class="bi bi-arrow-left me-2 text-danger"></i> Kembali Berbelanja
            </a>
        </div>

        <div class="row g-4">
            {{-- Bagian Kiri: List Pesanan --}}
            <div class="col-lg-8">
                <div class="card card-custom h-100">
                    <div class="card-header bg-white py-3 border-0 rounded-top-4">
                        <h5 class="m-0 fw-bold text-dark-custom">
                            <i class="bi bi-cart-check-fill text-danger me-2"></i> Konfirmasi Booking
                        </h5>
                    </div>
                    <div class="card-body p-4 text-dark-custom">
                        @if($cartItems->count() > 0)
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead class="text-muted small text-uppercase">
                                        <tr>
                                            <th>Paket Pengantin</th>
                                            <th>Tanggal</th>
                                            <th>Harga</th>
                                            <th class="text-center">Hapus</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-dark-custom">
                                        @foreach($cartItems as $index => $item)
                                        <tr>
                                            <td>
                                                <h6 class="m-0 fw-bold">{{ $item['package_name'] }}</h6>
                                                <small class="text-muted">SysGRA Organizer</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border fw-normal">
                                                    {{ $item['event_date'] }}
                                                </span>
                                            </td>
                                            <td class="fw-bold">{{ $item['package_price'] }}</td>
                                            <td class="text-center">
                                                <form action="{{ route('cart.remove', $index) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger p-0">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-cart-x text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                                <h4 class="fw-bold mt-3">Keranjang Kosong</h4>
                                <p class="text-muted">Yuk, pilih paket pernikahan impianmu!</p>
                                <a href="{{ url('/') }}#pricing" class="btn btn-danger rounded-pill px-5 shadow-sm">Lihat Paket</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Bagian Kanan: Summary --}}
            <div class="col-lg-4">
                <div class="card card-custom p-2">
                    <div class="card-body text-dark-custom">
                        <h5 class="fw-bold mb-4 border-bottom pb-3">Ringkasan Biaya</h5>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Total Harga Paket</span>
                            <span class="fw-bold">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted text-danger">Minimal DP (50%)</span>
                            <span class="h5 fw-bold text-danger">Rp {{ number_format($minDp, 0, ',', '.') }}</span>
                        </div>

                        <div class="alert alert-secondary border-0 small rounded-4 mb-4" style="background-color: #f8f9fa;">
                            <i class="bi bi-info-circle-fill me-2 text-primary"></i>
                            Pembayaran dilakukan via **Transfer Bank**. Anda wajib mengunggah bukti bayar setelah ini.
                        </div>

                        <form action="{{ route('checkout.process') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-checkout w-100 shadow-sm" {{ $cartItems->count() == 0 ? 'disabled' : '' }}>
                                Lanjutkan Pembayaran <i class="bi bi-arrow-right-short fs-5 ms-1"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>