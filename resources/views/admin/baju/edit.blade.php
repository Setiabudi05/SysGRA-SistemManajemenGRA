@extends('layouts.master')

@section('title', 'Edit Koleksi Baju')

@push('css')
    {{-- Memastikan gaya visual sinkron dengan modul lainnya --}}
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
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
                        <li class="breadcrumb-item"><a href="{{ route('admin.baju.index') }}" class="text-muted">Baju Pengantin</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Edit Baju Pengantin</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Koleksi Baju</h3>
                <p class="text-muted mb-0 small">Perbarui data atau kategori nama paket untuk koleksi gaun ini secara manual.</p>
            </div>

            {{-- Sisi Kanan --}}
            <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('admin.baju.index') }}" class="text-muted small fw-bold text-decoration-none">
                    <i class="bi bi-chevron-left"></i> Kembali ke daftar koleksi
                </a>
            </div>
        </div>
    </div>
    <hr class="mb-4">
</div>

<section class="section">
    {{-- PERBAIKAN UTAMA: Menampilkan pesan error jika validasi di controller menolak input data --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible show fade shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <h6 class="alert-heading mb-0 fw-bold">Gagal Memperbarui Data!</h6>
            </div>
            <ul class="mb-0 mt-2 small ps-3 text-start">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0 pt-4">
                    <h5 class="fw-bold text-warning mb-0">Formulir Perbarui Perubahan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.baju.update', $baju->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row mt-3">
                            
                            {{-- SINKRONISASI MANUAL: Input Nama Paket Teks Bebas dengan Nilai Default Data DB --}}
                            <div class="col-md-6 mb-3 text-start">
                                <label for="paket" class="form-label fw-bold">Nama Paket Pernikahan <span class="text-danger">*</span></label>
                                <input type="text" name="paket" id="paket" class="form-control shadow-sm @error('paket') is-invalid @enderror" 
                                    placeholder="Misal: Hemat, Standar, Silver, Gold, dll" value="{{ old('paket', $baju->paket) }}" required>
                                @error('paket') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Baris 1 Kolom 2: Nama Gaun --}}
                            <div class="col-md-6 mb-3 text-start">
                                <label for="nama_gown" class="form-label fw-bold">Nama Gaun / Baju <span class="text-danger">*</span></label>
                                <input type="text" name="nama_gown" id="nama_gown" class="form-control shadow-sm @error('nama_gown') is-invalid @enderror" 
                                    placeholder="Misal: Slim Gown Navy, Kebaya Satin Gold" value="{{ old('nama_gown', $baju->nama_gown) }}" required>
                                @error('nama_gown') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Baris 2: Stok & Foto Lama/Baru --}}
                            <div class="col-md-4 mb-3 text-start">
                                <label for="stok" class="form-label fw-bold">Stok Inventory</label>
                                <input type="number" name="stok" id="stok" class="form-control shadow-sm @error('stok') is-invalid @enderror" value="{{ old('stok', $baju->stok) }}" min="0" required>
                                @error('stok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-8 mb-3 text-start">
                                <label for="foto_gown" class="form-label fw-bold">Ganti Foto Koleksi</label>
                                <input type="file" name="foto_gown" id="foto_gown" class="form-control shadow-sm @error('foto_gown') is-invalid @enderror" accept="image/*">
                                <small class="text-muted d-block">Biarkan kosong jika tidak ingin mengganti foto yang sekarang.</small>
                                @error('foto_gown') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            {{-- Preview Foto Lama --}}
                            @if($baju->foto_gown)
                                <div class="col-12 mb-3 text-start">
                                    <label class="form-label d-block fw-bold text-muted">Foto Saat Ini:</label>
                                    <div class="img-thumbnail d-inline-block shadow-sm p-2" style="max-width: 150px; background-color: #f8f9fa;">
                                        <img src="{{ asset('storage/' . $baju->foto_gown) }}" alt="Foto Gaun Sekarang" class="img-fluid rounded">
                                    </div>
                                </div>
                            @endif

                            {{-- Baris 3: Deskripsi Gaun --}}
                            <div class="col-12 mb-3 text-start">
                                <label for="deskripsi_gown" class="form-label fw-bold">Deskripsi / Detail Gaun</label>
                                <textarea name="deskripsi_gown" id="deskripsi_gown" class="form-control shadow-sm @error('deskripsi_gown') is-invalid @enderror" 
                                    rows="3" placeholder="Tulis rincian ukuran baju atau kelengkapan aksesoris di sini...">{{ old('deskripsi_gown', $baju->deskripsi_gown) }}</textarea>
                                @error('deskripsi_gown') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Footer Action Buttons --}}
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('admin.baju.index') }}" class="btn btn-secondary shadow-sm px-4 fw-bold">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning text-dark px-4 fw-bold shadow">
                                    <i class="bi bi-check-circle-fill me-1"></i> Perbarui Data Baju
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection