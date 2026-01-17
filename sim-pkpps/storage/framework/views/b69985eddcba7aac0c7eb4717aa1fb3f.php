

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-book-open"></i> Master Materi Al-Qur'an & Hadist</h2>
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


<div class="content-box" style="margin-bottom: 20px;">
    <form method="GET" action="<?php echo e(route('admin.materi.index')); ?>" class="filter-form-inline">
        <select name="kategori" class="form-control" style="width: 200px;">
            <option value="">Semua Kategori</option>
            <option value="Al-Qur'an" <?php echo e(request('kategori') == 'Al-Qur\'an' ? 'selected' : ''); ?>>Al-Qur'an</option>
            <option value="Hadist" <?php echo e(request('kategori') == 'Hadist' ? 'selected' : ''); ?>>Hadist</option>
            <option value="Materi Tambahan" <?php echo e(request('kategori') == 'Materi Tambahan' ? 'selected' : ''); ?>>Materi Tambahan</option>
        </select>

        <select name="kelas" class="form-control" style="width: 180px;">
            <option value="">Semua Kelas</option>
            <option value="Lambatan" <?php echo e(request('kelas') == 'Lambatan' ? 'selected' : ''); ?>>Lambatan</option>
            <option value="Cepatan" <?php echo e(request('kelas') == 'Cepatan' ? 'selected' : ''); ?>>Cepatan</option>
            <option value="PB" <?php echo e(request('kelas') == 'PB' ? 'selected' : ''); ?>>PB</option>
        </select>

        <input type="text" name="search" class="form-control" placeholder="Cari nama kitab..." 
               value="<?php echo e(request('search')); ?>" style="width: 250px;">

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Filter
        </button>

        <?php if(request()->anyFilled(['kategori', 'kelas', 'search'])): ?>
            <a href="<?php echo e(route('admin.materi.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
        <?php endif; ?>

        <a href="<?php echo e(route('admin.materi.create')); ?>" class="btn btn-success" style="margin-left: auto;">
            <i class="fas fa-plus"></i> Tambah Materi
        </a>
    </form>
</div>


<div class="content-box">
    <?php if($materis->count() > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 10%;">ID Materi</th>
                    <th style="width: 15%;">Kategori</th>
                    <th style="width: 10%;">Kelas</th>
                    <th style="width: 25%;">Nama Kitab</th>
                    <th style="width: 15%;">Halaman</th>
                    <th style="width: 10%;">Total Hal</th>
                    <th class="text-center" style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $materis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $materi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($materis->firstItem() + $index); ?></td>
                        <td><strong><?php echo e($materi->id_materi); ?></strong></td>
                        <td><?php echo $materi->kategori_badge; ?></td>
                        <td><?php echo $materi->kelas_badge; ?></td>
                        <td><?php echo e($materi->nama_kitab); ?></td>
                        <td>
                            <span class="badge badge-info">
                                <?php echo e($materi->halaman_mulai); ?> - <?php echo e($materi->halaman_akhir); ?>

                            </span>
                        </td>
                        <td class="text-center">
                            <strong><?php echo e($materi->total_halaman); ?></strong> hal
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="<?php echo e(route('admin.materi.show', $materi)); ?>" 
                                   class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.materi.edit', $materi)); ?>" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('admin.materi.destroy', $materi)); ?>" 
                                      method="POST" style="display: inline-block;"
                                      onsubmit="return confirm('Yakin ingin menghapus materi <?php echo e($materi->nama_kitab); ?>?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        
        <div style="margin-top: 20px;">
            <?php echo e($materis->links()); ?>

        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-book-open"></i>
            <h3>Belum Ada Data Materi</h3>
            <p>Silakan tambahkan materi pembelajaran Al-Qur'an, Hadist, atau Materi Tambahan.</p>
            <a href="<?php echo e(route('admin.materi.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Materi Pertama
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/materi/index.blade.php ENDPATH**/ ?>