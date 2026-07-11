@extends('layouts.user')
@section('title', 'Detail Booking')

@section('content')
<div class="container py-4">
    <a href="{{ route('user.keranjang') }}" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>
    
    <div class="card shadow-sm border-0 adaptive-card">
        <div class="card-body p-5">
            <h4 class="fw-bold text-primary mb-4">Detail Booking: {{ $cart->package_name }}</h4>
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted">Tanggal: <span class="fw-bold text-dark">{{ date('d M Y', strtotime($cart->event_date)) }}</span></p>
                    <p class="text-muted">Lokasi: <span class="fw-bold text-dark">{{ $cart->event_address }}</span></p>
                    <p class="text-muted">Nama Pengantin: <span class="fw-bold text-dark">{{ $cart->bride_groom_name }}</span></p>
                </div>
                <div class="col-md-6">
                    <p class="text-muted">Durasi: <span class="fw-bold text-dark">{{ $cart->event_duration }} Hari</span></p>
                    <p class="text-muted">Catatan: <span class="fw-bold text-dark">{{ $cart->notes ?? '-' }}</span></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection