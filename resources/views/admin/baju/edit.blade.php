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
                        <li class="breadcrumb-item active text-primary" aria-current="page">Edit</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Koleksi Baju</h3>
                <p class="text-muted mb-0 small">Perbarui informasi stok atau foto koleksi baju.</p>
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
                {{-- Header disamakan dengan form lainnya (Warna Biru Primary) --}}
                <div class="card-header bg-transparent border-0 pb-0 pt-4">
                    <h5 class="fw-bold text-primary mb-0">Formulir Pembaruan Koleksi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.baju.update', $baju->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mt-3">
                            {{-- Preview Foto di Samping --}}
                            <div class="col-md-3 mb-3 text-center">
                                <label class="form-label fw-bold d-block text-muted">Foto Saat Ini</label>
                                @if($baju->foto)
                                    <img src="{{ asset('storage/'.$baju->foto) }}" 
                                         class="img-thumbnail shadow-sm mb-2" 
                                         style="width: 100%; max-height: 250px; object-fit: cover; border-radius: 8px;">
                                @else
                                    <div class="border rounded p-5 bg-light text-muted text-center">
                                        <i class="bi bi-image d-block mb-2" style="font-size: 2rem;"></i>
                                        Tidak Ada Foto
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-9">
                                <div class="row">
                                    {{-- Kategori & Warna --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                                        <select name="kategori" class="form-select shadow-sm" required>
                                            <option value="Kebaya" {{ $baju->kategori == 'Kebaya' ? 'selected' : '' }}>Kebaya</option>
                                            <option value="Jas" {{ $baju->kategori == 'Jas' ? 'selected' : '' }}>Jas / Beskap</option>
                                            <option value="Gaun" {{ $baju->kategori == 'Gaun' ? 'selected' : '' }}>Gaun</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Warna <span class="text-danger">*</span></label>
                                        <input type="text" name="warna" class="form-control shadow-sm" 
                                               value="{{ old('warna', $baju->warna) }}" placeholder="Misal: Hijau Sage" required>
                                    </div>

                                    {{-- Ukuran & Stok --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Ukuran (Size)</label>
                                        <select name="ukuran" class="form-select shadow-sm">
                                            @foreach(['S','M','L','XL','XXL','All Size'] as $sz)
                                                <option value="{{ $sz }}" {{ $baju->ukuran == $sz ? 'selected' : '' }}>{{ $sz }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Stok Inventory</label>
                                        <input type="number" name="stok" class="form-control shadow-sm" value="{{ $baju->stok }}" min="0">
                                    </div>

                                    {{-- Upload Foto Baru --}}
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">Ganti Foto (Opsional)</label>
                                        <input type="file" name="foto" class="form-control shadow-sm" accept="image/*">
                                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Biarkan kosong jika tidak ingin mengganti foto.</small>
                                    </div>
                                </div>
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
                                    <i class="bi bi-arrow-repeat me-1"></i> Perbarui Koleksi
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