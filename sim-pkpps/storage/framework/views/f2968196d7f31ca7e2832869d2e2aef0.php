

<li>
    <a href="<?php echo e(route('admin.dashboard')); ?>" class="<?php echo e(Request::routeIs('admin.dashboard') ? 'active' : ''); ?>">
        <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
    </a>
</li>

<!-- DATA MASTER -->
<li class="menu-toggle <?php echo e(Request::routeIs('admin.santri.*') || Request::routeIs('admin.users.*') || Request::routeIs('admin.kelas.*') ? 'active' : ''); ?>">
    <a href="javascript:void(0)" class="menu-parent">
        <i class="fas fa-database"></i><span>Data Master</span>
        <i class="fas fa-chevron-down toggle-icon"></i>
    </a>
    <ul class="submenu">
        <li>
            <a href="<?php echo e(route('admin.santri.index')); ?>" class="<?php echo e(Request::routeIs('admin.santri.index') ? 'active' : ''); ?>">
                <i class="fas fa-user-graduate"></i><span>Data Santri</span>
            </a>
        </li>
        <li>
            <a href="<?php echo e(route('admin.users.santri_accounts')); ?>" class="<?php echo e(Request::routeIs('admin.users.santri_accounts') ? 'active' : ''); ?>">
                <i class="fas fa-user-lock"></i><span>Akun Santri</span>
            </a>
        </li>
        <li>
            <a href="<?php echo e(route('admin.users.wali_accounts')); ?>" class="<?php echo e(Request::routeIs('admin.users.wali_accounts') ? 'active' : ''); ?>">
                <i class="fas fa-user-shield"></i><span>Akun Wali Santri</span>
            </a>
        </li>
        <li>
            <a href="<?php echo e(route('admin.kelas.index')); ?>" class="<?php echo e(Request::routeIs('admin.kelas.*') ? 'active' : ''); ?>">
                <i class="fas fa-layer-group"></i><span>Kelola Kelas</span>
            </a>
        </li>
    </ul>
</li>

<!-- CAPAIAN AL-QUR'AN & HADIST (BARU!) -->
<li class="menu-toggle <?php echo e(Request::routeIs('admin.materi.*') || Request::routeIs('admin.semester.*') || Request::routeIs('admin.capaian.*') ? 'active' : ''); ?>">
    <a href="javascript:void(0)" class="menu-parent">
        <i class="fas fa-book-quran"></i><span>Capaian Al-Qur'an</span>
        <i class="fas fa-chevron-down toggle-icon"></i>
    </a>
    <ul class="submenu">
        <li>
            <a href="<?php echo e(route('admin.capaian.dashboard')); ?>" class="<?php echo e(Request::routeIs('admin.capaian.dashboard') ? 'active' : ''); ?>">
                <i class="fas fa-chart-pie"></i><span>Dashboard Capaian</span>
            </a>
        </li>
        <li>
            <a href="<?php echo e(route('admin.materi.index')); ?>" class="<?php echo e(Request::routeIs('admin.materi.*') || Request::routeIs('admin.semester.*') ? 'active' : ''); ?>">
                <i class="fas fa-book"></i><span>Master Materi</span>
            </a>
        </li>
        <li>
            <a href="<?php echo e(route('admin.capaian.index')); ?>" class="<?php echo e(Request::routeIs('admin.capaian.index') || Request::routeIs('admin.capaian.show') ? 'active' : ''); ?>">
                <i class="fas fa-clipboard-list"></i><span>Data Capaian</span>
            </a>
        </li>
    </ul>
</li>

<!-- KEGIATAN SANTRI -->
<li class="menu-toggle <?php echo e(Request::routeIs('admin.kategori-kegiatan.*') || 
    Request::routeIs('admin.kegiatan.*') || 
    Request::routeIs('admin.kartu-rfid.*') || 
    Request::routeIs('admin.laporan-kegiatan.*') || 
    Request::routeIs('admin.riwayat-kegiatan.*') 
    ? 'active' : ''); ?>">
    <a href="javascript:void(0)" class="menu-parent">
        <i class="fas fa-calendar-alt"></i><span>Kegiatan Santri</span>
        <i class="fas fa-chevron-down toggle-icon"></i>
    </a>
    <ul class="submenu">
    <li>
        <a href="<?php echo e(route('admin.kegiatan.index')); ?>" 
           class="<?php echo e(Request::routeIs('admin.kegiatan.index') ? 'active' : ''); ?>">
            <i class="fas fa-tachometer-alt"></i><span>Dashboard Kegiatan</span>
        </a>
    </li>

    <li>
        <a href="<?php echo e(route('admin.laporan-kegiatan.index')); ?>" 
           class="<?php echo e(Request::routeIs('admin.laporan-kegiatan.*') || Request::routeIs('admin.riwayat-kegiatan.*') ? 'active' : ''); ?>">
            <i class="fas fa-chart-line"></i><span>Laporan & Analitik</span>
        </a>
    </li>

    <li>
        <a href="<?php echo e(route('admin.kartu-rfid.index')); ?>" 
           class="<?php echo e(Request::routeIs('admin.kartu-rfid.*') ? 'active' : ''); ?>">
            <i class="fas fa-id-card"></i><span>Kartu RFID</span>
        </a>
    </li>
