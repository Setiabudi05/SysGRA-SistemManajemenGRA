<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="SYSGRA System Register">
    <meta name="author" content="SYSGRA">

    <link href="{{ asset('assets-admin/img/logo.png') }}" rel="icon">
    <title>SYSGRA SYSTEM - Register</title>

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

        .login-form { padding: 25px 35px !important; }

        .login-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #3a3b45;
            letter-spacing: 1px;
            margin-bottom: 0;
        }

        .login-subtitle {
            color: #858796;
            font-size: 0.9rem;
            font-weight: 400;
        }

        .password-wrapper { position: relative; display: flex; align-items: center; }
        .password-wrapper input { padding-right: 40px !important; width: 100%; }

        .toggle-password {
            position: absolute;
            right: 15px;
            cursor: pointer;
            color: #d1d3e2;
            z-index: 10;
            top: 50%;
            transform: translateY(-50%);
            transition: color 0.2s;
        }

        .toggle-password:hover { color: #858796; }

        .btn-primary {
            background-color: #435ebe;
            border-color: #435ebe;
            font-weight: 700;
            padding: 0.6rem;
            border-radius: 0.5rem;
        }

        .form-group { margin-bottom: 0.75rem !important; }
        .form-control { height: 38px; font-size: 0.85rem; }
    </style>
</head>

<body class="bg-gradient-login">
    <div class="container-login d-flex align-items-center min-vh-100">
        <div class="row justify-content-center w-100 mx-0">
            <div class="col-xl-4 col-lg-5 col-md-7 col-sm-10">
                <div class="card shadow-lg">
                    <div class="card-body p-0">
                        <div class="login-form">

                            <div class="text-center pt-2 pb-3">
                                <a href="{{ route('home') }}">
                                    <img src="{{ asset('assets-admin/img/logo.png') }}" alt="Logo" style="max-height: 65px; margin-bottom: 6px;">
                                </a>
                                <h2 class="login-title">SYSGRA SYSTEM</h2>
                                <p class="login-subtitle mb-0">Sistem Informasi Griya Rias Asmara</p>
                            </div>

                            <div class="text-center">
                                <h1 class="h5 text-gray-900 mb-0">Create an Account</h1>
                                <hr class="mt-2 mb-3 w-25 mx-auto" style="border-top: 2px solid #6777ef;">
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger py-2 px-3 small mb-3" role="alert">
                                    <ul class="mb-0 pl-3">
                                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form class="user" method="POST" action="{{ route('register') }}">
                                @csrf

                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" placeholder="Full Name" name="name" value="{{ old('name') }}" required autofocus>
                                </div>

                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" placeholder="Email Address" name="email" value="{{ old('email') }}" required>
                                </div>

                                {{-- Password Field dengan Ikon Mata --}}
                                <div class="form-group">
                                    <div class="password-wrapper">
                                        <input type="password" class="form-control form-control-user" id="passwordField" placeholder="Password" name="password" required>
                                        <i class="far fa-eye toggle-password" onclick="togglePass('passwordField', this)"></i>
                                    </div>
                                </div>

                                {{-- Confirm Password Field dengan Ikon Mata --}}
                                <div class="form-group mb-4">
                                    <div class="password-wrapper">
                                        <input type="password" class="form-control form-control-user" id="passwordConfirmation" placeholder="Confirm Password" name="password_confirmation" required>
                                        <i class="far fa-eye toggle-password" onclick="togglePass('passwordConfirmation', this)"></i>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-user btn-block shadow-sm">
                                    Register Account
                                </button>
                            </form>

                            <hr class="my-3">
                            <div class="text-center">
                                <p class="small mb-0 text-muted">Already have an account? 
                                    <a class="font-weight-bold text-primary" href="{{ route('login') }}">Login here!</a>
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
    
    <script>
        /**
         * Fungsi Toggle Password Mandiri
         * fieldId: ID dari input password yang akan diubah
         * icon: Element ikon (this) yang diklik
         */
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
    <script>
        $(document).ready(function() {
            window.setTimeout(function() {
                $(".alert").fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove(); 
                });
            }, 3000); 
        });
    </script>
</body>
</body>

</html>