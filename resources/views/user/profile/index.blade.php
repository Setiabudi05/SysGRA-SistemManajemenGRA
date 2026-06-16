@extends('layouts.user')

@section('title', 'Profil Saya')

@section('content')
<div class="page-content mt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">
            <div class="row align-items-stretch"> 
                {{-- SISI KIRI: IDENTITAS VISUAL --}}
                <div class="col-12 col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100 mb-0" style="border-radius: 20px; background: white;">
                        <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center text-center">
                            <div class="avatar-wrapper mb-3">
                                <div class="avatar-circle">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=435ee0&color=fff&size=128" alt="Face">
                                </div>
                            </div>
                            
                            <h5 class="fw-bold text-dark mb-1">{{ $user->name }}</h5>
                            <span class="badge bg-light-primary text-primary px-3 rounded-pill extra-small fw-bold mb-4">MEMBER AKTIF</span>
                            
                            <div class="w-100 pt-3 border-top text-start">
                                <div class="mb-3">
                                    <label class="info-label-sm text-muted mb-1 text-uppercase">Email</label>
                                    <p class="fw-bold text-dark mb-0 text-truncate">
                                        <i class="bi bi-envelope me-2 text-primary"></i>{{ $user->email }}
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="info-label-sm text-muted mb-1 text-uppercase">WhatsApp / No. HP</label>
                                    <p class="fw-bold text-dark mb-0">
                                        <i class="bi bi-whatsapp me-2 text-success"></i>{{ $user->phone ?? '-' }}
                                    </p>
                                </div>
                                <div class="mb-0">
                                    <label class="info-label-sm text-muted mb-1 text-uppercase">Alamat Rumah</label>
                                    <p class="fw-bold text-dark mb-0 small text-secondary">
                                        <i class="bi bi-geo-alt me-2 text-danger"></i>{{ $user->address ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SISI KANAN: FORM UPDATE --}}
                <div class="col-12 col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm h-100 mb-0 text-start" style="border-radius: 20px;">
                        <div class="card-body p-4">
                            @if(session('success'))
                                <div class="alert alert-light-success border-0 small py-2 mb-4 d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                                </div>
                            @endif

                            <form action="{{ route('user.profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-badge me-2"></i>Informasi Pribadi</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="info-label-sm">Nama Lengkap</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                                            <input type="text" name="name" class="form-control bg-light" value="{{ old('name', $user->name) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="info-label-sm">Email</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                                            <input type="email" name="email" class="form-control bg-light" value="{{ old('email', $user->email) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="info-label-sm">Nomor WhatsApp</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-whatsapp"></i></span>
                                            <input type="number" name="phone" class="form-control bg-light" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="info-label-sm">Alamat Rumah</label>
                                        <textarea name="address" class="form-control form-control-sm bg-light" rows="3" placeholder="Masukkan alamat lengkap">{{ old('address', $user->address) }}</textarea>
                                    </div>
                                </div>

                                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-shield-lock me-2"></i>Ubah Password</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="info-label-sm">Password Baru <span class="text-muted fw-normal">(Opsional)</span></label>
                                        <input type="password" name="password" class="form-control form-control-sm bg-light shadow-none" placeholder="••••••••">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="info-label-sm">Konfirmasi Password</label>
                                        <input type="password" name="password_confirmation" class="form-control form-control-sm bg-light shadow-none" placeholder="••••••••">
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 d-flex justify-content-between border-top">
                                    <a href="{{ url('user/dashboard') }}" class="btn btn-light-secondary rounded-pill px-4 fw-bold">
                                        <i class="bi bi-arrow-left me-1"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .avatar-wrapper { padding: 4px; border: 3px solid #435ee0; border-radius: 50%; display: inline-block; }
    .avatar-circle { width: 100px; height: 100px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; }
    .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }
    .info-label-sm { font-weight: 800; font-size: 0.68rem; letter-spacing: 0.5px; color: #6c757d; display: block; margin-bottom: 4px; }
    .text-dark { color: #252d3a !important; }
    .extra-small { font-size: 0.65rem; }
    .form-control:focus { border-color: #435ee0; box-shadow: none !important; }
    .btn-light-secondary { background-color: #f2f4f6; color: #4b4b4b; border: none; }
    .btn-light-secondary:hover { background-color: #e2e5e8; }
</style>
@endpush