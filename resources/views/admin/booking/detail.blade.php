@extends('layouts.master')
@section('title', 'Detail Pesanan #' . $booking->id)

@push('css')
<link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
<style>
    /* CSS Bersih Anti-Error VS Code */
    .info-label { font-size: 0.72rem; color: #6c757d; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 3px; display: block; }
    .info-value { font-size: 0.9rem; font-weight: 600; color: #334155; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 0; }
    .bg-light-info { background-color: #f8fafc; border: 1px solid #e2e8f0; }
    .progress { background-color: #f1f5f9; border-radius: 10px; height: 12px !important; overflow: hidden; }
    .progress-bar { transition: width .6s ease; }
    .section-title-sm { font-size: 0.85rem; font-weight: 800; color: #435ebe; border-left: 4px solid #435ebe; padding-left: 10px; margin-bottom: 15px; text-transform: uppercase; }
</style>
@endpush

@section('content')
{{-- Header & Navigasi: Mengikuti Gaya Dekorasi --}}
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            {{-- Sisi Kiri: Judul dan Navigasi Breadcrumb --}}
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.booking.index') }}" class="text-muted">Pesanan</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Detail #{{ $booking->id }}</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">Detail Pesanan</h3>
                <p class="text-muted mb-0 small">Informasi rincian pengantin dan status pembayaran.</p>
            </div>

            {{-- POSISI KANAN: Navigasi Teks Halus --}}
            <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('admin.booking.index') }}" class="text-muted small fw-bold text-decoration-none">
                    <i class="bi bi-chevron-left"></i> Kembali ke daftar pesanan
                </a>
            </div>
        </div>
    </div>
    <hr class="mb-4">
</div>

<section class="section">
    <div class="row">
        {{-- SISI KIRI: INFORMASI UTAMA --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="section-title-sm">Identitas & Media Sosial</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="info-label text-uppercase">Nama Pemesan</label>
                            <p class="info-value">{{ $booking->customer_name }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="info-label text-uppercase">Nama Pengantin</label>
                            <p class="info-value text-primary fw-bold">{{ $booking->bride_groom_name }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="info-label text-uppercase">Facebook</label>
                            <p class="info-value text-muted">{{ $booking->facebook_name ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="info-label text-uppercase">Instagram</label>
                            <p class="info-value text-muted">{{ $booking->instagram_name ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="info-label text-uppercase">No. Telp / WA</label>
                            <p class="info-value">{{ $booking->whatsapp_number }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="info-label text-uppercase">Nama Orang Tua</label>
                            <p class="info-value">{{ $booking->parent_name ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="section-title-sm">Waktu & Tempat Pelaksanaan</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="info-label text-uppercase">Tanggal Pelaksanaan</label>
                            <p class="info-value text-danger fw-bold">{{ \Carbon\Carbon::parse($booking->event_date)->isoFormat('D MMMM Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="info-label text-uppercase">Durasi Acara</label>
                            <p class="info-value">{{ $booking->event_duration }} Hari</p>
                        </div>
                        <div class="col-md-4">
                            <label class="info-label text-uppercase">Alamat Lengkap</label>
                            <p class="info-value small text-truncate">{{ $booking->event_address }}</p>
                        </div>
                    </div>

                    <div class="section-title-sm">Rincian Paket & Layanan</div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-4">
                            <label class="info-label text-uppercase">Paket Utama</label>
                            <p class="info-value">{{ $booking->package_name }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="info-label text-uppercase">Paket Tambahan</label>
                            <p class="info-value text-muted">{{ $booking->add_ons ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="info-label text-uppercase">Catatan Khusus</label>
                            <p class="info-value text-muted">{{ $booking->notes ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end align-items-center">
                        <a href="{{ route('admin.booking.edit', $booking->id) }}" class="btn btn-warning btn-sm px-3 fw-bold shadow-sm">
                            <i class="bi bi-pencil-square"></i> Edit data
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- SISI KANAN: PROGRES & STATUS --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3 text-muted text-uppercase small">Progres Pembayaran</h6>
                    @php
                    $hargaBersih = (float) preg_replace('/[^0-9]/', '', $booking->package_price);
                    
                    // SINKRONISASI: Hitung total bayar real-time murni dari database pembayarans yang sukses
                    $totalBayar = DB::table('pembayarans')
                        ->where('pesanan_id', $booking->id)
                        ->where(function ($q) {
                            $q->whereRaw('LOWER(status_pembayaran) = ?', ['success'])
                              ->orWhereRaw('LOWER(status_pembayaran) = ?', ['lunas'])
                              ->orWhereNull('status_pembayaran');
                        })
                        ->sum('jumlah_bayar');

                    $sisaTagihan = $hargaBersih - $totalBayar;
                    $persenValue = ($hargaBersih > 0) ? ($totalBayar / $hargaBersih) * 100 : 0;
                    $persenValue = $persenValue > 100 ? 100 : round($persenValue);
                    @endphp

                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: <?php echo $persenValue; ?>%;">
                            {{ $persenValue }}%
                        </div>
                    </div>

                    <div class="bg-light-info p-3 rounded mb-3">
                        <div class="d-flex justify-content-between mb-2 small">
                            <span>Status Bayar</span>
                            <span class="badge {{ $sisaTagihan <= 0 ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $sisaTagihan <= 0 ? 'Lunas' : 'Belum Lunas' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span>Harga Paket</span>
                            <span class="fw-bold text-primary">Rp {{ number_format($hargaBersih, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-danger fw-bold small border-top pt-2">
                            <span>Sisa Tagihan</span>
                            <span>Rp {{ number_format($sisaTagihan < 0 ? 0 : $sisaTagihan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.booking.print', $booking->id) }}" target="_blank" class="btn btn-primary shadow-sm">
                            <i class="bi bi-printer"></i> CETAK DOKUMEN PDF
                        </a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3 text-muted text-uppercase small">Update Status Pesanan</h6>
                    <div class="d-grid gap-2">
                        
                        {{-- SOLUSI AMAN: Conditional Rendering Berdasarkan State Status Kapital --}}
                        @if(strtoupper($booking->status) == 'PENDING')
                            <button onclick="updateStatus('confirmed')" class="btn btn-primary w-100 shadow-sm">
                                <i class="bi bi-check2-circle"></i> Konfirmasi Pemesanan (DP)
                            </button>
                        @elseif(strtoupper($booking->status) == 'CONFIRMED')
                            <button class="btn btn-light-success text-success w-100 fw-bold border-1 border-success" disabled style="background-color: #e8f5e9;">
                                <i class="bi bi-shield-check"></i> TERKONFIRMASI (DP OK)
                            </button>
                        @else
                            <button class="btn btn-light-secondary text-muted w-100 fw-bold" disabled>
                                <i class="bi bi-lock-fill"></i> ACARA SELESAI (COMPLETED)
                            </button>
                        @endif

                        <button onclick="updateStatus('completed')" class="btn btn-success shadow-sm"
                            {{ $sisaTagihan > 0 || strtoupper($booking->status) == 'COMPLETED' ? 'disabled' : '' }}>
                            <i class="bi bi-patch-check"></i> Selesaikan Pesanan (Lunas)
                        </button>
                    </div>

                    @if($sisaTagihan > 0)
                    <div class="alert alert-warning mt-3 mb-0 py-2 border-0 shadow-none text-center" style="font-size: 0.75rem;">
                        <i class="bi bi-info-circle me-1"></i> Tombol <b>Selesaikan</b> aktif jika sisa tagihan Rp 0.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function updateStatus(val) {
        Swal.fire({
            title: 'Update Status Pesanan?',
            text: "Status akan diubah menjadi " + val.toUpperCase(),
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#435ebe',
            confirmButtonText: 'Ya, Update!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.booking.updateStatus', $booking->id) }}",
                    type: "PUT",
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: val
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    }
                });
            }
        });
    }
</script>
@endsection