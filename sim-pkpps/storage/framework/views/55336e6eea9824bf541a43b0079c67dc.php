

<?php $__env->startSection('title', 'Mapping ID Fingerprint'); ?>
<?php $__env->startSection('content'); ?>

<div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
    <h2><i class="fas fa-link"></i> Mapping ID Fingerprint</h2>
    <a href="<?php echo e(route('admin.kegiatan.index')); ?>" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger"><i class="fas fa-times-circle"></i> <?php echo e(session('error')); ?></div>
<?php endif; ?>


<div class="content-box" style="margin-bottom:14px">
    <h4 style="margin:0 0 6px;font-size:15px">
        <i class="fas fa-magic" style="color:#166534"></i> Auto-Import dari INFO.XLS
    </h4>
    <p style="margin:0 0 12px;color:#6B7280;font-size:13px">
        Upload INFO.XLS dari mesin Eppos sistem otomatis cocokkan nama dan buat mapping.
        Nama yang tidak cocok otomatis ke dropdown untuk dipilih manual.
    </p>
    <form action="<?php echo e(route('admin.mesin.mapping-santri.import-info')); ?>"
          method="POST" enctype="multipart/form-data"
          style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        <?php echo csrf_field(); ?>
        <input type="file" name="file_info" accept=".xls,.xlsx" required
               class="form-control" style="max-width:320px">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-magic"></i> Auto-Import
        </button>
    </form>
</div>


<?php
    $total      = $mappings->count();
    $terpetakan = $mappings->filter(fn($m) => !empty($m->id_santri))->count();
    $belum      = $total - $terpetakan;
?>

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:14px">
    <div style="background:#F9FAFB;border:1px solid #E5E7EB;border-radius:10px;padding:14px;text-align:center">
        <div style="font-size:28px;font-weight:700;color:#1F2937"><?php echo e($total); ?></div>
        <div style="font-size:12px;color:#6B7280">Total ID Mesin</div>
    </div>
    <div style="background:#DCFCE7;border:1px solid #BBF7D0;border-radius:10px;padding:14px;text-align:center">
        <div style="font-size:28px;font-weight:700;color:#166534"><?php echo e($terpetakan); ?></div>
        <div style="font-size:12px;color:#166534">Terpetakan</div>
    </div>
    <div style="background:<?php echo e($belum > 0 ? '#FEE2E2' : '#DCFCE7'); ?>;border:1px solid <?php echo e($belum > 0 ? '#FECACA' : '#BBF7D0'); ?>;border-radius:10px;padding:14px;text-align:center">
        <div style="font-size:28px;font-weight:700;color:<?php echo e($belum > 0 ? '#991B1B' : '#166534'); ?>"><?php echo e($belum); ?></div>
        <div style="font-size:12px;color:<?php echo e($belum > 0 ? '#991B1B' : '#166534'); ?>">
            <?php echo e($belum > 0 ? 'Belum Dipetakan' : 'Semua Terpetakan'); ?>

        </div>
    </div>
</div>


<?php if($belum > 0): ?>
<div style="background:#FEF9C3;border:1px solid #FDE68A;border-left:4px solid #F59E0B;
            border-radius:8px;padding:12px 16px;margin-bottom:14px;font-size:13px">
    <strong>âš  <?php echo e($belum); ?> ID mesin belum dipetakan ke santri.</strong>
    Data scan santri tersebut tidak akan tersimpan saat import absensi.
    Pilih santri yang sesuai dari dropdown di bawah.
</div>
<?php endif; ?>


