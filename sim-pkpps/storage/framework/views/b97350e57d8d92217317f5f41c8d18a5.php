

<?php $__env->startSection('title', 'Dashboard Santri'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-tachometer-alt"></i> Dashboard Progres</h2>
    <p style="margin: 5px 0 0 0; color: var(--text-light);">
        Selamat datang, <strong><?php echo e($data['nama_santri']); ?></strong> - Kelas <?php echo e($data['kelas']); ?>

    </p>
</div>


<?php if(isset($statusKesehatan) && $statusKesehatan): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>Perhatian:</strong> Anda sedang dalam perawatan UKP sejak <?php echo e($statusKesehatan->tanggal_masuk_formatted); ?> 
    (<?php echo e($statusKesehatan->lama_dirawat); ?> hari). Keluhan: <strong><?php echo e($statusKesehatan->keluhan); ?></strong>.
    <a href="<?php echo e(route('santri.kesehatan.index')); ?>" style="color: inherit; text-decoration: underline; font-weight: 600;">Lihat Detail</a>
</div>
<?php endif; ?>

<?php if(isset($kepulanganAktif) && $kepulanganAktif): ?>
<div class="alert alert-info">
    <i class="fas fa-home"></i>
    <strong>Sedang Pulang:</strong> Anda sedang dalam periode kepulangan 
    (<?php echo e($kepulanganAktif->tanggal_pulang_formatted); ?> - <?php echo e($kepulanganAktif->tanggal_kembali_formatted); ?>). 
    Pastikan kembali tepat waktu!
    <a href="<?php echo e(route('santri.kepulangan.show', $kepulanganAktif->id_kepulangan)); ?>" style="color: inherit; text-decoration: underline; font-weight: 600;">Lihat Detail</a>
</div>
<?php endif; ?>


<div class="row-cards">
    
    <div class="card card-info">
        <h3><i class="fas fa-book-quran"></i> Progres Al-Qur'an</h3>
        <div class="card-value"><?php echo e($data['progres_quran']); ?>%</div>
        <div class="card-icon"><i class="fas fa-book-quran"></i></div>
        <div style="margin-top: 10px;">
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo e($data['progres_quran']); ?>%; background: var(--info-color);"></div>
            </div>
        </div>
    </div>
    
    
    <div class="card card-primary">
        <h3><i class="fas fa-scroll"></i> Progres Hadist</h3>
        <div class="card-value"><?php echo e($data['progres_hadist']); ?>%</div>
        <div class="card-icon"><i class="fas fa-scroll"></i></div>
        <div style="margin-top: 10px;">
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo e($data['progres_hadist']); ?>%; background: var(--primary-color);"></div>
            </div>
        </div>
    </div>
    
    
    <div class="card card-success">
        <h3><i class="fas fa-wallet"></i> Saldo Uang Saku</h3>
        <div class="card-value"><?php echo e('Rp ' . number_format($data['saldo_uang_saku'], 0, ',', '.')); ?></div>
        <div class="card-icon"><i class="fas fa-wallet"></i></div>
        <div style="margin-top: 10px;">
            <a href="<?php echo e(route('santri.uang-saku.index')); ?>" class="btn btn-sm btn-success" style="width: 100%; justify-content: center;">
                <i class="fas fa-eye"></i> Lihat Riwayat
            </a>
        </div>
    </div>
    
    
    <div class="card card-<?php echo e($data['poin_pelanggaran'] > 0 ? 'danger' : 'warning'); ?>">
        <h3><i class="fas fa-exclamation-triangle"></i> Total Poin Pelanggaran</h3>
        <div class="card-value"><?php echo e($data['poin_pelanggaran']); ?></div>
        <div class="card-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <?php if($data['poin_pelanggaran'] > 0): ?>
            <div style="margin-top: 10px;">
                <a href="<?php echo e(route('santri.pelanggaran.index')); ?>" class="btn btn-sm btn-danger" style="width: 100%; justify-content: center;">
                    <i class="fas fa-eye"></i> Lihat Riwayat
                </a>
            </div>
        <?php else: ?>
            <div style="margin-top: 10px;">
                <span class="badge badge-success">
                    <i class="fas fa-check-circle"></i> Tidak ada pelanggaran
                </span>
            </div>
        <?php endif; ?>
    </div>
</div>


<?php if($beritaTerbaru->isNotEmpty()): ?>
<div class="content-box" style="margin-top: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h3 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-newspaper"></i> Berita Terbaru (7 Hari Terakhir)
        </h3>
        <a href="<?php echo e(route('santri.berita.index')); ?>" class="btn btn-sm btn-primary hover-lift">
            <i class="fas fa-arrow-right"></i> Lihat Semua
        </a>
    </div>
    
    <div style="display: flex; flex-direction: column; gap: 12px;">
        <?php $__currentLoopData = $beritaTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $berita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('santri.berita.show', $berita->id_berita)); ?>" 
           style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: linear-gradient(135deg, #FEFFFF 0%, #F8FBF9 100%); border-radius: var(--border-radius-sm); border-left: 4px solid var(--primary-color); text-decoration: none; transition: var(--transition-base);"
           onmouseover="this.style.boxShadow='var(--shadow-md)'; this.style.transform='translateX(5px)';"
           onmouseout="this.style.boxShadow='none'; this.style.transform='translateX(0)';">
            <div style="flex: 1;">
                <h4 style="margin: 0 0 5px 0; color: var(--text-color); font-size: 0.95rem; font-weight: 600;">
                    <i class="fas fa-circle" style="font-size: 0.5rem; color: var(--primary-color); margin-right: 8px;"></i>
                    <?php echo e($berita->judul); ?>

                </h4>
                <p style="margin: 0; font-size: 0.85rem; color: var(--text-light);">
                    <i class="fas fa-calendar"></i> <?php echo e($berita->created_at->diffForHumans()); ?>

                </p>
            </div>
            <span class="badge badge-primary">
                <i class="fas fa-chevron-right"></i>
            </span>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>


<div class="content-box" style="margin-top: 20px;">
    <h3 style="margin: 0 0 20px 0; color: var(--primary-color);">
        <i class="fas fa-bolt"></i> Akses Cepat
    </h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
        
        <a href="<?php echo e(route('santri.profil.index')); ?>" class="btn btn-primary hover-lift" style="padding: 15px; text-align: center;">
            <i class="fas fa-user" style="font-size: 1.5rem; display: block; margin-bottom: 8px;"></i>
            <span style="font-size: 0.9rem;">Profil Saya</span>
        </a>
        
        
        <a href="<?php echo e(route('santri.berita.index')); ?>" class="btn btn-info hover-lift" style="padding: 15px; text-align: center;">
            <i class="fas fa-newspaper" style="font-size: 1.5rem; display: block; margin-bottom: 8px;"></i>
            <span style="font-size: 0.9rem;">Berita</span>
        </a>
        
        
        <a href="<?php echo e(route('santri.uang-saku.index')); ?>" class="btn btn-success hover-lift" style="padding: 15px; text-align: center;">
            <i class="fas fa-wallet" style="font-size: 1.5rem; display: block; margin-bottom: 8px;"></i>
            <span style="font-size: 0.9rem;">Uang Saku</span>
        </a>
        
        
        <a href="<?php echo e(route('santri.pelanggaran.index')); ?>" class="btn btn-danger hover-lift" style="padding: 15px; text-align: center;">
            <i class="fas fa-exclamation-circle" style="font-size: 1.5rem; display: block; margin-bottom: 8px;"></i>
            <span style="font-size: 0.9rem;">Pelanggaran</span>
        </a>
        
        
        <a href="<?php echo e(route('santri.kesehatan.index')); ?>" class="btn btn-<?php echo e(isset($statusKesehatan) && $statusKesehatan ? 'danger' : 'info'); ?> hover-lift" style="padding: 15px; text-align: center; position: relative;">
            <i class="fas fa-heartbeat" style="font-size: 1.5rem; display: block; margin-bottom: 8px;"></i>
            <span style="font-size: 0.9rem;">Kesehatan</span>
            <?php if(isset($statusKesehatan) && $statusKesehatan): ?>
                <span class="badge badge-light" style="display: block; margin-top: 5px; font-size: 0.75rem; background: rgba(255,255,255,0.9); color: var(--danger-color);">
                    <i class="fas fa-exclamation-circle"></i> Sedang Dirawat
                </span>
            <?php endif; ?>
        </a>
        
        
        <a href="<?php echo e(route('santri.kepulangan.index')); ?>" class="btn btn-<?php echo e(isset($kepulanganAktif) && $kepulanganAktif ? 'info' : 'primary'); ?> hover-lift" style="padding: 15px; text-align: center; position: relative;">
            <i class="fas fa-home" style="font-size: 1.5rem; display: block; margin-bottom: 8px;"></i>
            <span style="font-size: 0.9rem;">Kepulangan</span>
            <?php if(isset($kepulanganAktif) && $kepulanganAktif): ?>
                <span class="badge badge-light" style="display: block; margin-top: 5px; font-size: 0.75rem; background: rgba(255,255,255,0.9); color: var(--info-color);">
                    <i class="fas fa-home"></i> Sedang Pulang
                </span>
            <?php endif; ?>
        </a>
    </div>
</div>


<div class="content-box" style="margin-top: 20px; background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%); border: 2px solid var(--primary-color);">
    <h4 style="margin: 0 0 15px 0; color: var(--primary-dark);">
        <i class="fas fa-lightbulb"></i> Tips Hari Ini
    </h4>
    <p style="margin: 0; color: var(--text-color); line-height: 1.6;">
        💡 <strong>Jaga Kedisiplinan:</strong> Hindari pelanggaran dengan mematuhi tata tertib pondok. 
        Lihat <strong><a href="<?php echo e(route('santri.pelanggaran.kategori')); ?>" style="color: var(--primary-color);">Daftar Kategori Pelanggaran</a></strong> 
        untuk mengetahui peraturan yang berlaku.
    </p>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/dashboardSantri.blade.php ENDPATH**/ ?>