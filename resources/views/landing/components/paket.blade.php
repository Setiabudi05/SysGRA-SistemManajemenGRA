{{-- ID disesuaikan menjadi id="paket" untuk navigasi --}}
<section id="paket" class="paket section" style="background-color: #fdfbf7; padding: 100px 0;">

    <div class="container section-title" data-aos="fade-up">
        <span class="subtitle" style="color: #b8860b; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 10px;">Paket Eksklusif</span>
        <h2 style="color: #2c3e50; font-weight: 700; font-size: 36px;">Pilihan Paket Pernikahan {{ date('Y') }}</h2>
        <p style="color: #7f8c8d; max-width: 600px; margin: auto;">Temukan harmoni sempurna untuk hari bahagia Anda dengan layanan terbaik kami yang elegan dan profesional.</p>
    </div>

    <div class="container">
        {{-- row-cols-lg-3 memastikan 3 kolom sebaris yang rapi --}}
        <div class="row gy-5 row-cols-1 row-cols-md-2 row-cols-lg-3">

            @foreach($pakets as $item)
            <div class="col" data-aos="fade-up" data-aos-delay="100">
                <div class="paket-item shadow-sm d-flex flex-column h-100">
                    
                    <h3 class="package-name">{{ strtoupper($item->nama_paket) }}</h3>
                    
                    <div class="price-box">
                        <span class="currency">Rp</span>
                        <span class="amount">{{ number_format($item->harga, 0, ',', '.') }}</span>
                        <span class="period">/ paket</span>
                    </div>
                    
                    {{-- Tombol Utama --}}
                    <a href="javascript:void(0)" class="cta-btn btn-pilih-paket" 
                        data-package-name="{{ $item->nama_paket }}" 
                        data-package-price="Rp {{ number_format($item->harga, 0, ',', '.') }}">
                        Pilih Paket
                    </a>

                    {{-- List Fitur dengan Jarak mt-4 agar tidak mepet dengan tombol --}}
                    <ul class="features-list flex-grow-1 mt-4">
                        <li>
                            <i class="bi bi-check2-circle text-gold"></i>
                            <div><strong>Makeup:</strong> {{ $item->makeup }}</div>
                        </li>
                        
                        @if($item->dekorasi && $item->dekorasi != '-')
                        <li>
                            <i class="bi bi-check2-circle text-gold"></i>
                            <div><strong>Dekorasi:</strong> {{ $item->dekorasi }}</div>
                        </li>
                        @endif
                    </ul>

                    {{-- Detail Paket (Slide Toggle) --}}
                    <div id="details-{{ $item->id }}" class="hidden-details" style="display: none;">
                        <ul class="features-list pt-0">
                            @if($item->dokumentasi && $item->dokumentasi != '-')
                            <li>
                                <i class="bi bi-check2-circle text-gold"></i>
                                <div><strong>Dokumentasi:</strong> {{ $item->dokumentasi }}</div>
                            </li>
                            @endif
                        </ul>
                    </div>

                    {{-- Link Detail di bagian bawah card --}}
                    <div class="text-center mt-auto pt-4">
                        <a href="#" class="read-more-link" id="readMore-{{ $item->id }}" 
                           onclick="toggleDetails('{{ $item->id }}'); return false;">
                            Lihat Detail Paket <i class="bi bi-chevron-down ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</section>

<style>
    :root {
        --primary-gold: #b8860b;   /* Warna Emas Redup (Elegant) */
        --hover-gold: #8b6b06;
        --text-dark: #2d3436;
        --text-muted: #636e72;
        --bg-card: #ffffff;
    }

    .paket-item {
        background: var(--bg-card);
        padding: 45px 35px;
        border-radius: 25px;
        border: 1px solid #f0f0f0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .paket-item:hover {
        transform: translateY(-12px);
        box-shadow: 0 25px 50px rgba(184, 134, 11, 0.12) !important;
        border-color: var(--primary-gold);
    }

    .package-name {
        font-size: 17px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 15px;
        letter-spacing: 1.2px;
    }

    .price-box {
        margin-bottom: 30px;
        color: var(--primary-gold);
    }

    .price-box .currency {
        font-size: 18px;
        font-weight: 600;
        vertical-align: top;
        display: inline-block;
        margin-top: 8px;
    }

    .price-box .amount {
        font-size: 34px;
        font-weight: 800;
        letter-spacing: -1px;
    }

    .price-box .period {
        font-size: 14px;
        color: var(--text-muted);
        font-weight: 400;
    }

    /* Tombol Pilih Paket */
    .cta-btn {
        background: var(--primary-gold);
        color: white !important;
        text-align: center;
        padding: 14px 25px;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        display: block;
        transition: 0.3s;
        box-shadow: 0 4px 15px rgba(184, 134, 11, 0.25);
        /* Memberikan jarak bawah agar konten di bawahnya tidak sesak */
        margin-bottom: 10px; 
    }

    .cta-btn:hover {
        background: var(--hover-gold);
        box-shadow: 0 6px 20px rgba(184, 134, 11, 0.4);
        transform: scale(1.02);
    }

    /* List Fitur */
    .features-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .features-list li {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 16px;
        font-size: 14.5px;
        color: var(--text-muted);
        line-height: 1.5;
        text-align: left;
    }

    .text-gold {
        color: var(--primary-gold);
        font-size: 18px;
        flex-shrink: 0;
    }

    /* Read More Link */
    .read-more-link {
        color: var(--primary-gold);
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: 0.3s;
        border-top: 1px solid #f8f8f8;
        display: inline-block;
        padding-top: 10px;
        width: 100%;
    }

    .read-more-link:hover {
        color: var(--text-dark);
    }
</style>