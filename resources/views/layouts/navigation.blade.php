<nav class="navbar navbar-expand navbar-light">
    <div class="container-fluid">
        {{-- Burger Button untuk Mobile --}}
        <a href="#" class="burger-btn d-block">
            <i class="bi bi-justify fs-3"></i>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown me-3">
                    <a class="nav-link active dropdown-toggle text-gray-600" href="#" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class='bi bi-bell bi-sub fs-4'></i>
                        <span class="badge badge-notification bg-danger">0</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><h6 class="dropdown-header">Notifikasi</h6></li>
                        <li><a class="dropdown-item">Belum ada notifikasi baru</a></li>
                    </ul>
                </li>
            </ul>

            <div class="dropdown">
                <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-menu d-flex align-items-center">
                        <div class="user-name text-end me-3">
                            <h6 class="mb-0 text-gray-600 fw-bold">{{ Auth::user()->name }}</h6>
                            <p class="mb-0 text-xs text-gray-500">Administrator</p>
                        </div>
                        <div class="user-img d-flex align-items-center">
                            <div class="avatar avatar-md shadow-sm">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=435ebe&color=fff">
                            </div>
                        </div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><h6 class="dropdown-header">Halo, {{ explode(' ', Auth::user()->name)[0] }}!</h6></li>
                    <li><a class="dropdown-item" href="#"><i class="icon-mid bi bi-person me-2"></i> Profil Saya</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        {{-- GANTI href="#" MENJADI route('logout') --}}
                        <a class="dropdown-item text-danger fw-bold" href="{{ route('logout') }}" id="logout-btn">
                            <i class="icon-mid bi bi-box-arrow-right me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

{{-- Form Logout Tetap Dibutuhkan --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnLogout = document.getElementById('logout-btn');
        if (btnLogout) {
            btnLogout.addEventListener('click', function(e) {
                // e.preventDefault() akan membatalkan navigasi normal ke URL logout (GET)
                // dan menggantinya dengan popup konfirmasi ini
                e.preventDefault();

                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: "Sesi Anda akan segera berakhir!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#435ebe',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Logout!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Jika dikonfirmasi, form POST akan dijalankan
                        document.getElementById('logout-form').submit();
                    }
                })
            });
        }
    });
</script>