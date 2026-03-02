
<?php
    $total         = ($spp['lunas'] ?? 0) + ($spp['belum'] ?? 0);
    $persenLunas   = $total > 0 ? round(($spp['lunas'] / $total) * 100) : 0;
    $terkumpul     = (float) ($spp['terkumpul']     ?? 0);
    $totalTagihan  = (float) ($spp['totalTagihan']  ?? 0);
    $persenNominal = $totalTagihan > 0 ? min(100, round($terkumpul / $totalTagihan * 100)) : 0;

    $pemasukanLain  = (float) ($spp['pemasukanLain'] ?? 0);
    $pengeluaran    = (float) ($spp['pengeluaran']   ?? 0);
    $totalPemasukan = $terkumpul + $pemasukanLain;
    $sisaKas        = $totalPemasukan - $pengeluaran;

    $kasMax       = max($totalPemasukan, $pengeluaran, 1);
    $pBarMasuk    = min(100, round($totalPemasukan / $kasMax * 100));
    $pBarKeluar   = min(100, round($pengeluaran    / $kasMax * 100));
?>


<div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
    <span style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;
                 background:linear-gradient(135deg,var(--primary-color),var(--secondary-color));border-radius:6px;flex-shrink:0;">
        <i class="fas fa-wallet" style="font-size:.7rem;color:#fff;"></i>
    </span>
    <span style="font-size:.88rem;font-weight:700;color:var(--text-color);">Keuangan Bulan Ini</span>
    <a href="<?php echo e(route('admin.keuangan.laporan', ['bulan'=>date('n'),'tahun'=>date('Y')])); ?>"
       style="margin-left:auto;font-size:.72rem;color:var(--primary-color);font-weight:600;text-decoration:none;display:flex;align-items:center;gap:4px;">
        Lihat Neraca <i class="fas fa-arrow-right" style="font-size:.6rem;"></i>
    </a>
</div>


