<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- Judul Tab Browser: SysGRA | Nama Halaman --}}
    <title>SysGRA | @yield('title')</title>

    {{-- Favicon menggunakan logo GRA --}}
    <link rel="shortcut icon" href="{{ asset('assets-admin/img/logo.png') }}" type="image/x-icon">
    
    {{-- CSS Bawaan Mazer --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/css/app.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* Optimasi agar konten lebih naik ke atas */
        #main {
            padding-top: 0 !important;
        }

        #main-content {
            padding: 1.2rem 2rem !important; /* Padding atas dikurangi agar lebih rapat */
        }

        .page-heading {
            margin-bottom: 0.5rem !important;
        }

        /* Styling tambahan untuk logo di sidebar agar terlihat pas */
        .sidebar-header img {
            height: auto;
            max-width: 50px; /* Ukuran logo GRA */
        }
    </style>
    @stack('css')
</head>

<body>
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/static/js/initTheme.js"></script>

    <div id="app">
        <div id="sidebar">
            @include('layouts.sidebar')
        </div>

        <div id="main" class='layout-navbar'>
            <header class='mb-2'> {{-- Margin header dikurangi --}}
                @include('layouts.navigation')
            </header>

            <div id="main-content">
                {{-- Area konten utama aplikasi --}}
                @yield('content')
            </div>
        </div>
    </div>

    {{-- Scripts Bawaan Mazer --}}
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/static/js/components/dark.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/extensions/apexcharts/apexcharts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/static/js/pages/dashboard.js"></script>

    {{-- Plugin Tambahan --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('js')
    @include('sweetalert::alert')

</body>
</html>