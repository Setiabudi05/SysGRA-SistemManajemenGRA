@extends('layouts.user')

@section('title', 'Riwayat Pernikahan Anda')

@section('content')
    {{-- HEADER HALAMAN --}}
    <div class="page-heading mb-3">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h3 class="fw-bold text-dark"><i class="bi bi-clock-history me-2 text-success"></i>Riwayat Pernikahan Anda</h3>
                <p class="text-muted small">Arsip data pesanan dan rincian lembar bukti pembayaran sah Anda bersama Griya Rias Asmara.</p>
            </div>
            <div class="col-12 col-md-6 text-md-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 justify-content-md-end">
                        <li class="breadcrumb-item"><a href="{{ url('user/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Riwayat</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <div class="page-content">
        @forelse($historyBookings as $hb)
            <div class="card shadow-sm border-0 mb-4 bg-white overflow-hidden" style="border-radius: 12px;">
                @php
                    $isCompletedStatus = in_array(strtoupper($hb->status), ['COMPLETED', 'SUCCESS']);
                @endphp
                <div class="card-header {{ $isCompletedStatus ? 'bg-success bg-opacity-10' : 'bg-warning bg-opacity-10' }} py-3 border-0 d-flex justify-content-between align-items-center"
                     style="{{ $isCompletedStatus ? 'background-color: #e8f5e9 !important;' : 'background-color: #fff3cd !important;' }}">
                    <span class="fw-bold {{ $isCompletedStatus ? 'text-success' : 'text-warning' }}" style="font-size: 0.9rem;">
                        <i class="{{ $isCompletedStatus ? 'bi bi-patch-check-fill' : 'bi bi-hourglass-split' }} me-1"></i>
                        ID TRANSAKSI: #GRA-{{ $hb->id }}
                        ({{ $isCompletedStatus ? 'ACARA SELESAI' : 'PESANAN AKTIF / BELUM LUNAS' }})
                    </span>
                    <a href="{{ route('user.booking.print', $hb->id) }}" target="_blank"
                       class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3 shadow-sm">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download Format Booking
                    </a>
                </div>

                <div class="card-body p-4">
                    <div class="row g-3 mb-4 pb-3 border-bottom border-light align-items-start">
                        <div class="col-md-3">
                            <small class="text-muted text-uppercase d-block fw-bold mb-1" style="font-size: 0.7rem;">Nama Pengantin</small>
                            <span class="fw-bold text-dark fs-5">{{ $hb->bride_groom_name }}</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted text-uppercase d-block fw-bold mb-1" style="font-size: 0.7rem;">Paket Pernikahan</small>
                            
                            {{-- NAMA PAKET & TOMBOL DETAIL (SEJAJAR PERSIS DI SAMPINGNYA) --}}
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold text-primary fs-6">{{ $hb->package_name }}</span>
                                
                                @if($hb->paket)
                                    <button class="btn btn-sm btn-outline-primary py-0 px-2 fw-bold text-nowrap shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#detailPaket{{ $hb->id }}" aria-expanded="false" style="font-size: 0.65rem; border-radius: 6px; height: 22px;">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </button>
                                @endif
                            </div>

                            <span class="text-secondary fw-semibold small d-block">Rp {{ number_format((float) $hb->total_harga, 0, ',', '.') }}</span>
                            
                            {{-- Menampilkan daftar Add-ons --}}
                            @if($hb->addOns->isNotEmpty())
                                <div class="mt-2">
                                    <small class="text-muted text-uppercase d-block fw-bold" style="font-size: 0.65rem;">Add-ons:</small>
                                    <ul class="list-unstyled mb-0 mt-1">
                                        @foreach($hb->addOns as $add)
                                            <li class="small text-dark fw-bold"><i class="bi bi-plus-circle text-primary me-1" style="font-size: 0.7rem;"></i>{{ $add->nama_item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted text-uppercase d-block fw-bold mb-1" style="font-size: 0.7rem;">Tanggal & Lokasi</small>
                            <span class="fw-bold text-dark d-block small"><i class="bi bi-calendar-event text-danger me-1"></i>{{ date('d M Y', strtotime($hb->event_date)) }}</span>
                            <span class="text-muted small text-truncate d-block">{{ $hb->event_address }}</span>
                        </div>
                        <div class="col-md-3 text-md-end">
                            <small class="text-muted text-uppercase d-block fw-bold mb-1" style="font-size: 0.7rem;">Status Keuangan</small>
                            @php $isLunas = ((float) $hb->total_terbayar >= (float) $hb->total_harga); @endphp
                            <span class="badge {{ $isLunas ? 'bg-success' : 'bg-danger' }} px-3 py-2 fw-bold rounded-pill shadow-sm">
                                {{ $isLunas ? 'TERBAYAR LUNAS' : 'BELUM LUNAS' }}
                            </span>
                        </div>
                    </div>

                    {{-- AREA DETAIL FASILITAS PAKET (YANG MUNCUL KETIKA TOMBOL DETAIL DIKLIK) --}}
                    @if($hb->paket)
                        <div class="collapse mb-4" id="detailPaket{{ $hb->id }}">
                            <div class="p-3 rounded-4 bg-light border shadow-sm">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-primary fw-bold text-uppercase" style="font-size: 0.75rem;">
                                        <i class="bi bi-box-seam-fill me-1"></i> Rincian Fasilitas & Layanan Paket ({{ $hb->package_name }})
                                    </span>
                                    <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#detailPaket{{ $hb->id }}" style="font-size: 0.6rem;"></button>
                                </div>
                                
                                <div class="row g-2 small">
                                    @if($hb->paket->makeup)
                                        <div class="col-md-6">
                                            <div class="p-2 rounded-3 bg-white border h-100">
                                                <strong class="text-dark d-block mb-1" style="font-size: 0.7rem;"><i class="bi bi-brush text-danger me-1"></i> Makeup:</strong>
                                                <span class="text-muted" style="font-size: 0.75rem; line-height: 1.3; display: block;">{{ $hb->paket->makeup }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if($hb->paket->dekorasi)
                                        <div class="col-md-6">
                                            <div class="p-2 rounded-3 bg-white border h-100">
                                                <strong class="text-dark d-block mb-1" style="font-size: 0.7rem;"><i class="bi bi-flower1 text-success me-1"></i> Dekorasi:</strong>
                                                <span class="text-muted" style="font-size: 0.75rem; line-height: 1.3; display: block;">{{ $hb->paket->dekorasi }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if($hb->paket->layos)
                                        <div class="col-md-6">
                                            <div class="p-2 rounded-3 bg-white border h-100">
                                                <strong class="text-dark d-block mb-1" style="font-size: 0.7rem;"><i class="bi bi-people text-info me-1"></i> Layos:</strong>
                                                <span class="text-muted" style="font-size: 0.75rem; line-height: 1.3; display: block;">{{ $hb->paket->layos }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if($hb->paket->dokumentasi)
                                        <div class="col-md-6">
                                            <div class="p-2 rounded-3 bg-white border h-100">
                                                <strong class="text-dark d-block mb-1" style="font-size: 0.7rem;"><i class="bi bi-camera text-warning me-1"></i> Dokumentasi:</strong>
                                                <span class="text-muted" style="font-size: 0.75rem; line-height: 1.3; display: block;">{{ $hb->paket->dokumentasi }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if($hb->paket->include)
                                        <div class="col-12">
                                            <div class="p-2 rounded-3 bg-white border">
                                                <strong class="text-dark d-block mb-1" style="font-size: 0.7rem;"><i class="bi bi-check-circle text-primary me-1"></i> Include Fasilitas Lainnya:</strong>
                                                <span class="text-muted" style="font-size: 0.75rem;">{{ $hb->paket->include }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if($hb->paket->bonus)
                                        <div class="col-12">
                                            <div class="p-2 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25">
                                                <strong class="text-success d-block mb-1" style="font-size: 0.7rem;"><i class="bi bi-gift-fill me-1"></i> Bonus Spesial:</strong>
                                                <span class="text-dark fw-semibold" style="font-size: 0.75rem;">{{ $hb->paket->bonus }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- TABEL HISTORI PEMBAYARAN --}}
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th class="text-end">Nominal</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hb->pembayarans as $index => $bayar)
                                    <tr>
                                        <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                                        <td class="text-dark">{{ \Carbon\Carbon::parse($bayar->created_at)->isoFormat('D MMMM Y') }}</td>
                                        <td>{{ $bayar->keterangan ?? 'Pembayaran Cicilan' }}</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('user.pembayaran.cetakNota', $bayar->id) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3">Cetak Nota</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-3">Tidak ada log pembayaran.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="card shadow-sm border-0 bg-white py-5 text-center" style="border-radius: 15px; min-height: 400px; display: flex; align-items: center; justify-content: center;">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light" style="width: 100px; height: 100px;">
                            <i class="bi bi-clock-history text-secondary" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Belum Ada Riwayat Acara</h5>
                    <p class="text-muted small mx-auto mb-4" style="max-width: 400px;">
                        Data pesanan Anda akan muncul di sini setelah status transaksi terupdate oleh sistem.
                    </p>
                    <a href="{{ url('user/booking') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                        <i class="bi bi-calendar-plus me-2"></i> Buat Pesanan Baru
                    </a>
                </div>
            </div>
        @endforelse
    </div>
@endsection