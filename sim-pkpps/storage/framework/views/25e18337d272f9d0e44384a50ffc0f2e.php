

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-wallet"></i> Manajemen Uang Saku Santri</h2>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?></div>
<?php endif; ?>

<div class="content-box">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
        <a href="<?php echo e(route('admin.uang-saku.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Transaksi
        </a>
        <form method="GET" action="<?php echo e(route('admin.uang-saku.index')); ?>" style="display:flex; gap:8px;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama / ID santri..."
                   value="<?php echo e(request('search')); ?>" style="width:250px;">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
            <?php if(request('search')): ?>
                <a href="<?php echo e(route('admin.uang-saku.index')); ?>" class="btn btn-secondary btn-sm"><i class="fas fa-redo"></i></a>
            <?php endif; ?>
        </form>
    </div>

    
    <?php if($santriList->count() > 0): ?>
        <?php $__currentLoopData = $santriList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="content-box" style="margin-bottom:12px; padding:16px;">
            
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; cursor:pointer;"
                 onclick="toggleDetail('detail-<?php echo e($santri->id_santri); ?>', this)">
                <div style="display:flex; align-items:center; gap:12px;">
                    <i class="fas fa-chevron-right toggle-arrow" style="transition:transform .2s; color:var(--text-light);"></i>
                    <div>
                        <strong><?php echo e($santri->nama_lengkap); ?></strong>
                        <small class="text-muted" style="margin-left:6px;"><?php echo e($santri->id_santri); ?></small>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
                    <span style="font-weight:700; font-size:1.1rem; color:<?php echo e($santri->saldo_terakhir >= 0 ? '#6FBA9D' : '#FF8B94'); ?>;">
                        Rp <?php echo e(number_format($santri->saldo_terakhir, 0, ',', '.')); ?>

                    </span>
                    <span class="badge badge-info"><?php echo e($santri->transaksi_bulan_ini); ?> transaksi bln ini</span>
                    <div style="display:flex; gap:6px;">
                        <a href="<?php echo e(route('admin.uang-saku.riwayat', $santri->id_santri)); ?>" class="btn btn-primary btn-sm" title="Riwayat Lengkap" onclick="event.stopPropagation();">
                            <i class="fas fa-history"></i>
                        </a>
                    </div>
                </div>
            </div>

            
            <div id="detail-<?php echo e($santri->id_santri); ?>" style="display:none; margin-top:12px; border-top:1px solid var(--primary-light); padding-top:12px;">
                <?php if($santri->transaksi_terbaru->isNotEmpty()): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                                <th>Saldo</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $santri->transaksi_terbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($tx->tanggal_transaksi->format('d/m/Y')); ?></td>
                                <td>
                                    <?php if($tx->jenis_transaksi === 'pemasukan'): ?>
                                        <span class="badge badge-success"><i class="fas fa-arrow-down"></i> Masuk</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><i class="fas fa-arrow-up"></i> Keluar</span>
                                    <?php endif; ?>
                                </td>
                                <td class="nominal-highlight"><?php echo e($tx->nominal_format); ?></td>
                                <td><div class="content-preview"><?php echo e($tx->keterangan ?? '-'); ?></div></td>
                                <td style="color:<?php echo e($tx->saldo_sesudah >= 0 ? '#6FBA9D' : '#FF8B94'); ?>; font-weight:600;">
                                    Rp <?php echo e(number_format($tx->saldo_sesudah, 0, ',', '.')); ?>

                                </td>
                                <td class="text-center">
                                    <div style="display:flex; gap:4px; justify-content:center;">
                                        <a href="<?php echo e(route('admin.uang-saku.show', $tx->id)); ?>" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>
                                        <a href="<?php echo e(route('admin.uang-saku.edit', $tx->id)); ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                        <form action="<?php echo e(route('admin.uang-saku.destroy', $tx->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php if($santri->transaksi_terbaru->count() >= 5): ?>
                        <div style="text-align:center; margin-top:8px;">
                            <a href="<?php echo e(route('admin.uang-saku.riwayat', $santri->id_santri)); ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-right"></i> Lihat Semua Riwayat
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted">Belum ada transaksi.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div style="margin-top:20px;"><?php echo e($santriList->links()); ?></div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-wallet"></i>
            <h3>Belum Ada Data</h3>
            <p>Belum ada santri dengan transaksi uang saku.</p>
            <a href="<?php echo e(route('admin.uang-saku.create')); ?>" class="btn btn-success"><i class="fas fa-plus"></i> Tambah Transaksi</a>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleDetail(id, el) {
    var detail = document.getElementById(id);
    var arrow = el.querySelector('.toggle-arrow');
    if (detail.style.display === 'none') {
        detail.style.display = 'block';
        arrow.style.transform = 'rotate(90deg)';
    } else {
        detail.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/uang-saku/index.blade.php ENDPATH**/ ?>