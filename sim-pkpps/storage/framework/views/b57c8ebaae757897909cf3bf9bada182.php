

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-cash-register"></i> Kas & Keuangan Pondok</h2>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <a href="<?php echo e(route('admin.keuangan.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Transaksi
        </a>
        <a href="<?php echo e(route('admin.keuangan.laporan')); ?>" class="btn btn-info">
            <i class="fas fa-chart-bar"></i> Laporan Neraca
        </a>
    </div>

    
    <form method="GET" action="<?php echo e(route('admin.keuangan.index')); ?>" id="filterForm" style="margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; align-items: end;">
            <div class="form-group" style="margin-bottom:0;">
                <input type="text" name="search" class="form-control" placeholder="Cari ID / keterangan..."
                       value="<?php echo e(request('search')); ?>">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <select name="jenis" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Jenis</option>
                    <option value="pemasukan" <?php echo e(request('jenis')=='pemasukan'?'selected':''); ?>>Pemasukan</option>
                    <option value="pengeluaran" <?php echo e(request('jenis')=='pengeluaran'?'selected':''); ?>>Pengeluaran</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <select name="bulan" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Bulan</option>
                    <?php for($i = 1; $i <= 12; $i++): ?>
                        <option value="<?php echo e($i); ?>" <?php echo e(request('bulan')==$i?'selected':''); ?>>
                            <?php echo e(\Carbon\Carbon::create()->month($i)->translatedFormat('F')); ?>

                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <input type="number" name="tahun" class="form-control" placeholder="Tahun"
                       value="<?php echo e(request('tahun', date('Y'))); ?>" min="2020" max="2100">
            </div>
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Filter</button>
                <?php if(request()->hasAny(['search','jenis','bulan','tahun'])): ?>
                    <a href="<?php echo e(route('admin.keuangan.index')); ?>" class="btn btn-secondary btn-sm"><i class="fas fa-redo"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </form>

    <?php if($transaksi->count() > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th style="width:10%;">ID</th>
                    <th style="width:12%;">Tanggal</th>
                    <th style="width:10%;">Jenis</th>
                    <th style="width:15%;">Nominal</th>
                    <th>Keterangan</th>
                    <th style="width:10%;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $transaksi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($transaksi->firstItem() + $i); ?></td>
                    <td><strong><?php echo e($item->id_keuangan); ?></strong></td>
                    <td><?php echo e($item->tanggal->format('d/m/Y')); ?></td>
                    <td>
                        <?php if($item->jenis === 'pemasukan'): ?>
                            <span class="badge badge-success"><i class="fas fa-arrow-down"></i> Masuk</span>
                        <?php else: ?>
                            <span class="badge badge-danger"><i class="fas fa-arrow-up"></i> Keluar</span>
                        <?php endif; ?>
                    </td>
                    <td class="nominal-highlight"><?php echo e($item->nominal_format); ?></td>
                    <td><div class="content-preview"><?php echo e($item->keterangan ?? '-'); ?></div></td>
                    <td class="text-center">
                        <div style="display:flex; gap:4px; justify-content:center;">
                            <a href="<?php echo e(route('admin.keuangan.show', $item->id)); ?>" class="btn btn-primary btn-sm" title="Detail"><i class="fas fa-eye"></i></a>
                            <a href="<?php echo e(route('admin.keuangan.edit', $item->id)); ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="<?php echo e(route('admin.keuangan.destroy', $item->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus transaksi ini?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div style="margin-top:20px;"><?php echo e($transaksi->links()); ?></div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-cash-register"></i>
            <h3>Belum Ada Transaksi</h3>
            <p>Tambahkan transaksi keuangan pondok pertama.</p>
            <a href="<?php echo e(route('admin.keuangan.create')); ?>" class="btn btn-success"><i class="fas fa-plus"></i> Tambah</a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/keuangan/index.blade.php ENDPATH**/ ?>