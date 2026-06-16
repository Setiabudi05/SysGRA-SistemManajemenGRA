@extends('layouts.user')

@section('title', 'Keranjang Booking')

@section('content')
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6">
            <h3 class="fw-bold text-dark"><i class="bi bi-cart-fill me-2 text-primary"></i>Keranjang Booking</h3>
            <p class="text-muted small">Kelola pesanan Anda dan lakukan konfirmasi untuk melanjutkan pembayaran.</p>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 justify-content-md-end">
                    <li class="breadcrumb-item"><a href="{{ url('user/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Keranjang</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="row">
        {{-- SISI KIRI: DAFTAR ITEM KERANJANG --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 adaptive-card">
                <div class="card-body p-4">
                    @if($carts->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-cart-x fs-1 text-muted opacity-50"></i>
                            <p class="mt-3 text-muted">Keranjang Anda kosong.</p>
                            <a href="{{ url('user/booking') }}" class="btn btn-primary rounded-pill px-4">Booking Sekarang</a>
                        </div>
                    @else
                        @foreach($carts as $cart)
                            <div class="p-4 mb-3 border rounded-4 bg-white shadow-xs">
                                <div class="row align-items-center">
                                    <div class="col-md-7 text-start">
                                        <span class="badge bg-light-primary text-primary mb-2 text-uppercase fw-bold" style="font-size: 0.6rem;">
                                            DRAFT
                                        </span>
                                        <h5 class="fw-bold text-dark mb-2">{{ $cart->package_name }}</h5>
                                        <p class="mb-1 small text-muted">
                                            <i class="bi bi-calendar-event me-2 text-primary"></i>
                                            {{ date('d M Y', strtotime($cart->event_date)) }}
                                        </p>
                                        <p class="mb-3 small text-muted">
                                            <i class="bi bi-geo-alt me-2 text-primary"></i>
                                            {{ $cart->event_address }}
                                        </p>
                                        
                                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-4 shadow-sm"
                                            data-bs-toggle="modal" data-bs-target="#modalDetail{{ $cart->id }}">
                                            <i class="bi bi-info-circle-fill me-1"></i> Lihat Detail
                                        </button>
                                    </div>

                                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                        <p class="fw-bold text-dark h5 mb-3">
                                            Rp {{ number_format((int) $cart->package_price, 0, ',', '.') }}
                                        </p>

                                        {{-- PERBAIKAN FORM: Form diisolasi ketat di sini agar tidak memicu auto-submit elemen lain --}}
                                        <form action="{{ route('user.keranjang.destroy', $cart->id) }}" method="POST" onsubmit="return confirm('Batalkan booking ini?')">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4 shadow-sm">
                                                <i class="bi bi-trash me-1"></i> Batalkan
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- MODAL DETAIL: FORM STYLE --}}
                            <div class="modal fade" id="modalDetail{{ $cart->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                        <div class="modal-header border-0 pb-0" style="background: linear-gradient(to right, #435ee0, #6c80e8); border-radius: 20px 20px 0 0; padding: 25px;">
                                            <h5 class="modal-title text-white fw-bold"><i class="bi bi-clipboard-check me-2"></i>Review Konfirmasi Booking</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row g-3">
                                                <div class="col-12 text-start">
                                                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">1. Informasi Layanan & Waktu</h6>
                                                </div>
                                                <div class="col-md-6 text-start">
                                                    <label class="info-label-sm fw-bold text-secondary">Paket Pernikahan</label>
                                                    <input type="text" class="form-control form-control-sm bg-light fw-bold" value="{{ $cart->package_name }}" readonly>
                                                </div>
                                                <div class="col-md-6 text-start">
                                                    <label class="info-label-sm fw-bold text-secondary">Tanggal Pelaksanaan</label>
                                                    <input type="text" class="form-control form-control-sm bg-light" value="{{ date('d F Y', strtotime($cart->event_date)) }}" readonly>
                                                </div>
                                                <div class="col-12 text-start">
                                                    <label class="info-label-sm fw-bold text-secondary">Alamat Lokasi Acara</label>
                                                    <textarea class="form-control form-control-sm bg-light" rows="2" readonly>{{ $cart->event_address }}</textarea>
                                                </div>

                                                <div class="col-12 mt-4 text-start">
                                                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">2. Identitas Pelanggan & Pengantin</h6>
                                                </div>
                                                <div class="col-md-6 text-start">
                                                    <label class="info-label-sm fw-bold text-secondary">Nama Pemesan</label>
                                                    <input type="text" class="form-control form-control-sm bg-light" value="{{ $cart->customer_name }}" readonly>
                                                </div>
                                                <div class="col-md-6 text-start">
                                                    <label class="info-label-sm fw-bold text-secondary">WhatsApp</label>
                                                    <input type="text" class="form-control form-control-sm bg-light text-primary fw-bold" value="{{ $cart->whatsapp_number }}" readonly>
                                                </div>
                                                <div class="col-md-6 text-start">
                                                    <label class="info-label-sm fw-bold text-secondary">Nama Pengantin</label>
                                                    <input type="text" class="form-control form-control-sm bg-light" value="{{ $cart->bride_groom_name }}" readonly>
                                                </div>
                                                <div class="col-md-6 text-start">
                                                    <label class="info-label-sm fw-bold text-secondary">Nama Orang Tua</label>
                                                    <input type="text" class="form-control form-control-sm bg-light" value="{{ $cart->parent_name ?? '-' }}" readonly>
                                                </div>

                                                <div class="col-md-4 text-start">
                                                    <label class="info-label-sm fw-bold text-secondary">Instagram</label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text bg-light"><i class="bi bi-instagram"></i></span>
                                                        <input type="text" class="form-control bg-light" value="{{ $cart->instagram_name ?? '-' }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-start">
                                                    <label class="info-label-sm fw-bold text-secondary">Facebook</label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text bg-light"><i class="bi bi-facebook"></i></span>
                                                        <input type="text" class="form-control bg-light" value="{{ $cart->facebook_name ?? '-' }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-start">
                                                    <label class="info-label-sm fw-bold text-secondary">Durasi</label>
                                                    <input type="text" class="form-control form-control-sm bg-light" value="{{ $cart->event_duration }} Hari" readonly>
                                                </div>
                                                <div class="col-12 text-start">
                                                    <label class="info-label-sm text-warning fw-bold">Catatan Khusus</label>
                                                    <div class="p-2 border rounded bg-light small text-dark">
                                                        {{ $cart->notes ?? 'Tidak ada catatan tambahan.' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 p-4 pt-0">
                                            <div class="w-100 d-flex justify-content-between align-items-center">
                                                <div class="text-start">
                                                    <small class="text-muted d-block">Total Pembayaran:</small>
                                                    <span class="fw-bold text-dark h5">Rp {{ number_format((int) $cart->package_price, 0, ',', '.') }}</span>
                                                </div>
                                                <button type="button" class="btn btn-secondary rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- SISI KANAN: RINGKASAN --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 adaptive-card sticky-top" style="top: 20px;">
                <div class="card-body p-4 text-start">
                    <h5 class="fw-bold mb-4 title-text"><i class="bi bi-receipt me-2 text-primary"></i>Ringkasan Tagihan</h5>
                    <div class="d-flex justify-content-between mb-4 small">
                        <span class="text-muted text-dark fw-bold">Total Tagihan</span>
                        <span class="fw-bold text-primary h5 mb-0">Rp {{ number_format($carts->sum('package_price'), 0, ',', '.') }}</span>
                    </div>
                    <hr class="opacity-25 my-4">

                    <form action="{{ route('user.keranjang.konfirmasi') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow" @if($carts->isEmpty()) disabled @endif>
                            Konfirmasi Booking <i class="bi bi-check2-circle ms-2"></i>
                        </button>
                    </form>
                    
                    <div class="alert alert-light-primary border-0 small mt-4 py-2" style="background-color: #f0f3ff; color: #435ee0; border-radius: 10px;">
                        <i class="bi bi-info-circle me-2"></i> Data akan dipindah ke menu <strong>Pembayaran</strong> untuk transaksi aman via Midtrans.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success_booking'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success_booking') }}",
            showConfirmButton: false,
            timer: 3000
        });
    @endif
</script>
@endpush