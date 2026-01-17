<!-- views/layouts/santri-wali-sidebar.blade.php -->
<ul class="sidebar-menu">
    {{-- Dashboard --}}
    <li>
        <a href="{{ route('santri.dashboard') }}" class="{{ Request::routeIs('santri.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard Progres</span>
        </a>
    </li>

    {{-- Profil Santri --}}
    <li>
        <a href="{{ route('santri.profil.index') }}" class="{{ Request::routeIs('santri.profil.*') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span>Profil Santri</span>
        </a>
    </li>

    {{-- ✅ Kegiatan & Absensi --}}
    <li>
        <a href="{{ route('santri.kegiatan.index') }}" class="{{ Request::routeIs('santri.kegiatan.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-check"></i>
            <span>Kegiatan & Absensi</span>
        </a>
    </li>

    {{-- Capaian Qur'an & Hadist --}}
    <li>
        <a href="{{ route('santri.capaian.index') }}" class="{{ request()->routeIs('santri.capaian.*') ? 'active' : '' }}">
            <i class="fas fa-book-reader"></i>
            <span>Capaian Qur'an & Hadist</span>
        </a>
    </li>

    {{-- Riwayat Pelanggaran --}}
    <li>
        <a href="{{ route('santri.pelanggaran.index') }}" class="{{ Request::routeIs('santri.pelanggaran.*') ? 'active' : '' }}">
            <i class="fas fa-exclamation-circle"></i>
            <span>Riwayat Pelanggaran</span>
        </a>
    </li>

    {{-- ✅ Riwayat Uang Saku (BARU - TAMBAHKAN INI) --}}
    <li>
        <a href="{{ route('santri.uang-saku.index') }}" class="{{ Request::routeIs('santri.uang-saku.*') ? 'active' : '' }}">
            <i class="fas fa-wallet"></i>
            <span>Riwayat Uang Saku</span>
        </a>
    </li>

    {{-- Tambahkan menu Kesehatan --}}
    <li>
        <a href="{{ route('santri.kesehatan.index') }}" class="{{ request()->routeIs('santri.kesehatan.*') ? 'active' : '' }}">
            <i class="fas fa-heartbeat"></i>
            <span>Riwayat Kesehatan</span>
        </a>
    </li>

    {{-- ✅ Riwayat Kepulangan (BARU) --}}
        <li>
            <a href="{{ route('santri.kepulangan.index') }}" class="{{ request()->routeIs('santri.kepulangan.*') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Riwayat Kepulangan</span>
            </a>
        </li>

    {{-- ✅ Menu Berita (BARU) --}}
    <li>
        <a href="{{ route('santri.berita.index') }}" class="{{ request()->routeIs('santri.berita.*') ? 'active' : '' }}">
            <i class="fas fa-newspaper"></i>
            <span>Berita</span>
        </a>
    </li>
    <li>

    {{-- Logout --}}
    <li class="logout-item">
        <form action="{{ route('santri.logout') }}" method="POST" id="logout-form-santri" style="display: none;">
            @csrf
        </form>
        <a href="#" onclick="event.preventDefault(); if(confirm('Yakin ingin logout?')) document.getElementById('logout-form-santri').submit();">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </li>
</ul>