<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Griya Rias Asmara</title>

        {{-- Favicon GRA --}}
        <link rel="icon" type="image/png" href="{{ asset('assets-admin/img/logo.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        
        {{-- 🌟 Wrapper Luar: Hanya untuk centering. Background diatur ke abu-abu muda/putih. 🌟 --}}
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            
            {{-- Kotak Login Utama (CARD): Ini adalah KOTAK PUTIH YANG ANDA INGINKAN. --}}
            {{-- Hapus padding (p-6) agar login.blade yang mengatur padding-nya --}}
            <div class="w-full max-w-sm mt-6 bg-white dark:bg-gray-800 shadow-xl overflow-hidden rounded-xl">
                
                {{-- $slot akan diisi oleh konten dari login.blade.php --}}
                {{ $slot }} 

            </div>
        </div>
    </body>
</html>