{{-- ================= NAVBAR (NAVIGATION) ================= --}}
<nav class="navbar navbar-expand navbar-light"
    style="background: transparent !important; box-shadow: none !important; min-height: 70px;">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center">
            <a href="#" class="burger-btn d-block d-xl-none me-3">
                <i class="bi bi-justify fs-3"></i>
            </a>
            <div class="d-xl-none me-3">
                <h5 class="mb-0 fw-bold text-primary">
                    {{ in_array(Auth::user()->role, ['owner', 'admin', 'kru']) ? 'SysGRA' : 'GriyaAsmara' }}
                </h5>
            </div>
        </div>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                @if(in_array(Auth::user()->role, ['owner', 'admin', 'kru']))
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link active dropdown-toggle text-gray-600" href="#" data-bs-toggle="dropdown"
                            data-bs-display="static" aria-expanded="false">
                            <i class='bi bi-bell fs-4'></i>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="badge bg-danger"
                                    style="position: absolute; top: 10px; right: 8px; font-size: 0.6rem; padding: 0.25em 0.45em;">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0"
                            style="min-width: 300px; margin-top: 15px;">
                            <li>
                                <h6 class="dropdown-header">Notifikasi Terbaru</h6>
                            </li>

                            @forelse(auth()->user()->unreadNotifications as $notification)
                                <li>
                                    <a class="dropdown-item d-flex align-items-start py-3" {{-- KUNCI: Gunakan prefix role user
                                        yang sedang login + .notification.read --}}
                                        href="{{ route(auth()->user()->role . '.notification.read', $notification->id) }}">

                                        <div class="avatar bg-light-primary me-3">
                                            <i class="bi {{ $notification->data['icon'] ?? 'bi-bell' }} text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 text-sm">{{ $notification->data['judul'] ?? 'Notifikasi Baru' }}
                                            </h6>
                                            <p class="mb-0 text-xs text-muted">{{ $notification->data['pesan'] ?? '' }}</p>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li>
                                    <p class="dropdown-item text-center text-muted py-3 mb-0">Tidak ada notifikasi baru</p>
                                </li>
                            @endforelse

                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                @php
                                    // Mengambil role user saat ini (misal: 'kru', 'owner', 'admin', 'pelanggan')
                                    $rolePrefix = auth()->user()->role; 
                                @endphp

                                <a class="dropdown-item text-center text-sm text-primary fw-bold"
                                    href="{{ route($rolePrefix . '.notification.all') }}"
                                    style="display: block; cursor: pointer;">
                                    Lihat Semua Notifikasi
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>

            <div class="dropdown">
                <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-menu d-flex align-items-center">
                        <div class="user-name text-end me-3">
                            <h6 class="mb-0 fw-bold">{{ Auth::user()->name }}</h6>
                            <p class="mb-0 text-xs text-muted">
                                @if(Auth::user()->role == 'owner') Owner
                                @elseif(Auth::user()->role == 'admin') Administrator
                                @elseif(Auth::user()->role == 'kru')
                                    @php
                                        $name = Auth::user()->name;
                                        $checkJob = \App\Models\JadwalPengantin::where('bulan', ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][(int) date('m') - 1])
                                            ->where('tahun', date('Y'))
                                            ->where(function ($q) use ($name) {
                                                $q->where('fg', $name)->orWhere('asisten', 'like', "%$name%")->orWhere('layos', $name);
                                            })->first();
                                        if ($checkJob) {
                                            if ($checkJob->fg == $name)
                                                echo "Fotografer";
                                            elseif (str_contains($checkJob->asisten, $name))
                                                echo "Asisten";
                                            elseif ($checkJob->layos == $name)
                                                echo "Kru Layos";
                                            else
                                                echo "Kru Lapangan";
                                        } else {
                                            echo "Kru Lapangan";
                                        }
                                    @endphp
                                @else Pelanggan @endif
                            </p>
                        </div>
                        <div class="avatar avatar-md border">
                            <img
                                src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background={{ Auth::user()->role == 'owner' ? 'ffc107' : '435ebe' }}&color=fff">
                        </div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                    <li>
                        @php
                            $profileRoute = match (Auth::user()->role) {
                                'owner' => route('owner.profile.index'),
                                'admin' => route('admin.profile.index'),
                                'kru' => route('kru.profile.index'),
                                default => route('user.profile.index'),
                            };
                        @endphp
                        <a class="dropdown-item" href="{{ $profileRoute }}"><i class="bi bi-person me-2"></i> Profil
                            Saya</a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="confirmLogout()">
                            <i class="bi bi-power me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Sesi Anda akan berakhir dan Anda harus login kembali.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#435ebe',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Logout!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            })
        }
    </script>
@endpush