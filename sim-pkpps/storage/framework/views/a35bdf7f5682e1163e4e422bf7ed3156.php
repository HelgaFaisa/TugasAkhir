<!-- views/layouts/santri-wali-sidebar.blade.php -->
<ul class="sidebar-menu">
    
    <li>
        <a href="<?php echo e(route('santri.dashboard')); ?>" class="<?php echo e(Request::routeIs('santri.dashboard') ? 'active' : ''); ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard Progres</span>
        </a>
    </li>

    
    <li>
        <a href="<?php echo e(route('santri.profil.index')); ?>" class="<?php echo e(Request::routeIs('santri.profil.*') ? 'active' : ''); ?>">
            <i class="fas fa-user"></i>
            <span>Profil Santri</span>
        </a>
    </li>

    
    <li>
        <a href="<?php echo e(route('santri.kegiatan.index')); ?>" class="<?php echo e(Request::routeIs('santri.kegiatan.*') ? 'active' : ''); ?>">
            <i class="fas fa-calendar-check"></i>
            <span>Kegiatan & Absensi</span>
        </a>
    </li>

    
    <li>
        <a href="<?php echo e(route('santri.capaian.index')); ?>" class="<?php echo e(request()->routeIs('santri.capaian.*') ? 'active' : ''); ?>">
            <i class="fas fa-book-reader"></i>
            <span>Capaian Materi</span>
        </a>
    </li>

    
    <li>
        <a href="<?php echo e(route('santri.pelanggaran.index')); ?>" class="<?php echo e(Request::routeIs('santri.pelanggaran.*') ? 'active' : ''); ?>">
            <i class="fas fa-exclamation-circle"></i>
            <span>Riwayat Pelanggaran</span>
        </a>
    </li>

    
    <li>
        <a href="<?php echo e(route('santri.uang-saku.index')); ?>" class="<?php echo e(Request::routeIs('santri.uang-saku.*') ? 'active' : ''); ?>">
            <i class="fas fa-wallet"></i>
            <span>Riwayat Uang Saku</span>
        </a>
    </li>

    
    <li>
        <a href="<?php echo e(route('santri.kesehatan.index')); ?>" class="<?php echo e(request()->routeIs('santri.kesehatan.*') ? 'active' : ''); ?>">
            <i class="fas fa-heartbeat"></i>
            <span>Riwayat Kesehatan</span>
        </a>
    </li>

    
        <li>
            <a href="<?php echo e(route('santri.kepulangan.index')); ?>" class="<?php echo e(request()->routeIs('santri.kepulangan.*') ? 'active' : ''); ?>">
                <i class="fas fa-home"></i>
                <span>Riwayat Kepulangan</span>
            </a>
        </li>

    
    <li>
        <a href="<?php echo e(route('santri.berita.index')); ?>" class="<?php echo e(request()->routeIs('santri.berita.*') ? 'active' : ''); ?>">
            <i class="fas fa-newspaper"></i>
            <span>Berita</span>
        </a>
    </li>
    <li>

    
    <li class="logout-item">
        <form action="<?php echo e(route('santri.logout')); ?>" method="POST" id="logout-form-santri" style="display: none;">
            <?php echo csrf_field(); ?>
        </form>
        <a href="#" onclick="event.preventDefault(); if(confirm('Yakin ingin logout?')) document.getElementById('logout-form-santri').submit();">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </li>
</ul><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/layouts/santri-wali-sidebar.blade.php ENDPATH**/ ?>