<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="SYSGRA System Forgot Password">
    <meta name="author" content="SYSGRA">

    <link href="{{ asset('assets-admin/img/logo.png') }}" rel="icon">
    <title>SYSGRA SYSTEM - Forgot Password</title>

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="{{ asset('assets-admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets-admin/vendor/bootstrap/css/bootstrap.min1.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets-admin/css/sysgra.min.css') }}" rel="stylesheet">

    <style>
        body { font-family: 'Nunito', sans-serif; }
        .bg-gradient-login {
            background-image: url('{{ asset("assets-admin/img/bg.png") }}') !important;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            background-attachment: fixed !important;
            background-color: rgba(50, 70, 100, 0.4) !important;
            background-blend-mode: multiply !important;
        }
        .card { border: none; border-radius: 1rem; }
        .login-form { padding: 40px !important; }
        .login-title { font-size: 1.5rem; font-weight: 800; color: #3a3b45; margin-bottom: 0; letter-spacing: 1px; }
        .login-subtitle { color: #858796; font-size: 0.9rem; font-weight: 400; }
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
                                {{-- PERBAIKAN: Ganti 'home' menjadi 'landing' sesuai rute di web.php Anda --}}
                                <a href="{{ route('landing') }}">
                                    <img src="{{ asset('assets-admin/img/logo.png') }}" alt="Logo" style="max-height: 80px; margin-bottom: 6px;">
                                </a>
                                <h2 class="login-title">SYSGRA SYSTEM</h2>
                                <p class="login-subtitle mb-0">Sistem Informasi Griya Rias Asmara</p>
                            </div>

                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-2">Forgot Password?</h1>
                                <p class="mb-4 small text-muted">No problem. Just let us know your email address and we will email you a password reset link.</p>
                                <hr class="mt-2 mb-4 w-25 mx-auto" style="border-top: 2px solid #6777ef;">
                            </div>

                            {{-- Pesan Status Berhasil --}}
                            @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show small" role="alert">
                                {{ session('status') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif

                            {{-- Pesan Error Validasi --}}
                            @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                                {{ $errors->first() }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif

                            <form class="user" method="POST" action="{{ route('password.email') }}">
                                @csrf
                                <div class="form-group mb-4">
                                    <input type="email" class="form-control form-control-user" id="exampleInputEmail"
                                        aria-describedby="emailHelp" placeholder="Enter Email Address..."
                                        name="email" value="{{ old('email') }}" required autofocus>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-user btn-block shadow-sm">
                                        Email Password Reset Link
                                    </button>
                                </div>
                            </form>

                            <hr>
                            <div class="text-center">
                                <p class="small mb-0 text-muted">
                                    Remembered your password? <a class="font-weight-bold" href="{{ route('login') }}">Back to login</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script ditempatkan sekali saja di akhir body --}}
    <script src="{{ asset('assets-admin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/bootstrap/js/bootstrap.bundle.min1.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('assets-admin/js/sysgra.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Alert otomatis hilang dalam 3 detik agar user sempat membaca
            window.setTimeout(function() {
                $(".alert").fadeTo(500, 0).slideUp(500, function() {
                    $(this).remove();
                });
            }, 3000); 
        });
    </script>
</body>
</html>