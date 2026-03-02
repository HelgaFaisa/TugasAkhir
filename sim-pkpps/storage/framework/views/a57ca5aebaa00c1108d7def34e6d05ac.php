
<div class="row-cards row-cards-5" style="margin-bottom:16px;">

    <div class="card card-info">
        <h3>Santri Aktif</h3>
        <div class="card-value"><?php echo e($kpi['totalSantriAktif']); ?></div>
        <span class="card-sub">terdaftar &amp; aktif</span>
        <i class="fas fa-user-graduate card-icon"></i>
    </div>

    <div class="card <?php echo e($kpi['belumAbsensi'] > 0 ? 'card-warning' : 'card-success'); ?>">
        <h3>Kegiatan Hari Ini</h3>
        <div class="card-value"><?php echo e($kpi['totalKegiatan']); ?></div>
        <span class="card-sub">
            <span style="color:#27ae60;font-weight:700;"><?php echo e($kpi['sudahAbsensi']); ?> absen</span>
            &nbsp;·&nbsp;
            <span style="<?php echo e($kpi['belumAbsensi'] > 0 ? 'color:#e67e22;font-weight:700;' : ''); ?>"><?php echo e($kpi['belumAbsensi']); ?> belum</span>
        </span>
        <i class="fas fa-calendar-check card-icon"></i>
    </div>

    <div class="card <?php echo e($kpi['santriSakit'] > 0 ? 'card-danger' : 'card-success'); ?>">
        <h3>Santri di UKP</h3>
        <div class="card-value"><?php echo e($kpi['santriSakit']); ?></div>
        <span class="card-sub">sedang dirawat</span>
        <i class="fas fa-briefcase-medical card-icon"></i>
    </div>

    <div class="card <?php echo e($kpi['kepulanganMenunggu'] > 0 ? 'card-warning' : 'card-success'); ?>">
        <h3>Menunggu Approval</h3>
        <div class="card-value"><?php echo e($kpi['kepulanganMenunggu']); ?></div>
        <span class="card-sub">pengajuan kepulangan</span>
        <i class="fas fa-clock card-icon"></i>
    </div>

    <?php if(auth()->user()->isSuperAdmin()): ?>
    <div class="card <?php echo e($kpi['santriTanpaWali'] > 0 ? 'card-secondary' : 'card-success'); ?>">
        <h3>Belum Ada Akun Wali</h3>
        <div class="card-value"><?php echo e($kpi['santriTanpaWali']); ?></div>
        <span class="card-sub">santri tanpa wali mobile</span>
        <i class="fas fa-user-plus card-icon"></i>
    </div>
    <?php endif; ?>

</div><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/dashboard/_kpi-cards.blade.php ENDPATH**/ ?>