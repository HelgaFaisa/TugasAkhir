<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-calendar-alt"></i> Detail Kegiatan</h2>
</div>

<div class="content-box">
    <div class="detail-header">
        <h3><?php echo e($kegiatan->nama_kegiatan); ?></h3>
        <div style="display: flex; gap: 10px;">
            <a href="<?php echo e(route('admin.kegiatan.edit', $kegiatan)); ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form action="<?php echo e(route('admin.kegiatan.destroy', $kegiatan)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus kegiatan ini?')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </form>
            <a href="<?php echo e(route('admin.kegiatan.jadwal')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="detail-section">
        <h4><i class="fas fa-info-circle"></i> Informasi Kegiatan</h4>
        <table class="detail-table">
            <tr>
                <th>ID Kegiatan</th>
                <td><strong><?php echo e($kegiatan->kegiatan_id); ?></strong></td>
            </tr>
            <tr>
                <th>Kategori</th>
                <td>
                    <span class="badge badge-primary badge-lg"><?php echo e($kegiatan->kategori->nama_kategori); ?></span>
                </td>
            </tr>
            <tr>
                <th>Nama Kegiatan</th>
                <td><strong><?php echo e($kegiatan->nama_kegiatan); ?></strong></td>
            </tr>
            <tr>
                <th>Hari</th>
                <td><span class="badge badge-info badge-lg"><?php echo e($kegiatan->hari); ?></span></td>
            </tr>
            <tr>
                <th>Waktu Pelaksanaan</th>
                <td>
                    <i class="fas fa-clock" style="color: var(--primary-color);"></i> 
                    <?php echo e(date('H:i', strtotime($kegiatan->waktu_mulai))); ?> - <?php echo e(date('H:i', strtotime($kegiatan->waktu_selesai))); ?> WIB
                </td>
            </tr>
            <tr>
                <th>Materi/Topik</th>
                <td><?php echo e($kegiatan->materi ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Kelas yang Mengikuti</th>
                <td>
                    <?php if($kegiatan->kelasKegiatan->isEmpty()): ?>
                        <span class="badge badge-info">Kegiatan Umum (Semua Santri)</span>
                    <?php else: ?>
                        <?php $__currentLoopData = $kegiatan->kelasKegiatan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="badge badge-secondary badge-lg" style="margin-right: 5px;">
                                <?php echo e($kelas->kelompok->nama_kelompok); ?> - <?php echo e($kelas->nama_kelas); ?>

                            </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <div style="margin-top: 8px;">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                <?php echo e($kegiatan->kelasKegiatan->count()); ?> kelas dipilih
                            </small>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td><?php echo e($kegiatan->keterangan ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Dibuat Pada</th>
                <td><?php echo e($kegiatan->created_at->format('d F Y, H:i')); ?> WIB</td>
            </tr>
            <tr>
                <th>Terakhir Diubah</th>
                <td><?php echo e($kegiatan->updated_at->format('d F Y, H:i')); ?> WIB</td>
            </tr>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kegiatan/data/show.blade.php ENDPATH**/ ?>