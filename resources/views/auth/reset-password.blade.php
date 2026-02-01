<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="SYSGRA System Reset Password">
    <meta name="author" content="SYSGRA">

    <link href="{{ asset('assets-admin/img/logo.png') }}" rel="icon">
    <title>SYSGRA SYSTEM - Atur Password Baru</title>

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="{{ asset('assets-admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets-admin/vendor/bootstrap/css/bootstrap.min1.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets-admin/css/sysgra.min.css') }}" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }

        .bg-gradient-login {
            background-image: url('{{ asset("assets-admin/img/bg.jpg") }}') !important;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            background-attachment: fixed !important;
            background-color: rgba(50, 70, 100, 0.4) !important;
            background-blend-mode: multiply !important;
        }

        .card {
            border: none;
            border-radius: 1.25rem;
            overflow: hidden;
        }

        .login-form {
            padding: 40px !important;
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #3a3b45;
            letter-spacing: 1px;
        }

        /* Perbaikan Kontainer Password agar Ikon Lurus */
        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-wrapper input {
            padding-right: 45px !important;
            /* Ruang untuk ikon */
            width: 100%;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            cursor: pointer;
            color: #d1d3e2;
            z-index: 10;
            transition: color 0.2s;
            /* Memastikan posisi vertikal tepat di tengah input saja */
            top: 50%;
            transform: translateY(-50%);
        }

        .toggle-password:hover {
            color: #435ebe;
            /* Warna primer SysGRA */
        }

        .btn-primary {
            background-color: #435ebe;
            border-color: #435ebe;
            font-weight: 700;
            padding: 0.75rem;
            border-radius: 0.5rem;
        }
    </style>
</head>

<body class="bg-gradient-login">
    <div class="container-login d-flex align-items-center min-vh-100">
        <div class="row justify-content-center w-100 mx-0">
            <div class="col-xl-4 col-lg-5 col-md-7 col-sm-10">
                <div class="card shadow-lg my-3">
                    <div class="card-body p-0">
                        <div class="login-form">

                            <div class="text-center pt-2 pb-3">
                                <a href="{{ route('home') }}">
                                    <img src="{{ asset('assets-admin/img/logo.png') }}" alt="Logo" style="max-height: 70px; margin-bottom: 10px;">
                                </a>
                                <h2 class="login-title mb-0">SYSGRA SYSTEM</h2>
                                <p class="text-muted small">Sistem Informasi Griya Rias Asmara</p>
                            </div>

                            <div class="text-center mb-4">
                                <h5 class="font-weight-bold text-gray-900">Atur Password Baru</h5>
                                <p class="small text-muted">Tentukan kata sandi baru untuk akun Anda.</p>
                                <hr style="border-top: 2px solid #435ebe; width: 40px; margin: 10px auto;">
                            </div>

                            @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                                <ul class="mb-0 px-3">
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif

                            <form class="user" method="POST" action="{{ route('password.store') }}" autocomplete="off">
                                @csrf
                                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                {{-- Email (Readonly) --}}
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-dark">Email Anda</label>
                                    <input type="email" class="form-control bg-light"
                                        name="email" value="{{ old('email', $request->email) }}" required readonly>
                                </div>

                                {{-- Password Baru - Menggunakan autocomplete="new-password" --}}
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-dark">Password Baru</label>
                                    <div class="password-wrapper">
                                        <input type="password" class="form-control" id="passwordField"
                                            placeholder="Min. 8 Karakter" name="password"
                                            required autofocus autocomplete="new-password">
                                        <i class="far fa-eye toggle-password" onclick="togglePass('passwordField', this)"></i>
                                    </div>
                                </div>

                                {{-- Konfirmasi Password --}}
                                <div class="form-group mb-4">
                                    <label class="small font-weight-bold text-dark">Konfirmasi Password</label>
                                    <div class="password-wrapper">
                                        <input type="password" class="form-control" id="passwordConfirmField"
                                            placeholder="Ulangi Password Baru" name="password_confirmation"
                                            required autocomplete="new-password">
                                        <i class="far fa-eye toggle-password" onclick="togglePass('passwordConfirmField', this)"></i>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block shadow-sm">
                                    <i class="fas fa-key me-1"></i> Simpan Password Baru
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets-admin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/bootstrap/js/bootstrap.bundle.min1.js') }}"></script>

    <script>
        // Fungsi Toggle Password yang lebih simpel dan akurat
        function togglePass(fieldId, icon) {
            const field = document.getElementById(fieldId);
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>

</html>