<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-chart-line"></i> Detail Capaian Santri</h2>
</div>

<div class="content-box">
    
    <div class="detail-header">
        <div>
            <h3><?php echo e($capaian->santri->nama_lengkap); ?></h3>
            <p class="text-muted"><?php echo e($capaian->id_capaian); ?> | <?php echo e($capaian->materi->nama_kitab); ?></p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="<?php echo e(route('admin.capaian.edit', $capaian)); ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="<?php echo e(route('admin.capaian.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    
    <div style="background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%); padding: 22px; border-radius: 12px; margin: 22px 0;">
        <h4 style="margin: 0 0 20px 0; color: var(--primary-dark);">
            <i class="fas fa-chart-pie"></i> Progress Capaian
        </h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
            <div style="text-align: center; padding: 14px; background: white; border-radius: 8px;">
                <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Halaman Selesai</p>
                <h2 style="margin: 10px 0; color: var(--primary-color);"><?php echo e($capaian->jumlah_halaman_selesai); ?></h2>
                <small class="text-muted">dari <?php echo e($capaian->materi->total_halaman); ?> halaman</small>
            </div>
            <div style="text-align: center; padding: 14px; background: white; border-radius: 8px;">
                <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Persentase</p>
                <h2 style="margin: 10px 0; color: var(--success-color);"><?php echo e(number_format($capaian->persentase, 2)); ?>%</h2>
                <small class="text-muted">progress keseluruhan</small>
            </div>
            <div style="text-align: center; padding: 14px; background: white; border-radius: 8px;">
                <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Status</p>
                <div style="margin: 10px 0;">
                    <?php echo $capaian->persentase_badge; ?>

                </div>
                <small class="text-muted">
                    <?php if($capaian->persentase >= 100): ?>
                        Selesai 100%
                    <?php elseif($capaian->persentase >= 75): ?>
                        Hampir Selesai
                    <?php elseif($capaian->persentase >= 50): ?>
                        Sedang Berlangsung
                    <?php else: ?>
                        Baru Dimulai
                    <?php endif; ?>
                </small>
            </div>
        </div>
        <div style="margin-top: 14px;">
            <div class="progress-bar" style="height: 20px;">
                <div class="progress-fill" style="width: <?php echo e($capaian->persentase); ?>%; background: linear-gradient(90deg, var(--primary-color), var(--success-color)); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.85rem;">
                    <?php echo e(number_format($capaian->persentase, 1)); ?>%
                </div>
            </div>
        </div>
    </div>

    
    <div class="detail-section">
        <h4><i class="fas fa-user"></i> Informasi Santri</h4>
        <table class="detail-table">
            <tr>
                <th><i class="fas fa-user"></i> Nama Santri</th>
                <td><strong><?php echo e($capaian->santri->nama_lengkap); ?></strong></td>
            </tr>
            <tr>
                <th><i class="fas fa-id-card"></i> NIS</th>
                <td><?php echo e($capaian->santri->nis); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-users"></i> Kelas</th>
                <td><span class="badge badge-secondary"><?php echo e($capaian->santri->kelas); ?></span></td>
            </tr>
        </table>
    </div>

    <div class="detail-section">
        <h4><i class="fas fa-book"></i> Informasi Materi</h4>
        <table class="detail-table">
            <tr>
                <th><i class="fas fa-book"></i> Nama Kitab</th>
                <td><strong><?php echo e($capaian->materi->nama_kitab); ?></strong></td>
            </tr>
            <tr>
                <th><i class="fas fa-layer-group"></i> Kategori</th>
                <td><?php echo $capaian->materi->kategori_badge; ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-file-alt"></i> Total Halaman</th>
                <td><strong><?php echo e($capaian->materi->total_halaman); ?></strong> halaman</td>
            </tr>
            <tr>
                <th><i class="fas fa-book-open"></i> Range Halaman</th>
                <td>Halaman <?php echo e($capaian->materi->halaman_mulai); ?> - <?php echo e($capaian->materi->halaman_akhir); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-calendar-alt"></i> Semester</th>
                <td>
                    <strong><?php echo e($capaian->semester->nama_semester); ?></strong>
                    <?php echo $capaian->semester->status_badge; ?>

                </td>
            </tr>
        </table>
    </div>

    
    <div class="detail-section">
        <h4><i class="fas fa-clipboard-check"></i> Detail Halaman yang Selesai</h4>
        <table class="detail-table">
            <tr>
                <th><i class="fas fa-list-ol"></i> Halaman Selesai (Range)</th>
                <td>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 1.1rem;">
                        <?php echo e($capaian->halaman_selesai); ?>

                    </div>
                </td>
            </tr>
            <tr>
                <th><i class="fas fa-calculator"></i> Jumlah Halaman</th>
                <td>
                    <strong><?php echo e($capaian->jumlah_halaman_selesai); ?></strong> dari <?php echo e($capaian->materi->total_halaman); ?> halaman
                    (<?php echo e($capaian->materi->total_halaman - $capaian->jumlah_halaman_selesai); ?> halaman tersisa)
                </td>
            </tr>
            <tr>
                <th><i class="fas fa-percentage"></i> Persentase</th>
                <td><?php echo $capaian->persentase_badge; ?> (<?php echo e(number_format($capaian->persentase, 2)); ?>%)</td>
            </tr>
            <tr>
                <th><i class="fas fa-sticky-note"></i> Catatan</th>
                <td><?php echo e($capaian->catatan ?: '-'); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-calendar-day"></i> Tanggal Input</th>
                <td><?php echo e($capaian->tanggal_input->format('d F Y')); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-calendar-plus"></i> Dibuat Pada</th>
                <td><?php echo e($capaian->created_at->format('d F Y, H:i')); ?> WIB</td>
            </tr>
            <tr>
                <th><i class="fas fa-calendar-edit"></i> Terakhir Diupdate</th>
                <td><?php echo e($capaian->updated_at->format('d F Y, H:i')); ?> WIB</td>
            </tr>
        </table>
    </div>

    
    <div class="detail-section">
        <h4><i class="fas fa-th"></i> Visual Halaman yang Selesai</h4>
        <div style="background: #f8f9fa; padding: 14px; border-radius: 8px;">
            <div id="visualGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(45px, 1fr)); gap: 8px;">
                <!-- Will be generated by JavaScript -->
            </div>
            <p style="margin-top: 15px; color: var(--text-light); text-align: center;">
                <i class="fas fa-check-circle" style ="color: var(--success-color);"></i> = Selesai &nbsp;&nbsp;
                <i class="fas fa-square" style="color: #ddd;"></i> = Belum
            </p>
        </div>
    </div>

    
    <div style="margin-top: 22px; display: flex; gap: 10px; justify-content: flex-end;">
        <a href="<?php echo e(route('admin.capaian.edit', $capaian)); ?>" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Capaian
        </a>
        <a href="<?php echo e(route('admin.capaian.riwayat-santri', $capaian->id_santri)); ?>" class="btn btn-info">
            <i class="fas fa-history"></i> Lihat Riwayat Santri
        </a>
        <form action="<?php echo e(route('admin.capaian.destroy', $capaian)); ?>" method="POST" style="display: inline-block;"
              onsubmit="return confirm('Yakin ingin menghapus capaian ini?')">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Hapus Capaian
            </button>
        </form>
    </div>
</div>

<script>
// Generate visual grid
document.addEventListener('DOMContentLoaded', function() {
    const halamanSelesai = "<?php echo e($capaian->halaman_selesai); ?>";
    const halamanMulai = <?php echo e($capaian->materi->halaman_mulai); ?>;
    const halamanAkhir = <?php echo e($capaian->materi->halaman_akhir); ?>;
    
    const selectedPages = parseRangeString(halamanSelesai, halamanMulai, halamanAkhir);
    const gridContainer = document.getElementById('visualGrid');
    
    for (let i = halamanMulai; i <= halamanAkhir; i++) {
        const pageBox = document.createElement('div');
        pageBox.textContent = i;
        pageBox.style.cssText = `
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 6px;
            text-align: center;
            font-weight: 600;
            font-size: 0.85rem;
        `;
        
        if (selectedPages.has(i)) {
            pageBox.style.background = 'linear-gradient(135deg, var(--primary-color), var(--success-color))';
            pageBox.style.borderColor = 'var(--primary-color)';
            pageBox.style.color = 'white';
        } else {
            pageBox.style.background = 'white';
            pageBox.style.borderColor = '#ddd';
            pageBox.style.color = '#999';
        }
        
        gridContainer.appendChild(pageBox);
    }
});

function parseRangeString(rangeString, mulai, akhir) {
    const pages = new Set();
    const ranges = rangeString.split(',');
    
    ranges.forEach(range => {
        range = range.trim();
        if (range.includes('-')) {
            const [start, end] = range.split('-').map(num => parseInt(num.trim()));
            for (let i = start; i <= end; i++) {
                if (i >= mulai && i <= akhir) {
                    pages.add(i);
                }
            }
        } else {
            const pageNum = parseInt(range);
            if (pageNum >= mulai && pageNum <= akhir) {
                pages.add(pageNum);
            }
        }
    });
    
    return pages;
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/capaian/show.blade.php ENDPATH**/ ?>