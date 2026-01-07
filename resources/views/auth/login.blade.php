<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="SYSGRA System Login">
    <meta name="author" content="SYSGRA">

    <link href="{{ asset('assets-admin/img/logo.png') }}" rel="icon">
    <title>SYSGRA SYSTEM - Login</title>

    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link href="{{ asset('assets-admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets-admin/vendor/bootstrap/css/bootstrap.min1.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets-admin/css/sysgra.min.css') }}" rel="stylesheet">

    <style>
        /* 🌟 CSS GLOBAL 🌟 */
        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }

        .bg-gradient-login {
            background-image: url('{{ asset("assets-admin/img/bg.png") }}') !important;
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

        /* Font Styling untuk Judul */
        .login-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #3a3b45;
            margin-bottom: 0;
            letter-spacing: 0.05rem;
        }

        .login-subtitle {
            color: #858796;
            font-size: 0.8rem;
            font-weight: 400;
        }

        /* Password Toggle */
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

        .toggle-password:hover {
            color: #858796;
        }

        /* Tombol Google */
        .btn-google {
            color: #555 !important;
            background-color: #ffffff !important;
            border: 1px solid #d1d3e2 !important;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }

        .btn-google:hover {
            background-color: #f8f9fc !important;
            border-color: #b7b9cc !important;
            text-decoration: none;
        }

        .btn-google img {
            width: 18px;
            margin-right: 8px;
            margin-top: -3px;
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
                                <img src="{{ asset('assets-admin/img/logo.png') }}" alt="Logo"
                                    style="max-height: 80px; margin-bottom: 6px;">

                                <h2 class="login-title"
                                    style="font-family: 'Nunito', sans-serif; font-weight: 800; color: #3a3b45; letter-spacing: 1px;">
                                    SYSGRA SYSTEM
                                </h2>

                                <p class="login-subtitle mb-0"
                                    style="font-family: 'Nunito', sans-serif; color: #858796; font-size: 0.9rem; font-weight: 400;">
                                    Sistem Informasi Griya Rias Asmara
                                </p>
                            </div>

                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-0">Login</h1>
                                <hr class="mt-2 mb-4 w-25 mx-auto" style="border-top: 2px solid #6777ef;">
                            </div>

                            <form class="user" method="POST" action="{{ route('login') }}">
                                @csrf

                                @if (session('status'))
                                    <div class="alert alert-success alert-dismissible fade show small" role="alert">
                                        {{ session('status') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                                        {{ $errors->first() }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                <div class="form-group mb-3">
                                    <input type="email" class="form-control form-control-user"
                                        placeholder="Enter Email Address" name="email" value="{{ old('email') }}"
                                        required autofocus>
                                </div>

                                <div class="form-group mb-3 password-container">
                                    <input type="password" class="form-control form-control-user" id="passwordField"
                                        placeholder="Password" name="password" required>
                                    <i class="far fa-eye toggle-password" id="btnToggle"></i>
                                </div>

                                <div class="form-group d-flex justify-content-between align-items-center mb-4">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" id="customCheck"
                                            name="remember">
                                        <label class="custom-control-label" for="customCheck">Remember Me</label>
                                    </div>
                                    <a class="font-weight-bold small" href="{{ route('password.request') }}">Forgot
                                        Password?</a>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-user btn-block shadow-sm">Login
                                        Sekarang</button>
                                </div>

                                <hr>

                                <a href="{{ route('socialite.google.redirect') }}"
                                    class="btn btn-google btn-user btn-block">
                                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"
                                        alt="Google">
                                    Login with Google
                                </a>

                            </form>

                            <hr>
                            <div class="text-center">
                                <p class="small mb-0 text-muted">Belum punya akun?
                                    <a class="font-weight-bold" href="{{ route('register') }}">Create an Account!</a>
                                </p>
                            </div>
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
        // Toggle Password Visibility
        const btnToggle = document.querySelector('#btnToggle');
        const passwordField = document.querySelector('#passwordField');

        btnToggle.addEventListener('click', function () {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>