

<?php $__env->startSection('title', 'Riwayat Kesehatan'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-heartbeat"></i> Riwayat Kesehatan</h2>
    <p style="margin: 5px 0 0 0; color: var(--text-light);">
        Riwayat kunjungan UKP <strong><?php echo e($santri->nama_lengkap); ?></strong>
    </p>
</div>


<?php if($errors->any()): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>Error:</strong> <?php echo e($errors->first()); ?>

</div>
<?php endif; ?>


<div class="row-cards">
    <div class="card card-info">
        <h3><i class="fas fa-notes-medical"></i> Total Kunjungan</h3>
        <div class="card-value"><?php echo e($statistik['total_kunjungan']); ?></div>
        <div class="card-icon"><i class="fas fa-notes-medical"></i></div>
        <p style="margin-top: 10px; font-size: 0.85rem; color: var(--text-light);">
            Periode yang dipilih
        </p>
    </div>
    
    <div class="card card-danger">
        <h3><i class="fas fa-procedures"></i> Sedang Dirawat</h3>
        <div class="card-value"><?php echo e($statistik['sedang_dirawat']); ?></div>
        <div class="card-icon"><i class="fas fa-procedures"></i></div>
        <?php if($statistik['sedang_dirawat'] > 0): ?>
            <p style="margin-top: 10px; font-size: 0.85rem; color: var(--danger-color);">
                <i class="fas fa-exclamation-circle"></i> Perlu perhatian
            </p>
        <?php else: ?>
            <p style="margin-top: 10px; font-size: 0.85rem; color: var(--text-light);">
                Tidak ada yang dirawat
            </p>
        <?php endif; ?>
    </div>
    
    <div class="card card-success">
        <h3><i class="fas fa-check-circle"></i> Sembuh</h3>
        <div class="card-value"><?php echo e($statistik['sembuh']); ?></div>
        <div class="card-icon"><i class="fas fa-check-circle"></i></div>
        <p style="margin-top: 10px; font-size: 0.85rem; color: var(--text-light);">
            Alhamdulillah
        </p>
    </div>
    
    <div class="card card-warning">
        <h3><i class="fas fa-home"></i> Izin Sakit</h3>
        <div class="card-value"><?php echo e($statistik['izin']); ?></div>
        <div class="card-icon"><i class="fas fa-home"></i></div>
        <p style="margin-top: 10px; font-size: 0.85rem; color: var(--text-light);">
            Izin pulang
        </p>
    </div>
</div>


<div class="content-box" style="margin-bottom: 20px;">
    <form method="GET" action="<?php echo e(route('santri.kesehatan.index')); ?>" id="filterForm">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
            
            <div class="form-group" style="margin-bottom: 0;">
                <label style="margin-bottom: 8px; display: block;">
                    <i class="fas fa-calendar-alt form-icon"></i> Tanggal Dari
                </label>
                <input type="date" 
                       name="tanggal_dari" 
                       class="form-control" 
                       value="<?php echo e($tanggalDari->format('Y-m-d')); ?>"
                       max="<?php echo e(date('Y-m-d')); ?>">
            </div>
            
            
            <div class="form-group" style="margin-bottom: 0;">
                <label style="margin-bottom: 8px; display: block;">
                    <i class="fas fa-calendar-check form-icon"></i> Tanggal Sampai
                </label>
                <input type="date" 
                       name="tanggal_sampai" 
                       class="form-control" 
                       value="<?php echo e($tanggalSampai->format('Y-m-d')); ?>"
                       max="<?php echo e(date('Y-m-d')); ?>">
            </div>
            
            
            <div class="form-group" style="margin-bottom: 0;">
                <label style="margin-bottom: 8px; display: block;">
                    <i class="fas fa-filter form-icon"></i> Status
                </label>
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php echo e(request('status') == $value ? 'selected' : ''); ?>>
                            <?php echo e($label); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary hover-lift" style="flex: 1;">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="<?php echo e(route('santri.kesehatan.index')); ?>" class="btn btn-secondary hover-lift" style="flex: 1;">
                    <i class="fas fa-sync"></i> Reset
                </a>
            </div>
        </div>
    </form>
    
    
    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--primary-light);">
        <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">
            <i class="fas fa-info-circle"></i> 
            Menampilkan data periode: 
            <strong style="color: var(--primary-color);">
                <?php echo e($tanggalDari->locale('id')->isoFormat('D MMMM Y')); ?> - <?php echo e($tanggalSampai->locale('id')->isoFormat('D MMMM Y')); ?>

            </strong>
            (<?php echo e($tanggalDari->diffInDays($tanggalSampai) + 1); ?> hari)
        </p>
    </div>
</div>


<?php if($riwayatKesehatan->isEmpty()): ?>
    <div class="empty-state" style="margin-top: 20px;">
        <i class="fas fa-notes-medical"></i>
        <h3>Tidak Ada Data</h3>
        <p>Tidak ada riwayat kesehatan pada periode yang dipilih.</p>
        <a href="<?php echo e(route('santri.kesehatan.index')); ?>" class="btn btn-primary" style="margin-top: 15px;">
            <i class="fas fa-sync"></i> Lihat Semua Data
        </a>
    </div>
