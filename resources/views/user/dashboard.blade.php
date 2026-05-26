@extends('layouts.user')

@section('title', 'Dashboard Pengantin')

@section('content')
<div class="page-heading">
    <div class="row">
        <div class="col-12">
            {{-- Welcome Card dengan Gradasi Biru --}}
            <div class="card shadow-sm border-0 mb-4"
                style="background: linear-gradient(135deg, #435ee0 0%, #2e43ad 100%); border-radius: 15px;">
                <div class="card-body py-4 px-4">
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="avatar avatar-xl bg-white p-1 shadow-sm">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=fff&color=435ee0&bold=true"
                                alt="User">
                        </div>
                        <div class="ms-sm-4 mt-3 mt-sm-0">
                            <h3 class="text-white fw-bold mb-1">Selamat Datang, {{ Auth::user()->name }}!</h3>
                            <p class="text-white-50 mb-0">Mari wujudkan pernikahan impian Anda bersama Griya Rias Asmara.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-content">
    <section class="row">
        <div class="col-12 col-lg-9">
            {{-- Row Statistik Utama --}}
            <div class="row">
                {{-- Total Pesanan --}}
                <div class="col-6 col-md-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body px-4 py-4-5 text-center text-md-start">
                            <div class="stats-icon purple mb-2 mx-auto mx-md-0">
                                <i class="bi bi-bag-heart-fill"></i>
                            </div>
                            <h6 class="text-muted font-semibold small">Total Pesanan</h6>
                            <h6 class="font-extrabold mb-0">{{ $total_pesanan ?? 0 }} Paket</h6>
                        </div>
                    </div>
                </div>
                {{-- Status Pesanan --}}
                <div class="col-6 col-md-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body px-4 py-4-5 text-center text-md-start">
                            <div class="stats-icon blue mb-2 mx-auto mx-md-0">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <h6 class="text-muted font-semibold small">Status Terakhir</h6>
                            <h6 class="font-extrabold mb-0 text-primary text-uppercase">{{ $status_terakhir ?? 'Belum Ada' }}</h6>
                        </div>
                    </div>
                </div>
                {{-- Sisa Tagihan --}}
                <div class="col-12 col-md-4 mt-3 mt-md-0">
                    <div class="card shadow-sm border-0 h-100 bg-light-danger">
                        <div class="card-body px-4 py-4-5 text-center text-md-start">
                            <div class="stats-icon red mb-2 mx-auto mx-md-0">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <h6 class="text-danger font-semibold small">Sisa Tagihan</h6>
                            <h6 class="font-extrabold mb-0 text-danger">Rp {{ number_format($sisa_tagihan ?? 0, 0, ',', '.') }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tahapan Persiapan dengan Progress Bar --}}
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-body p-4">
                    <h5 class="mb-4 d-flex align-items-center">
                        <i class="bi bi-stars me-2 text-warning"></i>
                        <span>Progres Persiapan Pernikahan</span>
                    </h5>
                    <div class="steps-wrapper mt-4">
                        <div class="row text-center position-relative">
                            {{-- Step 1: Booking --}}
                            <div class="col-4">
                                <div class="step-circle mx-auto bg-success text-white mb-2 shadow-sm">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <span class="fw-bold small d-block">Booking</span>
                                <small class="text-muted">Tercatat</small>
                            </div>
                            {{-- Step 2: Bayar DP --}}
                            <div class="col-4">
                                @php 
                                    $isConfirmed = in_array($status_terakhir, ['confirmed', 'completed']);
                                @endphp
                                <div class="step-circle mx-auto {{ $isConfirmed ? 'bg-primary text-white shadow' : 'bg-light text-muted' }} mb-2">
                                    @if($isConfirmed) <i class="bi bi-check-lg"></i> @else 2 @endif
                                </div>
                                <span class="fw-bold small d-block">Bayar DP</span>
                                <small class="text-muted">Konfirmasi</small>
                            </div>
                            {{-- Step 3: Hari H --}}
                            <div class="col-4">
                                @php $isCompleted = ($status_terakhir == 'completed'); @endphp
                                <div class="step-circle mx-auto {{ $isCompleted ? 'bg-primary text-white shadow' : 'bg-light text-muted' }} mb-2">
                                    3
                                </div>
                                <span class="fw-bold small d-block">Fitting & Acara</span>
                                <small class="text-muted">Hari H</small>
                            </div>
                        </div>
                        {{-- Garis Progress --}}
                        <div class="progress mt-3 shadow-sm" style="height: 6px; border-radius: 10px;">
                            <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" 
                                style="width: {{ $status_terakhir == 'completed' ? '100%' : ($status_terakhir == 'confirmed' ? '66%' : '33%') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Kanan --}}
        <div class="col-12 col-lg-3">
            {{-- Card Chatbot (Materi Skripsi) --}}
            <div class="card shadow-sm border-0 mb-3 text-center">
                <div class="card-body py-4">
                    <div class="stats-icon purple mx-auto mb-3">
                        <i class="bi bi-robot"></i>
                    </div>
                    <h6 class="font-bold">Asmara AI Bot</h6>
                    <p class="text-muted small">Chatbot kami siap membantu menjawab pertanyaan Anda 24/7.</p>
                    <button onclick="toggleChat()" class="btn btn-primary w-100 rounded-pill btn-sm fw-bold shadow-sm">
                        <i class="bi bi-chat-text me-2"></i> Tanya Chatbot
                    </button>
                </div>
            </div>

            {{-- Bantuan Cepat --}}
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold mb-3 small">Bantuan Cepat</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item border-0 px-0 py-2">
                            <a href="{{ route('user.pembayaran') }}" class="small text-decoration-none text-primary d-flex align-items-center">
                                <i class="bi bi-credit-card me-2"></i> Cara Pembayaran
                            </a>
                        </li>
                        <li class="list-group-item border-0 px-0 py-2">
                            <a href="#" class="small text-decoration-none text-primary d-flex align-items-center">
                                <i class="bi bi-journal-text me-2"></i> Syarat & Ketentuan
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- Widget Chatbot Floating --}}
<div id="chatbot-widget" class="position-fixed" style="bottom: 30px; right: 30px; z-index: 1050;">
    {{-- Tombol --}}
    <button class="btn btn-primary rounded-circle shadow-lg p-3 d-flex align-items-center justify-content-center" 
            style="width: 60px; height: 60px; border: none; background: linear-gradient(135deg, #435ee0 0%, #2e43ad 100%);"
            onclick="toggleChat()">
        <i class="bi bi-chat-dots-fill fs-3 text-white"></i>
    </button>

    {{-- Kotak Chat --}}
    <div id="chat-window" class="card shadow-lg border-0 d-none" style="width: 320px; border-radius: 15px; overflow: hidden; position: absolute; bottom: 80px; right: 0;">
        <div class="card-header border-0 text-white p-3" style="background: #435ee0;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2 bg-white p-1">
                        <img src="https://ui-avatars.com/api/?name=Asmara+Bot&background=fff&color=435ee0" alt="Bot">
                    </div>
                    <div><h6 class="mb-0 text-white fw-bold small">Asmara Bot</h6><small class="text-white-50" style="font-size: 10px;">Online</small></div>
                </div>
                <button type="button" class="btn-close btn-close-white" onclick="toggleChat()"></button>
            </div>
        </div>
        <div class="card-body p-3 bg-light" style="height: 250px; overflow-y: auto;" id="chat-content">
            <div class="chat-message bot-msg mb-3">
                <div class="bg-white p-2 rounded shadow-sm small" style="max-width: 85%;">
                    Halo **{{ Auth::user()->name }}**! 👋 Ada yang bisa saya bantu terkait pesanan Anda?
                </div>
            </div>
        </div>
        <div class="card-footer bg-white border-0 p-3">
            <div class="input-group">
                <input type="text" class="form-control form-control-sm border-light bg-light" placeholder="Ketik pesan...">
                <button class="btn btn-primary btn-sm"><i class="bi bi-send"></i></button>
            </div>
        </div>
    </div>
</div>

<style>
    .step-circle { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; transition: all 0.3s ease; }
    .stats-icon.purple { background-color: #9694ff; }
    .stats-icon.blue { background-color: #57caeb; }
    .stats-icon.red { background-color: #ff7976; }
    .bg-light-danger { background-color: #ffe5e5 !important; }
    .stats-icon i { color: #fff; font-size: 1.3rem; }
    .avatar-xl img { width: 70px; height: 70px; border-radius: 50%; }
</style>

<script>
    function toggleChat() {
        document.getElementById('chat-window').classList.toggle('d-none');
    }
</script>
@endsection