<div class="content-box" style="padding:0;overflow:hidden;margin-bottom:14px">
    <div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:70px;text-align:center">ID Mesin</th>
                <th>Nama di Mesin</th>
                <th style="width:90px">Dept/Kel</th>
                <th>Santri Web yang Dipetakan</th>
                <th style="width:110px;text-align:center">Status</th>
                <th style="width:70px;text-align:center">Hapus</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $mappings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr style="background:<?php echo e(empty($m->id_santri) ? '#FFFBEB' : 'white'); ?>">

                
                <td style="text-align:center">
                    <strong style="font-family:monospace;font-size:15px;color:#1D4ED8">
                        <?php echo e($m->id_mesin); ?>

                    </strong>
                </td>

                
                <td>
                    <div style="font-weight:600;color:#1F2937"><?php echo e($m->nama_mesin ?? '-'); ?></div>
                    <?php if($m->catatan): ?>
                        <div style="font-size:11px;color:#9CA3AF"><?php echo e($m->catatan); ?></div>
                    <?php endif; ?>
                </td>

                
                <td style="color:#6B7280;font-size:12px"><?php echo e($m->dept_mesin ?? '-'); ?></td>

                
                <td>
                    <form action="<?php echo e(route('admin.mesin.mapping-santri.update', $m->id)); ?>"
                          method="POST" style="margin:0">
                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                        <div style="display:flex;align-items:center;gap:8px">
                            <select name="id_santri"
                                    class="form-control"
                                    style="font-size:13px;
                                           border-color:<?php echo e(empty($m->id_santri) ? '#FCA5A5' : '#D1D5DB'); ?>;
                                           background:<?php echo e(empty($m->id_santri) ? '#FFF5F5' : 'white'); ?>"
                                    onchange="this.form.submit()">
                                <option value="">-- Pilih Santri --</option>
                                <?php $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($s->id_santri); ?>"
                                    <?php echo e($m->id_santri == $s->id_santri ? 'selected' : ''); ?>>
                                    <?php echo e($s->nama_lengkap); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php if(!empty($m->id_santri)): ?>
                                <i class="fas fa-check-circle" style="color:#22C55E;font-size:16px" title="Sudah dipetakan"></i>
                            <?php else: ?>
                                <i class="fas fa-exclamation-circle" style="color:#EF4444;font-size:16px" title="Belum dipetakan"></i>
                            <?php endif; ?>
                        </div>
                    </form>
                </td>

                
                <td style="text-align:center">
                    <?php if(!empty($m->id_santri)): ?>
                        <span style="background:#DCFCE7;color:#166534;border-radius:12px;
                                     padding:3px 10px;font-size:11px;font-weight:700">
                            Terpetakan
                        </span>
                    <?php else: ?>
                        <span style="background:#FEE2E2;color:#991B1B;border-radius:12px;
                                     padding:3px 10px;font-size:11px;font-weight:700">
                            âš  Belum
                        </span>
                    <?php endif; ?>
                </td>

                
                <td style="text-align:center">
                    <form action="<?php echo e(route('admin.mesin.mapping-santri.destroy', $m->id)); ?>"
                          method="POST"
                          onsubmit="return confirm('Hapus mapping ID Mesin <?php echo e($m->id_mesin); ?> (<?php echo e($m->nama_mesin); ?>)?')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit"
                                class="btn btn-sm btn-danger"
                                style="padding:4px 10px">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="6" style="text-align:center;padding:40px;color:#9CA3AF">
                    <i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:8px"></i>
                    Belum ada mapping. Upload INFO.XLS di atas untuk mulai.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>


<div class="content-box">
    <h4 style="margin:0 0 6px;font-size:15px">
        <i class="fas fa-plus-circle" style="color:#1D4ED8"></i> Tambah Mapping Manual
    </h4>
    <p style="margin:0 0 12px;color:#6B7280;font-size:13px">
        Untuk santri yang baru daftar ke mesin setelah INFO.XLS diekspor,
        atau santri yang nama di mesin sangat berbeda dari nama di sistem.
    </p>
    <form action="<?php echo e(route('admin.mesin.mapping-santri.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div style="display:grid;grid-template-columns:120px 160px 1fr auto;gap:10px;align-items:end">
            <div class="form-group" style="margin:0">
                <label style="font-size:12px;font-weight:600">ID Mesin <span style="color:red">*</span></label>
                <input type="text" name="id_mesin" class="form-control"
                       placeholder="cth: 8" required value="<?php echo e(old('id_mesin')); ?>"
                       style="font-family:monospace;font-size:15px;font-weight:700">
                <?php $__errorArgs = ['id_mesin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p style="color:#EF4444;font-size:11px;margin:3px 0 0"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="form-group" style="margin:0">
                <label style="font-size:12px;font-weight:600">Nama di Mesin</label>
                <input type="text" name="nama_mesin" class="form-control"
                       placeholder="cth: ilham" value="<?php echo e(old('nama_mesin')); ?>">
            </div>
            <div class="form-group" style="margin:0">
                <label style="font-size:12px;font-weight:600">Santri Web</label>
                <select name="id_santri" class="form-control">
                    <option value="">-- Pilih Santri (bisa diisi nanti) --</option>
                    <?php $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s->id_santri); ?>"
                        <?php echo e(old('id_santri') == $s->id_santri ? 'selected' : ''); ?>>
                        <?php echo e($s->nama_lengkap); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary" style="white-space:nowrap;padding:9px 18px">
                    <i class="fas fa-plus"></i> Tambah
                </button>
            </div>
        </div>
    </form>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/mesin/mapping-santri/index.blade.php ENDPATH**/ ?>