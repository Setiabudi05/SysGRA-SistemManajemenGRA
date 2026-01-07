@extends('layouts.master')

@section('title', 'Profil Saya')

@section('content')
<div class="page-heading">
    <h3><i class="bi bi-person-circle me-2 text-primary"></i>Profil Admin</h3>
</div>

<div class="page-content">
    <section class="row">
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="avatar avatar-xl mb-3">
                        <img src="https://ui-avatars.com/api/?name=Admin+Administrator&background=435ebe&color=fff" alt="Face 1" style="width: 100px; height: 100px;">
                    </div>
                    <h5 class="fw-bold">Admin Administrator</h5>
                    <p class="text-muted small">administrator@sysgra.com</p>
                    <span class="badge bg-light-primary text-primary px-3">Super Admin</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent">
                    <h4 class="card-title mb-0">Pengaturan Akun</h4>
                </div>
                <div class="card-body">
                    <form action="#" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">NAMA LENGKAP</label>
                                <input type="text" class="form-control" value="Admin Administrator">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">EMAIL</label>
                                <input type="email" class="form-control" value="administrator@sysgra.com">
                            </div>
                            <hr class="my-3">
                            <h6 class="text-danger small fw-bold"><i class="bi bi-shield-lock me-1"></i> GANTI PASSWORD (OPSIONAL)</h6>
                            <div class="col-md-12 mb-3">
                                <label class="form-label small">PASSWORD LAMA</label>
                                <input type="password" class="form-control" placeholder="********">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small">PASSWORD BARU</label>
                                <input type="password" class="form-control" placeholder="Minimal 8 karakter">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small">KONFIRMASI PASSWORD BARU</label>
                                <input type="password" class="form-control">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection