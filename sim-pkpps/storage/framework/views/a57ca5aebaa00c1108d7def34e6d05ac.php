
<div class="row-cards row-cards-5">
    <div class="card card-info">
        <h3>Santri Aktif</h3>
        <p class="card-value"><?php echo e($kpi['totalSantriAktif']); ?></p>
        <i class="fas fa-user-graduate card-icon"></i>
    </div>

    <div class="card <?php echo e($kpi['belumAbsensi'] > 0 ? 'card-warning' : 'card-success'); ?>">
        <h3>Kegiatan Hari Ini</h3>
        <p class="card-value"><?php echo e($kpi['totalKegiatan']); ?></p>
        <span class="card-sub"><?php echo e($kpi['sudahAbsensi']); ?> sudah absen &middot; <?php echo e($kpi['belumAbsensi']); ?> belum</span>
        <i class="fas fa-calendar-check card-icon"></i>
    </div>

    <div class="card <?php echo e($kpi['santriSakit'] > 0 ? 'card-danger' : 'card-success'); ?>">
        <h3>Santri di UKP</h3>
        <p class="card-value"><?php echo e($kpi['santriSakit']); ?></p>
        <span class="card-sub">sedang dirawat</span>
        <i class="fas fa-briefcase-medical card-icon"></i>
    </div>

    <div class="card <?php echo e($kpi['kepulanganMenunggu'] > 0 ? 'card-warning' : 'card-success'); ?>">
        <h3>Menunggu Approval</h3>
        <p class="card-value"><?php echo e($kpi['kepulanganMenunggu']); ?></p>
        <span class="card-sub">pengajuan kepulangan</span>
        <i class="fas fa-clock card-icon"></i>
    </div>

    <div class="card <?php echo e($kpi['santriTanpaWali'] > 0 ? 'card-secondary' : 'card-success'); ?>">
        <h3>Belum Ada Akun Wali</h3>
        <p class="card-value"><?php echo e($kpi['santriTanpaWali']); ?></p>
        <span class="card-sub">santri tanpa wali mobile</span>
        <i class="fas fa-user-plus card-icon"></i>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/dashboard/_kpi-cards.blade.php ENDPATH**/ ?>