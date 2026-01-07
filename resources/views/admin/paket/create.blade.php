@extends('layouts.master')

@section('title', 'Tambah Paket Pernikahan')

@push('css')
{{-- Memanggil style eksternal untuk sinkronisasi jarak konten --}}
<link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            {{-- Sisi Kiri: Judul dan Breadcrumb --}}
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.paket.index') }}" class="text-muted">Paket</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Tambah Paket</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0"><i class="bi bi-box-seam-fill me-2"></i>Tambah Paket</h3>
                <p class="text-muted mb-0 small">Kelola rincian layanan paket pernikahan baru.</p>
            </div>

            {{-- POSISI 1: Navigasi Teks Halus di Pojok Kanan Atas (Lurus dengan ujung form) --}}
            <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('admin.paket.index') }}" class="text-muted small fw-bold text-decoration-none">
                    <i class="bi bi-chevron-left"></i> Kembali ke daftar paket
                </a>
            </div>
        </div>
    </div>
    <hr class="mb-4">
</div>

<section class="section">
    <div class="row">
        {{-- col-lg-12 memastikan lebar kartu lurus dengan navigasi teks di atas --}}
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0 pt-4">
                    <h5 class="fw-bold text-primary mb-0">Form Tambah Paket</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.paket.store') }}" method="POST" data-parsley-validate>
                        @csrf
                        <div class="row mt-3">
                            {{-- Baris 1: Nama Paket & Harga --}}
                            <div class="col-md-6 mb-3">
                                <label for="nama_paket" class="form-label fw-bold">Nama Paket <span class="text-danger">*</span></label>
                                <input type="text" id="nama_paket" name="nama_paket"
                                    class="form-control shadow-sm @error('nama_paket') is-invalid @enderror"
                                    placeholder="Contoh: Paket Hemat / Standar" value="{{ old('nama_paket') }}" required>
                                @error('nama_paket')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="tahun" class="form-label">Tahun Pricelist</label>
                                <select name="tahun" id="tahun" class="form-select @error('tahun') is-invalid @enderror" required>
                                    @php
                                    $currentYear = date('Y');
                                    @endphp
                                    @for ($i = $currentYear + 3; $i >= ($currentYear - 2); $i--)
                                    <option value="{{ $i }}" {{ $i == $currentYear ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                                @error('tahun')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="harga" class="form-label fw-bold">Harga Paket (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-light fw-bold">Rp</span>
                                    <input type="number" id="harga" name="harga"
                                        class="form-control @error('harga') is-invalid @enderror"
                                        placeholder="0" value="{{ old('harga') }}" required>
                                </div>
                                @error('harga')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <hr class="my-3">
                            </div>

                            {{-- Baris 2: Detail Layanan --}}
                            <div class="col-md-4 mb-3">
                                <label for="makeup" class="form-label fw-bold">Detail Makeup</label>
                                <textarea id="makeup" name="makeup" rows="4"
                                    class="form-control shadow-sm"
                                    placeholder="Contoh: Make Up Pengantin 1x + retouch">{{ old('makeup') }}</textarea>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="dekorasi" class="form-label fw-bold">Detail Dekorasi</label>
                                <textarea id="dekorasi" name="dekorasi" rows="4"
                                    class="form-control shadow-sm"
                                    placeholder="Contoh: Dekorasi Ukuran 6M, Panggung Pelaminan">{{ old('dekorasi') }}</textarea>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="dokumentasi" class="form-label fw-bold">Detail Dokumentasi</label>
                                <textarea id="dokumentasi" name="dokumentasi" rows="4"
                                    class="form-control shadow-sm"
                                    placeholder="Contoh: 1 Album Magnetik, Soft File Flashdisk">{{ old('dokumentasi') }}</textarea>
                            </div>
                        </div>

                        {{-- POSISI 2: Footer Form (Kembali di kiri, Reset & Simpan di kanan lurus dengan form) --}}
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            {{-- Tombol Kembali Fisik Sejajar di Kiri --}}
                            <a href="{{ route('admin.paket.index') }}" class="btn btn-secondary shadow-sm px-4 fw-bold">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>

                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-light px-4 fw-bold border">Reset</button>
                                <button type="submit" class="btn btn-primary px-4 fw-bold shadow">
                                    <i class="bi bi-save me-1"></i> Simpan Paket
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

@include('sweetalert::alert')
@endsection

@push('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://parsleyjs.org/dist/parsley.min.js"></script>
<script src="{{ asset('assets/admin/static/js/pages/parsley.js') }}"></script>
@endpush