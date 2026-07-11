@extends('layouts.master')

@section('title', 'Edit Dekorasi')

@push('css')
    {{-- Memastikan gaya visual sinkron dengan modul lainnya --}}
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
                            <li class="breadcrumb-item active text-primary" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Dekorasi</h3>
                    <p class="text-muted mb-0 small">Ubah rincian atau ganti foto dekorasi yang sudah ada.</p>
                </div>

                {{-- Sisi Kanan: Navigasi Teks Halus (Lurus dengan ujung form) --}}
                <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('admin.dekorasi.index') }}" class="text-muted small fw-bold text-decoration-none">
                        <i class="bi bi-chevron-left"></i> Kembali ke daftar dekorasi
                    </a>
                </div>
            </div>
        </div>
        <hr class="mb-4">

        <section id="form-edit-dekorasi">
            <div class="row justify-content-center">
                <div class="col-lg-12"> {{-- Lebar penuh agar sejajar dengan navigasi di atas --}}
                    <div class="card border-0 shadow-sm">
                        {{-- Header disamakan dengan form tambah (Warna Biru Primary) --}}
                        <div class="card-header bg-transparent border-0 pb-0 pt-4">
                            <h5 class="fw-bold text-primary mb-0">Formulir Edit Dekorasi</h5>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <form class="form" action="{{ route('admin.dekorasi.update', $dekorasi->id) }}"
                                    method="POST" enctype="multipart/form-data" data-parsley-validate>
                                    @csrf
                                    @method('PUT')

                                    <div class="row mt-3">
                                        {{-- Preview Foto Saat Ini --}}
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-bold d-block text-muted">Foto Saat Ini</label>
                                            @if ($dekorasi->foto)
                                                <img src="{{ asset('storage/' . $dekorasi->foto) }}" 
                                                     class="img-thumbnail shadow-sm mb-2" 
                                                     style="width: 100%; max-height: 220px; object-fit: cover; border-radius: 8px;" 
                                                     alt="Foto Dekorasi">
                                            @else
                                                <div class="border rounded p-5 bg-light text-muted text-center">
                                                    <i class="bi bi-image shadow-sm d-block mb-2" style="font-size: 2rem;"></i>
                                                    Tidak ada foto
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Input Paket & Ganti Foto --}}
                                        <div class="col-md-8 mb-3">
                                            <div class="mb-4">
                                                <label for="paket_id" class="form-label fw-bold">Pilih Paket <span class="text-danger">*</span></label>
                                                <select id="paket_id" name="paket_id" class="form-select shadow-sm @error('paket_id') is-invalid @enderror" required>
                                                    <option value="">-- Pilih Paket --</option>
                                                    @foreach($pakets as $paket)
                                                        <option value="{{ $paket->id }}" 
                                                            {{ old('paket_id', $dekorasi->paket_id) == $paket->id ? 'selected' : '' }}>
                                                            {{ $paket->nama_paket }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('paket_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="foto" class="form-label fw-bold">Ganti Foto (Opsional)</label>
                                                <input type="file" id="foto" class="form-control shadow-sm @error('foto') is-invalid @enderror" 
                                                    name="foto" accept="image/*" />
                                                <p class="text-muted x-small mt-2 mb-0">
                                                    <i class="bi bi-info-circle me-1"></i> Biarkan kosong jika tidak ingin mengganti foto.
                                                </p>
                                                @error('foto')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Deskripsi --}}
                                        <div class="col-12 mb-4">
                                            <label for="deskripsi" class="form-label fw-bold">Deskripsi / Detail Dekorasi <span class="text-danger">*</span></label>
                                            <textarea id="deskripsi" rows="4"
                                                class="form-control shadow-sm @error('deskripsi') is-invalid @enderror" name="deskripsi"
                                                placeholder="Deskripsi Dekorasi" required>{{ old('deskripsi', $dekorasi->deskripsi) }}</textarea>
                                            @error('deskripsi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Footer Action Buttons --}}
                                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                        {{-- Tombol Kembali Fisik di Kiri --}}
                                        <a href="{{ route('admin.dekorasi.index') }}" class="btn btn-secondary shadow-sm px-4 fw-bold">
                                            <i class="bi bi-arrow-left me-1"></i> Kembali
                                        </a>
                                        
                                        <div class="d-flex gap-2">
                                            <button type="reset" class="btn btn-light px-4 fw-bold border">Reset</button>
                                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow">
                                                <i class="bi bi-arrow-repeat me-1"></i> Perbarui Dekorasi
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
    </div>

    @include('sweetalert::alert')
@endsection