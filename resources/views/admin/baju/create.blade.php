@extends('layouts.master')

@section('title', 'Tambah Koleksi Baju')

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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Tambah Baju Pengantin</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0"><i class="bi bi-person-badge-fill me-2"></i>Tambah Koleksi Baju</h3>
                <p class="text-muted mb-0 small">Isi formulir di bawah ini untuk menambah stok inventory baru.</p>
            </div>

            {{-- Sisi Kanan: Navigasi Teks Halus (Lurus dengan ujung form) --}}
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
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0 pt-4">
                    <h5 class="fw-bold text-primary mb-0">Formulir Stok Inventory</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.baju.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mt-3">
                            {{-- Baris 1: Kategori & Warna --}}
                            <div class="col-md-6 mb-3">
                                <label for="kategori" class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori" id="kategori" class="form-select shadow-sm @error('kategori') is-invalid @enderror" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="Kebaya" {{ old('kategori') == 'Kebaya' ? 'selected' : '' }}>Kebaya</option>
                                    <option value="Jas" {{ old('kategori') == 'Jas' ? 'selected' : '' }}>Jas / Beskap</option>
                                    <option value="Gaun" {{ old('kategori') == 'Gaun' ? 'selected' : '' }}>Gaun</option>
                                </select>
                                @error('kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="warna" class="form-label fw-bold">Warna <span class="text-danger">*</span></label>
                                <input type="text" name="warna" id="warna" class="form-control shadow-sm @error('warna') is-invalid @enderror" 
                                    placeholder="Misal: Hijau Sage, Putih Tulang" value="{{ old('warna') }}" required>
                                @error('warna') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Baris 2: Ukuran, Stok, & Foto --}}
                            <div class="col-md-3 mb-3">
                                <label for="ukuran" class="form-label fw-bold">Ukuran (Size)</label>
                                <select name="ukuran" id="ukuran" class="form-select shadow-sm">
                                    <option value="S">S</option>
                                    <option value="M">M</option>
                                    <option value="L">L</option>
                                    <option value="XL">XL</option>
                                    <option value="XXL">XXL</option>
                                    <option value="All Size">All Size</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="stok" class="form-label fw-bold">Stok Inventory</label>
                                <input type="number" name="stok" id="stok" class="form-control shadow-sm" value="1" min="1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="foto" class="form-label fw-bold">Foto Koleksi</label>
                                <input type="file" name="foto" id="foto" class="form-control shadow-sm @error('foto') is-invalid @enderror" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG (Max: 2MB)</small>
                                @error('foto') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Footer Action Buttons --}}
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            {{-- Tombol Kembali Fisik di Kiri --}}
                            <a href="{{ route('admin.baju.index') }}" class="btn btn-secondary shadow-sm px-4 fw-bold">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-light px-4 fw-bold border">Reset</button>
                                <button type="submit" class="btn btn-primary px-4 fw-bold shadow">
                                    <i class="bi bi-save me-1"></i> Simpan Koleksi
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
@endsection