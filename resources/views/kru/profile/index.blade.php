@extends('layouts.master')
@section('title', 'Profil Saya - SysGRA')

@push('css')
    <style>
        /* Mengurangi jarak heading atas agar lebih rapat ke logo/navbar */
        .page-heading {
            margin-bottom: 0.5rem !important;
        }

        .page-title h3 {
            font-size: 1.4rem !important;
            margin-bottom: 0 !important;
        }

        .page-title p {
            margin-bottom: 0 !important;
            font-size: 0.8rem !important;
        }

        hr {
            margin: 0.5rem 0 !important;
            opacity: 0.1;
        }

        /* Optimasi Visual Card (Kiri) */
        .card-body.py-5 {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }

        .rounded-circle.shadow-sm {
            width: 100px !important;
            height: 100px !important;
        }

        .badge.rounded-pill {
            font-size: 0.7rem !important;
        }

        /* Optimasi Form Card (Kanan) */
        .card-body.p-4 {
            padding: 1.2rem !important;
        }

        .stats-icon {
            width: 35px !important;
            height: 35px !important;
        }

        .stats-icon i {
            font-size: 1.1rem !important;
        }

        /* Rapatkan jarak antar input */
        .mb-3 {
            margin-bottom: 0.6rem !important;
        }

        .mb-4 {
            margin-bottom: 0.8rem !important;
        }

        .my-4 {
            margin-top: 0.8rem !important;
            margin-bottom: 0.8rem !important;
        }

        /* Perkecil Alert */
        .alert.py-2 {
            padding-top: 0.4rem !important;
            padding-bottom: 0.4rem !important;
            font-size: 0.75rem !important;
            margin-bottom: 0.8rem !important;
        }

        /* Input field lebih slim */
        .form-control {
            padding: 0.4rem 0.75rem !important;
            font-size: 0.85rem !important;
        }

        .input-group-text {
            padding: 0.4rem 0.75rem !important;
            background-color: #f8f9fa;
        }

        /* Footer / Button */
        .border-top.pt-4 {
            padding-top: 0.8rem !important;
            margin-top: 0.8rem !important;
        }

        .btn-primary.px-5 {
            padding-left: 2rem !important;
            padding-right: 2rem !important;
            padding-top: 0.4rem !important;
            padding-bottom: 0.4rem !important;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-md-6">
                    <nav aria-label="breadcrumb" class="mb-1">
                        <ol class="breadcrumb" style="font-size: 0.85rem;">
                            <li class="breadcrumb-item"><a href="{{ route('kru.dashboard') }}"
                                    class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item active text-primary">Profil Saya</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold mb-0"><i class="bi bi-person-badge-fill text-primary me-2"></i>Pengaturan Profil</h3>
                    <p class="text-muted small">Kelola informasi personal dan keamanan akun Kru Anda.</p>
                </div>
            </div>
        </div>
        <hr>
    </div>

    <div class="page-content">
        <div class="row">
            {{-- Sisi Kiri: Visual Card --}}
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center d-flex flex-column justify-content-center py-5">
                        <div class="position-relative d-inline-block mx-auto mb-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=435ebe&color=fff&size=150"
                                class="rounded-circle shadow-sm border border-3 border-white" alt="Avatar">
                            <div class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle"
                                style="width: 20px; height: 20px;"></div>
                        </div>
                        <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                        <span class="badge bg-light-info text-info px-3 rounded-pill uppercase">Kru Operasional</span>

                        <hr class="w-75 mx-auto opacity-25 my-3">

                        {{-- Statistik Performa Ringkas --}}
                        <div class="row px-3 mb-3">
                            <div class="col-6 border-end">
                                {{-- Angka ini sekarang akan menampilkan total akumulasi (misal: 34, 50, dst) --}}
                                <h6 class="fw-bold mb-0 text-primary" style="font-size: 1.2rem;">{{ $tugasSelesai }}</h6>
                                <small class="text-muted fw-bold"
                                    style="font-size: 0.65rem; text-transform: uppercase;">Total Job</small>
                            </div>
                            <div class="col-6">
                                <h6 class="fw-bold mb-0 text-success" style="font-size: 1.1rem;">Aktif</h6>
                                <small class="text-muted fw-bold"
                                    style="font-size: 0.65rem; text-transform: uppercase;">Status Kru</small>
                            </div>
                        </div>

                        <div class="text-start px-4">
                            <p class="text-muted small mb-2"><i
                                    class="bi bi-envelope-fill me-2 text-primary"></i>{{ $user->email }}</p>
                            <p class="text-muted small mb-0"><i
                                    class="bi bi-calendar-check-fill me-2 text-primary"></i>Bergabung:
                                {{ $user->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sisi Kanan: Form --}}
            <div class="col-md-8 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <form action="{{ route('kru.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="d-flex align-items-center mb-4">
                                <div class="stats-icon purple me-3"
                                    style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-person-fill text-white fs-5"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-primary">Informasi Personal</h6>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $user->name) }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Alamat Email</label>
                                    <input type="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email) }}" required>
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <hr class="my-4 opacity-25">

                            <div class="d-flex align-items-center mb-3">
                                <div class="stats-icon blue me-3"
                                    style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-key-fill text-white fs-5"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-primary">Keamanan Akun</h6>
                            </div>

                            <div class="alert alert-light-warning border-0 py-2 small mb-3">
                                <i class="bi bi-exclamation-circle me-2"></i>Kosongkan kolom di bawah jika Anda tidak ingin
                                mengganti password.
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Password Baru</label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="••••••••">
                                        <span class="input-group-text toggle-password" data-target="#password"
                                            style="cursor: pointer;">
                                            <i class="bi bi-eye"></i>
                                        </span>
                                    </div>
                                    @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Konfirmasi Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            class="form-control" placeholder="••••••••">
                                        <span class="input-group-text toggle-password" data-target="#password_confirmation"
                                            style="cursor: pointer;">
                                            <i class="bi bi-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-4">
                                <p class="text-muted small mb-0 font-italic">Terakhir diperbarui:
                                    {{ $user->updated_at->diffForHumans() }}</p>
                                <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                                    <i class="bi bi-save me-2"></i>Update Profil
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            // Logika Toggle Password
            $('.toggle-password').click(function () {
                // Ambil target input dari atribut data-target
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
@endpush