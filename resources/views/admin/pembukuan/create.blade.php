@extends('layouts.master')

@section('title', 'Tambah ' . ucfirst($tipe))

@push('css')
    {{-- Memanggil style admin jika ada --}}
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            <div class="col-12 col-md-7">
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.pembukuan.index') }}" class="text-muted">Pembukuan</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Tambah {{ ucfirst($tipe) }}</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">
                    <i class="bi bi-{{ $tipe == 'pemasukan' ? 'plus-circle' : 'dash-circle' }}-fill me-2 text-{{ $tipe == 'pemasukan' ? 'primary' : 'danger' }}"></i>
                    Tambah {{ ucfirst($tipe) }}
                </h3>
                <p class="text-muted mb-0">Silahkan isi formulir di bawah ini untuk mencatat {{ $tipe }}.</p>
            </div>
        </div>
    </div>
    <hr>
</div>

<section class="section">
    <div class="row justify-content-center">
        <div class="col-lg-10"> {{-- Lebar diatur 10 agar pas di layar lebar --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom pb-2 pt-4">
                    <h5 class="fw-bold mb-0">Formulir Pencatatan</h5>
                </div>
                <div class="card-body mt-3">
                    <form action="{{ route('admin.pembukuan.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tipe" value="{{ $tipe }}">

                        <div class="row">
                            {{-- Input Tanggal --}}
                            <div class="col-md-6 mb-3">
                                <label for="tanggal" class="form-label fw-bold">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" id="tanggal"
                                    class="form-control shadow-sm @error('tanggal') is-invalid @enderror"
                                    value="{{ old('tanggal', now()->toDateString()) }}" required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Input Customer --}}
                            <div class="col-md-6 mb-3">
                                <label for="customer" class="form-label fw-bold">Customer / Nama Pihak</label>
                                <input type="text" name="customer" id="customer" 
                                    class="form-control shadow-sm @error('customer') is-invalid @enderror"
                                    value="{{ old('customer') }}" placeholder="Contoh: Bpk. Ali">
                                @error('customer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Input Nominal --}}
                        <div class="mb-3">
                            <label for="nominal" class="form-label fw-bold">Nominal (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="number" name="nominal" id="nominal"
                                    class="form-control @error('nominal') is-invalid @enderror" 
                                    value="{{ old('nominal') }}" 
                                    placeholder="0"
                                    required>
                            </div>
                            @error('nominal')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Input Keterangan --}}
                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-bold">Keterangan / Deskripsi</label>
                            <textarea name="keterangan" id="keterangan"
                                class="form-control shadow-sm @error('keterangan') is-invalid @enderror"
                                rows="3" placeholder="Tambahkan catatan tambahan jika perlu...">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('admin.pembukuan.index') }}" class="btn btn-light-secondary px-4">
                                <i class="bi bi-arrow-left me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm fw-bold">
                                <i class="bi bi-save me-1"></i> Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection