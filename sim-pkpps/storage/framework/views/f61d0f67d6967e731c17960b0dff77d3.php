<?php $__env->startSection('title', 'Dashboard Admin'); ?>

<?php $__env->startSection('content'); ?>


<div class="page-header">
    <div>
        <h2>
            <?php if(auth()->user()->role === 'super_admin'): ?>
                <i class="fas fa-crown"></i> Super Admin
            <?php elseif(auth()->user()->role === 'akademik'): ?>
                <i class="fas fa-book-open"></i> Akademik
            <?php else: ?>
                <i class="fas fa-shield-alt"></i> Pamong
            <?php endif; ?>
        </h2>
        <p style="margin:3px 0 0;font-size:.75rem;color:var(--text-light);">
            <?php echo e($hariIni); ?>, <?php echo e($today->translatedFormat('d F Y')); ?>

            &nbsp;·&nbsp;
            <span style="display:inline-flex;align-items:center;gap:5px;">
                <span style="width:7px;height:7px;border-radius:50%;background:#27ae60;
                             box-shadow:0 0 0 3px rgba(39,174,96,.2);display:inline-block;
                             animation:dashLivePulse 2s infinite;"></span>
                Live
            </span>
        </p>
    </div>
</div>

<style>
@keyframes dashLivePulse {
    0%,100%{box-shadow:0 0 0 3px rgba(39,174,96,.2)}
    50%    {box-shadow:0 0 0 6px rgba(39,174,96,.05)}
}

/* 2-col grid untuk SPP + Kas */
.dash-fin-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 16px;
}
@media (max-width: 768px) {
    .dash-fin-grid { grid-template-columns: 1fr; }
}
@media (max-width: 1024px) {
    .row-cards-5 { grid-template-columns: repeat(3,1fr); }
}
@media (max-width: 640px) {
    .row-cards-5 { grid-template-columns: repeat(2,1fr); }
}
</style>


<?php echo $__env->make('admin.dashboard._kpi-cards', ['kpi' => $kpiCards], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php echo $__env->make('admin.dashboard._alert-panel', ['alerts' => $alerts], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php echo $__env->make('admin.dashboard._jadwal-kegiatan', ['kegiatan' => $kegiatanHariIni, 'hari' => $hariIni], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php if(auth()->user()->isSuperAdmin()): ?>
    <?php echo $__env->make('admin.dashboard._ringkasan-spp', ['spp' => $sppBulanIni], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>


<?php echo $__env->make('admin.dashboard._tren-kehadiran', ['trenKehadiran' => $trenKehadiran], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Tren Kehadiran Line Chart ─────────────────────────────────────────
    var trenCtx = document.getElementById('trenKehadiranChart');
    if (!trenCtx) return;

    var trenData   = <?php echo json_encode($trenKehadiran, 15, 512) ?>;
    // Pakai palet warna yang sesuai theme (eucalyptus green + accents)
    var palette    = ['#6FBA9D','#FF8B94','#81C6E8','#FFD56B','#B39DDB','#FFAB91'];
    var datasets   = [];

    Object.keys(trenData.series).forEach(function (key, i) {
        var c = palette[i % palette.length];
        datasets.push({
            label            : key,
            data             : trenData.series[key],
            borderColor      : c,
            backgroundColor  : c + '20',
            borderWidth      : 2.5,
            tension          : 0.4,
            fill             : true,
            pointRadius      : 5,
            pointHoverRadius : 7,
            pointBackgroundColor : c,
            pointBorderColor     : '#fff',
            pointBorderWidth     : 2
        });
    });

    new Chart(trenCtx, {
        type: 'line',
        data: { labels: trenData.labels, datasets: datasets },
        options: {
            responsive         : true,
            maintainAspectRatio: false,
            interaction        : { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels  : {
                        padding      : 18,
                        usePointStyle: true,
                        font         : { size: 11 }
                    }
                },
                tooltip: {
                    backgroundColor: '#2C3E50',
                    titleFont      : { size: 12, weight: '700' },
                    bodyFont       : { size: 11 },
                    padding        : 10,
                    cornerRadius   : 8,
                    callbacks      : {
                        label: function (ctx) {
                            return '  ' + ctx.dataset.label + ': ' + ctx.parsed.y + '%';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid : { display: false },
                    ticks: { font: { size: 11 }, color: '#7F8C8D' }
                },
                y: {
                    beginAtZero: true,
                    max        : 100,
                    grid       : { color: '#E8F7F2' },
                    ticks      : {
                        callback: function (v) { return v + '%'; },
                        font    : { size: 10 },
                        color   : '#7F8C8D'
                    }
                }
            }
        }
    });

    // SPP ring chart diinisialisasi dari dalam _ringkasan-spp.blade.php
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', ['isAdmin' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/dashboardAdmin.blade.php ENDPATH**/ ?>