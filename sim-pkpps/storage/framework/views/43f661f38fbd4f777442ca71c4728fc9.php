

<?php $__env->startSection('title', 'Detail Berita - ' . $berita->id_berita); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-newspaper"></i> Detail Berita</h2>
</div>

<!-- Header Actions -->
<div class="content-box" style="margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div>
            <span class="badge <?php echo e($berita->status_badge); ?>" style="font-size: 1em; padding: 8px 15px;">
                <?php if($berita->status === 'published'): ?>
                    <i class="fas fa-check-circle"></i> Published
                <?php else: ?>
                    <i class="fas fa-edit"></i> Draft
                <?php endif; ?>
            </span>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="<?php echo e(route('admin.berita.edit', $berita->id_berita)); ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="<?php echo e(route('admin.berita.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

<!-- Detail Berita -->
<div class="content-box">
    <div style="padding: 10px;">
        <!-- Header Berita -->
        <div style="border-bottom: 3px solid var(--primary-color); padding-bottom: 25px; margin-bottom: 30px;">
            <div style="margin-bottom: 15px;">
                <span style="background: var(--primary-light); color: var(--primary-dark); padding: 6px 12px; border-radius: var(--border-radius-sm); font-weight: 600; font-size: 0.9em;">
                    ID: <?php echo e($berita->id_berita); ?>

                </span>
            </div>
            
            <h1 style="color: var(--primary-dark); margin-bottom: 20px; font-size: 2em; line-height: 1.3;">
                <?php echo e($berita->judul); ?>

            </h1>
            
            <div style="display: flex; flex-wrap: wrap; gap: 20px; align-items: center; color: var(--text-light); font-size: 0.95em;">
                <span>
                    <i class="fas fa-user"></i> 
                    <strong>Penulis:</strong> <?php echo e($berita->penulis); ?>

                </span>
                <span>
                    <i class="fas fa-calendar"></i> 
                    <strong>Tanggal:</strong> <?php echo e($berita->created_at->format('d M Y, H:i')); ?> WIB
                </span>
                <span>
                    <?php
                        $badgeClass = match($berita->target_berita) {
                            'semua' => 'badge-primary',
                            'kelas_tertentu' => 'badge-info',
                            'santri_tertentu' => 'badge-warning',
                            default => 'badge-secondary'
                        };
                    ?>
                    <span class="badge <?php echo e($badgeClass); ?>">
                        <i class="fas fa-bullseye"></i> <?php echo e($berita->target_audience); ?>

                    </span>
                </span>
            </div>
        </div>

        <!-- Gambar Berita -->
        <?php if($berita->gambar): ?>
        <div style="text-align: center; margin: 40px 0;">
            <img src="<?php echo e(asset('storage/' . $berita->gambar)); ?>" 
                 alt="Gambar Berita" 
                 style="max-width: 100%; max-height: 500px; border-radius: var(--border-radius); box-shadow: var(--shadow-lg); object-fit: cover;">
        </div>
        <?php endif; ?>

        <!-- Konten Berita -->
        <div class="detail-section">
            <h4><i class="fas fa-align-left"></i> Konten Berita</h4>
            <div style="line-height: 1.9; font-size: 1.05em; color: var(--text-color); background: var(--primary-light); padding: 25px; border-radius: var(--border-radius-sm); border-left: 4px solid var(--primary-color);">
                <?php echo nl2br(e($berita->konten)); ?>

            </div>
        </div>

        <!-- Info Target Santri -->
        <?php if($berita->target_berita === 'santri_tertentu' || $berita->target_berita === 'kelas_tertentu'): ?>
        <div class="detail-section">
            <?php if($berita->target_berita === 'kelas_tertentu'): ?>
                <h4>
                    <i class="fas fa-graduation-cap"></i> 
                    Target Kelas: <?php echo e(implode(', ', $berita->target_kelas ?? [])); ?>

                </h4>
                <div style="background: var(--info-color); background: linear-gradient(135deg, #E3F2FD 0%, #D1E9F9 100%); padding: 20px; border-radius: var(--border-radius-sm); border-left: 4px solid var(--info-color);">
                    <p style="margin: 0; color: var(--text-color); font-size: 1em;">
                        <i class="fas fa-info-circle"></i>
                        Berita ini ditujukan untuk santri dari kelas: 
                        <strong><?php echo e(implode(', ', $berita->target_kelas ?? [])); ?></strong>
                    </p>
                </div>
            <?php endif; ?>
            
            <?php if($berita->santriTertentu->count() > 0): ?>
                <h4 style="margin-top: 25px;">
                    <i class="fas fa-users"></i> 
                    Daftar Penerima Berita (<?php echo e($berita->santriTertentu->count()); ?> Santri)
                </h4>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
                    <?php $__currentLoopData = $berita->santriTertentu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="background: white; border: 2px solid var(--primary-light); border-radius: var(--border-radius-sm); padding: 15px; transition: all 0.3s ease; box-shadow: var(--shadow-sm);">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <!-- Hanya tampilkan initial, tanpa foto -->
                            <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--primary-color); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.5em; flex-shrink: 0;">
                                <?php echo e(strtoupper(substr($santri->nama_lengkap, 0, 1))); ?>

                            </div>
                            
                            <div style="flex-grow: 1; min-width: 0;">
                                <div style="font-weight: 600; color: var(--primary-color); margin-bottom: 3px;">
                                    <?php echo e($santri->id_santri); ?>

                                </div>
                                <div style="font-weight: 600; color: var(--text-color); font-size: 1em; margin-bottom: 3px;">
                                    <?php echo e($santri->nama_lengkap); ?>

                                </div>
                                <div style="font-size: 0.85em; color: var(--text-light);">
                                    <i class="fas fa-graduation-cap"></i> <?php echo e($santri->kelas); ?>

                                </div>
                            </div>
                            
                            <!-- Status Baca -->
                            <div style="text-align: center; flex-shrink: 0;">
                                <?php if($santri->pivot->sudah_dibaca): ?>
                                    <span class="badge badge-success" title="Dibaca pada <?php echo e($santri->pivot->tanggal_baca); ?>">
                                        <i class="fas fa-check"></i> Dibaca
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-warning">
                                        <i class="fas fa-clock"></i> Belum
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; background: var(--primary-light); border-radius: var(--border-radius-sm);">
                    <i class="fas fa-users" style="font-size: 3em; color: #ccc; margin-bottom: 15px;"></i>
                    <p style="color: var(--text-light); margin: 0;">Belum ada santri yang dipilih untuk berita ini.</p>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Aksi -->
        <div style="border-top: 2px solid var(--primary-light); padding-top: 30px; margin-top: 40px; text-align: center;">
            <div style="display: flex; justify-content: center; gap: 10px; flex-wrap: wrap;">
                <a href="<?php echo e(route('admin.berita.edit', $berita->id_berita)); ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit Berita
                </a>
                
                <form action="<?php echo e(route('admin.berita.destroy', $berita->id_berita)); ?>" 
                      method="POST" 
                      style="display: inline;"
                      onsubmit="return confirm('Yakin ingin menghapus berita ini? Tindakan ini tidak dapat dibatalkan!')">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Hapus Berita
                    </button>
                </form>
                
                <a href="<?php echo e(route('admin.berita.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Daftar Berita
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/berita/show.blade.php ENDPATH**/ ?>