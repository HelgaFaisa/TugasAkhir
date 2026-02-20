

<?php $__env->startSection('content'); ?>
<?php
    $namaBulan = \Carbon\Carbon::create()->month($bulan)->translatedFormat('F');
?>

<div class="page-header">
    <h2><i class="fas fa-chart-bar"></i> Laporan Neraca Keuangan</h2>
    <p>Periode: <?php echo e($namaBulan); ?> <?php echo e($tahun); ?></p>
</div>


<div class="content-box" style="margin-bottom:20px;">
    <form method="GET" action="<?php echo e(route('admin.keuangan.laporan')); ?>" style="display:flex; gap:12px; align-items:end; flex-wrap:wrap;">
        <div class="form-group" style="margin-bottom:0;">
            <label>Bulan</label>
            <select name="bulan" class="form-control">
                <?php for($i = 1; $i <= 12; $i++): ?>
                    <option value="<?php echo e($i); ?>" <?php echo e($bulan==$i?'selected':''); ?>>
                        <?php echo e(\Carbon\Carbon::create()->month($i)->translatedFormat('F')); ?>

                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label>Tahun</label>
            <input type="number" name="tahun" class="form-control" value="<?php echo e($tahun); ?>" min="2020" max="2100" style="width:100px;">
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
        <a href="<?php echo e(route('admin.keuangan.index')); ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    </form>
</div>


<div class="row-cards" style="grid-template-columns: repeat(4, 1fr);">
    <div class="card card-info">
        <h3>SPP Terkumpul</h3>
        <p class="card-value-small">Rp <?php echo e(number_format($sppTerkumpul, 0, ',', '.')); ?></p>
        <i class="fas fa-file-invoice-dollar card-icon"></i>
    </div>
    <div class="card card-success">
        <h3>Pemasukan Lain</h3>
        <p class="card-value-small">Rp <?php echo e(number_format($pemasukanPondok, 0, ',', '.')); ?></p>
        <i class="fas fa-arrow-down card-icon"></i>
    </div>
    <div class="card card-danger">
        <h3>Total Pengeluaran</h3>
        <p class="card-value-small">Rp <?php echo e(number_format($pengeluaranPondok, 0, ',', '.')); ?></p>
        <i class="fas fa-arrow-up card-icon"></i>
    </div>
    <div class="card <?php echo e($sisaKas >= 0 ? 'card-primary' : 'card-danger'); ?>">
        <h3>Sisa Kas</h3>
        <p class="card-value-small">Rp <?php echo e(number_format($sisaKas, 0, ',', '.')); ?></p>
        <i class="fas fa-wallet card-icon"></i>
    </div>
</div>


<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:24px;">

    
    <div class="content-box">
        <h4 style="margin-bottom:12px;"><i class="fas fa-arrow-up" style="color:var(--danger-color);"></i> Pengeluaran Terbesar</h4>
        <?php if($detailPengeluaran->count() > 0): ?>
            <table class="data-table">
                <thead><tr><th>Tanggal</th><th>Keterangan</th><th>Nominal</th></tr></thead>
                <tbody>
                    <?php $__currentLoopData = $detailPengeluaran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($item->tanggal->format('d/m')); ?></td>
                        <td><?php echo e($item->keterangan ?? '-'); ?></td>
                        <td class="nominal-highlight"><?php echo e($item->nominal_format); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">Tidak ada pengeluaran bulan ini.</p>
        <?php endif; ?>
    </div>

    
    <div class="content-box">
        <h4 style="margin-bottom:12px;"><i class="fas fa-arrow-down" style="color:var(--success-color);"></i> Pemasukan Non-SPP</h4>
        <?php if($detailPemasukan->count() > 0): ?>
            <table class="data-table">
                <thead><tr><th>Tanggal</th><th>Keterangan</th><th>Nominal</th></tr></thead>
                <tbody>
                    <?php $__currentLoopData = $detailPemasukan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($item->tanggal->format('d/m')); ?></td>
                        <td><?php echo e($item->keterangan ?? '-'); ?></td>
                        <td class="nominal-highlight"><?php echo e($item->nominal_format); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">Tidak ada pemasukan non-SPP bulan ini.</p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/keuangan/laporan.blade.php ENDPATH**/ ?>