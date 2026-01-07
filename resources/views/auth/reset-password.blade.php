<!DOCTYPE html>
<html lang="en">

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
            border-radius: 1rem;
        }

        .login-form {
            padding: 40px !important;
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #3a3b45;
            margin-bottom: 0;
            letter-spacing: 1px;
        }

        .login-subtitle {
            color: #858796;
            font-size: 0.9rem;
            font-weight: 400;
        }

        .password-container {
            position: relative;
        }

        .password-container input {
            padding-right: 40px !important;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #d1d3e2;
            z-index: 10;
        }
    </style>
</head>

<body class="bg-gradient-login">
    <div class="container-login d-flex align-items-center min-vh-100">
        <div class="row justify-content-center w-100">
            <div class="col-xl-4 col-lg-5 col-md-7 col-sm-10">
                <div class="card shadow-lg my-3 w-100">
                    <div class="card-body p-0">
                        <div class="login-form">

                            <div class="text-center pt-2 pb-3">
                                <a href="{{ route('home') }}">
                                    <img src="{{ asset('assets-admin/img/logo.png') }}" alt="Logo" style="max-height: 80px; margin-bottom: 6px;">
                                </a>
                                <h2 class="login-title">SYSGRA SYSTEM</h2>
                                <p class="login-subtitle mb-0">Sistem Informasi Griya Rias Asmara</p>
                            </div>

                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-2">Atur Password Baru</h1>
                                <p class="mb-4 small text-muted">Silahkan masukkan email Anda dan tentukan password baru yang aman.</p>
                                <hr class="mt-2 mb-4 w-25 mx-auto" style="border-top: 2px solid #6777ef;">
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form class="user" method="POST" action="{{ route('password.store') }}">
                                @csrf

                                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold">Email Anda</label>
                                    <input type="email" class="form-control form-control-user bg-light" 
                                        name="email" value="{{ old('email', $request->email) }}" required readonly>
                                </div>

                                <div class="form-group mb-3 password-container">
                                    <label class="small font-weight-bold">Password Baru</label>
                                    <input type="password" class="form-control form-control-user" id="passwordField"
                                        placeholder="Min. 8 Karakter" name="password" required autofocus>
                                    <i class="far fa-eye toggle-password" id="btnToggle"></i>
                                </div>

                                <div class="form-group mb-4 password-container">
                                    <label class="small font-weight-bold">Konfirmasi Password Baru</label>
                                    <input type="password" class="form-control form-control-user" id="passwordConfirmField"
                                        placeholder="Ulangi Password Baru" name="password_confirmation" required>
                                    <i class="far fa-eye toggle-password" id="btnToggleConfirm"></i>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-user btn-block shadow-sm">
                                        Reset Password
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets-admin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/bootstrap/js/bootstrap.bundle.min1.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('assets-admin/js/sysgra.min.js') }}"></script>

    <script>
        function setupPasswordToggle(btnId, fieldId) {
            const btn = document.getElementById(btnId);
            const field = document.getElementById(fieldId);
            if (btn && field) {
                btn.addEventListener('click', function () {
                    const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
                    field.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }
        }

        // Jalankan toggle untuk kedua field
        setupPasswordToggle('btnToggle', 'passwordField');
        setupPasswordToggle('btnToggleConfirm', 'passwordConfirmField');
    </script>
</body>

</html>