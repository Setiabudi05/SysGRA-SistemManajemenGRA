<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya | SysGRA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('assets-user/css/custom-style.css') }}" rel="stylesheet">
    <style>
        body { background-color: #121212; color: white; }
        .card-order { background: white; color: #333; border-radius: 20px; border: none; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-success { background: #d4edda; color: #155724; }
    </style>
</head>
<body>

{{-- Header tetap sama dengan Landing Page --}}
<header class="header fixed-top py-3 shadow-sm" style="background: rgba(0,0,0,0.95);">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ url('/') }}" class="text-decoration-none"><h3 class="m-0 text-white fw-bold">GriyaRiasAsmara</h3></a>
        <a href="{{ url('/') }}" class="btn btn-outline-light rounded-pill btn-sm"><i class="bi bi-house me-1"></i> Beranda</a>
    </div>
</header>

<main style="margin-top: 120px; padding-bottom: 80px;">
    <div class="container">
        <h4 class="fw-bold mb-4"><i class="bi bi-clock-history text-danger me-2"></i> Histori Pesanan Saya</h4>

        @if($pesanan->count() > 0)
            @foreach($pesanan as $p)
            <div class="card card-order mb-4 shadow">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-6 border-end">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge {{ $p->status == 'Menunggu Pembayaran' ? 'status-pending' : 'status-success' }} rounded-pill">
                                    {{ $p->status }}
                                </span>
                                <small class="text-muted">Invoice: #{{ $p->id }}</small>
                            </div>
                            <h5 class="fw-bold text-dark">{{ $p->package_name }}</h5>
                            <p class="mb-1 small"><i class="bi bi-calendar-event me-2"></i> Jadwal: {{ $p->event_date }}</p>
                            <p class="mb-0 small"><i class="bi bi-geo-alt me-2 text-danger"></i> Lokasi: {{ $p->location }}</p>
                        </div>
                        <div class="col-md-6 ps-md-4 mt-3 mt-md-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="small text-muted mb-0">Total Biaya:</p>
                                    <h5 class="fw-bold text-danger">Rp {{ number_format($p->total_price, 0, ',', '.') }}</h5>
                                </div>
                                
                                {{-- Tombol Upload muncul jika status belum dibayar --}}
                                @if($p->status == 'Menunggu Pembayaran')
                                <button class="btn btn-danger btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#uploadModal{{ $p->id }}">
                                    <i class="bi bi-upload me-2"></i> Bayar Sekarang
                                </button>
                                @else
                                <span class="text-success fw-bold small"><i class="bi bi-check-circle-fill me-1"></i> Terverifikasi</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="uploadModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="color: #333;">
                        <form action="{{ route('pesanan.upload', $p->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header border-0">
                                <h5 class="modal-title fw-bold">Upload Bukti Transfer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="bg-light p-3 rounded-3 mb-3">
                                    <p class="small mb-1">Transfer ke Rekening SysGRA:</p>
                                    <p class="fw-bold mb-0">BRI: 0123-456-789 (a.n Griya Rias Asmara)</p>
                                    <p class="text-danger small fw-bold mt-2">* Minimal Bayar DP 50%: Rp {{ number_format($p->total_price * 0.5, 0, ',', '.') }}</p>
                                </div>
                                <input type="file" name="bukti_bayar" class="form-control rounded-pill" required>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold">Kirim Konfirmasi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="text-center py-5">
                <i class="bi bi-journal-x display-1 text-muted"></i>
                <p class="mt-3">Anda belum memiliki riwayat pesanan.</p>
            </div>
        @endif
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>