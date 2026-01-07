@extends('layouts.master')

@section('title', 'Edit Paket: ' . $paket->nama_paket)

@push('css')
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
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.paket.index') }}" class="text-muted">Paket</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Edit Paket</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Paket</h3>
                <p class="text-muted mb-0 small">Perbarui rincian layanan paket pernikahan: <strong>{{ $paket->nama_paket }}</strong></p>
            </div>

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
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0 pt-4">
                    <h5 class="fw-bold text-primary mb-0">Formulir Pembaruan Paket</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.paket.update', $paket->id) }}" method="POST" data-parsley-validate>
                        @csrf
                        @method('PUT')
                        
                        <div class="row mt-3">
                            {{-- Nama Paket --}}
                            <div class="col-md-4 mb-3">
                                <label for="nama_paket" class="form-label fw-bold">Nama Paket <span class="text-danger">*</span></label>
                                <input type="text" id="nama_paket" name="nama_paket" 
                                    class="form-control shadow-sm @error('nama_paket') is-invalid @enderror" 
                                    value="{{ old('nama_paket', $paket->nama_paket) }}" required>
                                @error('nama_paket')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tahun Pricelist (TAMBAHAN BARU) --}}
                            <div class="col-md-4 mb-3">
                                <label for="tahun" class="form-label fw-bold">Tahun Pricelist <span class="text-danger">*</span></label>
                                <select name="tahun" id="tahun" class="form-select shadow-sm @error('tahun') is-invalid @enderror" required>
                                    @php 
                                        $currentYear = date('Y'); 
                                        $selectedYear = old('tahun', $paket->tahun);
                                    @endphp
                                    @for ($i = 2028; $i >= ($currentYear - 3); $i--)
                                        <option value="{{ $i }}" {{ $i == $selectedYear ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                                @error('tahun')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Harga Paket --}}
                            <div class="col-md-4 mb-3">
                                <label for="harga" class="form-label fw-bold">Harga Paket (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-light fw-bold">Rp</span>
                                    <input type="number" id="harga" name="harga" 
                                        class="form-control @error('harga') is-invalid @enderror" 
                                        value="{{ old('harga', $paket->harga) }}" required>
                                </div>
                                @error('harga')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12"><hr class="my-3"></div>

                            {{-- Detail Layanan --}}
                            <div class="col-md-4 mb-3">
                                <label for="makeup" class="form-label fw-bold">Detail Makeup</label>
                                <textarea id="makeup" name="makeup" rows="4"
                                    class="form-control shadow-sm">{{ old('makeup', $paket->makeup) }}</textarea>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="dekorasi" class="form-label fw-bold">Detail Dekorasi</label>
                                <textarea id="dekorasi" name="dekorasi" rows="4"
                                    class="form-control shadow-sm">{{ old('dekorasi', $paket->dekorasi) }}</textarea>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="dokumentasi" class="form-label fw-bold">Detail Dokumentasi</label>
                                <textarea id="dokumentasi" name="dokumentasi" rows="4"
                                    class="form-control shadow-sm">{{ old('dokumentasi', $paket->dokumentasi) }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('admin.paket.index') }}" class="btn btn-secondary shadow-sm px-4 fw-bold">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-light px-4 fw-bold border">
                                    Reset
                                </button>
                                <button type="submit" class="btn btn-primary px-4 fw-bold shadow">
                                    <i class="bi bi-arrow-repeat me-1"></i> Perbarui Paket
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