<?php else: ?>
    <div class="content-box" style="margin-top: 20px;">
        <h3 style="margin: 0 0 15px 0; color: var(--primary-color);">
            <i class="fas fa-list"></i> Daftar Riwayat (<?php echo e($riwayatKesehatan->total()); ?> data)
        </h3>
        
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php $__currentLoopData = $riwayatKesehatan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('santri.kesehatan.show', $item->id)); ?>" 
               style="display: flex; gap: 15px; padding: 15px; background: linear-gradient(135deg, #FFFFFF 0%, #FEFFFE 100%); border-radius: var(--border-radius-sm); border-left: 4px solid 
               <?php if($item->status == 'dirawat'): ?> var(--danger-color)
               <?php elseif($item->status == 'sembuh'): ?> var(--success-color)
               <?php else: ?> var(--warning-color) <?php endif; ?>
               ; text-decoration: none; transition: var(--transition-base); position: relative;"
               onmouseover="this.style.boxShadow='var(--shadow-md)'; this.style.transform='translateX(5px)';"
               onmouseout="this.style.boxShadow='none'; this.style.transform='translateX(0)';">
                
                
                <div style="flex-shrink: 0; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: 
                    <?php if($item->status == 'dirawat'): ?> linear-gradient(135deg, #FFE8EA, #FFD5D8)
                    <?php elseif($item->status == 'sembuh'): ?> linear-gradient(135deg, #E8F7F2, #D4F1E3)
                    <?php else: ?> linear-gradient(135deg, #FFF8E1, #FFF3CD) <?php endif; ?>
                    ;">
                    <i class="fas 
                        <?php if($item->status == 'dirawat'): ?> fa-procedures
                        <?php elseif($item->status == 'sembuh'): ?> fa-check-circle
                        <?php else: ?> fa-home <?php endif; ?>
                        " style="font-size: 1.8rem; color: 
                        <?php if($item->status == 'dirawat'): ?> var(--danger-color)
                        <?php elseif($item->status == 'sembuh'): ?> var(--success-color)
                        <?php else: ?> var(--warning-color) <?php endif; ?>
                        ;"></i>
                </div>
                
                
                <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-color);">
                                <?php echo e($item->keluhan); ?>

                            </h3>
                            <span class="badge badge-<?php echo e($item->status_badge_color); ?>">
                                <?php echo e(ucfirst($item->status)); ?>

                            </span>
                        </div>
                        
                        <p style="margin: 0 0 8px 0; font-size: 0.9rem; color: var(--text-light);">
                            <i class="fas fa-code"></i> <?php echo e($item->id_kesehatan); ?>

                        </p>
                    </div>
                    
                    <div style="display: flex; flex-wrap: wrap; gap: 15px; font-size: 0.85rem; color: var(--text-light);">
                        <span>
                            <i class="fas fa-calendar-plus"></i> Masuk: <?php echo e($item->tanggal_masuk_formatted); ?>

                        </span>
                        <?php if($item->tanggal_keluar): ?>
                            <span>
                                <i class="fas fa-calendar-check"></i> Keluar: <?php echo e($item->tanggal_keluar_formatted); ?>

                            </span>
                            <span class="badge badge-info badge-sm">
                                <i class="fas fa-clock"></i> <?php echo e($item->lama_dirawat); ?> hari
                            </span>
                        <?php else: ?>
                            <span class="badge badge-danger badge-sm">
                                <i class="fas fa-procedures"></i> Masih dirawat (<?php echo e($item->lama_dirawat); ?> hari)
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                
                <div style="flex-shrink: 0; display: flex; align-items: center;">
                    <i class="fas fa-chevron-right" style="color: var(--text-light);"></i>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        
        
        <div style="margin-top: 25px;">
            <?php echo e($riwayatKesehatan->links()); ?>

        </div>
    </div>
<?php endif; ?>


<div class="info-box" style="margin-top: 20px;">
    <i class="fas fa-info-circle"></i>
    <strong>Info:</strong> Gunakan filter tanggal untuk melihat riwayat kesehatan pada periode tertentu. 
    Jika tidak difilter, data yang ditampilkan adalah untuk bulan berjalan.
</div>


<div style="margin-top: 20px; text-align: center;">
    <a href="<?php echo e(route('santri.dashboard')); ?>" class="btn btn-secondary hover-lift">
        <i class="fas fa-home"></i> Kembali ke Dashboard
    </a>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const tanggalDari = document.querySelector('input[name="tanggal_dari"]');
    const tanggalSampai = document.querySelector('input[name="tanggal_sampai"]');
    
    // Validasi tanggal
    tanggalDari.addEventListener('change', function() {
        if (tanggalSampai.value && tanggalSampai.value < this.value) {
            alert('Tanggal sampai tidak boleh lebih kecil dari tanggal dari!');
            this.value = tanggalSampai.value;
        }
    });
    
    tanggalSampai.addEventListener('change', function() {
        if (tanggalDari.value && this.value < tanggalDari.value) {
            alert('Tanggal sampai tidak boleh lebih kecil dari tanggal dari!');
            this.value = tanggalDari.value;
        }
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/kesehatan/index.blade.php ENDPATH**/ ?>