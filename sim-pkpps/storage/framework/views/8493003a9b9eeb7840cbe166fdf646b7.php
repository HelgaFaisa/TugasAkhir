

<?php $__env->startSection('title', 'Import Absensi Mesin'); ?>
<?php $__env->startSection('content'); ?>

<div class="page-header">
    <h2><i class="fas fa-file-import"></i> Import Absensi Fingerprint</h2>
    <a href="<?php echo e(route('admin.kegiatan.index')); ?>" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger">
        <i class="fas fa-times-circle"></i> <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>

<?php if($belumMapping > 0): ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle"></i>
    <strong><?php echo e($belumMapping); ?></strong> ID mesin belum dipetakan ke santri.
    Data santri tersebut tidak akan tersimpan saat import.
    <a href="<?php echo e(route('admin.mesin.mapping-santri.index')); ?>"
       class="btn btn-sm btn-warning" style="margin-left:8px;">
        Lengkapi Mapping
    </a>
</div>
<?php endif; ?>


<div class="content-box" style="margin-bottom:14px">
    <h4 style="margin:0 0 12px;color:var(--primary-color)">
        <i class="fas fa-info-circle"></i> Cara Kerja Matching
    </h4>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
        <div style="background:#FFF3E8;border:1px solid #FDBA74;border-radius:8px;padding:12px">
            <div style="font-size:18px;margin-bottom:4px"></div>
            <div style="font-weight:700;color:#C05621;margin-bottom:4px">Mesin Sholat</div>
            <div style="font-size:12px;color:#374151;line-height:1.6">
                JK1 Masuk Shubuh<br>
                JK1 Pulang Dhuhur<br>
                JK2 Masuk Ashar<br>
                JK2 Pulang Maghrib<br>
                Lb Masuk Isya
            </div>
        </div>
        <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;padding:12px">
            <div style="font-size:18px;margin-bottom:4px"></div>
            <div style="font-weight:700;color:#1D4ED8;margin-bottom:4px">Mesin Ngaji</div>
            <div style="font-size:12px;color:#374151;line-height:1.6">
                JK1 Masuk Ngaji Shubuh<br>
                JK1 Pulang sekolah<br>
                JK2 Masuk Ngaji Siang<br>
                JK2 Pulang Ngaji Maghrib<br>
                Lb Masuk Ngaji Malam
            </div>
        </div>
        <div style="background:#FEF9C3;border:1px solid #FDE68A;border-radius:8px;padding:12px">
            <div style="font-size:18px;margin-bottom:4px"></div>
            <div style="font-weight:700;color:#92400E;margin-bottom:4px">Konflik</div>
            <div style="font-size:12px;color:#374151;line-height:1.6">
                Jika santri sudah punya absen<br>
                Manual/RFID, sistem akan<br>
                minta pilihan Anda di halaman<br>
                preview sebelum disimpan.
            </div>
        </div>
    </div>
</div>


