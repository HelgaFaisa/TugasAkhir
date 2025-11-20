


<?php $__env->startSection('title', 'Daftar Kategori Pelanggaran'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-list-ul"></i> Daftar Kategori Pelanggaran & Poin</h2>
</div>

<div class="content-box">
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <p class="text-muted">
            <i class="fas fa-info-circle"></i> 
            Berikut adalah daftar kategori pelanggaran beserta poin yang berlaku di pondok.
        </p>
        <a href="<?php echo e(route('santri.pelanggaran.index')); ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
        </a>
    </div>

    <?php if($kategoriList->count() > 0): ?>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">No</th>
                        <th style="width: 15%;">Kode</th>
                        <th style="width: 57%;">Jenis Pelanggaran</th>
                        <th style="width: 20%; text-align: center;">Poin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $kategoriList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><strong><?php echo e($kategori->id_kategori); ?></strong></td>
                        <td><?php echo e($kategori->nama_pelanggaran); ?></td>
                        <td class="text-center">
                            <?php if($kategori->poin <= 5): ?>
                                <span class="badge badge-warning badge-lg">
                                    <i class="fas fa-star"></i> <?php echo e($kategori->poin); ?> Poin
                                </span>
                            <?php elseif($kategori->poin <= 15): ?>
                                <span class="badge badge-danger badge-lg">
                                    <i class="fas fa-star"></i> <?php echo e($kategori->poin); ?> Poin
                                </span>
                            <?php else: ?>
                                <span class="badge badge-danger badge-lg" style="background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);">
                                    <i class="fas fa-star"></i> <?php echo e($kategori->poin); ?> Poin
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-list"></i>
            <h3>Belum Ada Kategori</h3>
            <p>Daftar kategori pelanggaran belum tersedia.</p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/pelanggaran/kategori.blade.php ENDPATH**/ ?>