</ul>
</li>

<!-- PELANGGARAN -->
<li class="menu-toggle <?php echo e(Request::routeIs('admin.kategori-pelanggaran.*') || Request::routeIs('admin.riwayat-pelanggaran.*') ? 'active' : ''); ?>">
    <a href="javascript:void(0)" class="menu-parent">
        <i class="fas fa-exclamation-triangle"></i><span>Pelanggaran</span>
        <i class="fas fa-chevron-down toggle-icon"></i>
    </a>
    <ul class="submenu">
        <li>
            <a href="<?php echo e(route('admin.kategori-pelanggaran.index')); ?>" 
               class="<?php echo e(Request::routeIs('admin.kategori-pelanggaran.*') ? 'active' : ''); ?>">
                <i class="fas fa-list-ul"></i><span>Kategori Pelanggaran</span>
            </a>
        </li>
        <li>
            <a href="<?php echo e(route('admin.riwayat-pelanggaran.index')); ?>" 
               class="<?php echo e(Request::routeIs('admin.riwayat-pelanggaran.*') ? 'active' : ''); ?>">
                <i class="fas fa-history"></i><span>Riwayat Pelanggaran</span>
            </a>
        </li>
    </ul>
</li>

<!-- ADMINISTRASI -->
<li class="menu-toggle <?php echo e(Request::is('admin/pembayaran-spp*') || Request::is('admin/uang-saku*') || Request::is('admin/keuangan*') ? 'active' : ''); ?>">
    <a href="javascript:void(0)" class="menu-parent">
        <i class="fas fa-money-bill-wave"></i><span>Administrasi</span>
        <i class="fas fa-chevron-down toggle-icon"></i>
    </a>
    <ul class="submenu">
        <li>
            <a href="<?php echo e(route('admin.pembayaran-spp.index')); ?>"
                class="<?php echo e(Request::is('admin/pembayaran-spp*') ? 'active' : ''); ?>">
                <i class="fas fa-file-invoice-dollar"></i><span>Pembayaran SPP</span>
            </a>
        </li>
        <li>
            <a href="<?php echo e(route('admin.keuangan.index')); ?>"
                class="<?php echo e(Request::is('admin/keuangan*') ? 'active' : ''); ?>">
                <i class="fas fa-cash-register"></i><span>Kas & Keuangan</span>
            </a>
        </li>
        <li>
            <a href="<?php echo e(route('admin.uang-saku.index')); ?>"
                class="<?php echo e(Request::is('admin/uang-saku*') ? 'active' : ''); ?>">
                <i class="fas fa-wallet"></i><span>Uang Saku Santri</span>
            </a>
        </li>
    </ul>
</li>

<!-- KESEHATAN SANTRI -->
<li>
    <a href="<?php echo e(route('admin.kesehatan-santri.index')); ?>" 
       class="<?php echo e(Request::routeIs('admin.kesehatan-santri.*') ? 'active' : ''); ?>">
        <i class="fas fa-heartbeat"></i><span>Kesehatan Santri</span>
    </a>
</li>

<!-- KEPULANGAN SANTRI -->
<li>
    <a href="<?php echo e(route('admin.kepulangan.index')); ?>" 
       class="<?php echo e(Request::routeIs('admin.kepulangan.*') ? 'active' : ''); ?>">
        <i class="fas fa-home"></i><span>Kepulangan Santri</span>
    </a>
</li>

<!-- BERITA -->
<li>
    <a href="<?php echo e(route('admin.berita.index')); ?>" 
       class="<?php echo e(Request::routeIs('admin.berita.*') ? 'active' : ''); ?>">
        <i class="fas fa-newspaper"></i><span>Berita</span>
    </a>
</li>

<!-- LOGOUT -->
<li class="logout-item">
    <form action="<?php echo e(route('admin.logout')); ?>" method="POST" style="display: inline;">
        <?php echo csrf_field(); ?>
        <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
            <i class="fas fa-sign-out-alt"></i><span>Logout</span>
        </a>
    </form>
</li><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/layouts/admin-sidebar.blade.php ENDPATH**/ ?>