@extends('user.layouts.main')

@section('content')
    {{-- Section Publik --}}
    @include('user.components.hero')
    @include('user.components.about')

    {{-- Section Khusus User Login --}}
    @auth
        @include('user.components.features')
        @include('user.components.gallery')
        @include('user.components.pricing')
        @include('user.components.addons')
        @include('user.components.portfolio')
        @include('user.components.vendors')
        @include('user.components.testimonials')
    @endauth

    {{-- Kontak Selalu Tampil --}}
    @include('user.components.contact')
@endsection

{{-- MODAL BOOKING UNTUK USER LOGIN --}}
@auth
    @push('modals')
        @include('user.components.booking_modal')
    @endpush
@endauth

@section('scripts')
    {{-- Library --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets-admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/purecounter/purecounter_vanilla.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets-admin/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('assets-admin/js/main.js') }}"></script>

    <script>
        // Hapus preloader
        window.addEventListener('load', () => {
            const preloader = document.querySelector('#preloader');
            if (preloader) preloader.remove();
        });

        // Toggle detail paket
        function toggleDetails(packageId) {
            const detailsDiv = document.getElementById('details-' + packageId);
            const readMoreLink = document.getElementById('readMore-' + packageId);

            if (detailsDiv.style.display === 'block') {
                detailsDiv.style.display = 'none';
                readMoreLink.innerHTML = 'Lihat Detail <i class="bi bi-arrow-right"></i>';
            } else {
                detailsDiv.style.display = 'block';
                readMoreLink.innerHTML = 'Sembunyikan <i class="bi bi-arrow-up"></i>';
            }
        }
    </script>

    {{-- ============================================= --}}
    {{-- LOGIKA UNTUK TAMU (BELUM LOGIN) --}}
    {{-- ============================================= --}}
    @guest
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const restrictedSections = [
                    '#features',
                    '#gallery',
                    '#pricing',
                    '#portfolio-unggulan',
                    '#vendor-partners',
                    '#testimonials'
                ];

                // Loop semua id dan pasang event listener
                restrictedSections.forEach(id => {
                    // Tangkap semua link yang href-nya diakhiri dengan section tsb
                    document.querySelectorAll(`a[href$="${id}"]`).forEach(link => {
                        link.addEventListener('click', function (e) {
                            e.preventDefault();

                            Swal.fire({
                                icon: 'warning',
                                title: 'Akses Dibatasi',
                                text: 'Fitur ini hanya tersedia untuk pengguna yang sudah login.',
                                showCancelButton: true,
                                confirmButtonText: 'Login Sekarang',
                                cancelButtonText: 'Batal',
                                customClass: {
                                    popup: 'swal-custom-popup',
                                    confirmButton: 'btn btn-primary btn-sm',
                                    cancelButton: 'btn btn-secondary btn-sm'
                                },
                                buttonsStyling: false
                            }).then(result => {
                                if (result.isConfirmed) {
                                    window.location.href = "{{ route('login') }}";
                                }
                            });
                        });
                    });
                });
            });
        </script>
    @endguest


    {{-- ============================================= --}}
    {{-- LOGIKA UNTUK USER LOGIN --}}
    {{-- ============================================= --}}
    @auth
        <script type="text/javascript">
            $(function () {
                const bookingModal = document.getElementById('bookingModal');
                if (bookingModal) {
                    bookingModal.addEventListener('show.bs.modal', event => {
                        const button = event.relatedTarget;
                        const packageName = button.getAttribute('data-package-name');
                        const packagePrice = button.getAttribute('data-package-price');
                        bookingModal.querySelector('#formPaketNama').value = packageName;
                        bookingModal.querySelector('#formPaketHarga').value = packagePrice;
                    });
                }

                // AJAX kirim booking
                $('#btnKirimBooking').on('click', function (e) {
                    e.preventDefault();
                    const form = $('#formBooking');
                    const button = $(this);
                    button.html('Menyimpan data...').prop('disabled', true);

                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        dataType: 'json',
                        success: function (response) {
                            $('#bookingModal').modal('hide');
                            Swal.fire({
                                icon: response.success ? 'success' : 'error',
                                title: response.success ? 'Sukses!' : 'Gagal!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            if (response.success) $('#cart-badge').text(response.cart_count).show();
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Terhubung',
                                text: 'Tidak dapat terhubung ke server.'
                            });
                        },
                        complete: function () {
                            button.html('Kirim Formulir Booking').prop('disabled', false);
                        }
                    });
                });
            });
        </script>
    @endauth
@endsection