@extends('user.layouts.dashboard')

@section('content')
<div class="container-fluid">
    <h3 class="fw-bold mb-4">Selamat Datang, {{ Auth::user()->name }}!</h3>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <h6 class="text-muted small fw-bold">TOTAL PESANAN</h6>
                <h2 class="fw-bold text-danger">1</h2>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-3">Informasi Penting</h5>
                <p>Silakan lakukan pembayaran DP 50% untuk mengunci jadwal pernikahan Anda. Unggah bukti transfer di menu <strong>Pesanan Saya</strong>.</p>
            </div>
        </div>
    </div>
</div>
@endsection