@extends('layouts.master')
@section('title', 'Edit Data User')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-md-6">
                    <nav aria-label="breadcrumb" class="mb-1">
                        <ol class="breadcrumb" style="font-size: 0.85rem;">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-muted">Kelola User</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Edit Pengguna</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Edit Pengguna</h3>
                    <p class="text-muted mb-0 small">Perbarui data profil atau hak akses user di sistem.</p>
                </div>
            </div>
        </div>
        <hr>
    </div>

    {{-- TAMBAHKAN INI: Menampilkan error jika simpan gagal --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible show fade shadow-sm border-0 mb-4">
            <h6 class="fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Gagal Menyimpan:</h6>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <section class="section">
        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-primary">Form Perubahan Data</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0"><i class="bi bi-person"></i></span>
                                        <input type="text" name="name" class="form-control border-start-0" value="{{ old('name', $user->name) }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Alamat Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" class="form-control border-start-0" value="{{ old('email', $user->email) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 mb-2 pt-3 border-top">
                                <h6 class="text-section-header mb-1 small">Ganti Password (Opsional)</h6>
                                <p class="text-muted mb-3 small italic">Kosongkan jika tidak ingin mengubah password.</p>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password Baru</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0"><i class="bi bi-lock"></i></span>
                                        <input type="password" name="password" class="form-control border-start-0" placeholder="Minimal 8 karakter">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ulangi Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0"><i class="bi bi-shield-lock"></i></span>
                                        <input type="password" name="password_confirmation" class="form-control border-start-0" placeholder="Ulangi password baru">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Role Akses Sistem</label>
                                <div class="input-group">
                                    <span class="input-group-text border-end-0"><i class="bi bi-shield-check"></i></span>
                                    <select name="role" class="form-select border-start-0" required>
                                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User (Pelanggan / Klien)</option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (Pengelola Dashboard)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end align-items-center gap-2 border-top pt-3">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-light px-4 border">Batal</a>
                                <button type="submit" class="btn btn-primary px-4 shadow-sm fw-bold">
                                    <i class="bi bi-arrow-repeat me-1"></i> Perbarui Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4 mt-3 mt-lg-0">
                <div class="alert alert-light-primary border-0 shadow-sm p-4">
                    <h5 class="fw-bold small mb-3"><i class="bi bi-shield-lock-fill me-2"></i> Keamanan Akun</h5>
                    <p class="small mb-0">
                        Admin memiliki akses penuh terhadap data sensitif. Pastikan perubahan dilakukan sesuai dengan otoritas yang berlaku.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection