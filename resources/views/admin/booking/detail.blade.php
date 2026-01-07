@extends('layouts.master')
@section('title', 'Detail Pesanan #' . $booking->id)

@push('css')
<style>
    /* Style khusus untuk tampilan cetak */
    @media print {
        .btn, .sidebar-wrapper, .navbar, .page-heading hr, .breadcrumb {
            display: none !important;
        }
        .card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
        }
        .card-header {
            background-color: #f8f9fa !important;
            color: #000 !important;
            border-bottom: 2px solid #000 !important;
        }
        body {
            background-color: white !important;
        }
    }
</style>
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 text-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.booking.index') }}">Pesanan</a></li>
                        <li class="breadcrumb-item active">Detail #{{ $booking->id }}</li>
                    </ol>
                </nav>
                <h3 class="fw-bold">Rincian Lengkap Pesanan</h3>
            </div>
            <div class="col-12 col-md-6 d-flex justify-content-md-end gap-2 d-print-none">
                <a href="{{ route('admin.booking.index') }}" class="btn btn-secondary shadow-sm">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button onclick="window.print()" class="btn btn-dark shadow-sm">
                    <i class="bi bi-printer"></i> Cetak Format Booking
                </button>
            </div>
        </div>
    </div>
    <hr>
</div>

<section class="section">
    <div class="row">
        {{-- SISI KIRI: DATA LENGKAP --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 text-white text-center">FORMULIR BOOKING WEDDING</h5>
                    <p class="text-center mb-0 small opacity-75">Griya Rias Asmara Management</p>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted">NAMA PEMESAN</label>
                            <p class="border-bottom pb-1 fw-bold">{{ $booking->customer_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted">WHATSAPP</label>
                            <p class="border-bottom pb-1">{{ $booking->whatsapp_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted">NAMA PENGANTIN</label>
                            <p class="border-bottom pb-1 text-primary fw-bold">{{ $booking->bride_groom_name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted">NAMA ORANG TUA</label>
                            <p class="border-bottom pb-1">{{ $booking->parent_name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted">INSTAGRAM</label>
                            <p class="border-bottom pb-1">{{ $booking->instagram_name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted">FACEBOOK</label>
                            <p class="border-bottom pb-1">{{ $booking->facebook_name ?? '-' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="fw-bold small text-muted">ALAMAT LENGKAP ACARA</label>
                            <p class="border p-2 bg-light rounded">{{ $booking->event_address }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted">TANGGAL PELAKSANAAN</label>
                            <p class="border-bottom pb-1 fw-bold text-danger">{{ \Carbon\Carbon::parse($booking->event_date)->isoFormat('D MMMM Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted">DURASI ACARA</label>
                            <p class="border-bottom pb-1">{{ $booking->event_duration }}</p>
                        </div>
                        <div class="col-12 mt-4">
                            <label class="fw-bold small text-muted">PAKET TAMBAHAN (ADD-ONS)</label>
                            <p class="border p-2 min-vh-10">{{ $booking->add_ons ?? 'Tidak ada tambahan' }}</p>
                        </div>
                    </div>

                    {{-- Tanda Tangan Cetak --}}
                    <div class="row mt-5 d-none d-print-flex text-center">
                        <div class="col-6">
                            <p>Customer,</p>
                            <br><br><br>
                            <p>( ............................ )</p>
                        </div>
                        <div class="col-6">
                            <p>Admin SysGRA,</p>
                            <br><br><br>
                            <p>( ............................ )</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SISI KANAN: RINGKASAN BIAYA & STATUS --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <label class="fw-bold small text-muted mb-2 d-block">STATUS PESANAN</label>
                    <div class="mb-3">
                        @php $badges = ['pending'=>'bg-warning', 'confirmed'=>'bg-primary', 'completed'=>'bg-success']; @endphp
                        <span class="badge {{ $badges[$booking->status] }} p-2 px-3 w-100 text-uppercase fw-bold">{{ $booking->status == 'confirmed' ? 'Terkonfirmasi' : $booking->status }}</span>
                    </div>
                    <hr>
                    <label class="fw-bold small text-muted">PAKET TERPILIH</label>
                    <h5 class="fw-bold mt-1">{{ $booking->package_name }}</h5>
                    <div class="bg-light p-3 rounded">
                        <span class="small text-muted d-block">Total Biaya Paket:</span>
                        <h3 class="text-success fw-bold mb-0">{{ $booking->package_price }}</h3>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm d-print-none">
                <div class="card-body">
                    <label class="fw-bold small text-muted mb-3 d-block text-start">UPDATE STATUS</label>
                    <form action="{{ route('admin.booking.updateStatus', $booking->id) }}" method="POST" class="d-grid gap-2">
                        @csrf 
                        @method('PUT')
                        <button name="status" value="confirmed" class="btn btn-outline-primary btn-sm" {{ $booking->status != 'pending' ? 'disabled' : '' }}>Konfirmasi DP</button>
                        <button name="status" value="completed" class="btn btn-success btn-sm" {{ $booking->status == 'completed' ? 'disabled' : '' }}>Selesaikan (Lunas)</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection