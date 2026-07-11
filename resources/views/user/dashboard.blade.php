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

<style>
    /* Chatbot Widget Styles */
    #chatbot-widget {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 9999;
        font-family: 'Nunito', sans-serif;
    }
    #chatbot-toggle-btn {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #435ebe, #673ab7);
        border: none;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #chatbot-toggle-btn:hover {
        transform: scale(1.1) translateY(-3px);
        box-shadow: 0 10px 20px rgba(67, 94, 190, 0.4) !important;
    }
    #chatbot-toggle-btn i {
        transition: transform 0.3s ease;
    }
    #chatbot-toggle-btn.active i {
        transform: rotate(90deg);
    }
    #chat-window {
        width: 380px;
        height: 500px;
        position: absolute;
        bottom: 75px;
        right: 0;
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        opacity: 0;
        transform: translateY(20px) scale(0.95);
        pointer-events: none;
        transition: all 0.3s cubic-bezier(0.68, -0.6, 0.27, 1.55);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15) !important;
        background: #fff;
    }
    #chat-window.show {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }
    .chat-header {
        background: linear-gradient(135deg, #435ebe, #673ab7);
        padding: 15px;
        border-bottom: none;
    }
    .chat-body {
        background-color: #f7f9fb;
        flex: 1;
        overflow-y: auto;
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    /* Custom Scrollbar for Chat Body */
    .chat-body::-webkit-scrollbar {
        width: 5px;
    }
    .chat-body::-webkit-scrollbar-track {
        background: transparent;
    }
    .chat-body::-webkit-scrollbar-thumb {
        background: rgba(0,0,0,0.15);
        border-radius: 10px;
    }
    .chat-body::-webkit-scrollbar-thumb:hover {
        background: rgba(0,0,0,0.25);
    }
    .chat-message {
        max-width: 85%;
        padding: 10px 14px;
        font-size: 0.88rem;
        line-height: 1.45;
        position: relative;
        animation: messageBounce 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    @keyframes messageBounce {
        0% { transform: translateY(10px) scale(0.95); opacity: 0; }
        100% { transform: translateY(0) scale(1); opacity: 1; }
    }
    .chat-message.bot {
        align-self: flex-start;
        background: #ffffff;
        color: #2b303a;
        border-radius: 18px 18px 18px 4px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        border: 1px solid #eef2f6;
    }
    .chat-message.user {
        align-self: flex-end;
        background: linear-gradient(135deg, #435ebe, #5c78f1);
        color: #ffffff;
        border-radius: 18px 18px 4px 18px;
        box-shadow: 0 4px 10px rgba(67, 94, 190, 0.15);
    }
    .chat-message ul {
        margin-bottom: 0;
        padding-left: 18px;
    }
    .chat-message li {
        margin-bottom: 3px;
    }
    .chat-message li:last-child {
        margin-bottom: 0;
    }
    .chat-footer {
        padding: 10px 12px;
        border-top: 1px solid #f1f3f5;
        background: #fff;
    }
    .chat-input-group {
        background: #f1f3f8;
        border-radius: 25px;
        padding: 3px 3px 3px 14px;
        display: flex;
        align-items: center;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }
    .chat-input-group:focus-within {
        border-color: #435ebe;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(67, 94, 190, 0.15);
    }
    .chat-input-field {
        border: none;
        background: transparent;
        outline: none;
        flex: 1;
        font-size: 0.88rem;
        padding: 6px 0;
        color: #333;
    }
    .chat-send-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #435ebe, #673ab7);
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .chat-send-btn:hover {
        transform: scale(1.08);
        box-shadow: 0 4px 8px rgba(67, 94, 190, 0.2);
    }
    
    /* Typing Indicator styles */
    .typing-bubble {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 12px 18px;
        background: #fff;
        border-radius: 18px 18px 18px 4px;
        border: 1px solid #eef2f6;
        align-self: flex-start;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }
    .typing-dot {
        width: 6px;
        height: 6px;
        background: #888;
        border-radius: 50%;
        animation: typingBounce 1.4s infinite ease-in-out both;
    }
    .typing-dot:nth-child(1) { animation-delay: -0.32s; }
    .typing-dot:nth-child(2) { animation-delay: -0.16s; }
    .typing-dot:nth-child(3) { animation-delay: -0.0s; }
    @keyframes typingBounce {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1.0); }
    }
</style>

<div id="chatbot-widget">
    <button id="chatbot-toggle-btn" class="btn btn-primary rounded-circle shadow-lg" onclick="toggleChat()">
        <i class="bi bi-robot fs-3"></i> 
    </button>
    
    <div id="chat-window" class="card border-0">
        <div class="chat-header text-white d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi bi-robot fs-4 me-2"></i>
                <div>
                    <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Asmara Bot AI</h6>
                    <span class="small text-white-50" style="font-size: 0.75rem;">Griya Rias Asmara</span>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-link text-white p-0 me-3" onclick="clearChat()" title="Hapus Riwayat Chat" style="text-decoration: none;">
                    <i class="bi bi-trash fs-5"></i>
                </button>
                <button type="button" class="btn-close btn-close-white" onclick="toggleChat()"></button>
            </div>
        </div>
        
        <div class="chat-body" id="chat-content">
            <div class="chat-message bot">
                Halo {{ Auth::user()->name }}! Saya Asmara Bot AI. Ada yang bisa saya bantu terkait layanan Griya Rias Asmara (GRA)?
            </div>
        </div>
        
        <div class="chat-footer">
            <div class="chat-input-group">
                <input type="text" id="chat-input" class="chat-input-field" placeholder="Tanya tentang paket, harga, booking..." autocomplete="off">
                <button class="chat-send-btn" onclick="sendMessage()"><i class="bi bi-send-fill" style="font-size: 0.85rem;"></i></button>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleChat() {
        const chatWindow = document.getElementById('chat-window');
        const toggleBtn = document.getElementById('chatbot-toggle-btn');
        chatWindow.classList.toggle('show');
        toggleBtn.classList.toggle('active');
        if (chatWindow.classList.contains('show')) {
            document.getElementById('chat-input').focus();
        }
    }

    function formatReply(text) {
        if (!text) return '';
        // Escape HTML to prevent XSS
        let escaped = text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");

        // Format bold text **bold** -> <strong>bold</strong>
        escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

        // Parse line breaks and bullet lists
        let lines = escaped.split(/\n/);
        let inList = false;
        let formattedLines = [];

        for (let i = 0; i < lines.length; i++) {
            let line = lines[i].trim();
            // Check if line matches a bullet point: starting with - or * followed by a space
            if (line.startsWith('- ') || line.startsWith('* ')) {
                if (!inList) {
                    formattedLines.push('<ul>');
                    inList = true;
                }
                let itemText = line.substring(2).trim();
                formattedLines.push('<li>' + itemText + '</li>');
            } else {
                if (inList) {
                    formattedLines.push('</ul>');
                    inList = false;
                }
                formattedLines.push(lines[i]);
            }
        }
        if (inList) {
            formattedLines.push('</ul>');
        }

        return formattedLines.join('<br>').replace(/<\/ul><br>/g, '</ul>').replace(/<br><ul>/g, '<ul>');
    }

    async function clearChat() {
        if (confirm('Apakah Anda yakin ingin menghapus seluruh riwayat chat Anda dengan Asmara Bot AI?')) {
            try {
                const res = await fetch('/user/chat/clear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await res.json();
                if (data.status === 'success') {
                    const content = document.getElementById('chat-content');
                    content.innerHTML = `<div class="chat-message bot">Halo {{ Auth::user()->name }}! Saya Asmara Bot AI. Ada yang bisa saya bantu terkait layanan Griya Rias Asmara (GRA)?</div>`;
                }
            } catch (e) {
                console.error("Gagal menghapus histori: ", e);
            }
        }
    }

    async function sendMessage() {
        const input = document.getElementById('chat-input');
        const content = document.getElementById('chat-content');
        const message = input.value.trim();
        if (!message) return;

        // Tambahkan pesan User
        content.innerHTML += `<div class="chat-message user"><span>${formatReply(message)}</span></div>`;
        input.value = '';
        content.scrollTop = content.scrollHeight;

        // Tambahkan Typing Indicator
        const typingId = 'typing-' + Date.now();
        content.innerHTML += `
            <div class="typing-bubble" id="${typingId}">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        `;
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
            
            // Hapus typing indicator
            const typingIndicator = document.getElementById(typingId);
            if (typingIndicator) typingIndicator.remove();

            // Tambahkan pesan Bot
            content.innerHTML += `<div class="chat-message bot"><span>${formatReply(data.reply)}</span></div>`;
        } catch (e) {
            // Hapus typing indicator
            const typingIndicator = document.getElementById(typingId);
            if (typingIndicator) typingIndicator.remove();
            
            content.innerHTML += `<div class="chat-message bot"><span style="color: #dc3545;"><i class="bi bi-exclamation-triangle me-1"></i> Gagal terhubung ke AI. Silakan coba lagi nanti.</span></div>`;
        }
        content.scrollTop = content.scrollHeight;
    }
    
    document.getElementById('chat-input').addEventListener('keypress', (e) => { if (e.key === 'Enter') sendMessage(); });
</script>
@endsection
