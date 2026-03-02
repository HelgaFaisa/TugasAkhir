{{-- Sidebar Menu Admin - RBAC --}}

{{-- ── DASHBOARD ── --}}
<li>
    <a href="{{ route('admin.dashboard') }}" class="{{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
    </a>
</li>

{{-- ── DASHBOARD ABSENSI ── --}}
@if(auth()->user()->hasRole('super_admin', 'akademik', 'pamong'))
<li>
    <a href="{{ route('admin.kegiatan.index') }}"
       class="{{ Request::routeIs('admin.kegiatan.index') ? 'active' : '' }}">
        <i class="fas fa-calendar-check"></i><span>Dashboard Absensi</span>
    </a>
</li>
@endif

{{-- ── DATA MASTER ── --}}
@if(auth()->user()->hasRole('super_admin', 'akademik'))
<li class="menu-toggle {{ Request::routeIs('admin.santri.*') || Request::routeIs('admin.users.*') || Request::routeIs('admin.kelas.*') ? 'active' : '' }}">
    <a href="javascript:void(0)" class="menu-parent">
        <i class="fas fa-database"></i><span>Data Master</span>
        <i class="fas fa-chevron-down toggle-icon"></i>
    </a>
    <ul class="submenu">
        <li>
            <a href="{{ route('admin.santri.index') }}" class="{{ Request::routeIs('admin.santri.index') ? 'active' : '' }}">
                <i class="fas fa-user-graduate"></i><span>Data Santri</span>
            </a>
        </li>
        @if(auth()->user()->isSuperAdmin())
        <li>
            <a href="{{ route('admin.users.santri_accounts') }}" class="{{ Request::routeIs('admin.users.santri_accounts') ? 'active' : '' }}">
                <i class="fas fa-user-lock"></i><span>Akun Santri</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.users.wali_accounts') }}" class="{{ Request::routeIs('admin.users.wali_accounts') ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i><span>Akun Wali Santri</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.users.admin_accounts') }}" class="{{ Request::routeIs('admin.users.admin_accounts') || Request::routeIs('admin.users.admin_create') || Request::routeIs('admin.users.admin_edit') ? 'active' : '' }}">
                <i class="fas fa-user-cog"></i><span>Akun Admin</span>
            </a>
        </li>
        @endif
        <li>
            <a href="{{ route('admin.kelas.index') }}" class="{{ Request::routeIs('admin.kelas.*') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i><span>Kelola Kelas</span>
            </a>
        </li>
    </ul>
</li>
@endif

{{-- ── PAMONG: Data Santri (standalone) ── --}}
@if(auth()->user()->isPamong())
<li>
    <a href="{{ route('admin.santri.index') }}" class="{{ Request::routeIs('admin.santri.*') ? 'active' : '' }}">
        <i class="fas fa-user-graduate"></i><span>Data Santri</span>
    </a>
</li>
@endif

{{-- ── KEGIATAN ── --}}
@if(auth()->user()->hasRole('super_admin', 'akademik'))
<li class="menu-toggle {{ 
    Request::routeIs('admin.laporan-kegiatan.*') || 
    Request::routeIs('admin.kegiatan.jadwal') ||
    Request::routeIs('admin.kegiatan.show') ||
    Request::routeIs('admin.riwayat-kegiatan.*') || 
    Request::routeIs('admin.kartu-rfid.*') ||
    Request::routeIs('admin.absensi-kegiatan.*') ||
    Request::routeIs('admin.kategori-kegiatan.*')
    ? 'active' : '' }}">
    <a href="javascript:void(0)" class="menu-parent">
        <i class="fas fa-calendar-alt"></i><span>Kegiatan</span>
        <i class="fas fa-chevron-down toggle-icon"></i>
    </a>
    <ul class="submenu">
        <li>
            <a href="{{ route('admin.laporan-kegiatan.index') }}"
               class="{{ Request::routeIs('admin.laporan-kegiatan.*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i><span>Laporan & Analitik</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.kegiatan.jadwal') }}"
               class="{{ Request::routeIs('admin.kegiatan.jadwal') ? 'active' : '' }}">
                <i class="fas fa-calendar-week"></i><span>Jadwal Kegiatan</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.riwayat-kegiatan.index') }}"
               class="{{ Request::routeIs('admin.riwayat-kegiatan.*') ? 'active' : '' }}">
                <i class="fas fa-history"></i><span>Riwayat Kegiatan</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.kartu-rfid.index') }}"
               class="{{ Request::routeIs('admin.kartu-rfid.*') ? 'active' : '' }}">
                <i class="fas fa-id-card"></i><span>Kartu RFID</span>
            </a>
        </li>
    </ul>
</li>
@endif

{{-- ── CAPAIAN MATERI ── --}}
@if(auth()->user()->hasRole('super_admin', 'akademik'))
<li class="menu-toggle {{ Request::routeIs('admin.materi.*') || Request::routeIs('admin.semester.*') || Request::routeIs('admin.capaian.*') ? 'active' : '' }}">
    <a href="javascript:void(0)" class="menu-parent">
        <i class="fas fa-book-quran"></i><span>Capaian Materi</span>
        <i class="fas fa-chevron-down toggle-icon"></i>
    </a>
    <ul class="submenu">
        <li>
            <a href="{{ route('admin.capaian.dashboard') }}" class="{{ Request::routeIs('admin.capaian.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i><span>Laporan Capaian</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.capaian.index') }}" class="{{ Request::routeIs('admin.capaian.index') || Request::routeIs('admin.capaian.show') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list"></i><span>Input Capaian</span>
            </a>
        </li>
        @if(auth()->user()->hasRole('super_admin', 'akademik'))
        <li>
            <a href="{{ route('admin.materi.index') }}" class="{{ Request::routeIs('admin.materi.*') || Request::routeIs('admin.semester.*') ? 'active' : '' }}">
                <i class="fas fa-book"></i><span>Master Materi</span>
            </a>
        </li>
        @endif
    </ul>
