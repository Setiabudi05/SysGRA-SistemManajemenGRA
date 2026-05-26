@extends('layouts.master')
@section('title', 'Tambah User')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
    <style>
        .form-label { font-size: 0.85rem; font-weight: bold; color: #6c757d; text-transform: uppercase; }
        .password-toggle { cursor: pointer; background-color: #fff !important; }
        .input-group-text { background-color: #f8f9fa; }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-md-6">
                    <nav aria-label="breadcrumb" class="mb-1">
                        <ol class="breadcrumb" style="font-size: 0.85rem;">
                            <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}" class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('owner.users.index') }}" class="text-muted">Kelola User</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Tambah Baru</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Manajemen User</h3>
                    <p class="text-muted mb-0 small">Daftarkan pengguna baru ke dalam sistem.</p>
                </div>
            </div>
        </div>
        <hr class="mb-4">
    </div>

    <section class="section">
        <form action="{{ route('owner.users.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 pb-0 pt-4">
                            <h5 class="fw-bold text-primary mb-0">Informasi Akun</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                {{-- Nama Lengkap --}}
                                <div class="col-12 mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
                                    </div>
                                    @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                {{-- Email & WhatsApp --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Alamat Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="contoh@gmail.com" value="{{ old('email') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor WhatsApp</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                                        <input type="number" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="08xxxxxxxxxx" value="{{ old('phone') }}" required>
                                    </div>
                                </div>

                                {{-- Password & Konfirmasi --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" name="password" id="password" class="form-control border-end-0 @error('password') is-invalid @enderror" placeholder="••••••••" required>
                                        <span class="input-group-text password-toggle border-start-0" onclick="togglePassword('password')">
                                            <i class="bi bi-eye" id="password-icon"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-end-0" placeholder="Ulangi password" required>
                                        <span class="input-group-text password-toggle border-start-0" onclick="togglePassword('password_confirmation')">
                                            <i class="bi bi-eye" id="password_confirmation-icon"></i>
                                        </span>
                                    </div>
                                </div>

                                {{-- Role & Jabatan --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Role Akses Sistem</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                                        <select name="role" id="role-select" class="form-select" required onchange="handleRoleChange()">
                                            <option value="" disabled selected>-- Pilih Role --</option>
                                            <option value="owner">Owner</option>
                                            <option value="admin">Admin</option>
                                            <option value="kru">Kru (Vendor)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3" id="jabatan-row" style="display: none;">
                                    <label class="form-label text-primary">Jabatan Spesifik Kru</label>
                                    <select name="jabatan" class="form-select border-primary shadow-sm">
                                        <option value="">-- Pilih Jabatan --</option>
                                        <option value="asisten">Asisten MUA</option>
                                        <option value="fg">Fotografer (FG)</option>
                                        <option value="layos">Layos / Dekorasi</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                <a href="{{ route('owner.users.index') }}" class="btn btn-secondary px-4 fw-bold">Kembali</a>
                                <div class="d-flex gap-2">
                                    <button type="reset" class="btn btn-light border px-4 fw-bold">Reset</button>
                                    <button type="submit" class="btn btn-primary px-4 shadow-sm fw-bold">Simpan User</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="alert alert-light-primary color-primary shadow-sm border-0 p-4">
                        <h5 class="fw-bold"><i class="bi bi-info-circle me-2"></i> Ketentuan Akun</h5>
                        <ul class="mb-0 small mt-2 list-unstyled">
                            <li class="mb-2"><i class="bi bi-dot text-primary"></i> Password minimal 8 karakter.</li>
                            <li class="mb-2"><i class="bi bi-dot text-primary"></i> WhatsApp wajib aktif untuk notifikasi.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection

@push('js')
<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(inputId + '-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }

    function handleRoleChange() {
        const role = document.getElementById('role-select').value;
        const jabatanRow = document.getElementById('jabatan-row');
        jabatanRow.style.display = (role === 'kru') ? 'block' : 'none';
    }
</script>
@endpush