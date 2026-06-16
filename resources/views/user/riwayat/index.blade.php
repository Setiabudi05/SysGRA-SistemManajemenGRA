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
            {{-- BLOCK CARDS INDUK ACARA PER KONTRAK --}}
            <div class="card shadow-sm border-0 mb-4 bg-white overflow-hidden" style="border-radius: 12px;">
                {{-- ATAS CARD: STATUS UTAMA --}}
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
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download Kontrak
                    </a>
                </div>

                <div class="card-body p-4">
                    {{-- INFO DATA PENGANTIN --}}
                    <div class="row g-3 mb-4 pb-3 border-bottom border-light">
                        <div class="col-md-3">
                            <small class="text-muted text-uppercase d-block fw-bold mb-1" style="font-size: 0.7rem;">Nama Pengantin</small>
                            <span class="fw-bold text-dark fs-5">{{ $hb->bride_groom_name }}</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted text-uppercase d-block fw-bold mb-1" style="font-size: 0.7rem;">Paket Pernikahan</small>
                            <span class="fw-bold text-primary d-block">{{ $hb->package_name }}</span>
                            <span class="text-secondary fw-semibold small">Rp {{ number_format((float) $hb->package_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted text-uppercase d-block fw-bold mb-1" style="font-size: 0.7rem;">Tanggal & Lokasi</small>
                            <span class="fw-bold text-dark d-block small"><i class="bi bi-calendar-event text-danger me-1"></i>{{ date('d M Y', strtotime($hb->event_date)) }}</span>
                            <span class="text-muted small text-truncate d-block">{{ $hb->event_address }}</span>
                        </div>
                        <div class="col-md-3 text-md-end">
                            <small class="text-muted text-uppercase d-block fw-bold mb-1" style="font-size: 0.7rem;">Status Keuangan</small>
                            @php $isLunas = ((float) $hb->total_terbayar >= (float) $hb->package_price); @endphp
                            <span class="badge {{ $isLunas ? 'bg-success' : 'bg-danger' }} px-3 py-2 fw-bold rounded-pill shadow-sm">
                                {{ $isLunas ? 'TERBAYAR LUNAS' : 'BELUM LUNAS' }}
                            </span>
                        </div>
                    </div>

                    {{-- TABEL HISTORI --}}
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
            {{-- ESTETIC EMPTY STATE --}}
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