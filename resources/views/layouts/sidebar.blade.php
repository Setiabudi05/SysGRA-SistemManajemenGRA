<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative" style="padding-bottom: 0.5rem;">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    <a href="{{ Auth::user()->role == 'owner' ? route('owner.dashboard') : (Auth::user()->role == 'admin' ? route('admin.dashboard') : (Auth::user()->role == 'kru' ? route('kru.dashboard') : route('user.dashboard'))) }}"
                        class="d-flex align-items-center gap-2">
                        <img src="{{ asset('assets-admin/img/logo.png') }}" alt="Logo GRA"
                            style="height: 45px; width: auto; object-fit: contain;">
                        <h4 class="mb-0 fw-bold">
                            {{ in_array(Auth::user()->role, ['owner', 'admin', 'kru']) ? 'SysGRA' : 'GriyaAsmara' }}
                        </h4>
                    </a>
                </div>
                <div class="burger-btn d-xl-none d-block">
                    <a href="#" class="sidebar-hide"><i class="bi bi-justify fs-3" style="color: #435ebe;"></i></a>
                </div>
            </div>
        </div>

        <div class="sidebar-menu">
            <ul class="menu mt-4">
                {{-- ================= MENU OWNER ================= --}}
                @if(Auth::user()->role == 'owner')
                    <li class="sidebar-title">Analitik & Strategi</li>
                    <li class="sidebar-item {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('owner.dashboard') }}" class="sidebar-link"><i
                                class="bi bi-columns-gap"></i><span>Dashboard Strategis</span></a>
                    </li>
                    <li class="sidebar-title">Kendali Operasional</li>
                    @php $isJadwalOwnerActive = request()->is('owner/jadwal*'); @endphp
                    <li class="sidebar-item has-sub {{ $isJadwalOwnerActive ? 'active' : '' }}">
                        <a href="#" class="sidebar-link"><i class="bi bi-people-fill"></i><span>Pembagian Kru</span></a>
                        <ul class="submenu {{ $isJadwalOwnerActive ? 'active' : '' }}">
                            <li class="submenu-item {{ request()->routeIs('owner.jadwalpengantin.*') ? 'active' : '' }}"><a
                                    href="{{ route('owner.jadwalpengantin.index') }}" class="submenu-link">Jadwal
                                    Pengantin</a></li>
                            <li class="submenu-item {{ request()->routeIs('owner.jadwaldekor.*') ? 'active' : '' }}"><a
                                    href="{{ route('owner.jadwaldekor.index') }}" class="submenu-link">Jadwal Dekor</a></li>
                            <li class="submenu-item {{ request()->routeIs('owner.jadwallayos.*') ? 'active' : '' }}"><a
                                    href="{{ route('owner.jadwallayos.index') }}" class="submenu-link">Jadwal Layos</a></li>
                        </ul>
                    </li>
                    <li class="sidebar-title">Keuangan & SDM</li>

                    {{-- Menu Laporan Pesanan --}}
                    <li class="sidebar-item {{ request()->routeIs('owner.booking.*') ? 'active' : '' }}">
                        <a href="{{ route('owner.booking.index') }}" class="sidebar-link">
                            <i class="bi bi-cart-check"></i>
                            <span>Laporan Pesanan</span>
                        </a>
                    </li>

                    {{-- Menu Pembayaran --}}
                    <li class="sidebar-item {{ request()->routeIs('owner.pembayaran.*') ? 'active' : '' }}">
                        <a href="{{ route('owner.pembayaran.index') }}" class="sidebar-link">
                            <i class="bi bi-credit-card"></i>
                            <span>Laporan Pembayaran</span>
                        </a>
                    </li>

                    {{-- Menu Laporan Pembukuan --}}
                    <li class="sidebar-item {{ request()->routeIs('owner.pembukuan.*') ? 'active' : '' }}">
                        <a href="{{ route('owner.pembukuan.index') }}" class="sidebar-link">
                            <i class="bi bi-journal-check"></i>
                            <span>Laporan Pembukuan</span>
                        </a>
                    </li>

                    {{-- Menu Kelola Akun --}}
                    <li class="sidebar-item {{ request()->routeIs('owner.users.*') ? 'active' : '' }}">
                        <a href="{{ route('owner.users.index') }}" class="sidebar-link">
                            <i class="bi bi-person-gear"></i>
                            <span>Kelola Akun Pegawai</span>
                        </a>
                    </li>
                    {{-- ================= MENU ADMIN ================= --}}
                @elseif(Auth::user()->role == 'admin')
                    <li class="sidebar-title">Menu Utama</li>
                    <li class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="sidebar-link"><i
                                class="bi bi-speedometer2"></i><span>Dashboard Admin</span></a>
                    </li>
                    <li class="sidebar-title">Master Data</li>
                    <li class="sidebar-item {{ request()->routeIs('admin.paket.*') ? 'active' : '' }}"><a
                            href="{{ route('admin.paket.index') }}" class="sidebar-link"><i
                                class="bi bi-card-list"></i><span>Paket Pernikahan</span></a></li>
                    <li class="sidebar-item {{ request()->routeIs('admin.dekorasi.*') ? 'active' : '' }}"><a
                            href="{{ route('admin.dekorasi.index') }}" class="sidebar-link"><i
                                class="bi bi-brush"></i><span>Dekorasi</span></a></li>
                    <li class="sidebar-item {{ request()->routeIs('admin.baju.*') ? 'active' : '' }}"><a
                            href="{{ route('admin.baju.index') }}" class="sidebar-link"><i
                                class="bi bi-person-hearts"></i><span>Baju Pengantin</span></a></li>
                    <li class="sidebar-title">Manajemen</li>
                    @php $isJadwalAdminActive = request()->is('admin/jadwal*'); @endphp
                    <li class="sidebar-item has-sub {{ $isJadwalAdminActive ? 'active' : '' }}">
                        <a href="#" class="sidebar-link"><i class="bi bi-calendar-event"></i><span>Jadwal
                                Operasional</span></a>
                        <ul class="submenu {{ $isJadwalAdminActive ? 'active' : '' }}">
                            <li class="submenu-item {{ request()->routeIs('admin.jadwalpengantin.*') ? 'active' : '' }}"><a
                                    href="{{ route('admin.jadwalpengantin.index') }}" class="submenu-link">Jadwal
                                    Pengantin</a></li>
                            <li class="submenu-item {{ request()->routeIs('admin.jadwaldekor.*') ? 'active' : '' }}"><a
                                    href="{{ route('admin.jadwaldekor.index') }}" class="submenu-link">Jadwal Dekor</a></li>
                            <li class="submenu-item {{ request()->routeIs('admin.jadwalgown.*') ? 'active' : '' }}"><a
                                    href="{{ route('admin.jadwalgown.index') }}" class="submenu-link">Jadwal Gown</a></li>
                            <li class="submenu-item {{ request()->routeIs('admin.jadwallayos.*') ? 'active' : '' }}"><a
                                    href="{{ route('admin.jadwallayos.index') }}" class="submenu-link">Jadwal Layos</a></li>
                        </ul>
                    </li>
                    <li class="sidebar-item {{ request()->routeIs('admin.booking.*') ? 'active' : '' }}"><a
                            href="{{ route('admin.booking.index') }}" class="sidebar-link"><i
                                class="bi bi-cart-check"></i><span>Pesanan</span></a></li>
                    <li class="sidebar-item {{ request()->routeIs('admin.pembayaran.*') ? 'active' : '' }}"><a
                            href="{{ route('admin.pembayaran.index') }}" class="sidebar-link"><i
                                class="bi bi-credit-card"></i><span>Pembayaran</span></a></li>
                    <li class="sidebar-item {{ request()->routeIs('admin.pembukuan.*') ? 'active' : '' }}"><a
                            href="{{ route('admin.pembukuan.index') }}" class="sidebar-link"><i
                                class="bi bi-cash-stack"></i><span>Pembukuan</span></a></li>

                    {{-- ================= MENU KRU ================= --}}
                @elseif(Auth::user()->role == 'kru')
                    <li class="sidebar-title">Panel Operasional</li>
                    <li class="sidebar-item {{ request()->routeIs('kru.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('kru.dashboard') }}" class="sidebar-link"><i
                                class="bi bi-grid-fill"></i><span>Dashboard Kru</span></a>
                    </li>
                    <li class="sidebar-item {{ request()->routeIs('kru.jadwal.*') ? 'active' : '' }}">
                        <a href="{{ route('kru.jadwal.index') }}" class='sidebar-link'>
                            <i class="bi bi-calendar-event"></i>
                            <span>Jadwal</span>
                        </a>
                    </li>
                    {{-- RIWAYAT TUGAS (Tugas yang sudah selesai) --}}
                    <li class="sidebar-item {{ request()->routeIs('kru.riwayat.*') ? 'active' : '' }}">
                        <a href="{{ route('kru.riwayat.index') }}" class='sidebar-link'>
                            <i class="bi bi-clock-history"></i>
                            <span>Riwayat Tugas</span>
                        </a>
                    </li>

                    <li class="sidebar-title">Informasi</li>

                    {{-- Panduan Teknis --}}
                    <li class="sidebar-item {{ request()->routeIs('kru.panduan') ? 'active' : '' }}">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-info-square-fill"></i>
                            <span>Panduan Teknis</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>