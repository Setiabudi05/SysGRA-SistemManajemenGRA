@extends('layouts.master')
@section('title', 'Profil Saya - SysGRA')

@push('css')
    <style>
        /* Rapatkan jarak heading utama ke atas */
        .page-heading {
            margin-bottom: 0.3rem !important;
        }

        .page-title h3 {
            font-size: 1.25rem !important;
            margin-bottom: 0 !important;
        }

        .page-title p {
            margin-bottom: 0 !important;
            font-size: 0.78rem !important;
        }

        hr {
            margin: 0.4rem 0 !important;
            opacity: 0.08;
        }

        /* Buat Avatar & Info Kiri Lebih Proporsional */
        .rounded-circle.shadow-sm {
            width: 85px !important;
            height: 85px !important;
        }

        .card {
            border-radius: 10px !important;
        }

        .card-body {
            padding: 1rem !important;
        }

        /* Perkecil icon section info */
        .stats-icon {
            width: 32px !important;
            height: 32px !important;
            border-radius: 6px !important;
        }

        .stats-icon i {
            font-size: 0.95rem !important;
        }

        /* Bikin form input super slim dan padat */
        .form-label {
            font-size: 0.8rem !important;
            margin-bottom: 0.2rem !important;
            color: #475569;
        }

        .form-control {
            padding: 0.35rem 0.7rem !important;
            font-size: 0.82rem !important;
            border-radius: 6px !important;
        }

        .input-group-text {
            padding: 0.35rem 0.7rem !important;
            font-size: 0.82rem !important;
            background-color: #f8f9fa;
        }

        /* Siasati jarak antar baris row grid */
        .mb-3 {
            margin-bottom: 0.5rem !important;
        }

        .mb-4 {
            margin-bottom: 0.6rem !important;
        }

        .my-4 {
            margin-top: 0.6rem !important;
            margin-bottom: 0.6rem !important;
        }

        /* Info Alert Box */
        .alert {
            padding: 0.4rem 0.75rem !important;
            font-size: 0.75rem !important;
            margin-bottom: 0.6rem !important;
            border-radius: 6px !important;
        }

        /* Tombol Simpan Mini-Premium */
        .btn-primary {
            padding: 0.38rem 1.5rem !important;
            font-size: 0.85rem !important;
            border-radius: 6px !important;
        }

        .font-italic {
            font-size: 0.75rem !important;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12">
                    <nav aria-label="breadcrumb" class="mb-1">
                        <ol class="breadcrumb" style="font-size: 0.8rem;">
                            <li class="breadcrumb-item"><a href="{{ route('kru.dashboard') }}" class="text-muted">Dashboard</a></li>
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
            {{-- Sisi Kiri: Visual Card Singkas --}}
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center d-flex flex-column justify-content-center py-4">
                        <div class="position-relative d-inline-block mx-auto mb-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=435ebe&color=fff&size=120"
                                class="rounded-circle shadow-sm border border-3 border-white" alt="Avatar">
                            <div class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle"
                                style="width: 16px; height: 16px;"></div>
                        </div>
                        <h5 class="fw-bold mb-1" style="font-size: 1.1rem;">{{ $user->name }}</h5>
                        <span class="badge bg-light-info text-info px-2 py-1 rounded-pill text-uppercase" style="font-size: 0.65rem;">
                            {{ $user->jabatan ?? 'Kru Operasional' }}
                        </span>

                        <hr class="w-75 mx-auto opacity-25 my-2">

                        {{-- Statistik Performa --}}
                        <div class="row px-2 mb-2">
                            <div class="col-6 border-end">
                                <h6 class="fw-bold mb-0 text-primary" style="font-size: 1.05rem;">{{ $tugasSelesai }}</h6>
                                <small class="text-muted fw-bold" style="font-size: 0.6rem; text-transform: uppercase;">Total Job</small>
                            </div>
                            <div class="col-6">
                                <h6 class="fw-bold mb-0 text-success" style="font-size: 1.05rem;">Aktif</h6>
                                <small class="text-muted fw-bold" style="font-size: 0.6rem; text-transform: uppercase;">Status Kru</small>
                            </div>
                        </div>

                        <div class="text-start px-3 mt-2" style="font-size: 0.8rem;">
                            <p class="text-muted mb-1 text-truncate"><i class="bi bi-envelope-fill me-2 text-primary"></i>{{ $user->email }}</p>
                            @if($user->phone)
                                <p class="text-muted mb-1"><i class="bi bi-whatsapp me-2 text-success"></i>{{ $user->phone }}</p>
                            @endif
                            <p class="text-muted mb-0"><i class="bi bi-calendar-check-fill me-2 text-primary"></i>Bergabung: {{ $user->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sisi Kanan: Form Slim-Fit --}}
            <div class="col-md-8 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3">
                        <form action="{{ route('kru.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT') {{-- PERBAIKAN: Mengganti @html menjadi @method agar tidak bocor kodenya --}}

                            <div class="d-flex align-items-center mb-3">
                                <div class="stats-icon purple me-2 d-flex align-items-center justify-content-center bg-light-primary">
                                    <i class="bi bi-person-fill text-primary"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-primary" style="font-size: 0.95rem;">Informasi Personal</h6>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $user->name) }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold">Alamat Email</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email) }}" required>
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold">Nomor WhatsApp / Telepon</label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                        placeholder="Contoh: 0857xxxx" value="{{ old('phone', $user->phone) }}">
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold">Spesialisasi Jabatan</label>
                                    <input type="text" class="form-control bg-light text-muted fw-bold" 
                                        value="{{ strtoupper($user->jabatan ?? 'Kru Operasional') }}" readonly>
                                </div>
                            </div>

                            <hr class="my-3 opacity-25">

                            <div class="d-flex align-items-center mb-2">
                                <div class="stats-icon blue me-2 d-flex align-items-center justify-content-center bg-light-info">
                                    <i class="bi bi-key-fill text-info"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-primary" style="font-size: 0.95rem;">Keamanan Akun</h6>
                            </div>

                            <div class="alert alert-light-warning border-0 small mb-2">
                                <i class="bi bi-exclamation-circle me-1"></i>Kosongkan jika tidak ingin mengganti password.
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold">Password Baru</label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password"
                                            class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
                                        <span class="input-group-text toggle-password" data-target="#password" style="cursor: pointer;">
                                            <i class="bi bi-eye"></i>
                                        </span>
                                    </div>
                                    @error('password') <div class="text-danger small mt-1" style="font-size: 0.75rem;">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label fw-bold">Konfirmasi Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            class="form-control" placeholder="••••••••">
                                        <span class="input-group-text toggle-password" data-target="#password_confirmation" style="cursor: pointer;">
                                            <i class="bi bi-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3 border-top pt-3">
                                <p class="text-muted small mb-0 font-italic">
                                    Terakhir diubah: {{ $user->updated_at->diffForHumans() }}
                                </p>
                                <button type="submit" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-1">
                                    <i class="bi bi-save"></i> Update Profil
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
            $('.toggle-password').click(function () {
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