<div class="content-box">
    <h4 style="margin:0 0 16px">
        <i class="fas fa-upload" style="color:var(--primary-color)"></i>
        Upload File GLog.txt
    </h4>

    <form action="<?php echo e(route('admin.mesin.import.preview')); ?>"
          method="POST"
          enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

        <div class="form-group" style="margin-bottom:16px">
            <label style="font-weight:600;font-size:14px">
                <i class="fas fa-database" style="color:#1A56DB"></i>
                File GLog.txt <span style="color:red">*</span>
            </label>
            <input type="file" name="file_glog" class="form-control"
                   accept=".txt,.csv,.xls,.xlsx" required>
            <small class="text-muted">
                Export dari software Eppos: menu
                <strong>Report Download Log</strong>.
                Pilih periode tanggal yang diinginkan lalu export.
            </small>
            <?php $__errorArgs = ['file_glog'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div style="color:#EF4444;font-size:12px;margin-top:4px">
                    <?php echo e($message); ?>

                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
            <div class="form-group" style="margin:0">
                <label style="font-weight:600;font-size:14px">
                    <i class="fas fa-clock"></i>
                    Toleransi Sebelum Kegiatan (menit)
                </label>
                <input type="number" name="tol_sebelum" class="form-control"
                       value="15" min="0" max="60">
                <small class="text-muted">
                    Scan diterima berapa menit <strong>sebelum</strong> kegiatan mulai
                </small>
            </div>
            <div class="form-group" style="margin:0">
                <label style="font-weight:600;font-size:14px">
                    <i class="fas fa-clock"></i>
                    Toleransi Sesudah Kegiatan (menit)
                </label>
                <input type="number" name="tol_sesudah" class="form-control"
                       value="10" min="0" max="60">
                <small class="text-muted">
                    Scan diterima berapa menit <strong>setelah</strong> kegiatan selesai
                </small>
            </div>
        </div>

        <div class="form-group" style="margin-bottom:20px">
            <div style="display:flex;align-items:center;gap:10px;
                        background:#F8FAFC;border:1px solid #E2E8F0;
                        border-radius:8px;padding:12px">
                <input type="checkbox" name="isi_alpa" value="1"
                       id="isiAlpa" checked
                       style="width:18px;height:18px;cursor:pointer">
                <label for="isiAlpa" style="margin:0;cursor:pointer;font-weight:500">
                    Isi <strong>Alpa</strong> otomatis untuk santri yang tidak scan
                    <span style="color:#6B7280;font-size:12px;display:block;font-weight:400">
                        Jika tidak dicentang, santri yang tidak scan tidak akan diisi apapun
                    </span>
                </label>
            </div>
        </div>

        
        <div class="form-group" style="margin-bottom:20px">
            <label style="font-weight:600;font-size:14px;margin-bottom:8px;display:block">
                <i class="fas fa-exchange-alt" style="color:#DC2626"></i>
                Jika ada konflik dengan data absen yang sudah ada:
            </label>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
                <label style="background:#DCFCE7;border:2px solid #86EFAC;border-radius:8px;padding:12px;cursor:pointer;display:flex;align-items:flex-start;gap:8px;margin:0"
                       id="lbl_mesin">
                    <input type="radio" name="conflict_strategy" value="mesin" checked
                           style="margin-top:3px;width:16px;height:16px">
                    <div>
                        <div style="font-weight:700;color:#166534">Utamakan Data Mesin</div>
                        <div style="font-size:11px;color:#374151;margin-top:2px">
                            Timpa semua data lama dengan hasil mesin. Paling umum dipakai.
                        </div>
                    </div>
                </label>
                <label style="background:#DBEAFE;border:2px solid #93C5FD;border-radius:8px;padding:12px;cursor:pointer;display:flex;align-items:flex-start;gap:8px;margin:0"
                       id="lbl_exist">
                    <input type="radio" name="conflict_strategy" value="exist"
                           style="margin-top:3px;width:16px;height:16px">
                    <div>
                        <div style="font-weight:700;color:#1D4ED8">Pertahankan Data Lama</div>
                        <div style="font-size:11px;color:#374151;margin-top:2px">
                            Data Manual/RFID yang sudah ada tidak diubah.
                        </div>
                    </div>
                </label>
                <label style="background:#FEF9C3;border:2px solid #FDE68A;border-radius:8px;padding:12px;cursor:pointer;display:flex;align-items:flex-start;gap:8px;margin:0"
                       id="lbl_manual">
                    <input type="radio" name="conflict_strategy" value="manual"
                           style="margin-top:3px;width:16px;height:16px">
                    <div>
                        <div style="font-weight:700;color:#92400E">Pilih Manual per Sel</div>
                        <div style="font-size:11px;color:#374151;margin-top:2px">
                            Review tiap konflik satu per satu di halaman preview.
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <div style="display:flex;gap:10px;align-items:center">
            <button type="submit" class="btn btn-primary"
                    style="padding:10px 28px;font-size:15px">
                <i class="fas fa-search"></i> Preview Data Import
            </button>
            <a href="<?php echo e(route('admin.mesin.mapping-santri.index')); ?>"
               class="btn btn-secondary" style="padding:10px 20px">
                <i class="fas fa-link"></i> Kelola Mapping Santri
            </a>
        </div>
    </form>
</div>


<?php if($riwayat->count() > 0): ?>
<div class="content-box" style="margin-top:14px">
    <h4 style="margin:0 0 12px">
        <i class="fas fa-history"></i> Riwayat Import Terakhir
    </h4>
    <div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Jumlah Scan</th>
                <th>Berhasil</th>
                <th>Konflik Selesai</th>
                <th>Duplikat</th>
                <th>Tanpa Mapping</th>
                <th>Oleh</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $riwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($r->created_at->format('d/m/Y H:i')); ?></td>
                <td><?php echo e(number_format($r->jumlah_scan)); ?></td>
                <td>
                    <span class="badge badge-success"><?php echo e($r->berhasil); ?></span>
                </td>
                <td>
                    <?php if($r->konflik_selesai > 0): ?>
                        <span class="badge badge-warning"><?php echo e($r->konflik_selesai); ?></span>
                    <?php else: ?> <span style="color:#9CA3AF">-</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($r->dilewati > 0): ?>
                        <span class="badge badge-secondary"><?php echo e($r->dilewati); ?></span>
                    <?php else: ?> <span style="color:#9CA3AF">-</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($r->no_santri > 0): ?>
                        <span class="badge badge-danger"><?php echo e($r->no_santri); ?></span>
                    <?php else: ?> <span style="color:#9CA3AF">-</span>
                    <?php endif; ?>
                </td>
                <td style="color:#6B7280"><?php echo e($r->user->name ?? '-'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    </div>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/mesin/import/index.blade.php ENDPATH**/ ?>