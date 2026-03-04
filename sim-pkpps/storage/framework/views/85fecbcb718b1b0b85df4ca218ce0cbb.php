

<?php $__env->startSection('title', 'Preview Kenaikan Kelas'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-users"></i> Preview Kenaikan Kelas - <?php echo e($kelas->nama_kelas); ?></h2>
    <p class="text-muted">Pilih santri yang akan dinaikkan kelasnya</p>
</div>

<!-- Flash Messages -->
<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Info Cards -->
<div class="content-box" style="margin-bottom: 14px;">
    <div class="row-cards">
        <div class="card card-info">
            <h3>Kelas Asal</h3>
            <div class="card-value-small"><?php echo e($kelas->nama_kelas); ?></div>
            <p class="text-muted" style="margin: 0;"><?php echo e($kelas->kelompok->nama_kelompok); ?></p>
        </div>
        <div class="card card-success">
            <h3>Total Santri</h3>
            <div class="card-value"><?php echo e($santriList->count()); ?></div>
            <p class="text-muted" style="margin: 0;">Santri aktif di kelas ini</p>
        </div>
        <div class="card card-warning">
            <h3>Tahun Ajaran</h3>
            <div class="card-value-small"><?php echo e($tahunAjaranAktif); ?></div>
            <div style="margin-top: 8px; font-size: 0.9rem; color: var(--text-light);">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="card-value-small" style="color: var(--success-color);"><?php echo e($tahunAjaranBaru); ?></div>
        </div>
    </div>
</div>

<!-- Form Kenaikan Kelas -->
<div class="content-box">
    <form action="<?php echo e(route('admin.kelas.kenaikan.process-selected')); ?>" method="POST" id="formKenaikanKelas">
        <?php echo csrf_field(); ?>
        
        <input type="hidden" name="id_kelas_asal" value="<?php echo e($kelas->id); ?>">
        
        <!-- Pilih Kelas Tujuan -->
        <div class="form-group">
            <label for="id_kelas_tujuan">
                <i class="fas fa-graduation-cap"></i> Kelas Tujuan <span class="text-danger">*</span>
            </label>
            <select class="form-control <?php $__errorArgs = ['id_kelas_tujuan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                    id="id_kelas_tujuan" 
                    name="id_kelas_tujuan" 
                    required>
                <option value="">-- Pilih Kelas Tujuan --</option>
                <?php $__currentLoopData = $kelasOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelompok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <optgroup label="<?php echo e($kelompok->nama_kelompok); ?>">
                        <?php $__currentLoopData = $kelompok->kelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelasOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($kelasOption->id != $kelas->id): ?>
                                <option value="<?php echo e($kelasOption->id); ?>">
                                    <?php echo e($kelasOption->nama_kelas); ?>

                                </option>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </optgroup>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['id_kelas_tujuan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <small class="form-text text-muted">
                Pilih kelas yang akan menjadi tujuan kenaikan untuk santri yang dipilih
            </small>
        </div>

        <hr>

        <!-- Daftar Santri -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h4 style="margin: 0;">
                <i class="fas fa-users"></i> Daftar Santri
                <span class="badge badge-info" id="selectedCount">0 dipilih</span>
            </h4>
            <div>
                <button type="button" class="btn btn-sm btn-info" id="btnSelectAll">
                    <i class="fas fa-check-square"></i> Pilih Semua
                </button>
                <button type="button" class="btn btn-sm btn-secondary" id="btnDeselectAll">
                    <i class="fas fa-square"></i> Batal Pilih
                </button>
            </div>
        </div>

        <?php if($santriList->count() > 0): ?>
            <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">
                            <input type="checkbox" id="checkAll" style="width: 18px; height: 18px; cursor: pointer;">
                        </th>
                        <th style="width: 50px;">No</th>
                        <th>Foto</th>
                        <th>NIS</th>
                        <th>Nama Santri</th>
                        <th>Jenis Kelamin</th>
                        <th>Status</th>
                    </tr>
                </thead>
                    <tbody>
                        <?php $__currentLoopData = $santriList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <input type="checkbox" 
                                           name="santri_ids[]" 
                                           value="<?php echo e($santri->id_santri); ?>" 
                                           class="santri-checkbox"
                                           style="width: 18px; height: 18px; cursor: pointer;">
                                </td>
                                <td><?php echo e($index + 1); ?></td>
                                <td>
                                    <?php if($santri->foto): ?>
                                        <img src="<?php echo e($santri->foto_url); ?>" 
                                             alt="<?php echo e($santri->nama_lengkap); ?>" 
                                             class="santri-avatar">
                                    <?php else: ?>
                                        <div class="santri-avatar-initial">
                                            <?php echo e(strtoupper(substr($santri->nama_lengkap, 0, 1))); ?>

                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($santri->nis ?? '-'); ?></td>
                                <td><strong><?php echo e($santri->nama_lengkap); ?></strong></td>
                                <td>
                                    <?php if($santri->jenis_kelamin === 'Laki-laki'): ?>
                                        <i class="fas fa-mars text-primary"></i> Laki-laki
                                    <?php else: ?>
                                        <i class="fas fa-venus" style="color: #FF8B94;"></i> Perempuan
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> <?php echo e($santri->status); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <hr>

            <!-- Action Buttons -->
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> <strong>Perhatian:</strong>
                <ul class="mb-0 mt-2">
                    <li>Pastikan kelas tujuan sudah dipilih</li>
                    <li>Pilih santri yang akan dinaikkan kelasnya (minimal 1 santri)</li>
                    <li>Proses kenaikan kelas akan memindahkan santri ke tahun ajaran <strong><?php echo e($tahunAjaranBaru); ?></strong></li>
                    <li>Santri yang dipilih akan otomatis terdaftar di kelas tujuan</li>
                </ul>
            </div>

            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button type="submit" class="btn btn-success" id="btnSubmit" disabled>
                    <i class="fas fa-arrow-up"></i> Naikkan Kelas yang Dipilih
                </button>
                <a href="<?php echo e(route('admin.kelas.kenaikan.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada santri aktif di kelas ini</h5>
                <p class="text-muted">Kelas <?php echo e($kelas->nama_kelas); ?> tidak memiliki santri aktif.</p>
                <a href="<?php echo e(route('admin.kelas.kenaikan.index')); ?>" class="btn btn-secondary mt-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.santri-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    const btnSubmit = document.getElementById('btnSubmit');
    const btnSelectAll = document.getElementById('btnSelectAll');
    const btnDeselectAll = document.getElementById('btnDeselectAll');
    const kelasSelect = document.getElementById('id_kelas_tujuan');
    
    function updateSelectedCount() {
        const checked = document.querySelectorAll('.santri-checkbox:checked').length;
        selectedCount.textContent = `${checked} dipilih`;
        
        // Enable submit button if kelas tujuan selected and at least 1 santri checked
        if (kelasSelect.value && checked > 0) {
            btnSubmit.disabled = false;
        } else {
            btnSubmit.disabled = true;
        }
    }
    
    // Check all functionality
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
    }
    
    // Individual checkbox
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Update check all status
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            if (checkAll) {
                checkAll.checked = allChecked;
            }
            updateSelectedCount();
        });
    });
    
    // Select all button
    if (btnSelectAll) {
        btnSelectAll.addEventListener('click', function() {
            checkboxes.forEach(checkbox => checkbox.checked = true);
            if (checkAll) checkAll.checked = true;
            updateSelectedCount();
        });
    }
    
    // Deselect all button
    if (btnDeselectAll) {
        btnDeselectAll.addEventListener('click', function() {
            checkboxes.forEach(checkbox => checkbox.checked = false);
            if (checkAll) checkAll.checked = false;
            updateSelectedCount();
        });
    }
    
    // Kelas tujuan change
    if (kelasSelect) {
        kelasSelect.addEventListener('change', updateSelectedCount);
    }
    
    // Form submit confirmation
    const form = document.getElementById('formKenaikanKelas');
    if (form) {
        form.addEventListener('submit', function(e) {
            const checked = document.querySelectorAll('.santri-checkbox:checked').length;
            const kelasText = kelasSelect.options[kelasSelect.selectedIndex].text;
            
            if (!confirm(`Apakah Anda yakin ingin menaikkan ${checked} santri ke kelas "${kelasText}"?\n\nProses ini tidak dapat dibatalkan.`)) {
                e.preventDefault();
            }
        });
    }
    
    // Initial count
    updateSelectedCount();
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kelas/kenaikan/preview.blade.php ENDPATH**/ ?>