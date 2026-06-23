@extends('layouts.master')
@section('title', 'Edit Item Tambahan')

@section('content')
<div class="page-heading">
    <div class="row align-items-center">
        <div class="col-12 col-md-6">
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.addons.index') }}" class="text-muted">Add-ons</a></li>
                    <li class="breadcrumb-item active text-primary">Edit Add-on</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Add-on</h3>
            <p class="text-muted mb-0 small">Perbarui data item tambahan yang sudah ada.</p>
        </div>
        <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.addons.index') }}" class="text-muted small fw-bold text-decoration-none">
                <i class="bi bi-chevron-left"></i> Kembali ke daftar add-on
            </a>
        </div>
    </div>
    <hr class="mb-4">
</div>

<section class="section">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.addons.update', $addOn->id) }}" method="POST">
                @csrf 
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nama Item <span class="text-danger">*</span></label>
                        <input type="text" name="nama_item" class="form-control shadow-sm" value="{{ $addOn->nama_item }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Harga (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light fw-bold">Rp</span>
                            <input type="number" name="harga" class="form-control" value="{{ $addOn->harga }}" required>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="deskripsi" rows="4" class="form-control shadow-sm">{{ $addOn->deskripsi }}</textarea>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                    <a href="{{ route('admin.addons.index') }}" class="btn btn-secondary shadow-sm px-4 fw-bold">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow">
                            <i class="bi bi-save me-1"></i> Update Add-on
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection