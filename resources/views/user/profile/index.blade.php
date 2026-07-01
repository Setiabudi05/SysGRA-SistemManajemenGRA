@extends('layouts.user')
@section('title', 'Profil Saya')

@section('content')
<div class="page-content mt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">
            <div class="row align-items-stretch">
                {{-- SISI KIRI: PROFIL --}}
                <div class="col-12 col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100 mb-0" style="border-radius: 20px;">
                        <div class="card-body p-4 d-flex flex-column align-items-center text-center">
                            <div class="avatar-wrapper mb-3">
                                <div class="avatar-circle">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=435ee0&color=fff&size=128" alt="Avatar">
                                </div>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">{{ Auth::user()->name }}</h5>
                            <span class="badge bg-light-primary text-primary px-3 rounded-pill extra-small fw-bold mb-4">MEMBER AKTIF</span>
                            <div class="w-100 pt-3 border-top text-start">
                                <div class="mb-3"><label class="info-label-sm text-muted text-uppercase">Email</label>
                                    <p class="fw-bold text-dark mb-0"><i class="bi bi-envelope me-2 text-primary"></i>{{ Auth::user()->email }}</p>
                                </div>
                                <div class="mb-3"><label class="info-label-sm text-muted text-uppercase">WhatsApp</label>
                                    <p class="fw-bold text-dark mb-0"><i class="bi bi-whatsapp me-2 text-success"></i>{{ Auth::user()->phone ?? '-' }}</p>
                                </div>
                                <div class="mb-0"><label class="info-label-sm text-muted text-uppercase">Alamat</label>
                                    <p class="fw-bold text-dark mb-0 small text-secondary"><i class="bi bi-geo-alt me-2 text-danger"></i>{{ Auth::user()->address ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SISI KANAN: FORM UPDATE --}}
                <div class="col-12 col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm h-100 mb-0 text-start" style="border-radius: 20px;">
                        <div class="card-body p-4">
                            <form action="{{ route('user.profile.update') }}" method="POST">
                                @csrf @method('PUT')
                                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-badge me-2"></i>Informasi Pribadi</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6"><label class="info-label-sm">Nama Lengkap</label>
                                        <input type="text" name="name" class="form-control form-control-sm bg-light" value="{{ old('name', $user->name) }}" required>
                                    </div>
                                    <div class="col-md-6"><label class="info-label-sm">Email</label>
                                        <input type="email" name="email" class="form-control form-control-sm bg-light" value="{{ old('email', $user->email) }}" required>
                                    </div>
                                    <div class="col-12"><label class="info-label-sm">Nomor WhatsApp</label>
                                        <input type="number" name="phone" class="form-control form-control-sm bg-light" value="{{ old('phone', $user->phone) }}">
                                    </div>
                                    <div class="col-12"><label class="info-label-sm">Alamat Rumah</label>
                                        <textarea name="address" class="form-control form-control-sm bg-light" rows="3">{{ old('address', $user->address) }}</textarea>
                                    </div>
                                </div>
                                
                                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-shield-lock me-2"></i>Ubah Password</h6>
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="info-label-sm">Password Baru</label>
                                        <div class="input-group input-group-sm">
                                            <input type="password" name="password" id="pass" class="form-control bg-light">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePass('pass', 'icon1')"><i class="bi bi-eye" id="icon1"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-md-6"><label class="info-label-sm">Konfirmasi Password</label>
                                        <div class="input-group input-group-sm">
                                            <input type="password" name="password_confirmation" id="conf" class="form-control bg-light">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePass('conf', 'icon2')"><i class="bi bi-eye" id="icon2"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                                    <a href="{{ url('user/dashboard') }}" class="btn btn-light-secondary rounded-pill px-4 fw-bold">Kembali</a>
                                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">Simpan Perubahan</button>
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

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function togglePass(id, iconId) {
        let input = document.getElementById(id);
        let icon = document.getElementById(iconId);
        if (input.type === "password") {
            input.type = "text";
            icon.className = "bi bi-eye-slash";
        } else {
            input.type = "password";
            icon.className = "bi bi-eye";
        }
    }
</script>
@if(session('success'))
<script>
    Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 2000, showConfirmButton: false });
</script>
@endif
@endpush