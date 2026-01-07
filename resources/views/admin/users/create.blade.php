@extends('layouts.master')
@section('title', 'Tambah User')

@push('css')
    {{-- Memastikan jarak rapat ke atas konsisten dengan halaman index --}}
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
    <style>
        .form-label { font-size: 0.9rem; }
    </style>
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            {{-- Sisi Kiri: Judul dan Breadcrumb --}}
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-muted">Kelola User</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Tambah Baru</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">Tambah User Baru</h3>
                <p class="text-muted mb-0 small">Daftarkan akun administrator atau pelanggan baru ke sistem.</p>
            </div>
        </div>
    </div>
    <hr class="mb-4">
</div>

<section class="section">
    <div class="row">
        {{-- Menggunakan col-lg-8 agar seimbang dengan kolom info di samping --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0 pt-4">
                    <h5 class="card-title mb-0 fw-bold text-primary">Informasi Akun</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            {{-- Nama Lengkap --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                                    <input type="text" name="name" class="form-control border-start-0 @error('name') is-invalid @enderror" 
                                           placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
                                </div>
                                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Alamat Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                           placeholder="contoh@gmail.com" value="{{ old('email') }}" required>
                                </div>
                                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            {{-- Password --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                           placeholder="Minimal 8 karakter" required>
                                </div>
                                @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- Konfirmasi Password --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">Konfirmasi Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-lock"></i></span>
                                    <input type="password" name="password_confirmation" class="form-control border-start-0" 
                                           placeholder="Ulangi password" required>
                                </div>
                            </div>
                        </div>

                        {{-- Role Akses --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">Role Akses Sistem</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-check"></i></span>
                                <select name="role" class="form-select border-start-0 shadow-none" required>
                                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User (Pelanggan / Klien)</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (Pengelola Dashboard)</option>
                                </select>
                            </div>
                        </div>

                        {{-- POSISI 2: Footer Form (Kembali di kiri, Reset & Simpan di kanan) --}}
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            {{-- Tombol Kembali Fisik di Kiri --}}
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary shadow-sm px-4 fw-bold">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            
                            <div class="d-flex gap-2">
                                {{-- Tombol Reset --}}
                                <button type="reset" class="btn btn-light px-4 fw-bold border">Reset</button>
                                {{-- Tombol Simpan --}}
                                <button type="submit" class="btn btn-primary px-4 shadow-sm fw-bold">
                                    <i class="bi bi-save me-1"></i> Simpan User
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Info Card --}}
        <div class="col-12 col-lg-4 mt-4 mt-lg-0">
            <div class="alert alert-light-primary color-primary shadow-sm border-0 p-4">
                <h5 class="fw-bold"><i class="bi bi-info-circle me-2"></i> Ketentuan Akun</h5>
                <ul class="mb-0 small mt-2 list-unstyled">
                    <li class="mb-2"><i class="bi bi-dot"></i> Password minimal 8 karakter.</li>
                    <li class="mb-2"><i class="bi bi-dot"></i> Gunakan alamat email aktif yang belum terdaftar.</li>
                    <li><i class="bi bi-dot"></i> <b>Role Admin:</b> Akses penuh dashboard.<br><b>Role User:</b> Akses terbatas pelanggan.</li>
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection