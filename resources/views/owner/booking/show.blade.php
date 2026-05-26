@extends('layouts.master')
@section('title', 'Detail Pesanan - ' . $jadwal->nama)

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3 class="fw-bold">Rincian Lengkap Pesanan</h3>
                <p class="text-subtitle text-muted">Informasi identitas, media sosial, dan catatan khusus pelanggan.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first d-flex justify-content-md-end mb-3">
                <a href="{{ route('owner.booking.index') }}" class="btn btn-secondary shadow-sm">
                    <i class="bi bi-arrow-left"></i> Kembali ke Laporan
                </a>
            </div>
        </div>
    </div>

    <section class="section mt-3">
        <div class="row">
            <div class="col-12">
                {{-- Card Utama: Biodata & Identitas --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h4 class="card-title text-white mb-0">
                            <i class="bi bi-person-lines-fill me-2"></i> Identitas & Media Sosial
                        </h4>
                    </div>
                    <div class="card-body mt-4">
                        <div class="row">
                            {{-- Sisi Kiri: Nama & Orang Tua --}}
                            <div class="col-md-6 border-end">
                                <div class="row mb-3">
                                    <div class="col-5 fw-bold text-muted">Nama Pemesan</div>
                                    <div class="col-7">: {{ $jadwal->customer_name ?? '-' }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-5 fw-bold text-muted">Nama Pengantin</div>
                                    <div class="col-7 text-dark fw-bold">: {{ $jadwal->nama }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-5 fw-bold text-muted">Nama Orang Tua</div>
                                    <div class="col-7">: {{ $jadwal->parent_name ?? '-' }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-5 fw-bold text-muted">WhatsApp</div>
                                    <div class="col-7">
                                        : <a href="https://wa.me/{{ $jadwal->phone }}" target="_blank" class="text-success fw-bold">
                                            <i class="bi bi-whatsapp"></i> {{ $jadwal->phone ?? '-' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            {{-- Sisi Kanan: Media Sosial & Layanan --}}
                            <div class="col-md-6">
                                <div class="row mb-3">
                                    <div class="col-5 fw-bold text-muted">Facebook</div>
                                    <div class="col-7">: {{ $jadwal->facebook_name ?? '-' }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-5 fw-bold text-muted">Instagram</div>
                                    <div class="col-7">: {{ $jadwal->instagram_name ?? '-' }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-5 fw-bold text-muted">Paket Diambil</div>
                                    <div class="col-7">
                                        : <span class="badge bg-light-primary text-primary fw-bold">{{ $jadwal->paket->nama_paket ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-5 fw-bold text-muted">Lokasi Acara</div>
                                    <div class="col-7">: {{ $jadwal->alamat }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        {{-- Bagian Catatan Khusus --}}
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-bold text-primary mb-2"><i class="bi bi-chat-left-text me-2"></i>Catatan Khusus / Request:</h6>
                                <div class="p-3 rounded bg-light border-start border-4 border-primary">
                                    {!! nl2br(e($jadwal->notes ?? 'Tidak ada catatan khusus.')) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Penugasan Kru --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-bottom py-3">
                        <h4 class="card-title mb-0"><i class="bi bi-people-fill me-2"></i> Penugasan Tim (Kru)</h4>
                    </div>
                    <div class="card-body mt-4">
                        <div class="row text-center">
                            <div class="col-md-4 border-end">
                                <h6 class="text-muted fw-normal mb-2 text-uppercase small">Asisten Rias</h6>
                                <h5 class="fw-bold">{{ $jadwal->asisten ?? 'Belum diplot' }}</h5>
                            </div>
                            <div class="col-md-4 border-end">
                                <h6 class="text-muted fw-normal mb-2 text-uppercase small">Photographer</h6>
                                <h5 class="fw-bold">{{ $jadwal->fg ?? 'Belum diplot' }}</h5>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted fw-normal mb-2 text-uppercase small">Layos</h6>
                                <h5 class="fw-bold">{{ $jadwal->layos ?? 'Belum diplot' }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection