@extends('layouts.master')

@section('title', 'Edit ' . ucfirst($pembukuan->tipe))

@push('css')
    {{-- Memastikan gaya visual sinkron dengan modul lainnya --}}
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
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                                    class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.pembukuan.index') }}"
                                    class="text-muted">Pembukuan</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Edit
                                {{ ucfirst($pembukuan->tipe) }}</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i>Edit {{ ucfirst($pembukuan->tipe) }}
                    </h3>
                    <p class="text-muted mb-0 small">Perbarui informasi data {{ $pembukuan->tipe }} yang telah dipilih.</p>
                </div>

                {{-- Sisi Kanan: Navigasi Teks Halus (Lurus dengan ujung form) --}}
                <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('admin.pembukuan.index') }}" class="text-muted small fw-bold text-decoration-none">
                        <i class="bi bi-chevron-left"></i> Kembali ke daftar pembukuan
                    </a>
                </div>
            </div>
        </div>
        <hr class="mb-4">
    </div>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-12"> {{-- Lebar penuh agar lurus dengan navigasi di atas --}}
                <div class="card border-0 shadow-sm">
                    {{-- Header disamakan dengan form tambah (Warna Biru Primary) --}}
                    <div class="card-header bg-transparent border-0 pb-0 pt-4">
                        <h5 class="fw-bold text-primary mb-0">Formulir Pembaruan Data</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.pembukuan.update', $pembukuan->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Hidden tipe tetap dijaga --}}
                            <input type="hidden" name="tipe" value="{{ $pembukuan->tipe }}">

                            <div class="row mt-3">
                                {{-- Input Tanggal --}}
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal" class="form-label fw-bold">TANGGAL <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="tanggal" id="tanggal"
                                        class="form-control shadow-sm @error('tanggal') is-invalid @enderror"
                                        value="{{ old('tanggal', $pembukuan->tanggal ? \Carbon\Carbon::parse($pembukuan->tanggal)->format('Y-m-d') : now()->format('Y-m-d')) }}"
                                        required>
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Input Customer --}}
                                <div class="col-md-6 mb-3">
                                    <label for="customer" class="form-label fw-bold">CUSTOMER / NAMA PIHAK</label>
                                    <input type="text" name="customer" id="customer"
                                        class="form-control shadow-sm @error('customer') is-invalid @enderror"
                                        value="{{ old('customer', $pembukuan->customer) }}" placeholder="Contoh: Bpk. Ali">
                                    @error('customer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Input Nominal --}}
                                <div class="col-md-12 mb-3">
                                    <label for="nominal" class="form-label fw-bold">NOMINAL (RP) <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-light fw-bold">Rp</span>
                                        <input type="number" name="nominal" id="nominal"
                                            class="form-control @error('nominal') is-invalid @enderror"
                                            value="{{ old('nominal', $pembukuan->nominal) }}" placeholder="0" required>
                                    </div>
                                    @error('nominal')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Input Keterangan --}}
                                <div class="col-md-12 mb-4">
                                    <label for="keterangan" class="form-label fw-bold">KETERANGAN / DESKRIPSI</label>
                                    <textarea name="keterangan" id="keterangan" rows="3"
                                        class="form-control shadow-sm @error('keterangan') is-invalid @enderror"
                                        placeholder="Tambahkan catatan tambahan jika perlu...">{{ old('keterangan', $pembukuan->keterangan) }}</textarea>
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Footer Action Buttons --}}
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                {{-- Tombol Kembali Fisik di Kiri --}}
                                <a href="{{ route('admin.pembukuan.index') }}"
                                    class="btn btn-secondary shadow-sm px-4 fw-bold">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali
                                </a>

                                <div class="d-flex gap-2">
                                    <button type="reset" class="btn btn-light px-4 fw-bold border">Reset</button>
                                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow">
                                        <i class="bi bi-arrow-repeat me-1"></i> Perbarui Data
                                    </button>
                                </div>
                            </div>

                            {{-- Hidden input untuk menjaga jejak filter tanggal --}}
                            <input type="hidden" name="last_tanggal" value="{{ request('f_tanggal') }}">

                            {{-- Hidden tipe tetap dijaga --}}
                            <input type="hidden" name="tipe" value="{{ $pembukuan->tipe }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </div>
@endsection