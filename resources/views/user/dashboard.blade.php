@extends('layouts.user')
@section('title', 'Dashboard Pengantin')

@section('content')
<div class="page-content mt-4">
    {{-- WELCOME CARD --}}
    <div class="card shadow-sm border-0 mb-4" style="background: #435ee0; border-radius: 20px;">
        <div class="card-body py-4 px-4">
            <div class="d-flex align-items-center">
                <div class="avatar avatar-xl bg-white p-1 rounded-circle">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=fff&color=435ee0&bold=true" class="rounded-circle" alt="User">
                </div>
                <div class="ms-4 text-white">
                    <h3 class="fw-bold mb-1 text-white">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    <p class="mb-0 text-white-50">Mari wujudkan pernikahan impian Anda bersama Griya Rias Asmara.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- STATISTIK --}}
<div class="row">
    @foreach([
        ['icon' => 'lock-fill', 'title' => 'Total Pesanan', 'val' => ($total_pesanan ?? 0).' Paket', 'color' => 'text-primary', 'bg' => 'bg-primary-light'],
        ['icon' => 'wallet2', 'title' => 'Status Terakhir', 'val' => ($status_terakhir ?? 'DRAFT'), 'color' => 'text-success', 'bg' => 'bg-success-light'],
        ['icon' => 'cash-stack', 'title' => 'Sisa Tagihan', 'val' => 'Rp '.number_format($sisa_tagihan ?? 0, 0, ',', '.'), 'color' => 'text-danger', 'bg' => 'bg-danger-light']
    ] as $stat)
    <div class="col-md-4">
        <div class="card shadow-sm border-0" style="border-radius: 20px;">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="{{ $stat['bg'] }} p-3 rounded-3 me-3 {{ $stat['color'] }}"><i class="bi bi-{{ $stat['icon'] }} fs-4"></i></div>
                <div>
                    <p class="text-muted mb-0 small">{{ $stat['title'] }}</p>
                    <h5 class="fw-bold mb-0 {{ $stat['color'] }}">{{ $stat['val'] }}</h5>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

   {{-- ALUR PEMESANAN (Ditarik naik lebih tinggi) --}}
    <div class="card shadow-sm border-0 mt-n5" style="border-radius: 20px; position: relative; z-index: 1;">
        <div class="card-body p-3">
            <h6 class="mb-3 fw-bold text-primary"><i class="bi bi-stars me-2"></i>Alur Pemesanan</h6>
            
            <div class="d-flex justify-content-between align-items-start position-relative px-2">
                {{-- Garis Penghubung --}}
                <div class="position-absolute bg-light" style="height: 2px; top: 25px; width: 85%; left: 7.5%; z-index: 0;"></div>

                @foreach([
                    ['icon' => 'star-fill', 'label' => 'Booking', 'desc' => 'Pilih paket.', 'color' => 'text-primary'],
                    ['icon' => 'cart-fill', 'label' => 'Checkout', 'desc' => 'Proses keranjang.', 'color' => 'text-info'],
                    ['icon' => 'wallet2', 'label' => 'Pembayaran', 'desc' => 'Selesaikan DP.', 'color' => 'text-success'],
                    ['icon' => 'bell-fill', 'label' => 'Konfirmasi', 'desc' => 'Tunggu admin.', 'color' => 'text-warning']
                ] as $step)
                <div class="text-center" style="width: 25%; z-index: 1;">
                    <div class="circle-icon-sm mx-auto shadow-sm {{ $step['color'] }}" style="background: white;">
                        <i class="bi bi-{{ $step['icon'] }}"></i>
                    </div>
                    <h6 class="fw-bold mt-2 mb-0" style="font-size: 0.9rem;">{{ $step['label'] }}</h6>
                    <p class="text-muted small px-1" style="font-size: 0.75rem; line-height: 1.1;">{{ $step['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
{{-- CHATBOT WIDGET --}}
<div id="chatbot-widget" class="position-fixed" style="bottom: 30px; right: 30px; z-index: 1050;">
    <button class="btn btn-primary rounded-circle shadow-lg p-3" style="width: 60px; height: 60px;" onclick="toggleChat()">
        <i class="bi bi-robot fs-3"></i> </button>
    <div id="chat-window" class="card shadow-lg border-0 d-none" style="width: 340px; border-radius: 15px; position: absolute; bottom: 80px; right: 0;">
        <div class="card-header border-0 text-white p-3" style="background: #435ee0; border-radius: 15px 15px 0 0;">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="mb-0 text-white"><i class="bi bi-robot me-2"></i>Asmara Bot AI</h6>
                <button type="button" class="btn-close btn-close-white" onclick="toggleChat()"></button>
            </div>
        </div>
        <div class="card-body p-3 bg-light" style="height: 300px; overflow-y: auto;" id="chat-content">
            <div class="d-flex mb-3">
                <div class="bg-white p-2 rounded shadow-sm small text-dark">
                    Halo {{ Auth::user()->name }}! Saya Asmara Bot AI, siap membantu persiapan pernikahan impian Anda. Ada yang bisa saya bantu?
                </div>
            </div>
        </div>
        <div class="card-footer bg-white border-0 p-3" style="border-radius: 0 0 15px 15px;">
            <div class="input-group">
                <input type="text" id="chat-input" class="form-control form-control-sm" placeholder="Tanya tentang paket, jadwal, atau fitting...">
                <button class="btn btn-primary btn-sm" onclick="sendMessage()"><i class="bi bi-send"></i></button>
            </div>
        </div>
    </div>
</div>
<style>
    .circle-icon-sm { width: 50px; height: 50px; border-radius: 50%; background: #fff; border: 2px solid #eef2ff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin: 0 auto; }
</style>

<script>
    function toggleChat() {
        const chatWindow = document.getElementById('chat-window');
        chatWindow.classList.toggle('d-none');
    }
    function sendMessage() {
        // Logika kirim pesan akan diintegrasikan dengan AI Chatbot Anda nanti
        alert('Fitur Chatbot Sedang Disiapkan!');
    }
</script>
@endsection