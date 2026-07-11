@extends('layouts.master')
@section('title', 'Detail Pesanan - ' . ($booking->bride_groom_name ?? 'Detail'))

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('owner.booking.index') }}" class="text-muted">Laporan Pesanan</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Detail</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">Detail Pesanan</h3>
                <p class="text-muted mb-0 small">Informasi lengkap pesanan <strong>{{ $booking->bride_groom_name }}</strong></p>
            </div>
            <div class="col-12 col-md-6 d-flex justify-content-md-end">
                <a href="{{ route('owner.booking.index') }}" class="btn btn-secondary shadow-sm px-3 fw-bold">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>
    <hr>
</div>

<section class="section">
    <div class="row">
        {{-- Kolom Kiri: Detail Informasi --}}
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom p-4">
                    <h5 class="card-title mb-0">Informasi Acara</h5>
                </div>
                <div class="card-body p-4">
                    <table class="table table-borderless">
                        <tr><td width="30%" class="text-muted">Nama Pengantin</td><td class="fw-bold">: {{ $booking->bride_groom_name }}</td></tr>
                        <tr><td class="text-muted">Tanggal Acara</td><td class="fw-bold">: {{ \Carbon\Carbon::parse($booking->event_date)->translatedFormat('d F Y') }}</td></tr>
                        <tr><td class="text-muted">WhatsApp</td><td class="fw-bold">: {{ $booking->whatsapp ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Paket Dipilih</td><td class="fw-bold">: {{ $booking->paket->nama_paket ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Status Pesanan</td><td>: 
                            <span class="badge bg-{{ $booking->status == 'CONFIRMED' ? 'success' : 'warning' }}">{{ strtoupper($booking->status) }}</span>
                        </td></tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Ringkasan Keuangan --}}
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom p-4">
                    <h5 class="card-title mb-0">Status Keuangan</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Total Harga Paket</span>
                        <span class="fw-bold">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Sudah Dibayar</span>
                        <span class="text-success fw-bold">Rp {{ number_format($totalBayar, 0, ',', '.') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fw-bold">Sisa Tagihan</span>
                        <span class="text-danger fw-bold fs-5">Rp {{ number_format(max(0, $sisaTagihan), 0, ',', '.') }}</span>
                    </div>

                    @if($sisaTagihan <= 0 && $totalHarga > 0)
                        <div class="alert alert-primary mt-4 text-center fw-bold">
                            <i class="bi bi-check-all me-2"></i> PESANAN LUNAS
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection