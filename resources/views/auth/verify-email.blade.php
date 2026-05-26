<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="{{ asset('assets-admin/img/logo.png') }}" rel="icon">
    <title>SYSGRA SYSTEM - Verifikasi Email</title>

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
        .card { border: none; border-radius: 1rem; overflow: hidden; }
        .login-form { padding: 40px !important; }
        .login-title { font-size: 1.5rem; font-weight: 800; color: #3a3b45; margin-bottom: 0; letter-spacing: 1px; }
        .login-subtitle { color: #858796; font-size: 0.9rem; font-weight: 400; }
        
        .icon-box {
            width: 80px;
            height: 80px;
            background: #f8f9fc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #435ebe;
            font-size: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .btn-primary { background-color: #435ebe; border-color: #435ebe; font-weight: 700; border-radius: 0.5rem; }
    </style>
</head>

<body class="bg-gradient-login">
    <div class="container-login d-flex align-items-center min-vh-100">
        <div class="row justify-content-center w-100 mx-0">
            <div class="col-xl-4 col-lg-5 col-md-7 col-sm-10">
                <div class="card shadow-lg w-100">
                    <div class="card-body p-0">
                        <div class="login-form">

                            {{-- Header Logo --}}
                            <div class="text-center pt-2 pb-3">
                                <a href="{{ route('landing') }}">
                                    <img src="{{ asset('assets-admin/img/logo.png') }}" alt="Logo" style="max-height: 70px; margin-bottom: 10px;">
                                </a>
                                <h2 class="login-title">SYSGRA SYSTEM</h2>
                                <p class="login-subtitle">Sistem Informasi Griya Rias Asmara</p>
                            </div>

                            <div class="text-center mt-3">
                                <div class="icon-box">
                                    <i class="fas fa-envelope-open-text"></i>
                                </div>
                                <h1 class="h4 text-gray-900 mb-2 fw-bold">Verifikasi Email</h1>
                                <p class="mb-4 small text-muted">
                                    Satu langkah lagi! Silakan klik tautan verifikasi yang baru saja kami kirimkan ke email Anda untuk mengaktifkan akun.
                                </p>
                                <hr class="mt-2 mb-4 w-25 mx-auto" style="border-top: 2px solid #435ebe;">
                            </div>

                            {{-- Notifikasi Status Berhasil Kirim Ulang --}}
                            @if (session('status') == 'verification-link-sent')
                                <div class="alert alert-success alert-dismissible fade show small shadow-sm mb-4" role="alert">
                                    <i class="fas fa-check-circle mr-1"></i> Tautan baru telah dikirimkan ke email Anda.
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            {{-- Tombol Kirim Ulang --}}
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-block shadow-sm py-2">
                                    <i class="fas fa-paper-plane mr-2"></i> Kirim Ulang Email Verifikasi
                                </button>
                            </form>

                            <hr class="my-4">

                            {{-- Tombol Logout --}}
                            <div class="text-center">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-link btn-sm text-gray-500 font-weight-bold text-decoration-none">
                                        <i class="fas fa-sign-out-alt mr-1"></i> Keluar (Logout)
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
                {{-- Footer Copyright --}}
                <div class="text-center mt-4 text-white-50 small">
                    &copy; {{ date('Y') }} Griya Rias Asmara - SysGRA System
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="{{ asset('assets-admin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/bootstrap/js/bootstrap.bundle.min1.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Auto-dismiss alert dalam 4 detik
            window.setTimeout(function() {
                $(".alert").fadeTo(500, 0).slideUp(500, function() {
                    $(this).remove();
                });
            }, 4000); 
        });
    </script>
</body>
</html>