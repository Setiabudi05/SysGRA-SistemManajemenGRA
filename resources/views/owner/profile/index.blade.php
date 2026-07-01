@extends('layouts.master')
@section('title', 'Profil Saya - SysGRA')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-4">
            <h3 class="fw-bold text-primary"><i class="bi bi-person-gear me-2"></i>Pengaturan Profil</h3>
            <p class="text-muted">Perbarui data diri dan tingkatkan keamanan akun SysGRA Anda.</p>
        </div>

        <div class="page-content">
            <div class="row">
                {{-- Sisi Kiri: Visual Profile --}}
                {{-- Sisi Kiri: Visual Card --}}
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                        <div class="card-body text-center py-5">
                            {{-- Avatar Wrapper --}}
                            <div class="position-relative d-inline-block mb-3">
                                <div class="avatar avatar-xl shadow-sm border p-1 rounded-circle"
                                    style="width: 120px; height: 120px; margin: 0 auto;">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=435ebe&color=fff&size=200"
                                        alt="Avatar" class="rounded-circle w-100 h-100">
                                </div>
                            </div>

                            <h5 class="fw-bold mb-1 mt-2">{{ $user->name }}</h5>
                            <span class="badge bg-light-primary text-primary px-3 rounded-pill mb-3">
                                Owner SysGRA
                            </span>

                            <div class="mt-4 text-start p-3 rounded"
                                style="background-color: #f8f9fa; border-left: 4px solid #435ebe;">
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-envelope-fill me-1"></i> Email Terdaftar:
                                </p>
                                <p class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">
                                    {{ $user->email }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sisi Kanan: Form --}}
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <form action="{{ route('owner.profile.update') }}" method="POST">
                                @csrf @method('PUT')

                                <h5 class="fw-bold mb-4 border-bottom pb-2">Informasi Dasar</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold">Nama Lengkap</label>
                                        <input type="text" name="name"
                                            class="form-control form-control-lg @error('name') is-invalid @enderror"
                                            value="{{ old('name', $user->name) }}" required>
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold">Alamat Email</label>
                                        <input type="email" name="email"
                                            class="form-control form-control-lg @error('email') is-invalid @enderror"
                                            value="{{ old('email', $user->email) }}" required>
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <h5 class="fw-bold mt-4 mb-3 border-bottom pb-2">Keamanan Akun</h5>
                                <div class="alert alert-light-warning text-warning border-0 d-flex align-items-center mb-4"
                                    role="alert">
                                    <i class="bi bi-shield-lock me-2"></i>
                                    <span class="small">Kosongkan kolom password jika tidak ingin mengubahnya.</span>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold">Password Baru</label>
                                        <div class="input-group">
                                            <input type="password" name="password" id="password"
                                                class="form-control form-control-lg" placeholder="••••••••">
                                            <span class="input-group-text toggle-password" data-target="#password"><i
                                                    class="bi bi-eye"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold">Konfirmasi Password</label>
                                        <div class="input-group">
                                            <input type="password" name="password_confirmation" id="password_confirmation"
                                                class="form-control form-control-lg" placeholder="••••••••">
                                            <span class="input-group-text toggle-password"
                                                data-target="#password_confirmation"><i class="bi bi-eye"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                                        <i class="bi bi-save2 me-1"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
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
        $(document).ready(function () {
            $('.toggle-password').css('cursor', 'pointer').on('click', function () {
                const input = $($(this).data('target'));
                const icon = $(this).find('i');
                input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
                icon.toggleClass('bi-eye bi-eye-slash');
            });
        });
    </script>
    @if(session('swal_success'))
        <script>
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('swal_success') }}", timer: 2000, showConfirmButton: false });
        </script>
    @endif
@endpush