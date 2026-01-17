


<?php $__env->startSection('title', 'Pembayaran SPP'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-money-bill-wave"></i> Pembayaran SPP</h2>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>

<div class="content-box">
    <!-- Header Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <!-- Search & Filter Form -->
        <form method="GET" action="<?php echo e(route('admin.pembayaran-spp.index')); ?>" style="display: flex; gap: 10px; flex-wrap: wrap; flex: 1;">
            <input type="text" 
                   name="search" 
                   class="form-control" 
                   placeholder="Cari santri atau ID..." 
                   value="<?php echo e(request('search')); ?>"
                   style="max-width: 250px;">
            
            <select name="status" class="form-control" style="max-width: 180px;">
                <option value="">Semua Status</option>
                <option value="Lunas" <?php echo e(request('status') === 'Lunas' ? 'selected' : ''); ?>>Lunas</option>
                <option value="Belum Lunas" <?php echo e(request('status') === 'Belum Lunas' ? 'selected' : ''); ?>>Belum Lunas</option>
                <option value="Telat" <?php echo e(request('status') === 'Telat' ? 'selected' : ''); ?>>Telat</option>
            </select>

            <select name="bulan" class="form-control" style="max-width: 150px;">
                <option value="">Semua Bulan</option>
                <?php for($i = 1; $i <= 12; $i++): ?>
                    <option value="<?php echo e($i); ?>" <?php echo e(request('bulan') == $i ? 'selected' : ''); ?>>
                        <?php echo e(DateTime::createFromFormat('!m', $i)->format('F')); ?>

                    </option>
                <?php endfor; ?>
            </select>

            <select name="tahun" class="form-control" style="max-width: 120px;">
                <option value="">Semua Tahun</option>
                <?php $__currentLoopData = $tahunList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tahun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($tahun); ?>" <?php echo e(request('tahun') == $tahun ? 'selected' : ''); ?>>
                        <?php echo e($tahun); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-search"></i> Cari
            </button>
            
            <?php if(request()->hasAny(['search', 'status', 'bulan', 'tahun'])): ?>
                <a href="<?php echo e(route('admin.pembayaran-spp.index')); ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times"></i> Reset
                </a>
            <?php endif; ?>
        </form>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 10px;">
            <a href="<?php echo e(route('admin.pembayaran-spp.generate')); ?>" class="btn btn-warning btn-sm hover-shadow">
                <i class="fas fa-cogs"></i> Generate SPP
            </a>
            <a href="<?php echo e(route('admin.pembayaran-spp.create')); ?>" class="btn btn-success btn-sm hover-shadow">
                <i class="fas fa-plus-circle"></i> Tambah Data
            </a>
        </div>
    </div>

    <!-- Table -->
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pembayaran</th>
                    <th>Santri</th>
                    <th>Periode</th>
                    <th>Nominal</th>
                    <th>Batas Bayar</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $pembayaranSpp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $spp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($pembayaranSpp->firstItem() + $index); ?></td>
                        <td><strong><?php echo e($spp->id_pembayaran); ?></strong></td>
                        <td>
                            <strong><?php echo e($spp->santri->nama_lengkap); ?></strong><br>
                            <small class="text-muted"><?php echo e($spp->santri->id_santri); ?> - <?php echo e($spp->santri->kelas); ?></small>
                        </td>
                        <td><?php echo e($spp->periode_lengkap); ?></td>
                        <td><strong><?php echo e($spp->nominal_format); ?></strong></td>
                        <td>
                            <?php echo e($spp->batas_bayar->format('d/m/Y')); ?>

                            <?php if($spp->isTelat()): ?>
                                <br><small class="text-muted" style="color: #FF8B94 !important;">
                                    <i class="fas fa-exclamation-triangle"></i> Telat
                                </small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $spp->status_badge; ?></td>
                        <td class="text-center">
                            <a href="<?php echo e(route('admin.pembayaran-spp.show', $spp->id)); ?>" 
                               class="btn btn-sm btn-primary" 
                               title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo e(route('admin.pembayaran-spp.edit', $spp->id)); ?>" 
                               class="btn btn-sm btn-warning" 
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('admin.pembayaran-spp.destroy', $spp->id)); ?>" 
                                  method="POST" 
                                  style="display: inline-block;"
                                  onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 40px;">
                            <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; display: block; margin-bottom: 15px;"></i>
                            <p style="color: #999;">Tidak ada data pembayaran SPP.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if($pembayaranSpp->hasPages()): ?>
        <div style="margin-top: 20px;">
            <?php echo e($pembayaranSpp->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/pembayaran-spp/index.blade.php ENDPATH**/ ?>