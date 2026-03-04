

<?php $__env->startSection('title', 'Daftar Berita'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-newspaper"></i> Daftar Berita</h2>
</div>

<!-- Header Actions -->
<div class="content-box" style="margin-bottom: 14px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 11px;">
        <!-- Search & Filter Form -->
        <form method="GET" action="<?php echo e(route('admin.berita.index')); ?>" style="display: flex; gap: 10px; flex-wrap: wrap; flex-grow: 1;">
            <input type="text" 
                   name="search" 
                   class="form-control" 
                   placeholder="Cari judul, penulis, atau ID..." 
                   value="<?php echo e(request('search')); ?>"
                   style="max-width: 300px;">
            
            <select name="status" class="form-control" style="max-width: 150px;">
                <option value="">Semua Status</option>
                <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>Draft</option>
                <option value="published" <?php echo e(request('status') == 'published' ? 'selected' : ''); ?>>Published</option>
            </select>
            
            <select name="target" class="form-control" style="max-width: 150px;">
                <option value="">Semua Target</option>
                <option value="semua" <?php echo e(request('target') == 'semua' ? 'selected' : ''); ?>>Semua Santri</option>
                <option value="kelas_tertentu" <?php echo e(request('target') == 'kelas_tertentu' ? 'selected' : ''); ?>>Kelas Tertentu</option>
            </select>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Filter
            </button>
            
            <a href="<?php echo e(route('admin.berita.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
        </form>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 10px;">
            <a href="<?php echo e(route('admin.berita.statistik')); ?>" class="btn btn-secondary">
                <i class="fas fa-chart-bar"></i> Statistik
            </a>
            <a href="<?php echo e(route('admin.berita.create')); ?>" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Berita
            </a>
        </div>
    </div>
</div>

<!-- Alert Messages -->
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

<!-- Tabel Berita -->
<div class="content-box">
    <?php if($berita->count() > 0): ?>
        <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th>Judul & Konten</th>
                    <th style="width: 150px;">Penulis</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th style="width: 100px;">Status</th>
                    <th style="width: 150px;">Target</th>
                    <th style="width: 150px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $berita; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><strong><?php echo e($item->id_berita); ?></strong></td>
                    <td>
                        <div style="max-width: 250px;">
                            <strong style="color: var(--primary-color);"><?php echo e($item->judul); ?></strong>
                            <br>
                            <small class="text-muted"><?php echo e(Str::limit(strip_tags($item->konten), 80)); ?></small>
                        </div>
                    </td>
                    <td><?php echo e($item->penulis); ?></td>
                    <td><?php echo e($item->tanggal_formatted); ?></td>
                    <td>
                        <span class="badge <?php echo e($item->status_badge); ?>">
                            <?php if($item->status === 'published'): ?>
                                <i class="fas fa-check-circle"></i> Published
                            <?php else: ?>
                                <i class="fas fa-edit"></i> Draft
                            <?php endif; ?>
                        </span>
                    </td>
                    <td>
                        <?php
                            $badgeClass = match($item->target_berita) {
                                'semua' => 'badge-primary',
                                'kelas_tertentu' => 'badge-info',
                                default => 'badge-secondary'
                            };
                        ?>
                        <span class="badge <?php echo e($badgeClass); ?>">
                            <?php echo e($item->target_audience); ?>

                        </span>
                    </td>
                    <td class="text-center">
                        <div style="display: flex; gap: 5px; justify-content: center;">
                            <a href="<?php echo e(route('admin.berita.show', $item->id_berita)); ?>" 
                               class="btn btn-primary btn-sm" 
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo e(route('admin.berita.edit', $item->id_berita)); ?>" 
                               class="btn btn-warning btn-sm" 
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('admin.berita.destroy', $item->id_berita)); ?>" 
                                  method="POST" 
                                  style="display: inline;"
                                  onsubmit="return confirm('Yakin ingin menghapus berita ini?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        </div>

        <!-- Pagination -->
        <div style="margin-top: 14px; display: flex; justify-content: center;">
            <?php echo e($berita->appends(request()->query())->links()); ?>

        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 44px 14px;">
            <i class="fas fa-newspaper" style="font-size: 4em; color: #ccc; margin-bottom: 14px;"></i>
            <h3 style="color: var(--text-light);">Belum Ada Berita</h3>
            <p style="color: var(--text-light); margin-bottom: 25px;">
                Mulai tambahkan berita pertama untuk santri pesantren.
            </p>
            <a href="<?php echo e(route('admin.berita.create')); ?>" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Berita Pertama
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/berita/index.blade.php ENDPATH**/ ?>