<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">

    
    <div class="content-box" style="display:flex;flex-direction:column;gap:0;">

        <h4 style="margin:0 0 14px;font-size:.8rem;font-weight:700;color:var(--primary-dark);
                   display:flex;align-items:center;gap:6px;">
            <i class="fas fa-money-check-alt" style="color:var(--primary-color);"></i>
            Status Pembayaran SPP
        </h4>

        
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:14px;">

            
            <div style="position:relative;width:110px;height:110px;flex-shrink:0;">
                <canvas id="sppRingChart"></canvas>
                <div style="position:absolute;inset:0;display:flex;flex-direction:column;
                            align-items:center;justify-content:center;pointer-events:none;">
                    <span style="font-size:1.5rem;font-weight:800;color:var(--text-color);line-height:1;"><?php echo e($persenLunas); ?>%</span>
                    <span style="font-size:.6rem;color:var(--text-light);font-weight:500;">lunas</span>
                </div>
            </div>

            
            <div style="display:flex;flex-direction:column;gap:8px;flex:1;min-width:100px;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="width:10px;height:10px;border-radius:50%;background:var(--primary-color);flex-shrink:0;display:inline-block;"></span>
                    <span style="font-size:.72rem;color:var(--text-light);flex:1;">Lunas</span>
                    <strong style="font-size:.82rem;color:var(--primary-color);"><?php echo e($spp['lunas'] ?? 0); ?></strong>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="width:10px;height:10px;border-radius:50%;background:var(--danger-color);flex-shrink:0;display:inline-block;"></span>
                    <span style="font-size:.72rem;color:var(--text-light);flex:1;">Belum Lunas</span>
                    <strong style="font-size:.82rem;color:var(--danger-color);"><?php echo e($spp['belum'] ?? 0); ?></strong>
                </div>
                <div style="height:1px;background:var(--primary-light);margin:2px 0;"></div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:.68rem;color:var(--text-light);">Terkumpul</span>
                    <span style="font-size:.72rem;font-weight:700;color:var(--text-color);">Rp <?php echo e(number_format($terkumpul/1000000,1)); ?>jt</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:.68rem;color:var(--text-light);">Target</span>
                    <span style="font-size:.72rem;font-weight:600;color:var(--text-light);">Rp <?php echo e(number_format($totalTagihan/1000000,1)); ?>jt</span>
                </div>
            </div>
        </div>

        
        <div style="margin-bottom:14px;">
            <div style="display:flex;justify-content:space-between;font-size:.68rem;color:var(--text-light);margin-bottom:4px;">
                <span>Nominal terkumpul</span>
                <span style="font-weight:700;color:var(--text-color);"><?php echo e($persenNominal); ?>%</span>
            </div>
            <div class="progress-bar-wrap">
                <div class="progress-bar-fill" style="width:<?php echo e($persenNominal); ?>%;
                     background:linear-gradient(90deg,var(--primary-color),var(--primary-dark));"></div>
            </div>
        </div>

        
        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:auto;">
            <a href="<?php echo e(route('admin.pembayaran-spp.index', ['tab'=>'belum-bayar','bulan'=>date('n'),'tahun'=>date('Y')])); ?>"
               class="btn btn-danger btn-sm" style="flex:1;justify-content:center;">
                <i class="fas fa-exclamation-circle"></i> Belum (<?php echo e($spp['belum'] ?? 0); ?>)
            </a>
            <a href="<?php echo e(route('admin.pembayaran-spp.generate')); ?>"
               class="btn btn-warning btn-sm" style="flex:1;justify-content:center;">
                <i class="fas fa-cogs"></i> Generate
            </a>
            <a href="<?php echo e(route('admin.pembayaran-spp.index', ['tab'=>'sudah-bayar','bulan'=>date('n'),'tahun'=>date('Y')])); ?>"
               class="btn btn-success btn-sm" style="flex:1;justify-content:center;">
                <i class="fas fa-check-circle"></i> Lunas (<?php echo e($spp['lunas'] ?? 0); ?>)
            </a>
        </div>
    </div>

    
    <div class="content-box" style="display:flex;flex-direction:column;gap:0;">

        <h4 style="margin:0 0 14px;font-size:.8rem;font-weight:700;color:var(--primary-dark);
                   display:flex;align-items:center;gap:6px;">
            <i class="fas fa-landmark" style="color:var(--info-color);"></i>
            Neraca Kas Pondok
        </h4>

        
        <div style="display:flex;flex-direction:column;gap:14px;margin-bottom:14px;">

            
            <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">
                    <span style="font-size:.72rem;color:var(--text-light);font-weight:600;display:flex;align-items:center;gap:6px;">
                        <span style="width:10px;height:10px;border-radius:50%;background:var(--success-color);display:inline-block;flex-shrink:0;"></span>
                        SPP + Pemasukan
                    </span>
                    <strong style="font-size:.78rem;color:var(--success-color);">
                        Rp <?php echo e(number_format($totalPemasukan,0,',','.')); ?>

                    </strong>
                </div>
                <div class="progress-bar-wrap" style="height:10px;">
                    <div class="progress-bar-fill" style="width:<?php echo e($pBarMasuk); ?>%;height:100%;
                         background:linear-gradient(90deg,var(--primary-color),#38ef7d);"></div>
                </div>
            </div>

            
            <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">
                    <span style="font-size:.72rem;color:var(--text-light);font-weight:600;display:flex;align-items:center;gap:6px;">
                        <span style="width:10px;height:10px;border-radius:50%;background:var(--danger-color);display:inline-block;flex-shrink:0;"></span>
                        Pengeluaran
                    </span>
                    <strong style="font-size:.78rem;color:var(--danger-color);">
                        Rp <?php echo e(number_format($pengeluaran,0,',','.')); ?>

                    </strong>
                </div>
                <div class="progress-bar-wrap" style="height:10px;">
                    <div class="progress-bar-fill" style="width:<?php echo e($pBarKeluar); ?>%;height:100%;
                         background:linear-gradient(90deg,var(--danger-color),#FF6B7A);"></div>
                </div>
            </div>

            <?php if($pemasukanLain > 0): ?>
            
            <div style="opacity:.8;">
                <?php $pBarSpp = min(100, round($terkumpul / $kasMax * 100)); ?>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">
                    <span style="font-size:.68rem;color:var(--text-light);display:flex;align-items:center;gap:6px;">
                        <span style="width:8px;height:8px;border-radius:50%;background:var(--info-color);display:inline-block;flex-shrink:0;"></span>
                        └ dari SPP saja
                    </span>
                    <span style="font-size:.7rem;color:var(--info-color);font-weight:600;">
                        Rp <?php echo e(number_format($terkumpul,0,',','.')); ?>

                    </span>
                </div>
                <div class="progress-bar-wrap" style="height:7px;">
                    <div class="progress-bar-fill" style="width:<?php echo e($pBarSpp); ?>%;height:100%;
                         background:linear-gradient(90deg,var(--info-color),#5FAFE0);"></div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        
        <div style="padding:11px 14px;border-radius:var(--border-radius-sm);margin-bottom:14px;
                    background:<?php echo e($sisaKas >= 0 ? 'linear-gradient(135deg,#E8F7F2,#D4F1E3)' : 'linear-gradient(135deg,#FFE8EA,#FFD5D8)'); ?>;
                    border-left:4px solid <?php echo e($sisaKas >= 0 ? 'var(--success-color)' : 'var(--danger-color)'); ?>;
                    display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:.75rem;font-weight:700;color:var(--text-color);display:flex;align-items:center;gap:6px;">
                <i class="fas fa-<?php echo e($sisaKas >= 0 ? 'piggy-bank' : 'exclamation-triangle'); ?>"
                   style="color:<?php echo e($sisaKas >= 0 ? 'var(--success-color)' : 'var(--danger-color)'); ?>;"></i>
                Sisa Kas Bulan Ini
            </span>
            <strong style="font-size:1rem;font-weight:800;color:<?php echo e($sisaKas >= 0 ? 'var(--success-color)' : 'var(--danger-color)'); ?>;">
                <?php echo e($sisaKas >= 0 ? '+' : ''); ?>Rp <?php echo e(number_format($sisaKas,0,',','.')); ?>

            </strong>
        </div>

        
        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:auto;">
            <a href="<?php echo e(route('admin.keuangan.index')); ?>"
               class="btn btn-info btn-sm" style="flex:1;justify-content:center;">
                <i class="fas fa-book-open"></i> Buku Kas
            </a>
            <a href="<?php echo e(route('admin.keuangan.laporan', ['bulan'=>date('n'),'tahun'=>date('Y')])); ?>"
               class="btn btn-warning btn-sm" style="flex:1;justify-content:center;">
                <i class="fas fa-chart-bar"></i> Laporan
            </a>
        </div>
    </div>

</div>


<script>
(function () {
    var SPP_LUNAS = <?php echo e((int)($spp['lunas'] ?? 0)); ?>;
    var SPP_BELUM = <?php echo e((int)($spp['belum'] ?? 0)); ?>;

    function initRing() {
        if (typeof Chart === 'undefined') { setTimeout(initRing, 50); return; }
        var el = document.getElementById('sppRingChart');
        if (!el) return;
        if (el._ci) { el._ci.destroy(); }
        var allZero = (SPP_LUNAS === 0 && SPP_BELUM === 0);
        el._ci = new Chart(el, {
            type: 'doughnut',
            data: {
                labels  : allZero ? ['Belum ada data'] : ['Lunas', 'Belum Lunas'],
                datasets: [{
                    data           : allZero ? [1] : [SPP_LUNAS, SPP_BELUM],
                    backgroundColor: allZero ? ['#E8F7F2'] : ['#6FBA9D', '#FF8B94'],
                    borderWidth    : allZero ? 0 : 3,
                    borderColor    : '#fff',
                    hoverOffset    : allZero ? 0 : 6
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: !allZero,
                        callbacks: {
                            label: function (ctx) {
                                var total = ctx.dataset.data.reduce(function(a,b){return a+b;},0);
                                var pct   = total > 0 ? Math.round(ctx.parsed/total*100) : 0;
                                return '  '+ctx.label+': '+ctx.formattedValue+' ('+pct+'%)';
                            }
                        }
                    }
                }
            }
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRing);
    } else { initRing(); }
})();
</script><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/dashboard/_ringkasan-spp.blade.php ENDPATH**/ ?>