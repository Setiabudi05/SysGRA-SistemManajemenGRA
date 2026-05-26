<section id="hero" class="hero section dark-background">
    {{-- 1. Video Background --}}
    <div class="video-background">
        <video autoplay muted loop playsinline>
            <source src="{{ asset('assets-admin/img/video/video.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="video-overlay"></div>
    </div>

    {{-- 2. Hero Content --}}
    <div class="hero-content">
        <div class="container position-relative">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 data-aos="fade-up" data-aos-delay="100">
                        Wujudkan Hari Bahagia Anda Bersama Griya Rias Asmara
                    </h1>
                    <p data-aos="fade-up" data-aos-delay="200">
                        Dari persiapan hingga hari pernikahan, kami hadir untuk menjadikan momen Anda indah, elegan, dan tak terlupakan.
                    </p>
                    
                    <div class="hero-buttons" data-aos="fade-up" data-aos-delay="300">
                        @auth
                            {{-- Jika sudah login, langsung ke section pricing --}}
                            <a href="#pricing" class="btn btn-primary">Pesan Sekarang</a>
                        @else
                            {{-- Jika belum login, panggil fungsi SweetAlert --}}
                            <a href="javascript:void(0)" onclick="tampilkanAlertLogin()" class="btn btn-primary">
                                Pesan Sekarang
                            </a>
                        @endauth
                        
                        <a href="{{ asset('assets-user/docs/pricelist new.pdf') }}" target="_blank" class="btn btn-outline">
                            Lihat Price List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 3. Scripts & Styles (Hanya untuk section ini) --}}
@push('css')
<style>
    .hero { position: relative; min-height: 100vh; display: flex; align-items: center; overflow: hidden; }
    .video-background { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; }
    .video-background video { width: 100%; height: 100%; object-fit: cover; }
    .video-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.45); }
    .hero-content { position: relative; z-index: 2; width: 100%; }
    .btn-primary { background-color: #435ee0; border-color: #435ee0; border-radius: 50px; padding: 12px 30px; font-weight: 700; transition: 0.3s; }
    .btn-outline { border: 2px solid #fff; color: #fff; border-radius: 50px; padding: 12px 30px; font-weight: 700; transition: 0.3s; margin-left: 10px; }
    .btn-outline:hover { background: #fff; color: #435ee0; }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function tampilkanAlertLogin() {
        Swal.fire({
            title: 'Mulai Rencanakan Harimu!',
            text: "Silakan login terlebih dahulu untuk melakukan pemesanan jadwal rias.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#435ee0',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Login Sekarang',
            cancelButtonText: 'Daftar Akun',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('login') }}";
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                window.location.href = "{{ route('register') }}";
            }
        });
    }
</script>
@endpush