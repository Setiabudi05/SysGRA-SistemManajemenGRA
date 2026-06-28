@extends('layouts.master')
@section('title', 'Tambah Paket Pernikahan')

@section('content')
    <div class="page-heading">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"
                                class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.paket.index') }}" class="text-muted">Paket</a>
                        </li>
                        <li class="breadcrumb-item active text-primary">Tambah Paket</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0"><i class="bi bi-box-seam-fill me-2"></i>Tambah Paket</h3>
                <p class="text-muted mb-0 small">Kelola rincian layanan paket pernikahan baru.</p>
            </div>
            <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('admin.paket.index') }}" class="text-muted small fw-bold text-decoration-none">
                    <i class="bi bi-chevron-left"></i> Kembali ke daftar paket
                </a>
            </div>
        </div>
        <hr class="mb-4">
    </div>

    <section class="section">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0 pt-4">
                <h5 class="fw-bold text-primary mb-0">Form Tambah Paket</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.paket.store') }}" method="POST" data-parsley-validate>
                    @csrf
                    {{-- Baris 1: Informasi Utama --}}
                    <div class="row mt-3">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Nama Paket <span class="text-danger">*</span></label>
                            <input type="text" name="nama_paket" class="form-control shadow-sm" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Tahun Pricelist <span class="text-danger">*</span></label>
                            <select name="tahun" class="form-select shadow-sm" required>
                                @for ($i = date('Y') + 2; $i >= (date('Y') - 1); $i--)
                                    <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Harga (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="number" name="harga" class="form-control" placeholder="0" required>
                            </div>
                        </div>
                    </div>

                    {{-- Baris 2: Detail Layanan Utama (3 Kolom) --}}
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Detail Makeup & Busana</label>
                            <textarea name="makeup" rows="4" class="form-control shadow-sm"></textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Detail Dekorasi</label>
                            <textarea name="dekorasi" rows="4" class="form-control shadow-sm"></textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Detail Layos/Tenda</label>
                            <textarea name="layos" rows="4" class="form-control shadow-sm"></textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Detail Dokumentasi</label>
                            <textarea name="dokumentasi" rows="4" class="form-control shadow-sm"></textarea>
                        </div>
                         <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-success"><i
                                    class="bi bi-check-circle me-1"></i>Include</label>
                            <textarea name="include" rows="4" class="form-control shadow-sm border-success"></textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-warning"><i class="bi bi-gift me-1"></i>Free
                                (Bonus)</label>
                            <textarea name="bonus" rows="4" class="form-control shadow-sm border-warning"></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <a href="{{ route('admin.paket.index') }}" class="btn btn-secondary shadow-sm px-4 fw-bold"><i
                                class="bi bi-arrow-left me-1"></i> Kembali</a>
                        <div class="d-flex gap-2">
                            <button type="reset" class="btn btn-light px-4 fw-bold border">Reset</button>
                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow"><i
                                    class="bi bi-save me-1"></i> Simpan Paket</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection