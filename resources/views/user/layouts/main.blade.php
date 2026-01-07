<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>@yield('title', 'Griya Rias Asmara')</title>

    <meta name="description" content="Jasa makeup, dekorasi, dan katering pernikahan profesional.">
    <meta name="keywords" content="wedding organizer, rias pengantin, catering pernikahan, dekorasi pernikahan">

    <link href="{{ asset('assets-admin/img/logo.png') }}" rel="icon">
    <link href="{{ asset('assets-admin/img/logo.png') }}" rel="apple-touch-icon">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">

    <link href="{{ asset('assets-admin/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets-admin/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets-admin/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('assets-admin/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets-admin/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link href="{{ asset('assets-admin/css/global.css') }}" rel="stylesheet">
    
    <link href="{{ asset('assets-admin/css/landing-page.css') }}" rel="stylesheet">

</head>

<body class="index-page">
    @include('user.components.header')

    <main class="main">
        @yield('content')
        @stack('modals')
    </main>

    @include('user.components.footer')

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <div id="preloader"></div>

    @yield('scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success_message'))
        <script>
            Swal.fire({
                icon: "success",
                title: "Login Berhasil!",
                text: "{{ session('success_message') }}", // Mengambil pesan dari LoginController
                showConfirmButton: false,
                timer: 3000
            });
        </script>
    @endif
</body>

</html>