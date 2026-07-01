<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SysGRA</title>

    {{-- Logo/Favicon --}}
    <link rel="shortcut icon" href="{{ asset('assets/assets-admin/logo.png') }}" type="image/png">

    {{-- CSS Mazer Compiled --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/css/app.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* Mengatur agar konten tidak tertutup navbar */
        #main {
            padding-top: 0 !important;
        }
        #main-content {
            padding: 1.5rem 2rem !important;
        }
        /* Styling tambahan untuk navbar agar terlihat rapi */
        .navbar {
            padding: 1rem 2rem !important;
            background-color: white !important;
        }
    </style>
    @stack('css')
</head>

<body>
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/static/js/initTheme.js"></script>

    <div id="app">
        {{-- 1. Sidebar (Kiri) --}}
        <div id="sidebar">
            @include('layouts.sidebar')
        </div>

        {{-- 2. Bagian Utama (Main Area) dengan class layout-navbar --}}
        <div id="main" class="layout-navbar">
            
            {{-- 3. Header/Navbar (Atas) --}}
            <header>
                @include('layouts.navigation')
            </header>

            {{-- 4. Konten Halaman (Tanpa Footer) --}}
            <div id="main-content">
                <div class="page-heading">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    
    {{-- Scripts Bawaan Mazer --}}
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/static/js/components/dark.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/extensions/apexcharts/apexcharts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/static/js/pages/dashboard.js"></script>
    
    {{-- SweetAlert2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

    @stack('js')
    @include('sweetalert::alert') 
    
</body>
</html>