


<?php $__env->startSection('title', 'Dashboard Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2>Dashboard Admin</h2>
    <p><?php echo e($hariIni); ?>, <?php echo e($today->translatedFormat('d F Y')); ?></p>
</div>


<?php echo $__env->make('admin.dashboard._kpi-cards', ['kpi' => $kpiCards], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php echo $__env->make('admin.dashboard._jadwal-kegiatan', ['kegiatan' => $kegiatanHariIni, 'hari' => $hariIni], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php echo $__env->make('admin.dashboard._alert-panel', ['alerts' => $alerts], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<div class="dash-grid-2">
    
    <?php echo $__env->make('admin.dashboard._tren-kehadiran', ['trenKehadiran' => $trenKehadiran], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('admin.dashboard._ringkasan-spp', ['spp' => $sppBulanIni], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>


<?php echo $__env->make('admin.dashboard._feed-aktivitas', ['feed' => $feedAktivitas], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Tren Kehadiran (Line Chart) ──
    const trenCtx = document.getElementById('trenKehadiranChart');
    if (trenCtx) {
        const trenData = <?php echo json_encode($trenKehadiran, 15, 512) ?>;
        const colors = ['#6FBA9D', '#FF8B94', '#81C6E8', '#FFD56B', '#B39DDB', '#FFAB91'];
        const datasets = Object.keys(trenData.series).map((label, i) => ({
            label: label,
            data: trenData.series[label],
            borderColor: colors[i % colors.length],
            backgroundColor: colors[i % colors.length] + '20',
            tension: 0.3,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 6,
        }));

        new Chart(trenCtx, {
            type: 'line',
            data: { labels: trenData.labels, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } },
                    tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + ctx.parsed.y + '%' } }
                },
                scales: {
                    y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } }
                }
            }
        });
    }

    // ── Ringkasan SPP (Donut Chart) ──
    const sppCtx = document.getElementById('sppDonutChart');
    if (sppCtx) {
        const sppData = <?php echo json_encode($sppBulanIni, 15, 512) ?>;
        new Chart(sppCtx, {
            type: 'doughnut',
            data: {
                labels: ['Lunas', 'Belum Lunas'],
                datasets: [{
                    data: [sppData.lunas, sppData.belum],
                    backgroundColor: ['#6FBA9D', '#FF8B94'],
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } },
                }
            }
        });
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', ['isAdmin' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/dashboardAdmin.blade.php ENDPATH**/ ?>