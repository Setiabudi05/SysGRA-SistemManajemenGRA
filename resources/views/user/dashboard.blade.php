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

{{-- CHATBOT WIDGET --}}

<div id="chatbot-widget" style="position: fixed; bottom: 30px; right: 30px; z-index: 9999;">
    <button class="btn btn-primary rounded-circle shadow-lg" style="width: 60px; height: 60px;" onclick="toggleChat()">
        <i class="bi bi-robot fs-3"></i> 
    </button>
    
    <div id="chat-window" class="card shadow-lg border-0 d-none" style="width: 350px; height: 450px; position: absolute; bottom: 75px; right: 0; border-radius: 15px; overflow: hidden; display: flex; flex-direction: column;">
        <div class="card-header text-white p-3 d-flex justify-content-between align-items-center" style="background: #435ee0;">
            <h6 class="mb-0"><i class="bi bi-robot me-2"></i>Asmara Bot AI</h6>
            <button type="button" class="btn-close btn-close-white" onclick="toggleChat()"></button>
        </div>
        
        <div class="card-body p-3 bg-light" style="flex: 1; overflow-y: auto;" id="chat-content">
            <div class="d-flex mb-3">
                <div class="bg-white p-2 rounded shadow-sm small text-dark">
                    Halo {{ Auth::user()->name }}! Saya Asmara Bot AI. Ada yang bisa saya bantu terkait persiapan pernikahan Anda?
                </div>
            </div>
        </div>
        
        <div class="card-footer bg-white border-0 p-2">
            <div class="input-group">
                <input type="text" id="chat-input" class="form-control" placeholder="Tanya paket...">
                <button class="btn btn-primary" onclick="sendMessage()"><i class="bi bi-send"></i></button>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleChat() { document.getElementById('chat-window').classList.toggle('d-none'); }

    async function sendMessage() {
        const input = document.getElementById('chat-input');
        const content = document.getElementById('chat-content');
        const message = input.value.trim();
        if (!message) return;

        // Tambahkan pesan User (Class msg-user akan otomatis membuatnya ke kanan)
        content.innerHTML += `<div class="msg-user"><span>${message}</span></div>`;
        input.value = '';
        content.scrollTop = content.scrollHeight;

        try {
            const res = await fetch('/user/chat', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ message: message })
            });

            const data = await res.json();
            
            // Tambahkan pesan Bot (Class msg-bot akan otomatis membuatnya ke kiri)
            content.innerHTML += `<div class="msg-bot"><span>${data.reply}</span></div>`;
        } catch (e) {
            content.innerHTML += `<div class="msg-bot"><span style="color:red;">${e.message}</span></div>`;
        }
        content.scrollTop = content.scrollHeight;
        
    }
    
    document.getElementById('chat-input').addEventListener('keypress', (e) => { if (e.key === 'Enter') sendMessage(); });
</script>
@endsection

