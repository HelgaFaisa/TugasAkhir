


<?php $__env->startSection('title', 'Riwayat Pelanggaran'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-history"></i> Riwayat Pelanggaran Santri</h2>
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

<!-- Statistik Cards -->
<div class="row-cards">
    <div class="card card-primary">
        <h3><i class="fas fa-list"></i> Total Pelanggaran</h3>
        <div class="card-value"><?php echo e($totalPelanggaran); ?></div>
        <p style="margin: 0; color: var(--text-light);">Semua Riwayat</p>
        <i class="fas fa-clipboard-list card-icon"></i>
    </div>

    <div class="card card-warning">
        <h3><i class="fas fa-calendar-alt"></i> Bulan Ini</h3>
        <div class="card-value"><?php echo e($pelanggaranBulanIni); ?></div>
        <p style="margin: 0; color: var(--text-light);"><?php echo e(\Carbon\Carbon::now()->format('F Y')); ?></p>
        <i class="fas fa-calendar-check card-icon"></i>
    </div>

    <div class="card card-danger">
        <h3><i class="fas fa-star"></i> Total Poin</h3>
        <div class="card-value"><?php echo e($totalPoin); ?></div>
        <p style="margin: 0; color: var(--text-light);">Akumulasi Poin</p>
        <i class="fas fa-fire card-icon"></i>
    </div>
</div>

<!-- Filter & Search -->
<div class="content-box" style="margin-bottom: 22px;">
    <h3 style="margin-bottom: 14px; color: var(--primary-color);">
        <i class="fas fa-filter"></i> Filter & Pencarian
    </h3>
    
    <form method="GET" action="<?php echo e(route('admin.riwayat-pelanggaran.index')); ?>">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 11px; margin-bottom: 14px;">
            <!-- Search -->
            <div class="form-group" style="margin-bottom: 0;">
                <label for="search">
                    <i class="fas fa-search form-icon"></i>
                    Pencarian
                </label>
                <input type="text" 
                       name="search" 
                       id="search"
                       class="form-control"
                       value="<?php echo e(request('search')); ?>"
                       placeholder="Cari santri, kategori...">
            </div>

            <!-- Filter Santri -->
            <div class="form-group" style="margin-bottom: 0;">
                <label for="id_santri">
                    <i class="fas fa-user form-icon"></i>
                    Santri
                </label>
                <select name="id_santri" id="id_santri" class="form-control">
                    <option value="">-- Semua Santri --</option>
                    <?php $__currentLoopData = $santriList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($santri->id_santri); ?>" 
                                <?php echo e(request('id_santri') == $santri->id_santri ? 'selected' : ''); ?>>
                            <?php echo e($santri->nama_lengkap); ?> (<?php echo e($santri->id_santri); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Filter Kategori -->
            <div class="form-group" style="margin-bottom: 0;">
                <label for="id_kategori">
                    <i class="fas fa-tags form-icon"></i>
                    Kategori
                </label>
                <select name="id_kategori" id="id_kategori" class="form-control">
                    <option value="">-- Semua Kategori --</option>
                    <?php $__currentLoopData = $kategoriList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($kategori->id_kategori); ?>" 
                                <?php echo e(request('id_kategori') == $kategori->id_kategori ? 'selected' : ''); ?>>
                            <?php echo e($kategori->nama_pelanggaran); ?> (<?php echo e($kategori->poin); ?> poin)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Tanggal Mulai -->
            <div class="form-group" style="margin-bottom: 0;">
                <label for="tanggal_mulai">
                    <i class="fas fa-calendar form-icon"></i>
                    Tanggal Mulai
                </label>
                <input type="date" 
                       name="tanggal_mulai" 
                       id="tanggal_mulai"
                       class="form-control"
                       value="<?php echo e(request('tanggal_mulai')); ?>">
            </div>

            <!-- Tanggal Selesai -->
            <div class="form-group" style="margin-bottom: 0;">
                <label for="tanggal_selesai">
                    <i class="fas fa-calendar-check form-icon"></i>
                    Tanggal Selesai
                </label>
                <input type="date" 
                       name="tanggal_selesai" 
                       id="tanggal_selesai"
                       class="form-control"
                       value="<?php echo e(request('tanggal_selesai')); ?>">
            </div>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Cari
            </button>
            <a href="<?php echo e(route('admin.riwayat-pelanggaran.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
            <label style="display: inline-flex; align-items: center; margin-left: 20px;">
                <input type="checkbox" name="bulan_ini" value="1" <?php echo e(request('bulan_ini') ? 'checked' : ''); ?> style="margin-right: 8px;">
                <span>Bulan Ini Saja</span>
            </label>
        </div>
    </form>
</div>

<!-- Tabel Data -->
<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
        <h3 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-table"></i> Daftar Riwayat Pelanggaran
        </h3>
        <a href="<?php echo e(route('admin.riwayat-pelanggaran.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Riwayat
        </a>
    </div>

    <?php if($data->isNotEmpty()): ?>
        <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 110px;">ID Riwayat</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th>Santri</th>
                    <th>Kategori Pelanggaran</th>
                    <th style="width: 100px; text-align: center;">Poin</th>
                    <th style="width: 200px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($data->firstItem() + $index); ?></td>
                        <td>
                            <span class="badge badge-secondary"><?php echo e($item->id_riwayat); ?></span>
                        </td>
                        <td>
                            <i class="fas fa-calendar" style="color: var(--text-light);"></i>
                            <?php echo e(\Carbon\Carbon::parse($item->tanggal)->format('d M Y')); ?>

                        </td>
                        <td>
                            <?php if($item->santri): ?>
                                <strong><?php echo e($item->santri->nama_lengkap); ?></strong><br>
                                <small style="color: var(--text-light);">
                                    <i class="fas fa-id-card"></i> <?php echo e($item->id_santri); ?>

                                </small>
                            <?php else: ?>
                                <span style="color: var(--danger-color);">Santri tidak ditemukan</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($item->kategori): ?>
                                <strong><?php echo e($item->kategori->nama_pelanggaran); ?></strong><br>
                                <small style="color: var(--text-light);">
                                    <i class="fas fa-tag"></i> <?php echo e($item->id_kategori); ?>

                                </small>
                            <?php else: ?>
                                <span style="color: var(--danger-color);">Kategori tidak ditemukan</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-danger" style="font-size: 1em; padding: 8px 12px;">
                                <i class="fas fa-fire"></i> <?php echo e($item->poin); ?>

                            </span>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 8px;">
                                <a href="<?php echo e(route('admin.riwayat-pelanggaran.show', $item)); ?>" 
                                   class="btn btn-sm btn-success" 
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.riwayat-pelanggaran.edit', $item)); ?>" 
                                   class="btn btn-sm btn-warning" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('admin.riwayat-pelanggaran.destroy', $item)); ?>" 
                                      method="POST" 
                                      style="display: inline;"
                                      onsubmit="return confirm('Yakin ingin menghapus riwayat ini?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" 
                                            class="btn btn-sm btn-danger" 
                                            title="Hapus">
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
        <div style="margin-top: 14px;">
            <?php echo e($data->links()); ?>

        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h3>Belum ada riwayat pelanggaran</h3>
            <p>Silakan tambah riwayat pelanggaran baru menggunakan tombol di atas.</p>
            <a href="<?php echo e(route('admin.riwayat-pelanggaran.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Riwayat
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
// Auto hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/riwayat_pelanggaran/index.blade.php ENDPATH**/ ?>