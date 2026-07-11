@extends('layouts.landing')

@section('title', 'Beranda - Griya Rias Asmara')

@section('content')
    @include('landing.components.hero')
    @include('landing.components.about')
    @include('landing.components.features')
    @include('landing.components.gallery')
    @include('landing.components.paket') {{-- Di sini class .btn-pilih-paket berada --}}
    @include('landing.components.addons')
    @include('landing.components.portfolio')
    @include('landing.components.vendors')
    @include('landing.components.testimonials')
    @include('landing.components.contact')
@endsection

{{-- Modal booking dimuat hanya jika user sudah login --}}
@auth
    @push('modals')
        @include('landing.components.booking_modal')
    @endpush
@endauth

@push('js')
    {{-- Pastikan SweetAlert2 terload --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // 1. Script Preloader (Disederhanakan)
        window.addEventListener('load', () => {
            const preloader = document.querySelector('#preloader');
            if (preloader) {
                preloader.style.transition = 'opacity 0.5s ease';
                preloader.style.opacity = '0';
                setTimeout(() => preloader.remove(), 500);
            }
        });

        // 2. Script Toggle Detail Paket
        function toggleDetails(packageId) {
            const detailsDiv = $('#details-' + packageId);
            const readMoreLink = $('#readMore-' + packageId);

            if (detailsDiv.is(':visible')) {
                detailsDiv.slideUp();
                readMoreLink.html('Lihat Detail <i class="bi bi-arrow-right"></i>');
            } else {
                detailsDiv.slideDown();
                readMoreLink.html('Sembunyikan <i class="bi bi-arrow-up"></i>');
            }
        }

        $(document).ready(function () {
            // Cek status login
            const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

            // 3. Logika Klik Tombol Pilih Paket (Disesuaikan dengan gaya Hero)
            $(document).on('click', '.btn-pilih-paket', function (e) {
                e.preventDefault();
                const btn = $(this);

                if (!isLoggedIn) {
                    // Alert untuk bagian Paket (Pricing)
                    Swal.fire({
                        title: 'Tertarik dengan Paket Ini?',
                        text: "Silakan login terlebih dahulu untuk memeriksa ketersediaan tanggal dan melakukan pemesanan.",
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#435ee0',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Login Sekarang',
                        cancelButtonText: 'Daftar Akun',
                        reverseButtons: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('login') }}";
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            window.location.href = "{{ route('register') }}";
                        }
                    });
                } else {
                    // Jika sudah login: Isi data ke modal
                    const packageName = btn.data('package-name');
                    const packagePrice = btn.data('package-price');

                    $('#formPaketNama').val(packageName);
                    $('#formPaketHarga').val(packagePrice);

                    // Tampilkan modal
                    $('#bookingModal').modal('show');
                }
            });
        });
    </script>
@endpush