</li>
@endif

{{-- ── PAMONG: Capaian Materi ── --}}
@if(auth()->user()->isPamong())
<li class="menu-toggle {{ Request::routeIs('admin.capaian.*') ? 'active' : '' }}">
    <a href="javascript:void(0)" class="menu-parent">
        <i class="fas fa-book-quran"></i><span>Capaian Materi</span>
        <i class="fas fa-chevron-down toggle-icon"></i>
    </a>
    <ul class="submenu">
        <li>
            <a href="{{ route('admin.capaian.dashboard') }}" class="{{ Request::routeIs('admin.capaian.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i><span>Laporan Capaian</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.capaian.index') }}" class="{{ Request::routeIs('admin.capaian.index') || Request::routeIs('admin.capaian.show') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list"></i><span>Data Capaian</span>
            </a>
        </li>
    </ul>
</li>
@endif

{{-- ── ADMINISTRASI ── --}}
@if(auth()->user()->isSuperAdmin())
<li class="menu-toggle {{ Request::is('admin/pembayaran-spp*') || Request::is('admin/uang-saku*') || Request::is('admin/keuangan*') ? 'active' : '' }}">
    <a href="javascript:void(0)" class="menu-parent">
        <i class="fas fa-money-bill-wave"></i><span>Administrasi</span>
        <i class="fas fa-chevron-down toggle-icon"></i>
    </a>
    <ul class="submenu">
        <li>
            <a href="{{ route('admin.keuangan.index') }}"
               class="{{ Request::is('admin/keuangan*') ? 'active' : '' }}">
                <i class="fas fa-cash-register"></i><span>Kas & Keuangan</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.pembayaran-spp.index') }}"
               class="{{ Request::is('admin/pembayaran-spp*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i><span>Pembayaran SPP</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.uang-saku.index') }}"
               class="{{ Request::is('admin/uang-saku*') ? 'active' : '' }}">
                <i class="fas fa-wallet"></i><span>Uang Saku Santri</span>
            </a>
        </li>
    </ul>
</li>
@endif

{{-- ── PAMONG: Uang Saku (standalone) ── --}}
@if(auth()->user()->isPamong())
<li>
    <a href="{{ route('admin.uang-saku.index') }}"
       class="{{ Request::is('admin/uang-saku*') ? 'active' : '' }}">
        <i class="fas fa-wallet"></i><span>Uang Saku</span>
    </a>
</li>
@endif

{{-- ── PELANGGARAN ── --}}
@if(auth()->user()->hasRole('super_admin', 'akademik'))
<li class="menu-toggle {{ Request::routeIs('admin.kategori-pelanggaran.*') || Request::routeIs('admin.riwayat-pelanggaran.*') || Request::routeIs('admin.pembinaan-sanksi.*') ? 'active' : '' }}">
    <a href="javascript:void(0)" class="menu-parent">
        <i class="fas fa-exclamation-triangle"></i><span>Pelanggaran</span>
        <i class="fas fa-chevron-down toggle-icon"></i>
    </a>
    <ul class="submenu">
        <li>
            <a href="{{ route('admin.pembinaan-sanksi.index') }}"
               class="{{ Request::routeIs('admin.pembinaan-sanksi.*') ? 'active' : '' }}">
                <i class="fas fa-gavel"></i><span>Peraturan</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.kategori-pelanggaran.index') }}"
               class="{{ Request::routeIs('admin.kategori-pelanggaran.*') ? 'active' : '' }}">
                <i class="fas fa-list-ul"></i><span>Kategori Pelanggaran</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.riwayat-pelanggaran.index') }}"
               class="{{ Request::routeIs('admin.riwayat-pelanggaran.*') ? 'active' : '' }}">
                <i class="fas fa-history"></i><span>Riwayat Pelanggaran</span>
            </a>
        </li>
    </ul>
</li>
@endif

{{-- ── KESEHATAN ── --}}
@if(auth()->user()->hasRole('super_admin', 'akademik', 'pamong'))
<li>
    <a href="{{ route('admin.kesehatan-santri.index') }}"
       class="{{ Request::routeIs('admin.kesehatan-santri.*') ? 'active' : '' }}">
        <i class="fas fa-heartbeat"></i><span>Kesehatan</span>
    </a>
</li>
@endif

{{-- ── KEPULANGAN ── --}}
@if(auth()->user()->hasRole('super_admin', 'akademik', 'pamong'))
<li>
    <a href="{{ route('admin.kepulangan.index') }}"
       class="{{ Request::routeIs('admin.kepulangan.*') ? 'active' : '' }}">
        <i class="fas fa-home"></i><span>Kepulangan</span>
    </a>
</li>
@endif

{{-- ── BERITA ── --}}
@if(auth()->user()->hasRole('super_admin', 'akademik'))
<li>
    <a href="{{ route('admin.berita.index') }}"
       class="{{ Request::routeIs('admin.berita.*') ? 'active' : '' }}">
        <i class="fas fa-newspaper"></i><span>Berita</span>
    </a>
</li>
@endif

{{-- ── LOGOUT ── --}}
<li class="logout-item">
    <form action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
        @csrf
        <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
            <i class="fas fa-sign-out-alt"></i><span>Logout</span>
        </a>
    </form>
</li>