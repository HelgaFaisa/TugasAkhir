

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-book-open"></i> Detail Materi</h2>
</div>

<div class="content-box">
    
    <div class="detail-header">
        <div>
            <h3><?php echo e($materi->nama_kitab); ?></h3>
            <p class="text-muted">ID: <?php echo e($materi->id_materi); ?></p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="<?php echo e(route('admin.materi.edit', $materi)); ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="<?php echo e(route('admin.materi.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    
    <div class="detail-section">
        <h4><i class="fas fa-info-circle"></i> Informasi Materi</h4>
        <table class="detail-table">
            <tr>
                <th><i class="fas fa-fingerprint"></i> ID Materi</th>
                <td><strong><?php echo e($materi->id_materi); ?></strong></td>
            </tr>
            <tr>
                <th><i class="fas fa-layer-group"></i> Kategori</th>
                <td><?php echo $materi->kategori_badge; ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-users"></i> Kelas</th>
                <td><?php echo $materi->kelas_badge; ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-book"></i> Nama Kitab</th>
                <td><strong><?php echo e($materi->nama_kitab); ?></strong></td>
            </tr>
            <tr>
                <th><i class="fas fa-file-alt"></i> Halaman Mulai</th>
                <td><?php echo e($materi->halaman_mulai); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-file-alt"></i> Halaman Akhir</th>
                <td><?php echo e($materi->halaman_akhir); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-calculator"></i> Total Halaman</th>
                <td>
                    <span class="badge badge-lg badge-primary">
                        <i class="fas fa-book-open"></i> <?php echo e($materi->total_halaman); ?> Halaman
                    </span>
                </td>
            </tr>
            <tr>
                <th><i class="fas fa-align-left"></i> Deskripsi</th>
                <td><?php echo e($materi->deskripsi ?? '-'); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-calendar-plus"></i> Dibuat Pada</th>
                <td><?php echo e($materi->created_at->format('d F Y, H:i')); ?> WIB</td>
            </tr>
            <tr>
                <th><i class="fas fa-calendar-edit"></i> Terakhir Diupdate</th>
                <td><?php echo e($materi->updated_at->format('d F Y, H:i')); ?> WIB</td>
            </tr>
        </table>
    </div>

    
    <div class="detail-section">
        <h4><i class="fas fa-chart-bar"></i> Statistik Capaian</h4>
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <p style="margin: 0;">
                Fitur statistik capaian santri akan tersedia setelah implementasi <strong>Langkah 2: Input Capaian per Santri</strong>.
            </p>
        </div>
        
        
    </div>

    
    <div style="margin-top: 30px; display: flex; gap: 10px; justify-content: flex-end;">
        <a href="<?php echo e(route('admin.materi.edit', $materi)); ?>" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Materi
        </a>
        <form action="<?php echo e(route('admin.materi.destroy', $materi)); ?>" method="POST" style="display: inline-block;"
              onsubmit="return confirm('Yakin ingin menghapus materi <?php echo e($materi->nama_kitab); ?>? Data capaian santri yang terkait juga akan terhapus!')">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Hapus Materi
            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/materi/show.blade.php ENDPATH**/ ?>