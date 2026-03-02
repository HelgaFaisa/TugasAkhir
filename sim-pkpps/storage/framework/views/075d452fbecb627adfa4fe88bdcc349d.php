


<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-calendar-alt"></i> Jadwal Kegiatan Santri</h2>
    <div style="display: flex; gap: 8px;">
        <a href="<?php echo e(route('admin.kegiatan.create')); ?>" class="btn btn-success btn-sm">
            <i class="fas fa-plus"></i> Tambah Kegiatan
        </a>
        <a href="<?php echo e(route('admin.kategori-kegiatan.index')); ?>" class="btn btn-info btn-sm">
                <i class="fas fa-tags"></i> Kategori
            </a>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>


<div class="content-box" style="margin-bottom: 14px;">
    <form method="GET" class="filter-form-inline">
        <div class="filter-form-inline" style="gap: 8px;">
            <label style="font-weight: 600; white-space: nowrap; margin: 0;">
                <i class="fas fa-calendar-day"></i> Hari:
            </label>
            <select name="hari" class="form-control" onchange="this.form.submit()" style="max-width: 160px;">
                <option value="">Semua Hari</option>
                <?php $__currentLoopData = $hariList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hari): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($hari); ?>" <?php echo e(request('hari') == $hari ? 'selected' : ''); ?>><?php echo e($hari); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="filter-form-inline" style="gap: 8px;">
            <label style="font-weight: 600; white-space: nowrap; margin: 0;">
                <i class="fas fa-tags"></i> Kategori:
            </label>
            <select name="kategori_id" class="form-control" onchange="this.form.submit()" style="max-width: 180px;">
                <option value="">Semua Kategori</option>
                <?php $__currentLoopData = $kategoris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($kat->kategori_id); ?>" <?php echo e(request('kategori_id') == $kat->kategori_id ? 'selected' : ''); ?>>
                        <?php echo e($kat->nama_kategori); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="filter-form-inline" style="gap: 8px;">
            <label style="font-weight: 600; white-space: nowrap; margin: 0;">
                <i class="fas fa-school"></i> Kelas:
            </label>
            <select name="kelas_id" class="form-control" onchange="this.form.submit()" style="max-width: 200px;">
                <option value="">Semua Kelas</option>
                <option value="umum" <?php echo e(request('kelas_id') === 'umum' ? 'selected' : ''); ?>>Kegiatan Umum</option>
                <?php $__currentLoopData = $kelasList->groupBy('kelompok.nama_kelompok'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelompokNama => $kelasGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <optgroup label="<?php echo e($kelompokNama); ?>">
                        <?php $__currentLoopData = $kelasGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($kelas->id); ?>" <?php echo e(request('kelas_id') == $kelas->id ? 'selected' : ''); ?>>
                                <?php echo e($kelas->nama_kelas); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </optgroup>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="filter-form-inline" style="gap: 8px;">
            <label style="font-weight: 600; white-space: nowrap; margin: 0;">
                <i class="fas fa-search"></i>
            </label>
            <input type="text" name="search" class="form-control" placeholder="Cari kegiatan..."
                   value="<?php echo e(request('search')); ?>" style="max-width: 180px;">
        </div>

        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-filter"></i> Filter
        </button>

        <?php if(request()->hasAny(['hari', 'kategori_id', 'kelas_id', 'search'])): ?>
            <a href="<?php echo e(route('admin.kegiatan.jadwal')); ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-times"></i> Reset
            </a>
        <?php endif; ?>
    </form>
</div>


<?php
    $kegiatanPerHari = $kegiatans->groupBy('hari');
    $urutanHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];
?>

<?php if($kegiatans->count() > 0): ?>
    <?php $__currentLoopData = $urutanHari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hari): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $kegiatanHari = $kegiatanPerHari->get($hari, collect());
        ?>
        <?php if($kegiatanHari->count() > 0): ?>
            <div class="content-box" style="margin-bottom: 16px;">
                <h4 style="margin: 0 0 14px; color: var(--primary-color); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-calendar-day"></i> <?php echo e($hari); ?>

                    <span class="badge badge-primary"><?php echo e($kegiatanHari->count()); ?> kegiatan</span>
                </h4>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Kegiatan</th>
                            <th style="width: 130px;">Waktu</th>
                            <th style="width: 140px;">Kategori</th>
                            <th>Kelas</th>
                            <th style="width: 100px;">Materi</th>
                            <th style="width: 120px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $kegiatanHari->sortBy('waktu_mulai'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $kegiatan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><strong><?php echo e($kegiatan->nama_kegiatan); ?></strong></td>
                            <td>
                                <i class="fas fa-clock" style="color: var(--primary-color);"></i>
                                <?php echo e(date('H:i', strtotime($kegiatan->waktu_mulai))); ?> &ndash; <?php echo e(date('H:i', strtotime($kegiatan->waktu_selesai))); ?>

                            </td>
                            <td>
                                <span class="badge badge-info"><?php echo e($kegiatan->kategori->nama_kategori); ?></span>
                            </td>
                            <td>
                                <?php if($kegiatan->kelasKegiatan->isEmpty()): ?>
                                    <span class="badge badge-secondary"><i class="fas fa-users"></i> Umum</span>
                                <?php else: ?>
                                    <?php $__currentLoopData = $kegiatan->kelasKegiatan->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge badge-primary"><?php echo e($kls->nama_kelas); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($kegiatan->kelasKegiatan->count() > 3): ?>
                                        <span class="badge badge-light">+<?php echo e($kegiatan->kelasKegiatan->count() - 3); ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e(Str::limit($kegiatan->materi, 30) ?: '-'); ?></td>
                            <td class="text-center">
                                <div style="display: flex; gap: 4px; justify-content: center;">
                                    <a href="<?php echo e(route('admin.kegiatan.edit', $kegiatan)); ?>" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.kegiatan.show', $kegiatan)); ?>" class="btn btn-sm btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="<?php echo e(route('admin.kegiatan.destroy', $kegiatan)); ?>" method="POST"
                                          onsubmit="return confirm('Yakin hapus kegiatan ini?');" style="display: inline;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div style="margin-top: 14px;">
        <?php echo e($kegiatans->links()); ?>

    </div>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-calendar-times"></i>
        <h3>Belum Ada Jadwal Kegiatan</h3>
        <p>Silakan tambahkan kegiatan terlebih dahulu.</p>
        <a href="<?php echo e(route('admin.kegiatan.create')); ?>" class="btn btn-success">
            <i class="fas fa-plus"></i> Tambah Kegiatan
        </a>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kegiatan/data/index.blade.php ENDPATH**/ ?>