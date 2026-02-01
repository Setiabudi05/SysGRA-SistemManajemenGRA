<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SYSGRA - Verifikasi Email</title>
    
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="{{ asset('assets-admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets-admin/css/sysgra.min.css') }}" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }

        .bg-gradient-verify {
            background-image: url('{{ asset("assets-admin/img/bg.png") }}') !important;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            background-attachment: fixed !important;
            background-blend-mode: multiply !important;
            background-color: rgba(50, 70, 100, 0.4) !important;
        }

        .card {
            border: none;
            border-radius: 1.25rem;
            overflow: hidden;
        }

        .login-form {
            padding: 2.5rem !important;
        }

        .icon-circle {
            height: 80px;
            width: 80px;
            background-color: #f8f9fc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: #4e73df;
            font-size: 2rem;
            box-shadow: 0 0.125rem 0.25rem 0 rgba(58, 59, 69, 0.2);
        }

        .btn-user {
            border-radius: 10rem;
            padding: 0.75rem 1rem;
            font-weight: 700;
        }

        .login-title {
            font-weight: 800;
            color: #3a3b45;
            letter-spacing: 0.05rem;
        }
    </style>
</head>

<body class="bg-gradient-verify">
    <div class="container d-flex align-items-center min-vh-100">
        <div class="row justify-content-center w-100">
            <div class="col-xl-4 col-lg-5 col-md-8 col-sm-10">
                <div class="card shadow-lg">
                    <div class="card-body p-0">
                        <div class="login-form">
                            <div class="text-center">
                                <img src="{{ asset('assets-admin/img/logo.png') }}" alt="Logo" style="max-height: 70px; margin-bottom: 1.5rem;">
                                
                                <div class="icon-circle shadow-sm">
                                    <i class="fas fa-envelope-open-text"></i>
                                </div>
                                
                                <h1 class="h4 login-title mb-2">Verifikasi Email</h1>
                                <p class="small text-muted mb-4">Langkah terakhir untuk mengaktifkan akun Anda</p>
                            </div>

                            <div class="text-center mb-4">
                                <p class="small text-gray-600">
                                    Satu langkah lagi! Silakan cek inbox atau folder <strong>Spam</strong> Gmail Anda dan klik tombol verifikasi yang kami kirimkan.
                                </p>
                            </div>

                            @if (session('status') == 'verification-link-sent')
                            <div class="alert alert-success alert-dismissible fade show small text-center" role="alert">
                                <i class="fas fa-check-circle mr-1"></i> Tautan baru telah dikirimkan ke email Anda.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif

                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-user btn-block shadow-sm">
                                    <i class="fas fa-paper-plane mr-2"></i> Kirim Ulang Email
                                </button>
                            </form>

                            <hr class="my-4">

                            <div class="text-center">
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-link btn-sm text-gray-500 font-weight-bold text-decoration-none">
                                        <i class="fas fa-sign-out-alt mr-1"></i> Keluar (Logout)
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4 text-white-50 small">
                    &copy; {{ date('Y') }} Griya Rias Asmara - SysGRA System
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets-admin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Auto-dismiss alert dalam 3 detik untuk kesan yang bersih
            window.setTimeout(function() {
                $(".alert").fadeTo(500, 0).slideUp(500, function() {
                    $(this).remove();
                });
            }, 3000);
        });
    </script>
</body>

</html>