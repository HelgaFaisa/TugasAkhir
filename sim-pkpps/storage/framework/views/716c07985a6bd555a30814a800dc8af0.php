

<?php $__env->startSection('title', 'Kenaikan Kelas Massal'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
    <div>
        <h2><i class="fas fa-graduation-cap"></i> Kenaikan Kelas Massal</h2>
        <p class="text-muted" style="margin: 0;">Kelola kenaikan kelas santri per tahun ajaran</p>
    </div>
    <a href="<?php echo e(route('admin.kelas.index')); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali ke Kelola Kelas
    </a>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Info Card -->
<div class="content-box" style="margin-bottom: 14px;">
    <div class="row-cards">
        <div class="card card-info">
            <h3>Tahun Ajaran Aktif</h3>
            <div class="card-value"><?php echo e($tahunAjaranAktif); ?></div>
            <p class="text-muted" style="margin: 0;">Tahun ajaran saat ini</p>
        </div>
        <div class="card card-success">
            <h3>Total Santri Aktif</h3>
            <div class="card-value"><?php echo e($totalSantriAktif); ?></div>
            <p class="text-muted" style="margin: 0;">Santri dengan status aktif</p>
        </div>
        <div class="card card-warning">
            <h3>Tahun Ajaran Baru</h3>
            <div class="card-value" style="font-size: 1.8rem;"><?php echo e($tahunAjaranBaru); ?></div>
            <p class="text-muted" style="margin: 0;">Target kenaikan kelas</p>
        </div>
    </div>
</div>

<!-- Filter by Kelompok -->
<div class="content-box" style="margin-bottom: 14px;">
    <form method="GET" action="<?php echo e(route('admin.kelas.kenaikan.index')); ?>" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
        <label style="margin: 0; font-weight: 600; color: var(--primary-dark);">
            <i class="fas fa-filter"></i> Pilih Kelompok Kelas:
        </label>
        <select name="kelompok" class="form-control" style="max-width: 300px;" onchange="this.form.submit()">
            <?php $__currentLoopData = $kelompokKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelompok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($kelompok->id_kelompok); ?>"
                        <?php echo e($selectedKelompok == $kelompok->id_kelompok ? 'selected' : ''); ?>>
                    <?php echo e($kelompok->nama_kelompok); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </form>
</div>

<?php if($kelasList->isNotEmpty()): ?>
    <?php
        $currentKelompok = $kelompokKelas->where('id_kelompok', $selectedKelompok)->first();
    ?>

    <div class="content-box" style="margin-bottom: 25px;">
        <div style="background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%); padding: 14px; border-radius: 8px; margin-bottom: 14px;">
            <h3 style="margin: 0 0 8px 0; color: var(--primary-dark);">
                <i class="fas fa-layer-group"></i>
                <?php echo e($currentKelompok->nama_kelompok ?? 'Kelas'); ?>

            </h3>
            <p style="margin: 0; color: var(--text-light); font-size: 0.95rem;">
                <?php echo e($currentKelompok->deskripsi ?? 'Kelola kenaikan kelas untuk kelompok ini'); ?>

            </p>
        </div>

        <div class="table-wrapper">

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Kelas Asal</th>
                    <th style="width: 140px; text-align: center;">Santri Aktif</th>
                    <th style="width: 280px;">Naik ke Kelas</th>
                    <th style="width: 200px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $kelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td>
                            <strong style="color: var(--primary-dark);"><?php echo e($kelas->nama_kelas); ?></strong>
                            <br>
                            <span class="text-muted" style="font-size: 0.85rem;">
                                <i class="fas fa-tag"></i> <?php echo e($kelas->kode_kelas); ?>

                            </span>
                        </td>
                        <td style="text-align: center;">
                            <?php if($kelas->santri_aktif_count > 0): ?>
                                <span class="badge badge-info" style="font-size: 0.95rem; padding: 8px 14px;">
                                    <i class="fas fa-users"></i> <?php echo e($kelas->santri_aktif_count); ?> santri
                                </span>
                            <?php else: ?>
                                <span class="badge badge-secondary" style="font-size: 0.9rem; padding: 7px 12px;">
                                    <i class="fas fa-user-slash"></i> 0 santri
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($kelas->santri_aktif_count > 0): ?>
                                <select class="form-control form-control-sm target-kelas-select"
                                        data-kelas-id="<?php echo e($kelas->id); ?>"
                                        style="font-size: 0.9rem;">
                                    <option value="">-- Pilih Kelas Tujuan --</option>
                                    <?php $__currentLoopData = $allKelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $targetKelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($targetKelas->id != $kelas->id): ?>
                                            <option value="<?php echo e($targetKelas->id); ?>">
                                                <?php echo e($targetKelas->kelompok->nama_kelompok); ?> - <?php echo e($targetKelas->nama_kelas); ?>

                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            <?php else: ?>
                                <span class="text-muted" style="font-size:0.85rem;"></span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if($kelas->santri_aktif_count > 0): ?>
                                <div style="display: flex; gap: 5px; justify-content: center;">
                                    <button type="button"
                                            class="btn btn-sm btn-success btn-naikkan"
                                            data-kelas-id="<?php echo e($kelas->id); ?>"
                                            data-kelas-nama="<?php echo e($kelas->nama_kelas); ?>"
                                            data-santri-count="<?php echo e($kelas->santri_aktif_count); ?>"
                                            disabled
                                            title="Pilih kelas tujuan terlebih dahulu">
                                        <i class="fas fa-arrow-up"></i> Naikkan
                                    </button>
                                    <a href="<?php echo e(route('admin.kelas.kenaikan.preview', $kelas->id)); ?>"
                                       class="btn btn-sm btn-info"
                                       title="Lihat & Pilih Santri">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </div>
                            <?php else: ?>
                                <span class="badge badge-secondary">
                                    <i class="fas fa-user-slash"></i> Tidak ada santri
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        </div>
    </div>
<?php else: ?>
    <div class="content-box">
        <div class="text-center py-5">
            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Tidak ada kelas ditemukan</h5>
            <p class="text-muted">Belum ada kelas aktif di kelompok yang dipilih.</p>
        </div>
    </div>
<?php endif; ?>

<div class="alert alert-info" style="border-left: 4px solid var(--info-color); background: #E3F7FC;">
    <div style="display: flex; gap: 11px;">
        <div style="font-size: 2rem; color: var(--info-color);"><i class="fas fa-info-circle"></i></div>
        <div>
            <strong style="color: var(--primary-dark); font-size: 1.1rem;">Cara Menggunakan Kenaikan Kelas:</strong>
            <ol style="margin: 10px 0 0 0; padding-left: 20px; color: var(--text-color);">
                <li style="margin-bottom: 8px;"><strong>Pilih Kelompok Kelas</strong> dari dropdown untuk menampilkan daftar kelas</li>
                <li style="margin-bottom: 8px;"><strong>Pilih Kelas Tujuan</strong> di kolom dropdown tiap baris</li>
                <li style="margin-bottom: 8px;">Klik <span class="badge badge-success"><i class="fas fa-arrow-up"></i> Naikkan</span> untuk memproses semua santri di kelas tersebut</li>
                <li style="margin-bottom: 8px;">Atau klik <span class="badge badge-info"><i class="fas fa-eye"></i> Lihat</span> untuk memilih santri secara individual</li>
                <li style="margin-bottom: 8px;">Santri akan dipindahkan ke <strong>Tahun Ajaran <?php echo e($tahunAjaranBaru); ?></strong></li>
            </ol>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // â”€â”€ Enable/disable tombol Naikkan berdasarkan pilihan dropdown â”€â”€
    document.querySelectorAll('.target-kelas-select').forEach(function (select) {
        var kelasId = select.dataset.kelasId;
        var button  = document.querySelector('.btn-naikkan[data-kelas-id="' + kelasId + '"]');
        if (!button) return;

        select.addEventListener('change', function () {
            if (this.value) {
                button.disabled = false;
                button.title    = 'Klik untuk menaikkan kelas semua santri';
            } else {
                button.disabled = true;
                button.title    = 'Pilih kelas tujuan terlebih dahulu';
            }
        });
    });

    // â”€â”€ Handle klik tombol Naikkan â”€â”€
    document.querySelectorAll('.btn-naikkan').forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            var kelasId      = this.dataset.kelasId;
            var kelasNama    = this.dataset.kelasNama;
            var santriCount  = this.dataset.santriCount;
            var select       = document.querySelector('.target-kelas-select[data-kelas-id="' + kelasId + '"]');

            if (!select || !select.value) {
                if (select) {
                    select.focus();
                    select.style.border = '2px solid #FF8B94';
                    setTimeout(function () { select.style.border = ''; }, 2000);
                }
                alert('Silakan pilih kelas tujuan terlebih dahulu!');
                return;
            }

            var targetKelasText = select.options[select.selectedIndex].text;
            var tahunAjaranBaru = '<?php echo e($tahunAjaranBaru); ?>';

            var confirmMessage =
                'KONFIRMASI KENAIKAN KELAS\n\n' +
                'Kelas Asal   : ' + kelasNama + '\n' +
                'Kelas Tujuan : ' + targetKelasText + '\n' +
                'Jumlah Santri: ' + santriCount + ' orang\n' +
                'Tahun Ajaran : ' + tahunAjaranBaru + '\n\n' +
                'Proses ini akan memindahkan SEMUA santri aktif ke kelas dan tahun ajaran baru.\n' +
                'Lanjutkan?';

            if (!confirm(confirmMessage)) return;

            // Disable tombol agar tidak double-submit
            this.disabled  = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

            // Buat form dan submit
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo e(route("admin.kelas.kenaikan.process")); ?>';

            [
                { name: '_token',           value: '<?php echo e(csrf_token()); ?>' },
                { name: 'id_kelas_asal',    value: kelasId },
                { name: 'id_kelas_tujuan',  value: select.value },
            ].forEach(function (item) {
                var input   = document.createElement('input');
                input.type  = 'hidden';
                input.name  = item.name;
                input.value = item.value;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        });
    });

});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kelas/kenaikan/index.blade.php ENDPATH**/ ?>