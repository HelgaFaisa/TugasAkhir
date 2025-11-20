<!-- views/admin/dashboardAdmin.blade.php -->


<?php $__env->startSection('title', 'Dashboard Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2>Dashboard Admin</h2>
    <p>Selamat datang di Sistem Informasi Monitoring Santri.</p>
</div>

<div class="row-cards">
    <div class="card card-info">
        <h3>Total Santri</h3>
        <p class="card-value"><?php echo e($data['total_santri']); ?></p>
        <i class="fas fa-user-graduate card-icon"></i>
    </div>
    <div class="card card-success">
        <h3>Total Wali Santri</h3>
        <p class="card-value"><?php echo e($data['total_wali']); ?></p>
        <i class="fas fa-user-shield card-icon"></i>
    </div>
    <div class="card card-warning">
        <h3>Kegiatan Hari Ini</h3>
        <p class="card-value"><?php echo e($data['kegiatan_hari_ini']); ?></p>
        <i class="fas fa-calendar-alt card-icon"></i>
    </div>
    </div>

<div class="content-section">
    <h3>Statistik & Grafik</h3>
    <div class="content-box">
        <p>Area untuk menempatkan statistik dan grafik sistem.</p>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', ['isAdmin' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/dashboardAdmin.blade.php ENDPATH**/ ?>