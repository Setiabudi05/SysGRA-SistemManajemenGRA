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
                        </div>
                    @else
                        @foreach($carts as $cart)
                            <div class="p-4 mb-3 border rounded-4 bg-white shadow-xs">
                                <div class="row align-items-center">
                                    <div class="col-md-7">
                                        <span class="badge bg-light-primary text-primary mb-2 text-uppercase fw-bold" style="font-size: 0.6rem;">DRAFT</span>
                                        <h5 class="fw-bold text-dark mb-1">{{ $cart->package_name }}</h5>

                                        @if($cart->addOns->isNotEmpty())
                                            <div class="mb-2">
                                                <small class="text-muted fw-bold">Item Tambahan:</small>
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    @foreach($cart->addOns as $add)
                                                        <span class="badge bg-light-info text-info border border-info" style="font-size: 0.7rem;">
                                                            {{ $add->nama_item }} (+Rp {{ number_format($add->harga, 0, ',', '.') }})
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <p class="mb-1 small text-muted"><i class="bi bi-calendar-event me-2 text-primary"></i>{{ date('d M Y', strtotime($cart->event_date)) }}</p>
                                        <p class="mb-3 small text-muted"><i class="bi bi-geo-alt me-2 text-primary"></i>{{ $cart->event_address }}</p>

                                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $cart->id }}">
                                            <i class="bi bi-info-circle-fill me-1"></i> Lihat Detail
                                        </button>
                                    </div>

                                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Harga Paket (Flat):</small>
                                            <span class="fw-bold text-dark">Rp {{ number_format((int) $cart->paket->harga, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Harga Tambahan (Flat):</small>
                                            <span class="fw-bold text-info">Rp {{ number_format($cart->addOns->sum('harga'), 0, ',', '.') }}</span>
                                        </div>
                                        <div class="border-top pt-2 mt-2">
                                            <small class="text-muted d-block">Subtotal:</small>
                                            <span class="fw-bold text-primary h5">
                                                Rp {{ number_format((int)$cart->paket->harga + (int)$cart->addOns->sum('harga'), 0, ',', '.') }}
                                            </span>
                                        </div>

                                        <button type="button" class="btn btn-danger btn-sm rounded-pill px-4 shadow-sm mt-3" onclick="confirmDelete('{{ $cart->id }}')">
                                            <i class="bi bi-trash me-1"></i> Batalkan
                                        </button>
                                        <form id="delete-form-{{ $cart->id }}" action="{{ route('user.keranjang.destroy', $cart->id) }}" method="POST" class="d-none">
                                            @csrf @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal Detail --}}
                            <div class="modal fade" id="modalDetail{{ $cart->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                        <div class="modal-header border-0 bg-primary text-white p-4">
                                            <h5 class="modal-title fw-bold">Detail Pesanan #{{ $cart->id }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <table class="table table-borderless">
                                                <tr><td class="text-muted w-50">Nama Pemesan</td><td class="fw-bold">{{ $cart->customer_name }}</td></tr>
                                                <tr><td class="text-muted">Nama Pengantin</td><td class="fw-bold">{{ $cart->bride_groom_name }}</td></tr>
                                                <tr><td class="text-muted">Durasi Acara</td><td class="fw-bold">{{ $cart->event_duration }} Hari</td></tr>
                                                <tr><td class="text-muted">Catatan Khusus</td><td class="fw-bold">{{ $cart->notes ?? '-' }}</td></tr>
                                            </table>
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
                    <h5 class="fw-bold mb-4"><i class="bi bi-receipt me-2 text-primary"></i>Ringkasan Tagihan</h5>
                    
                    @php
                        $grandTotal = $carts->sum(function($cart) {
                            return (int)$cart->paket->harga + (int)$cart->addOns->sum('harga');
                        });
                    @endphp

                    <div class="d-flex justify-content-between mb-4">
                        <span class="text-muted fw-bold">Total Keseluruhan</span>
                        <span class="fw-bold text-primary h5 mb-0">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>

                    <form action="{{ route('user.keranjang.konfirmasi') }}" method="POST" id="form-konfirmasi">
                        @csrf
                        <button type="button" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow" 
                                @if($carts->isEmpty()) disabled @endif 
                                onclick="confirmKonfirmasi()">
                            Konfirmasi Booking <i class="bi bi-check2-circle ms-2"></i>
                        </button>
                    </form>

                    <div class="alert alert-light-primary border-0 small mt-4 py-2" style="background-color: #f0f3ff; color: #435ee0; border-radius: 10px;">
                        <i class="bi bi-info-circle me-2"></i> Data akan dipindah ke menu <strong>Pembayaran</strong>.
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
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success_booking') }}", confirmButtonColor: '#3085d6' });
    @endif

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 2000, showConfirmButton: false });
    @endif

    @if($errors->any())
        Swal.fire({ icon: 'error', title: 'Oops...', text: "{{ $errors->first() }}" });
    @endif

    function confirmDelete(cartId) {
        Swal.fire({
            title: 'Batalkan Booking?',
            text: "Data ini akan dihapus dari keranjang Anda.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Batalkan!'
        }).then((result) => {
            if (result.isConfirmed) { document.getElementById('delete-form-' + cartId).submit(); }
        });
    }

    function confirmKonfirmasi() {
        Swal.fire({
            title: 'Konfirmasi Booking?',
            text: "Apakah Anda yakin ingin memproses pesanan ini ke pembayaran?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#435ebe',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Konfirmasi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-konfirmasi').submit();
            }
        });
    }
</script>
@endpush