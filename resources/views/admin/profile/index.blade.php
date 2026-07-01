@extends('layouts.master')
@section('title', 'Profil Saya - SysGRA')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item active text-primary">Profil Saya</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0"><i class="bi bi-person-badge-fill text-primary me-2"></i>Pengaturan Profil</h3>
                <p class="text-muted small">Kelola informasi akun dan keamanan kata sandi Anda.</p>
            </div>
        </div>
    </div>
    <hr>
</div>

<div class="page-content">
    <div class="row">
        {{-- Sisi Kiri: Visual Card --}}
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-90">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <div class="position-relative d-inline-block mx-auto mb-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=435ebe&color=fff&size=150" 
                             class="rounded-circle shadow-sm border border-3 border-white" alt="Avatar">
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <span class="badge bg-light-primary text-primary px-3 rounded-pill">Administrator SysGRA</span>
                    <hr class="w-75 mx-auto opacity-25">
                    <p class="text-muted small mb-0"><i class="bi bi-envelope me-1"></i>{{ $user->email }}</p>
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: Form --}}
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <h6 class="fw-bold mb-3 text-primary">Informasi Dasar</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Alamat Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-4 opacity-25">
                        
                        <h6 class="fw-bold mb-2 text-primary">Keamanan Akun</h6>
                        <div class="alert alert-light-info border-0 py-2 small mb-3">
                            <i class="bi bi-info-circle me-2"></i>Kosongkan jika tidak ingin mengubah password.
                        </div>
                        
                        <div class="row">
                            {{-- Password Baru --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Password Baru</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
                                    <span class="input-group-text toggle-password" data-target="#password" style="cursor: pointer;">
                                        <i class="bi bi-eye"></i>
                                    </span>
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            {{-- Konfirmasi Password --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Konfirmasi Password</label>
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="••••••••">
                                    <span class="input-group-text toggle-password" data-target="#password_confirmation" style="cursor: pointer;">
                                        <i class="bi bi-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Fungsi Toggle Password
        $(document).on('click', '.toggle-password', function() {
            const target = $(this).data('target');
            const input = $(target);
            const icon = $(this).find('i');

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('bi-eye').addClass('bi-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('bi-eye-slash').addClass('bi-eye');
            }
        });
    });
</script>

@if(session('swal_success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('swal_success') }}",
        timer: 2000,
        showConfirmButton: false
    });
</script>
@endif
@endpush