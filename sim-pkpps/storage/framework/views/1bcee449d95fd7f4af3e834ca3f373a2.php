<?php $__env->startSection('title', 'Riwayat Pelanggaran'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-exclamation-circle"></i> Riwayat Pelanggaran Saya</h2>
</div>


<?php
    $tingkatPoin = '';
    $warnaPoin   = '';
    if ($totalPoin >= 50) {
        $tingkatPoin = 'Berat';
        $warnaPoin   = 'var(--danger-color)';
    } elseif ($totalPoin >= 20) {
        $tingkatPoin = 'Sedang';
        $warnaPoin   = '#e67e22';
    } elseif ($totalPoin > 0) {
        $tingkatPoin = 'Ringan';
        $warnaPoin   = 'var(--warning-color)';
    }
?>

<?php if($totalPoin >= 50): ?>
<div style="background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%); color: white; padding: 16px 20px; border-radius: var(--border-radius); margin-bottom: 18px; display: flex; align-items: center; gap: 14px; box-shadow: 0 4px 15px rgba(220,53,69,0.3);">
    <i class="fas fa-exclamation-triangle" style="font-size: 2em; opacity: 0.9;"></i>
    <div>
        <strong style="font-size: 1.05em;">⚠️ Perhatian! Akumulasi poin Anda sudah tinggi (<?php echo e($totalPoin); ?> poin)</strong><br>
        <span style="opacity: 0.9; font-size: 0.9em;">Harap segera hubungi pengurus pondok untuk konsultasi dan penyelesaian kafaroh yang ada.</span>
    </div>
</div>
<?php elseif($totalPoin >= 20): ?>
<div style="background: linear-gradient(135deg, #e67e22 0%, #ca6f1e 100%); color: white; padding: 16px 20px; border-radius: var(--border-radius); margin-bottom: 18px; display: flex; align-items: center; gap: 14px; box-shadow: 0 4px 15px rgba(230,126,34,0.3);">
    <i class="fas fa-exclamation-circle" style="font-size: 2em; opacity: 0.9;"></i>
    <div>
        <strong style="font-size: 1.05em;">Catatan: Poin pelanggaran Anda mulai bertambah (<?php echo e($totalPoin); ?> poin)</strong><br>
        <span style="opacity: 0.9; font-size: 0.9em;">Jaga perilaku dan taati peraturan pondok agar poin tidak terus bertambah.</span>
    </div>
</div>
<?php endif; ?>


<div class="row-cards">
    <div class="card card-danger">
        <h3>Total Pelanggaran</h3>
        <div class="card-value"><?php echo e($totalPelanggaran); ?></div>
        <p style="margin: 4px 0 0; color: var(--text-light); font-size: 0.85em;">Sepanjang waktu</p>
        <i class="fas fa-clipboard-list card-icon"></i>
    </div>

    <div class="card card-warning">
        <h3>Total Poin</h3>
        <div class="card-value"><?php echo e($totalPoin); ?></div>
        <p style="margin: 4px 0 0; color: var(--text-light); font-size: 0.85em;">
            <?php if($tingkatPoin): ?>
                Tingkat: <strong><?php echo e($tingkatPoin); ?></strong>
            <?php else: ?>
                Belum ada poin
            <?php endif; ?>
        </p>
        <i class="fas fa-star card-icon"></i>
    </div>

    <div class="card card-info">
        <h3>Bulan Ini</h3>
        <div class="card-value"><?php echo e($pelanggaranBulanIni); ?></div>
        <p style="margin: 4px 0 0; color: var(--text-light); font-size: 0.85em;"><?php echo e(\Carbon\Carbon::now()->isoFormat('MMMM YYYY')); ?></p>
        <i class="fas fa-calendar-alt card-icon"></i>
    </div>
</div>


<?php if($totalPoin > 0): ?>
<div class="content-box" style="margin-bottom: 18px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
        <h4 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-chart-bar"></i> Akumulasi Poin Pelanggaran
        </h4>
        <span style="font-size: 0.85em; color: var(--text-light);">Batas perhatian: 20 poin | Batas berat: 50 poin</span>
    </div>

    
    <?php
        $maxPoin = max(50, $totalPoin); // skala dinamis
        $persenBar = min(100, round(($totalPoin / $maxPoin) * 100));
        if ($totalPoin < 20) {
            $colorBar = 'var(--success-color)';
        } elseif ($totalPoin < 50) {
            $colorBar = '#e67e22';
        } else {
            $colorBar = 'var(--danger-color)';
        }
    ?>

    <div style="background: #f0f0f0; border-radius: 50px; height: 22px; overflow: hidden; position: relative; margin-bottom: 8px;">
        
        <div style="position: absolute; left: <?php echo e(round((20 / $maxPoin) * 100)); ?>%; top: 0; bottom: 0; width: 2px; background: rgba(0,0,0,0.2); z-index: 1;"></div>
        
        <?php if($maxPoin >= 50): ?>
        <div style="position: absolute; left: <?php echo e(round((50 / $maxPoin) * 100)); ?>%; top: 0; bottom: 0; width: 2px; background: rgba(0,0,0,0.2); z-index: 1;"></div>
        <?php endif; ?>
        <div style="height: 100%; width: <?php echo e($persenBar); ?>%; background: <?php echo e($colorBar); ?>; border-radius: 50px; transition: width 0.8s ease; display: flex; align-items: center; justify-content: flex-end; padding-right: 10px;">
            <span style="color: white; font-size: 0.78em; font-weight: 700; white-space: nowrap;"><?php echo e($totalPoin); ?> poin</span>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; font-size: 0.78em; color: var(--text-light);">
        <span>0</span>
        <span style="margin-left: <?php echo e(round((20/$maxPoin)*100)); ?>%">20</span>
        <?php if($maxPoin >= 50): ?><span>50+</span><?php endif; ?>
    </div>
</div>
<?php endif; ?>


<div class="content-box" style="margin-bottom: 14px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 11px;">
        <form method="GET" action="<?php echo e(route('santri.pelanggaran.index')); ?>" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
            <div>
                <label style="font-size: 0.82em; color: var(--text-light); display: block; margin-bottom: 4px;">
                    <i class="fas fa-calendar-day"></i> Tanggal Mulai
                </label>
                <input type="date" name="tanggal_mulai" class="form-control" style="width: auto;"
                       value="<?php echo e(request('tanggal_mulai')); ?>">
            </div>
            <div>
                <label style="font-size: 0.82em; color: var(--text-light); display: block; margin-bottom: 4px;">
                    <i class="fas fa-calendar-check"></i> Tanggal Selesai
                </label>
                <input type="date" name="tanggal_selesai" class="form-control" style="width: auto;"
                       value="<?php echo e(request('tanggal_selesai')); ?>">
            </div>
            <div style="display: flex; gap: 8px; padding-top: 2px;">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="<?php echo e(route('santri.pelanggaran.index')); ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-redo"></i> Reset
                </a>
                <a href="<?php echo e(route('santri.pelanggaran.index', ['bulan_ini' => 1])); ?>"
                   class="btn btn-sm <?php echo e(request('bulan_ini') ? 'btn-info' : 'btn-secondary'); ?>"
                   style="<?php echo e(request('bulan_ini') ? '' : 'border: 1px solid var(--primary-color); color: var(--primary-color); background: transparent;'); ?>">
                    <i class="fas fa-calendar-check"></i> Bulan Ini
                </a>
            </div>
        </form>

        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="<?php echo e(route('santri.pelanggaran.kategori')); ?>" class="btn btn-warning btn-sm">
                <i class="fas fa-list-ul"></i> Kategori & Poin
            </a>
            <a href="<?php echo e(route('santri.pembinaan.index')); ?>" class="btn btn-sm"
               style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark, #1a4980) 100%);
                      color: white; border: none;">
                <i class="fas fa-book-open"></i> Pembinaan & Sanksi
            </a>
        </div>
    </div>
</div>


<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
        <h3 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-history"></i> Daftar Riwayat Pelanggaran
        </h3>
        <?php if(request()->hasAny(['tanggal_mulai', 'tanggal_selesai', 'bulan_ini'])): ?>
        <span class="badge badge-info" style="font-size: 0.85em;">
            <i class="fas fa-filter"></i> Filter aktif — <?php echo e($riwayat->total()); ?> data
        </span>
        <?php endif; ?>
    </div>

    <?php if($riwayat->count() > 0): ?>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 10%;">ID</th>
                        <th style="width: 13%;">Tanggal</th>
                        <th style="width: 28%;">Jenis Pelanggaran</th>
                        <th style="width: 9%; text-align: center;">Poin</th>
                        <th style="width: 25%;">Keterangan</th>
                        <th style="width: 10%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $riwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr style="transition: background 0.2s;">
                        <td><?php echo e($riwayat->firstItem() + $index); ?></td>
                        <td>
                            <span class="badge badge-secondary" style="font-size: 0.8em;">
                                <?php echo e($item->id_riwayat); ?>

                            </span>
                        </td>
                        <td>
                            <div style="white-space: nowrap;">
                                <i class="fas fa-calendar-day" style="color: var(--text-light); font-size: 0.85em;"></i>
                                <span style="font-size: 0.9em;"><?php echo e(\Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMM YYYY')); ?></span>
                            </div>
                        </td>
                        <td>
                            <strong><?php echo e($item->kategori->nama_pelanggaran ?? '-'); ?></strong>
                            <?php if($item->kategori && $item->kategori->id_kategori): ?>
                            <br><small style="color: var(--text-light);">
                                <i class="fas fa-tag"></i> <?php echo e($item->kategori->id_kategori); ?>

                            </small>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <?php $poin = $item->poin; ?>
                            <span class="badge badge-danger"
                                  style="font-size: 0.9em; padding: 5px 10px;
                                         <?php echo e($poin >= 20 ? 'background: linear-gradient(135deg, #dc3545, #a71d2a);' : ''); ?>">
                                <i class="fas fa-fire"></i> <?php echo e($poin); ?>

                            </span>
                        </td>
                        <td>
                            <?php if($item->keterangan): ?>
                                <div style="max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
                                            font-size: 0.88em; color: var(--text-color);"
                                     title="<?php echo e($item->keterangan); ?>">
                                    <?php echo e($item->keterangan); ?>

                                </div>
                            <?php else: ?>
                                <span style="color: var(--text-light); font-size: 0.85em; font-style: italic;">Tidak ada keterangan</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <a href="<?php echo e(route('santri.pelanggaran.show', $item->id)); ?>"
                               class="btn btn-info btn-sm"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        
        <div style="margin-top: 14px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <p style="margin: 0; color: var(--text-light); font-size: 0.88em;">
                Menampilkan <?php echo e($riwayat->firstItem()); ?>–<?php echo e($riwayat->lastItem()); ?> dari <?php echo e($riwayat->total()); ?> data
            </p>
            <?php echo e($riwayat->links()); ?>

        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-check-circle" style="color: var(--success-color);"></i>
            <h3>Tidak Ada Riwayat Pelanggaran</h3>
            <?php if(request()->hasAny(['tanggal_mulai', 'tanggal_selesai', 'bulan_ini'])): ?>
                <p>Tidak ditemukan data dengan filter yang dipilih.</p>
                <a href="<?php echo e(route('santri.pelanggaran.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Hapus Filter
                </a>
            <?php else: ?>
                <p>Alhamdulillah, Anda belum memiliki catatan pelanggaran. Pertahankan!</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>


<?php if($totalPelanggaran > 0): ?>
<div class="content-box" style="margin-top: 18px;">
    <h3 style="margin-bottom: 18px; color: var(--primary-color);">
        <i class="fas fa-chart-pie"></i> Ringkasan Analisis
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">

        <div style="background: var(--primary-light); padding: 16px; border-radius: var(--border-radius-sm); text-align: center;">
            <i class="fas fa-calculator" style="font-size: 1.8em; color: var(--primary-color); margin-bottom: 8px;"></i>
            <div style="font-size: 1.6em; font-weight: 700; color: var(--primary-dark);">
                <?php echo e($totalPelanggaran > 0 ? number_format($totalPoin / $totalPelanggaran, 1) : 0); ?>

            </div>
            <p style="margin: 4px 0 0; color: var(--text-light); font-size: 0.85em;">Rata-rata Poin/Pelanggaran</p>
        </div>

        <div style="background: <?php echo e($totalPoin >= 50 ? 'var(--danger-color)' : ($totalPoin >= 20 ? '#fdebd0' : '#eafaf1')); ?>;
                    padding: 16px; border-radius: var(--border-radius-sm); text-align: center;">
            <i class="fas fa-signal" style="font-size: 1.8em; color: <?php echo e($totalPoin >= 50 ? 'white' : ($totalPoin >= 20 ? '#ca6f1e' : 'var(--success-color)')); ?>; margin-bottom: 8px;"></i>
            <div style="font-size: 1.4em; font-weight: 700; color: <?php echo e($totalPoin >= 50 ? 'white' : ($totalPoin >= 20 ? '#ca6f1e' : 'var(--success-color)')); ?>;">
                <?php if($totalPoin >= 50): ?> Berat
                <?php elseif($totalPoin >= 20): ?> Sedang
                <?php else: ?> Ringan
                <?php endif; ?>
            </div>
            <p style="margin: 4px 0 0; color: <?php echo e($totalPoin >= 50 ? 'rgba(255,255,255,0.8)' : 'var(--text-light)'); ?>; font-size: 0.85em;">Tingkat Pelanggaran</p>
        </div>

        <div style="background: #f8f9fa; padding: 16px; border-radius: var(--border-radius-sm); text-align: center;">
            <i class="fas fa-calendar-day" style="font-size: 1.8em; color: var(--primary-color); margin-bottom: 8px;"></i>
            <div style="font-size: 1em; font-weight: 600; color: var(--text-color);">
                <?php if($riwayat->first()): ?>
                    <?php echo e(\Carbon\Carbon::parse($riwayat->first()->tanggal)->isoFormat('D MMM YYYY')); ?>

                <?php else: ?>
                    —
                <?php endif; ?>
            </div>
            <p style="margin: 4px 0 0; color: var(--text-light); font-size: 0.85em;">Pelanggaran Terakhir</p>
        </div>

        <div style="background: #fff3cd; padding: 16px; border-radius: var(--border-radius-sm); text-align: center;">
            <i class="fas fa-calendar-check" style="font-size: 1.8em; color: #856404; margin-bottom: 8px;"></i>
            <div style="font-size: 1.6em; font-weight: 700; color: #856404;">
                <?php echo e($pelanggaranBulanIni); ?>

            </div>
            <p style="margin: 4px 0 0; color: #856404; font-size: 0.85em;">Pelanggaran Bulan Ini</p>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/pelanggaran/index.blade.php ENDPATH**/ ?>