<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SysGRA | @yield('title')</title>

    {{-- Favicon --}}
    <link rel="shortcut icon" href="{{ asset('assets-admin/img/logo.png') }}" type="image/x-icon">
    
    {{-- CSS Bawaan Mazer --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/css/app.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* FORCE SHOW HAMBURGER DI MOBILE */
        .burger-btn {
            display: block !important;
            cursor: pointer;
        }

        /* Sembunyikan di layar Desktop */
        @media (min-width: 1200px) {
            .burger-btn {
                display: none !important;
            }
        }

        .burger-btn i {
            color: #435ebe !important; /* Biru SysGRA */
        }

        body.theme-dark .burger-btn i {
            color: #fff !important;
        }

        /* Optimasi Layout Navbar & Content */
        #main {
            padding-top: 0 !important;
        }

        #main-content {
            padding: 1.2rem 2rem !important;
        }

        .sidebar-header .logo img {
            height: 45px !important;
            width: auto;
        }
    </style>
    @stack('css')
</head>

<body>
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/static/js/initTheme.js"></script>

    <div id="app">
        {{-- Sidebar --}}
        <div id="sidebar">
            @include('layouts.sidebar')
        </div>

        {{-- Main Content --}}
        <div id="main" class='layout-navbar'>
            <header class='mb-2'>
                @include('layouts.navigation')
            </header>

            <div id="main-content">
                @yield('content')
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/static/js/components/dark.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- SCRIPT TOGGLE SIDEBAR --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const burgerBtns = document.querySelectorAll('.burger-btn');
            const sidebar = document.getElementById('sidebar');

            burgerBtns.forEach(btn => {
                btn.onclick = function(e) {
                    e.preventDefault();
                    if (sidebar) sidebar.classList.toggle('active');
                };
            });
        });

        // Global Logout Function
        function confirmLogout(role) {
            Swal.fire({
                title: 'Konfirmasi Keluar',
                text: "Apakah Anda yakin ingin keluar?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#435ebe',
                confirmButtonText: 'Ya, Logout',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>

    @stack('js')
    @include('sweetalert::alert')
</body>
</html>