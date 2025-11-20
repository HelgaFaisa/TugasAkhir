


<?php $__env->startSection('title', 'Detail Transaksi Uang Saku'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-file-invoice"></i> Detail Transaksi Uang Saku</h2>
</div>

<div class="content-box">
    <div class="detail-header">
        <div>
            <h3><i class="fas fa-receipt"></i> Informasi Transaksi</h3>
            <p class="text-muted">Detail lengkap transaksi uang saku</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="<?php echo e(route('santri.uang-saku.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="detail-section">
        <h4><i class="fas fa-info-circle"></i> Data Transaksi</h4>
        <table class="detail-table">
            <tr>
                <th><i class="fas fa-hashtag"></i> ID Transaksi</th>
                <td><strong><?php echo e($transaksi->id_uang_saku); ?></strong></td>
            </tr>
            <tr>
                <th><i class="fas fa-calendar"></i> Tanggal Transaksi</th>
                <td><?php echo e(\Carbon\Carbon::parse($transaksi->tanggal_transaksi)->isoFormat('dddd, D MMMM YYYY')); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-exchange-alt"></i> Jenis Transaksi</th>
                <td>
                    <?php if($transaksi->jenis_transaksi === 'pemasukan'): ?>
                        <span class="badge badge-lg badge-success">
                            <i class="fas fa-arrow-down"></i> Pemasukan
                        </span>
                    <?php else: ?>
                        <span class="badge badge-lg badge-danger">
                            <i class="fas fa-arrow-up"></i> Pengeluaran
                        </span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><i class="fas fa-money-bill-wave"></i> Nominal</th>
                <td>
                    <span style="font-size: 1.3rem; font-weight: 700; color: <?php echo e($transaksi->jenis_transaksi === 'pemasukan' ? 'var(--success-color)' : 'var(--danger-color)'); ?>">
                        <?php echo e('Rp ' . number_format($transaksi->nominal, 0, ',', '.')); ?>

                    </span>
                </td>
            </tr>
            <tr>
                <th><i class="fas fa-wallet"></i> Saldo Sebelum</th>
                <td><?php echo e('Rp ' . number_format($transaksi->saldo_sebelum, 0, ',', '.')); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-wallet"></i> Saldo Sesudah</th>
                <td>
                    <strong style="color: var(--primary-color); font-size: 1.1rem;">
                        <?php echo e('Rp ' . number_format($transaksi->saldo_sesudah, 0, ',', '.')); ?>

                    </strong>
                </td>
            </tr>
            <tr>
                <th><i class="fas fa-sticky-note"></i> Keterangan</th>
                <td><?php echo e($transaksi->keterangan ?? '-'); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-clock"></i> Dicatat Pada</th>
                <td><?php echo e($transaksi->created_at->format('d/m/Y H:i:s')); ?></td>
            </tr>
        </table>
    </div>

    <div class="detail-section">
        <h4><i class="fas fa-user-graduate"></i> Data Santri</h4>
        <table class="detail-table">
            <tr>
                <th><i class="fas fa-id-card"></i> ID Santri</th>
                <td><?php echo e($transaksi->santri->id_santri); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-user"></i> Nama Lengkap</th>
                <td><?php echo e($transaksi->santri->nama_lengkap); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-chalkboard-teacher"></i> Kelas</th>
                <td><?php echo e($transaksi->santri->kelas); ?></td>
            </tr>
        </table>
    </div>

    
    <div class="detail-section">
        <h4><i class="fas fa-chart-line"></i> Visualisasi Transaksi</h4>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            
            <div style="background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%); padding: 20px; border-radius: 12px; text-align: center;">
                <i class="fas fa-arrow-down" style="font-size: 2rem; color: var(--success-color); margin-bottom: 10px;"></i>
                <h5 style="margin: 0 0 5px 0; color: var(--text-light); font-size: 0.9rem;">Nominal Pemasukan</h5>
                <p style="font-size: 1.5rem; font-weight: 700; color: var(--success-color); margin: 0;">
                    <?php echo e($transaksi->jenis_transaksi === 'pemasukan' ? 'Rp ' . number_format($transaksi->nominal, 0, ',', '.') : 'Rp 0'); ?>

                </p>
            </div>
            
            <div style="background: linear-gradient(135deg, #FFE8EA 0%, #FFD5D8 100%); padding: 20px; border-radius: 12px; text-align: center;">
                <i class="fas fa-arrow-up" style="font-size: 2rem; color: var(--danger-color); margin-bottom: 10px;"></i>
                <h5 style="margin: 0 0 5px 0; color: var(--text-light); font-size: 0.9rem;">Nominal Pengeluaran</h5>
                <p style="font-size: 1.5rem; font-weight: 700; color: var(--danger-color); margin: 0;">
                    <?php echo e($transaksi->jenis_transaksi === 'pengeluaran' ? 'Rp ' . number_format($transaksi->nominal, 0, ',', '.') : 'Rp 0'); ?>

                </p>
            </div>
        </div>

        
        <div style="background: white; padding: 20px; border-radius: 12px; border: 2px solid var(--primary-light);">
            <h5 style="margin: 0 0 15px 0; color: var(--text-color); font-size: 1rem;">
                <i class="fas fa-wallet"></i> Perubahan Saldo
            </h5>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span style="font-size: 0.9rem; color: var(--text-light);">Saldo Sebelum</span>
                <strong style="color: var(--text-color);"><?php echo e('Rp ' . number_format($transaksi->saldo_sebelum, 0, ',', '.')); ?></strong>
            </div>
            
            <?php
                $maxSaldo = max($transaksi->saldo_sebelum, $transaksi->saldo_sesudah);
                $persentaseSebelum = $maxSaldo > 0 ? ($transaksi->saldo_sebelum / $maxSaldo) * 100 : 0;
                $persentaseSesudah = $maxSaldo > 0 ? ($transaksi->saldo_sesudah / $maxSaldo) * 100 : 0;
            ?>
            
            <div style="position: relative; height: 40px; background: #e0e0e0; border-radius: 20px; overflow: hidden; margin-bottom: 10px;">
                <div style="position: absolute; height: 100%; background: linear-gradient(90deg, var(--primary-color), var(--primary-dark)); width: <?php echo e($persentaseSebelum); ?>%; border-radius: 20px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.85rem;">
                    <?php if($persentaseSebelum > 15): ?>
                        <?php echo e(number_format($persentaseSebelum, 0)); ?>%
                    <?php endif; ?>
                </div>
            </div>
            
            <div style="text-align: center; margin: 15px 0;">
                <i class="fas fa-arrow-<?php echo e($transaksi->jenis_transaksi === 'pemasukan' ? 'up' : 'down'); ?>" 
                   style="font-size: 1.5rem; color: <?php echo e($transaksi->jenis_transaksi === 'pemasukan' ? 'var(--success-color)' : 'var(--danger-color)'); ?>;"></i>
                <p style="margin: 5px 0 0 0; font-size: 0.85rem; color: var(--text-light);">
                    <?php echo e($transaksi->jenis_transaksi === 'pemasukan' ? 'Bertambah' : 'Berkurang'); ?> 
                    <strong><?php echo e('Rp ' . number_format(abs($transaksi->saldo_sesudah - $transaksi->saldo_sebelum), 0, ',', '.')); ?></strong>
                </p>
            </div>
            
            <div style="position: relative; height: 40px; background: #e0e0e0; border-radius: 20px; overflow: hidden; margin-bottom: 10px;">
                <div style="position: absolute; height: 100%; background: linear-gradient(90deg, <?php echo e($transaksi->jenis_transaksi === 'pemasukan' ? 'var(--success-color)' : 'var(--danger-color)'); ?>, <?php echo e($transaksi->jenis_transaksi === 'pemasukan' ? '#4CAF50' : '#E77580'); ?>); width: <?php echo e($persentaseSesudah); ?>%; border-radius: 20px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.85rem;">
                    <?php if($persentaseSesudah > 15): ?>
                        <?php echo e(number_format($persentaseSesudah, 0)); ?>%
                    <?php endif; ?>
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between;">
                <span style="font-size: 0.9rem; color: var(--text-light);">Saldo Sesudah</span>
                <strong style="color: <?php echo e($transaksi->jenis_transaksi === 'pemasukan' ? 'var(--success-color)' : 'var(--danger-color)'); ?>; font-size: 1.1rem;">
                    <?php echo e('Rp ' . number_format($transaksi->saldo_sesudah, 0, ',', '.')); ?>

                </strong>
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid var(--primary-light); text-align: center;">
        <a href="<?php echo e(route('santri.uang-saku.index')); ?>" class="btn btn-primary btn-lg">
            <i class="fas fa-list"></i> Lihat Semua Riwayat
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/uang-saku/show.blade.php ENDPATH**/ ?>