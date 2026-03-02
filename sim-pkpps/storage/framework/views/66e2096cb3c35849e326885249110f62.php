<?php $__env->startSection('title', 'Riwayat Kesehatan'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-heartbeat"></i> Riwayat Kesehatan</h2>
    <p style="margin: 4px 0 0 0; color: var(--text-light);">
        Kunjungan UKP — <strong><?php echo e($santri->nama_lengkap); ?></strong>
    </p>
</div>


<?php if($sedangDirawatSekarang): ?>
<div style="
    background: linear-gradient(135deg, #E74C3C, #C0392B);
    color: white; padding: 16px 20px; border-radius: 12px;
    margin-bottom: 18px; display: flex; align-items: center; gap: 14px;
    box-shadow: 0 4px 18px rgba(231,76,60,0.4); animation: alertPulse 2.5s infinite;">
    <div style="background: rgba(255,255,255,0.2); width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
        <i class="fas fa-procedures" style="font-size: 1.5rem;"></i>
    </div>
    <div style="flex: 1; min-width: 0;">
        <strong style="font-size: 1.05rem; display: block;">⚠️ Kamu Sedang Dalam Perawatan UKP</strong>
        <span style="font-size: 0.85rem; opacity: 0.9; display: block; margin-top: 2px;">
            Masuk sejak <?php echo e($sedangDirawatSekarang->tanggal_masuk->locale('id')->isoFormat('D MMMM Y')); ?>

            &bull; Hari ke-<?php echo e($sedangDirawatSekarang->lama_dirawat); ?>

            &bull; <?php echo e(Str::limit($sedangDirawatSekarang->keluhan, 55)); ?>

        </span>
    </div>
    <a href="<?php echo e(route('santri.kesehatan.show', $sedangDirawatSekarang->id)); ?>"
       style="background: rgba(255,255,255,0.2); color: white; padding: 8px 14px; border-radius: 8px;
              text-decoration: none; font-size: 0.82rem; white-space: nowrap; border: 1px solid rgba(255,255,255,0.35); flex-shrink: 0;"
       onmouseover="this.style.background='rgba(255,255,255,0.35)';"
       onmouseout="this.style.background='rgba(255,255,255,0.2)';">
        <i class="fas fa-eye"></i> Lihat Detail
    </a>
</div>
<?php endif; ?>

<?php if($errors->any()): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first()); ?>

</div>
<?php endif; ?>


<div class="row-cards">
    <div class="card card-info">
        <h3><i class="fas fa-notes-medical"></i> Total Kunjungan</h3>
        <div class="card-value"><?php echo e($statistik['total_kunjungan']); ?></div>
        <div class="card-icon"><i class="fas fa-notes-medical"></i></div>
        <p style="margin-top: 8px; font-size: 0.82rem; color: var(--text-light);">Periode dipilih</p>
    </div>
    <div class="card card-danger">
        <h3><i class="fas fa-procedures"></i> Sedang Dirawat</h3>
        <div class="card-value"><?php echo e($statistik['sedang_dirawat']); ?></div>
        <div class="card-icon"><i class="fas fa-procedures"></i></div>
        <p style="margin-top: 8px; font-size: 0.82rem; color: <?php echo e($statistik['sedang_dirawat'] > 0 ? 'var(--danger-color)' : 'var(--text-light)'); ?>;">
            <?php echo e($statistik['sedang_dirawat'] > 0 ? '⚠️ Perlu perhatian' : 'Tidak ada'); ?>

        </p>
    </div>
    <div class="card card-success">
        <h3><i class="fas fa-check-circle"></i> Sembuh</h3>
        <div class="card-value"><?php echo e($statistik['sembuh']); ?></div>
        <div class="card-icon"><i class="fas fa-check-circle"></i></div>
        <p style="margin-top: 8px; font-size: 0.82rem; color: var(--text-light);">Alhamdulillah</p>
    </div>
    <div class="card card-warning">
        <h3><i class="fas fa-home"></i> Izin Sakit</h3>
        <div class="card-value"><?php echo e($statistik['izin']); ?></div>
        <div class="card-icon"><i class="fas fa-home"></i></div>
        <p style="margin-top: 8px; font-size: 0.82rem; color: var(--text-light);">Izin pulang</p>
    </div>
</div>


<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
    <div style="background: linear-gradient(135deg, #E8F7F2, #D4F1E3); padding: 16px; border-radius: 12px; border-left: 4px solid #6FBA9D; display: flex; align-items: center; gap: 14px;">
        <i class="fas fa-history" style="font-size: 2rem; color: #6FBA9D; flex-shrink: 0;"></i>
        <div>
            <p style="margin: 0; font-size: 0.82rem; color: var(--text-light);">Total Kunjungan Semua Waktu</p>
            <p style="margin: 0; font-size: 1.7rem; font-weight: 700; color: #2C5F4F; line-height: 1.1;"><?php echo e($totalAllTime); ?> <span style="font-size: 1rem;">kali</span></p>
        </div>
    </div>
    <div style="background: linear-gradient(135deg, #FFF8E1, #FFF3CD); padding: 16px; border-radius: 12px; border-left: 4px solid #FFD56B; display: flex; align-items: center; gap: 14px;">
        <i class="fas fa-bed" style="font-size: 2rem; color: #F39C12; flex-shrink: 0;"></i>
        <div>
            <p style="margin: 0; font-size: 0.82rem; color: var(--text-light);">Total Hari Dirawat Semua Waktu</p>
            <p style="margin: 0; font-size: 1.7rem; font-weight: 700; color: #7D5A00; line-height: 1.1;"><?php echo e($totalHariDirawat); ?> <span style="font-size: 1rem;">hari</span></p>
        </div>
    </div>
</div>


<?php if($dataGrafik->count() > 0): ?>
<div class="content-box" style="margin-bottom: 14px;">
    <h3 style="margin: 0 0 16px 0; color: var(--primary-color);">
        <i class="fas fa-chart-bar"></i> Kunjungan 6 Bulan Terakhir
        <span style="font-size: 0.78rem; font-weight: 400; color: var(--text-light); margin-left: 6px;">per status</span>
    </h3>
    <canvas id="chartKesehatan" style="max-height: 240px;"></canvas>
</div>
<?php endif; ?>


<div class="content-box" style="margin-bottom: 14px;">
    <form method="GET" action="<?php echo e(route('santri.kesehatan.index')); ?>" id="filterForm">
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 11px; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 6px;">
                    <i class="fas fa-calendar-alt form-icon"></i> Tanggal Dari
                </label>
                <input type="date" name="tanggal_dari" class="form-control"
                       value="<?php echo e($tanggalDari->format('Y-m-d')); ?>" max="<?php echo e(date('Y-m-d')); ?>">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 6px;">
                    <i class="fas fa-calendar-check form-icon"></i> Tanggal Sampai
                </label>
                <input type="date" name="tanggal_sampai" class="form-control"
                       value="<?php echo e($tanggalSampai->format('Y-m-d')); ?>" max="<?php echo e(date('Y-m-d')); ?>">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 6px;">
                    <i class="fas fa-filter form-icon"></i> Status
                </label>
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php echo e(request('status') == $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary hover-lift">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="<?php echo e(route('santri.kesehatan.index')); ?>" class="btn btn-secondary hover-lift">
                    <i class="fas fa-sync"></i>
                </a>
            </div>
        </div>
    </form>
    <p style="margin: 11px 0 0 0; color: var(--text-light); font-size: 0.82rem;">
        <i class="fas fa-info-circle"></i>
        Periode: <strong style="color: var(--primary-color);">
            <?php echo e($tanggalDari->locale('id')->isoFormat('D MMMM Y')); ?> — <?php echo e($tanggalSampai->locale('id')->isoFormat('D MMMM Y')); ?>

        </strong>
        (<?php echo e($tanggalDari->diffInDays($tanggalSampai) + 1); ?> hari)
    </p>
</div>


<?php if($riwayatKesehatan->isEmpty()): ?>
    <div class="empty-state" style="margin-top: 14px;">
        <i class="fas fa-notes-medical"></i>
        <h3>Tidak Ada Data</h3>
        <p>Tidak ada riwayat kesehatan pada periode yang dipilih.</p>
        <a href="<?php echo e(route('santri.kesehatan.index')); ?>" class="btn btn-primary" style="margin-top: 14px;">
            <i class="fas fa-sync"></i> Lihat Semua Data
        </a>
    </div>
<?php else: ?>
    <div class="content-box" style="margin-top: 14px;">
        <h3 style="margin: 0 0 14px 0; color: var(--primary-color);">
            <i class="fas fa-list"></i> Daftar Riwayat
            <span style="color: var(--text-light); font-weight: 400; font-size: 0.9rem;">(<?php echo e($riwayatKesehatan->total()); ?> data)</span>
        </h3>

        <div style="display: flex; flex-direction: column; gap: 10px;">
            <?php $__currentLoopData = $riwayatKesehatan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $bColor = match($item->status) { 'dirawat' => '#E74C3C', 'sembuh' => '#6FBA9D', default => '#F39C12' };
                $iBg    = match($item->status) { 'dirawat' => 'linear-gradient(135deg,#FFE8EA,#FFD5D8)', 'sembuh' => 'linear-gradient(135deg,#E8F7F2,#D4F1E3)', default => 'linear-gradient(135deg,#FFF8E1,#FFF3CD)' };
                $ico    = match($item->status) { 'dirawat' => 'fa-procedures', 'sembuh' => 'fa-check-circle', default => 'fa-home' };
            ?>
            <a href="<?php echo e(route('santri.kesehatan.show', $item->id)); ?>"
               style="display:flex; gap:14px; padding:14px 16px; background:white; border-radius:10px; border-left:4px solid <?php echo e($bColor); ?>; text-decoration:none; box-shadow:0 2px 8px rgba(0,0,0,0.06); transition:all 0.2s;"
               onmouseover="this.style.transform='translateX(5px)'; this.style.boxShadow='0 4px 16px rgba(0,0,0,0.12)';"
               onmouseout="this.style.transform=''; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.06)';">

                <div style="flex-shrink:0; width:54px; height:54px; border-radius:50%; background:<?php echo e($iBg); ?>; display:flex; align-items:center; justify-content:center;">
                    <i class="fas <?php echo e($ico); ?>" style="font-size:1.5rem; color:<?php echo e($bColor); ?>;"></i>
                </div>

                <div style="flex:1; min-width:0;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:5px;">
                        <strong style="color:var(--text-color); font-size:0.95rem;"><?php echo e(Str::limit($item->keluhan, 65)); ?></strong>
                        <span class="badge badge-<?php echo e($item->status_badge_color); ?>" style="margin-left:8px; flex-shrink:0;"><?php echo e(ucfirst($item->status)); ?></span>
                    </div>
                    <div style="display:flex; flex-wrap:wrap; gap:10px; font-size:0.8rem; color:var(--text-light);">
                        <span><i class="fas fa-calendar-plus"></i> <?php echo e($item->tanggal_masuk_formatted); ?></span>
                        <?php if($item->tanggal_keluar): ?>
                            <span><i class="fas fa-calendar-check"></i> Keluar: <?php echo e($item->tanggal_keluar_formatted); ?></span>
                        <?php endif; ?>
                        <?php if($item->status === 'dirawat'): ?>
                            <span class="badge badge-danger badge-sm"><i class="fas fa-clock"></i> Hari ke-<?php echo e($item->lama_dirawat); ?></span>
                        <?php else: ?>
                            <span class="badge badge-info badge-sm"><i class="fas fa-clock"></i> <?php echo e($item->lama_dirawat); ?> hari</span>
                        <?php endif; ?>
                        <span style="color:#ccc; font-size:0.75rem;"><?php echo e($item->id_kesehatan); ?></span>
                    </div>
                </div>

                <div style="flex-shrink:0; align-self:center;">
                    <i class="fas fa-chevron-right" style="color:var(--text-light); font-size:0.8rem;"></i>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div style="margin-top: 18px;">
            <?php echo e($riwayatKesehatan->links()); ?>

        </div>
    </div>
<?php endif; ?>

<div class="info-box" style="margin-top: 14px;">
    <i class="fas fa-info-circle"></i>
    <strong>Info:</strong> Default menampilkan data bulan berjalan. Gunakan filter untuk melihat periode lain.
</div>


<?php if($dataGrafik->count() > 0): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const dataGrafik = <?php echo json_encode($dataGrafik, 15, 512) ?>;
new Chart(document.getElementById('chartKesehatan').getContext('2d'), {
    type: 'bar',
    data: {
        labels: dataGrafik.map(d => d.label),
        datasets: [
            { label: 'Sembuh',  data: dataGrafik.map(d => d.sembuh),  backgroundColor: 'rgba(111,186,157,0.85)', borderRadius: 5, borderSkipped: false },
            { label: 'Izin',    data: dataGrafik.map(d => d.izin),    backgroundColor: 'rgba(255,213,107,0.85)', borderRadius: 5, borderSkipped: false },
            { label: 'Dirawat', data: dataGrafik.map(d => d.dirawat), backgroundColor: 'rgba(231,76,60,0.85)',   borderRadius: 5, borderSkipped: false },
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: { afterBody: items => `Total: ${dataGrafik[items[0].dataIndex].total} kunjungan` }
            }
        },
        scales: {
            x: { stacked: true, grid: { display: false } },
            y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dari   = document.querySelector('input[name="tanggal_dari"]');
    const sampai = document.querySelector('input[name="tanggal_sampai"]');
    dari.addEventListener('change', () => { if (sampai.value && sampai.value < dari.value) sampai.value = dari.value; });
    sampai.addEventListener('change', function() {
        if (dari.value && this.value < dari.value) {
            alert('Tanggal sampai tidak boleh lebih kecil dari tanggal dari!');
            this.value = dari.value;
        }
    });
});
</script>

<style>
@keyframes alertPulse {
    0%, 100% { box-shadow: 0 4px 18px rgba(231,76,60,0.4); }
    50%       { box-shadow: 0 4px 28px rgba(231,76,60,0.75); }
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/kesehatan/index.blade.php ENDPATH**/ ?>