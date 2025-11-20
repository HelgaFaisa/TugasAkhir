


<?php $__env->startSection('title', 'Riwayat Pelanggaran'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-exclamation-circle"></i> Riwayat Pelanggaran Saya</h2>
</div>


<div class="row-cards">
    <div class="card card-danger">
        <h3>Total Pelanggaran</h3>
        <div class="card-value"><?php echo e($totalPelanggaran); ?></div>
        <i class="fas fa-exclamation-triangle card-icon"></i>
    </div>
    
    <div class="card card-warning">
        <h3>Total Poin</h3>
        <div class="card-value"><?php echo e($totalPoin); ?></div>
        <i class="fas fa-star card-icon"></i>
    </div>
    
    <div class="card card-info">
        <h3>Pelanggaran Bulan Ini</h3>
        <div class="card-value"><?php echo e($pelanggaranBulanIni); ?></div>
        <i class="fas fa-calendar-alt card-icon"></i>
    </div>
</div>


<div class="content-box" style="margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <form method="GET" action="<?php echo e(route('santri.pelanggaran.index')); ?>" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <input type="date" name="tanggal_mulai" class="form-control" style="width: auto;" value="<?php echo e(request('tanggal_mulai')); ?>" placeholder="Tanggal Mulai">
            <input type="date" name="tanggal_selesai" class="form-control" style="width: auto;" value="<?php echo e(request('tanggal_selesai')); ?>" placeholder="Tanggal Selesai">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-filter"></i> Filter
            </button>
            <a href="<?php echo e(route('santri.pelanggaran.index')); ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-redo"></i> Reset
            </a>
            <a href="<?php echo e(route('santri.pelanggaran.index', ['bulan_ini' => 1])); ?>" class="btn btn-info btn-sm">
                <i class="fas fa-calendar-check"></i> Bulan Ini
            </a>
        </form>
        
        <a href="<?php echo e(route('santri.pelanggaran.kategori')); ?>" class="btn btn-warning btn-sm">
            <i class="fas fa-list"></i> Lihat Daftar Kategori Pelanggaran
        </a>
    </div>
</div>


<div class="content-box">
    <?php if($riwayat->count() > 0): ?>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 10%;">ID Riwayat</th>
                        <th style="width: 12%;">Tanggal</th>
                        <th style="width: 25%;">Jenis Pelanggaran</th>
                        <th style="width: 8%;">Poin</th>
                        <th style="width: 30%;">Keterangan</th>
                        <th style="width: 10%;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $riwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($riwayat->firstItem() + $index); ?></td>
                        <td><strong><?php echo e($item->id_riwayat); ?></strong></td>
                        <td>
                            <i class="fas fa-calendar"></i> 
                            <?php echo e(\Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMM YYYY')); ?>

                        </td>
                        <td><?php echo e($item->kategori->nama_pelanggaran ?? '-'); ?></td>
                        <td>
                            <span class="badge badge-danger badge-lg">
                                <i class="fas fa-star"></i> <?php echo e($item->poin); ?>

                            </span>
                        </td>
                        <td>
                            <div class="content-preview">
                                <?php echo e($item->keterangan ?: '-'); ?>

                            </div>
                        </td>
                        <td class="text-center">
                            <a href="<?php echo e(route('santri.pelanggaran.show', $item->id)); ?>" 
                               class="btn btn-info btn-sm" 
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        
        
        <div style="margin-top: 20px;">
            <?php echo e($riwayat->links()); ?>

        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-check-circle"></i>
            <h3>Tidak Ada Riwayat Pelanggaran</h3>
            <p>Selamat! Anda belum memiliki catatan pelanggaran.</p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/pelanggaran/index.blade.php ENDPATH**/ ?>