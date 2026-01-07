@extends('layouts.master')

@section('title', 'Tambah Dekorasi')

@push('css')
    {{-- Memanggil style eksternal untuk sinkronisasi tampilan --}}
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
@endpush

@push('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://parsleyjs.org/dist/parsley.min.js"></script>
    <script src="{{ asset('assets/admin/static/js/pages/parsley.js') }}"></script>
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            {{-- Sisi Kiri: Judul dan Navigasi --}}
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.dekorasi.index') }}" class="text-muted">Dekorasi</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Tambah Dekorasi</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0"><i class="bi bi-flower1 me-2"></i>Tambah Dekorasi</h3>
                <p class="text-muted mb-0 small">Tambahkan referensi foto dan deskripsi dekorasi untuk paket pernikahan.</p>
            </div>

            {{-- POSISI 1: Navigasi Teks Halus di Pojok Kanan Atas (Lurus dengan ujung form) --}}
            <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('admin.dekorasi.index') }}" class="text-muted small fw-bold text-decoration-none">
                    <i class="bi bi-chevron-left"></i> Kembali ke daftar dekorasi
                </a>
            </div>
        </div>
    </div>
    <hr class="mb-4">
</div>

<section id="form-create-dekorasi">
    <div class="row">
        {{-- col-lg-12 memastikan lebar kartu lurus dengan navigasi teks di atas --}}
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0 pt-4">
                    <h5 class="fw-bold text-primary mb-0">Form Tambah Dekorasi</h5>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form class="form" action="{{ route('admin.dekorasi.store') }}" method="POST"
                            enctype="multipart/form-data" data-parsley-validate>
                            @csrf

                            <div class="row mt-3">
                                <div class="col-md-6 mb-3">
                                    <label for="paket_id" class="form-label fw-bold">Pilih Paket <span class="text-danger">*</span></label>
                                    <select id="paket_id" name="paket_id"
                                        class="form-select shadow-sm @error('paket_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Paket --</option>
                                        @foreach($pakets as $paket)
                                            <option value="{{ $paket->id }}" {{ old('paket_id') == $paket->id ? 'selected' : '' }}>
                                                {{ $paket->nama_paket }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('paket_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="foto" class="form-label fw-bold">Upload Foto Dekorasi <span class="text-danger">*</span></label>
                                    <input type="file" id="foto" name="foto"
                                        class="form-control shadow-sm @error('foto') is-invalid @enderror" 
                                        accept="image/*" required />
                                    @error('foto')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-4">
                                    <label for="deskripsi" class="form-label fw-bold">Deskripsi / Detail Dekorasi <span class="text-danger">*</span></label>
                                    <textarea id="deskripsi" name="deskripsi" rows="4"
                                        class="form-control shadow-sm @error('deskripsi') is-invalid @enderror"
                                        placeholder="Jelaskan detail dekorasi (contoh: Ukuran pelaminan, jenis bunga, dll)"
                                        required>{{ old('deskripsi') }}</textarea>
                                    @error('deskripsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- POSISI 2: Footer Form (Kembali di kiri, Reset & Simpan di kanan) --}}
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                {{-- Tombol Kembali Fisik Sejajar di Kiri --}}
                                <a href="{{ route('admin.dekorasi.index') }}" class="btn btn-secondary shadow-sm px-4 fw-bold">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali
                                </a>
                                
                                <div class="d-flex gap-2">
                                    <button type="reset" class="btn btn-light px-4 fw-bold border">Reset</button>
                                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow">
                                        <i class="bi bi-save me-1"></i> Simpan Dekorasi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('sweetalert::alert')
@endsection