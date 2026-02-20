

<?php $__env->startSection('title', 'Edit Santri: ' . $santri->nama_lengkap); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-user-edit"></i> Edit Data Santri</h2>
</div>

<div class="content-box">
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <strong><i class="fas fa-exclamation-triangle"></i> Terdapat kesalahan:</strong>
            <ul style="margin: 10px 0 0 20px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <div style="background: linear-gradient(135deg, #E3F2FD 0%, #D1E9F9 100%); padding: 20px; border-radius: 8px; border-left: 4px solid #81C6E8; margin-bottom: 25px;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <?php if($santri->foto): ?>
                <img src="<?php echo e(asset('storage/' . $santri->foto)); ?>" 
                     alt="Foto <?php echo e($santri->nama_lengkap); ?>" 
                     style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 3px solid #81C6E8;"
                     loading="lazy">
            <?php else: ?>
                <div style="width: 60px; height: 60px; border-radius: 50%; background: #81C6E8; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.5rem;">
                    <?php echo e(strtoupper(substr($santri->nama_lengkap, 0, 1))); ?>

                </div>
            <?php endif; ?>
            
            <div>
                <p style="margin: 0; color: #2D4A7C; font-size: 0.9rem;">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Sedang mengedit data:</strong>
                </p>
                <p style="margin: 5px 0 0 0; color: #2D4A7C; font-weight: 600; font-size: 1.1rem;">
                    <?php echo e($santri->nama_lengkap); ?> (<?php echo e($santri->id_santri); ?>)
                </p>
            </div>
        </div>
    </div>

    <?php echo $__env->make('admin.santri.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', ['isAdmin' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/santri/edit.blade.php ENDPATH**/ ?>