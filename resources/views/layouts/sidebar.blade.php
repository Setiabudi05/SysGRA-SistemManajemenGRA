<div class="sidebar-wrapper active">
    <div class="sidebar-header position-relative" style="padding-bottom: 0.5rem;">
        <div class="d-flex justify-content-between align-items-center">
            <div class="logo">
                <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets-admin/img/logo.png') }}" alt="Logo GRA"
                        style="height: 55px; width: auto; object-fit: contain;">
                    <h3 class="mb-0">SysGRA</h3>
                </a>
            </div>
            <div class="theme-toggle d-flex gap-2 align-items-center mt-2">
                <div class="form-check form-switch fs-6">
                    <input class="form-check-input me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                    <label class="form-check-label"></label>
                </div>
            </div>
            <div class="sidebar-toggler x">
                <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
            </div>
        </div>
    </div>

    <div class="sidebar-menu">
        <ul class="menu mt-0">
            <li class="sidebar-title" style="padding-top: 0;">Menu</li>
            <li class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                    <i class="bi bi-speedometer2"></i><span>Dashboard</span>
                </a>
            </li>

            <li class="sidebar-title">Master Data</li>
            <li class="sidebar-item {{ request()->routeIs('admin.paket.*') ? 'active' : '' }}">
                <a href="{{ route('admin.paket.index') }}" class="sidebar-link">
                    <i class="bi bi-card-list"></i><span>Paket</span>
                </a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.dekorasi.*') ? 'active' : '' }}">
                <a href="{{ route('admin.dekorasi.index') }}" class="sidebar-link">
                    <i class="bi bi-brush"></i><span>Dekorasi</span>
                </a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.baju.*') ? 'active' : '' }}">
                <a href="{{ route('admin.baju.index') }}" class="sidebar-link">
                    <i class="bi bi-person-bounding-box"></i><span>Baju Pengantin</span>
                </a>
            </li>

            <li class="sidebar-title">Manajemen</li>
            @php
                $isJadwalActive = request()->routeIs(['admin.jadwalpengantin.*', 'admin.jadwaldekor.*', 'admin.jadwalgown.*', 'admin.jadwallayos.*']);
            @endphp
            <li class="sidebar-item has-sub {{ $isJadwalActive ? 'active' : '' }}">
                <a href="#" class="sidebar-link">
                    <i class="bi bi-calendar-event"></i><span>Jadwal</span>
                </a>
                <ul class="submenu {{ $isJadwalActive ? 'active' : '' }}">
                    <li class="submenu-item {{ request()->routeIs('admin.jadwalpengantin.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.jadwalpengantin.index') }}" class="submenu-link">Jadwal Pengantin</a>
                    </li>
                    <li class="submenu-item {{ request()->routeIs('admin.jadwaldekor.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.jadwaldekor.index') }}" class="submenu-link">Jadwal Dekor</a>
                    </li>
                    <li class="submenu-item {{ request()->routeIs('admin.jadwalgown.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.jadwalgown.index') }}" class="submenu-link">Jadwal Gown</a>
                    </li>
                    <li class="submenu-item {{ request()->routeIs('admin.jadwallayos.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.jadwallayos.index') }}" class="submenu-link">Jadwal Layos</a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-item {{ request()->routeIs('admin.booking.*') ? 'active' : '' }}">
                <a href="{{ route('admin.booking.index') }}" class="sidebar-link">
                    <i class="bi bi-cart-check"></i><span>Pesanan</span>
                </a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.pembayaran.*') ? 'active' : '' }}">
                <a href="{{ route('admin.pembayaran.index') }}" class="sidebar-link">
                    <i class="bi bi-credit-card"></i><span>Pembayaran</span>
                </a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.pembukuan.*') ? 'active' : '' }}">
                <a href="{{ route('admin.pembukuan.index') }}" class="sidebar-link">
                    <i class="bi bi-cash-stack"></i><span>Pembukuan</span>
                </a>
            </li>

            <li class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <a href="{{ route('admin.users.index') }}" class="sidebar-link">
                    <i class="bi bi-people-fill"></i><span>Kelola User</span>
                </a>
            </li>
        </ul>
    </div>
</div>