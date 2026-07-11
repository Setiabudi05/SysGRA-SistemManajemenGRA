@extends('layouts.master')
@section('title', 'Edit Data User')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets-admin/css/admin-styles.css') }}">
    <style>
        .form-label { font-size: 0.9rem; }
        .password-toggle { cursor: pointer; background: #f8f9fa !important; }
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
                            <li class="breadcrumb-item active text-primary" aria-current="page">Edit Pengguna</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0">Edit Pengguna</h3>
                </div>
            </div>
        </div>
        <hr class="mb-4">
    </div>

    <section class="section">
        <form action="{{ route('owner.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 pb-0 pt-4">
                            <h5 class="card-title mb-0 fw-bold text-primary">Informasi Akun</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold text-muted text-uppercase">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-muted text-uppercase">Alamat Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-muted text-uppercase">Nomor WhatsApp</label>
                                    <input type="number" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" required>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-muted text-uppercase text-primary">Role Akses</label>
                                    <select name="role" id="role-select-edit" class="form-select border-primary shadow-none" required onchange="handleRoleChangeEdit()">
                                        <option value="owner" {{ $user->role == 'owner' ? 'selected' : '' }}>Owner</option>
                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="kru" {{ $user->role == 'kru' ? 'selected' : '' }}>Kru (Vendor)</option>
                                        <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-4" id="jabatan-row-edit" style="display: {{ $user->role == 'kru' ? 'block' : 'none' }};">
                                    <label class="form-label fw-bold text-danger text-uppercase">Jabatan Spesifik Kru</label>
                                    <select name="jabatan" class="form-select border-danger shadow-none">
                                        <option value="">-- Pilih Jabatan --</option>
                                        <option value="asisten" {{ $user->jabatan == 'asisten' ? 'selected' : '' }}>Asisten MUA</option>
                                        <option value="fg" {{ $user->jabatan == 'fg' ? 'selected' : '' }}>Fotografer (FG)</option>
                                        <option value="layos" {{ $user->jabatan == 'layos' ? 'selected' : '' }}>Layos / Dekorasi</option>
                                    </select>
                                </div>

                                <div class="col-12"><hr></div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-muted text-uppercase">Password Baru (Opsional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                                        <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0" placeholder="Minimal 8 karakter">
                                        <span class="input-group-text password-toggle border-start-0" onclick="togglePassword('password')">
                                            <i class="bi bi-eye" id="password-icon"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                <a href="{{ route('owner.users.index') }}" class="btn btn-secondary px-4 fw-bold">Kembali</a>
                                <button type="submit" class="btn btn-primary px-4 shadow-sm fw-bold">Perbarui Data</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4 mt-4 mt-lg-0">
                    <div class="alert alert-light-primary color-primary shadow-sm border-0 p-4 text-center">
                        <i class="bi bi-shield-check fs-1 text-primary"></i>
                        <p class="mt-2 small mb-0">Pastikan data yang diinput sudah benar sesuai dengan tugas kru masing-masing.</p>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
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

    function handleRoleChangeEdit() {
        const role = document.getElementById('role-select-edit').value;
        const jabatanRow = document.getElementById('jabatan-row-edit');
        jabatanRow.style.display = (role === 'kru') ? 'block' : 'none';
    }
</script>
@endpush