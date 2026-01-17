

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-calendar-alt"></i> Manajemen Semester</h2>
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


<div class="content-header-flex">
    <a href="<?php echo e(route('admin.semester.create')); ?>" class="btn btn-success">
        <i class="fas fa-plus"></i> Tambah Semester
    </a>
</div>


<div class="content-box">
    <?php if($semesters->count() > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 10%;">ID Semester</th>
                    <th style="width: 25%;">Nama Semester</th>
                    <th style="width: 15%;">Tahun Ajaran</th>
                    <th style="width: 10%;">Periode</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 10%;">Status</th>
                    <th class="text-center" style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $semesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $semester): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($semesters->firstItem() + $index); ?></td>
                        <td><strong><?php echo e($semester->id_semester); ?></strong></td>
                        <td><?php echo e($semester->nama_semester); ?></td>
                        <td><?php echo e($semester->tahun_ajaran); ?></td>
                        <td class="text-center">
                            <span class="badge <?php echo e($semester->periode == 1 ? 'badge-info' : 'badge-warning'); ?>">
                                Semester <?php echo e($semester->periode); ?>

                            </span>
                        </td>
                        <td>
                            <small>
                                <?php echo e($semester->tanggal_mulai->format('d/m/Y')); ?> -<br>
                                <?php echo e($semester->tanggal_akhir->format('d/m/Y')); ?>

                            </small>
                        </td>
                        <td><?php echo $semester->status_badge; ?></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="<?php echo e(route('admin.semester.show', $semester)); ?>" 
                                   class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.semester.edit', $semester)); ?>" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('admin.semester.destroy', $semester)); ?>" 
                                      method="POST" style="display: inline-block;"
                                      onsubmit="return confirm('Yakin ingin menghapus semester ini?')">
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
            <?php echo e($semesters->links()); ?>

        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>Belum Ada Semester</h3>
            <p>Silakan tambahkan semester terlebih dahulu sebelum mengelola capaian santri.</p>
            <a href="<?php echo e(route('admin.semester.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Semester Pertama
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/semester/index.blade.php ENDPATH**/ ?>