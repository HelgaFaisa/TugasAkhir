

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-wallet"></i> Manajemen Uang Saku Santri</h2>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?></div>
<?php endif; ?>


<div class="content-box" style="margin-bottom:16px;">
    <form method="GET" action="<?php echo e(route('admin.uang-saku.index')); ?>" id="filterForm"
          style="display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end; margin-bottom:18px;">
        <?php if(request('search')): ?> <input type="hidden" name="search" value="<?php echo e(request('search')); ?>"> <?php endif; ?>
        <?php if(request('sort')): ?>   <input type="hidden" name="sort"   value="<?php echo e(request('sort')); ?>">   <?php endif; ?>

        <div>
            <label style="font-size:.78rem;color:var(--text-light);display:block;margin-bottom:3px;">Dari Tanggal</label>
            <input type="date" name="dari" class="form-control" value="<?php echo e($dari); ?>" style="width:155px;">
        </div>
        <div>
            <label style="font-size:.78rem;color:var(--text-light);display:block;margin-bottom:3px;">Sampai Tanggal</label>
            <input type="date" name="sampai" class="form-control" value="<?php echo e($sampai); ?>" style="width:155px;">
        </div>

        
        <div style="display:flex;gap:5px;flex-wrap:wrap;align-self:flex-end;">
            <?php
                $bulanIniDari   = now()->startOfMonth()->format('Y-m-d');
                $bulanIniSampai = now()->endOfMonth()->format('Y-m-d');
                $isBulanIni     = $dari === $bulanIniDari && $sampai === $bulanIniSampai;
                $isHariIni      = $dari === now()->format('Y-m-d') && $sampai === now()->format('Y-m-d');
            ?>
            <button type="button" onclick="setPreset('today')"
                    class="btn btn-sm <?php echo e($isHariIni ? 'btn-primary' : 'btn-secondary'); ?>">
                Hari Ini
            </button>
            <button type="button" onclick="setPreset('month')"
                    class="btn btn-sm <?php echo e($isBulanIni ? 'btn-primary' : 'btn-secondary'); ?>">
                Bulan Ini
            </button>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-filter"></i> Terapkan
            </button>
        </div>
    </form>

    

    
    <div class="row-cards row-cards-4" style="margin-bottom:10px;">
        <div class="card card-info">
            <h3>Total Transaksi</h3>
            <p class="card-value"><?php echo e($kpi['total_transaksi']); ?></p>
            <span class="card-sub">dari <?php echo e($kpi['total_santri']); ?> santri</span>
            <i class="fas fa-exchange-alt card-icon"></i>
        </div>
        <div class="card card-success">
            <h3>Total Pemasukan</h3>
            <p class="card-value" style="font-size:1.05rem;">Rp <?php echo e(number_format($kpi['total_pemasukan'], 0, ',', '.')); ?></p>
            <span class="card-sub">
                <?php echo e(\Carbon\Carbon::parse($dari)->format('d M')); ?> &ndash; <?php echo e(\Carbon\Carbon::parse($sampai)->format('d M Y')); ?>

            </span>
            <i class="fas fa-arrow-circle-down card-icon"></i>
        </div>
        <div class="card card-warning">
            <h3>Total Pengeluaran</h3>
            <p class="card-value" style="font-size:1.05rem;">Rp <?php echo e(number_format($kpi['total_pengeluaran'], 0, ',', '.')); ?></p>
            <span class="card-sub">
                <?php echo e(\Carbon\Carbon::parse($dari)->format('d M')); ?> &ndash; <?php echo e(\Carbon\Carbon::parse($sampai)->format('d M Y')); ?>

            </span>
            <i class="fas fa-arrow-circle-up card-icon"></i>
        </div>
        <div class="card <?php echo e($kpi['selisih'] >= 0 ? 'card-success' : 'card-danger'); ?>">
            <h3>
                Selisih Periode
                <span title="Selisih = Total Pemasukan dikurangi Total Pengeluaran pada periode yang dipilih. Surplus berarti lebih banyak uang masuk; Defisit berarti lebih banyak uang keluar."
                      style="cursor:help;font-size:.75rem;color:var(--text-light);">
                    <i class="fas fa-question-circle"></i>
                </span>
            </h3>
            <p class="card-value" style="font-size:1.05rem;">
                <?php echo e($kpi['selisih'] >= 0 ? '+' : '-'); ?> Rp <?php echo e(number_format(abs($kpi['selisih']), 0, ',', '.')); ?>

            </p>
            <span class="card-sub"><?php echo e($kpi['selisih'] >= 0 ? 'âœ“ Surplus periode ini' : 'âœ— Defisit periode ini'); ?></span>
            <i class="fas fa-balance-scale card-icon"></i>
        </div>
    </div>

    
    <div class="row-cards row-cards-1">
        <div class="card card-primary" style="border-left: 4px solid var(--primary-color); position:relative;">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:space-between;">
                <div>
                    <h3 style="margin:0 0 4px;">
                        Total Saldo Seluruh Santri
                    </h3>
                    <p class="card-value" style="font-size:1.4rem;margin:0;color:var(--primary-color);">
                        Rp <?php echo e(number_format($kpi['total_saldo_realtime'], 0, ',', '.')); ?>

                    </p>
                </div>
                <div style="text-align:right;">
                    <span class="badge badge-info" style="font-size:.8rem;padding:5px 10px;">
                        <i class="fas fa-clock"></i> Real-time â€” tidak terpengaruh filter tanggal
                    </span>
                </div>
            </div>
            <i class="fas fa-piggy-bank card-icon"></i>
        </div>
    </div>
</div>


<div class="content-box">

    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
        <a href="<?php echo e(route('admin.uang-saku.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Transaksi
        </a>

        <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
            
            <form method="GET" action="<?php echo e(route('admin.uang-saku.index')); ?>" id="sortForm" style="display:flex;gap:6px;align-items:center;">
                <input type="hidden" name="dari"   value="<?php echo e($dari); ?>">
                <input type="hidden" name="sampai" value="<?php echo e($sampai); ?>">
                <?php if(request('search')): ?> <input type="hidden" name="search" value="<?php echo e(request('search')); ?>"> <?php endif; ?>
                <label style="font-size:.79rem;color:var(--text-light);white-space:nowrap;">
                    <i class="fas fa-sort"></i> Urut:
                </label>
                <select name="sort" class="form-control form-control-sm" onchange="this.form.submit()" style="width:auto;">
                    <option value="nama"           <?php echo e($sort==='nama'          ? 'selected' : ''); ?>>Nama Aâ€“Z</option>
                    <option value="saldo_asc"      <?php echo e($sort==='saldo_asc'     ? 'selected' : ''); ?>>Saldo Terendah</option>
                    <option value="saldo_desc"     <?php echo e($sort==='saldo_desc'    ? 'selected' : ''); ?>>Saldo Tertinggi</option>
                    <option value="transaksi_desc" <?php echo e($sort==='transaksi_desc'? 'selected' : ''); ?>>Transaksi Terbanyak</option>
                    <option value="terakhir"       <?php echo e($sort==='terakhir'      ? 'selected' : ''); ?>>Transaksi Terbaru</option>
                </select>
            </form>

            
            <form method="GET" action="<?php echo e(route('admin.uang-saku.index')); ?>" style="display:flex;gap:6px;">
                <input type="hidden" name="dari"   value="<?php echo e($dari); ?>">
                <input type="hidden" name="sampai" value="<?php echo e($sampai); ?>">
                <input type="hidden" name="sort"   value="<?php echo e($sort); ?>">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Cari nama / ID santri..."
                       value="<?php echo e(request('search')); ?>" style="width:210px;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
                <?php if(request('search')): ?>
                    <a href="<?php echo e(route('admin.uang-saku.index', ['dari'=>$dari,'sampai'=>$sampai,'sort'=>$sort])); ?>"
                       class="btn btn-secondary btn-sm"><i class="fas fa-times"></i></a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    
    <div style="display:flex;gap:14px;margin-bottom:12px;flex-wrap:wrap;font-size:.78rem;color:var(--text-light);">
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#6FBA9D;margin-right:4px;"></span>Saldo â‰¥ Rp 100rb</span>
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#f5a623;margin-right:4px;"></span>Saldo Rp 20rb â€“ 99rb</span>
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#FF8B94;margin-right:4px;"></span>Saldo &lt; Rp 20rb</span>
    </div>

    
    <?php if($santriList->count() > 0): ?>
        <?php $__currentLoopData = $santriList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $saldoColor = $santri->saldo_terakhir >= 100000 ? '#6FBA9D'
                : ($santri->saldo_terakhir >= 20000 ? '#f5a623' : '#FF8B94');
            $saldoDot   = $saldoColor;
        ?>
        <div class="content-box us-row" style="margin-bottom:10px;padding:0;overflow:hidden;">

            
            <div class="us-row-header"
                 onclick="toggleDetail('detail-<?php echo e($santri->id_santri); ?>', this)"
                 style="display:flex;align-items:center;gap:0;cursor:pointer;padding:13px 16px;flex-wrap:wrap;gap:10px;">

                
                <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:160px;">
                    <i class="fas fa-chevron-right toggle-arrow"
                       style="transition:transform .2s;color:var(--text-light);font-size:.8rem;flex-shrink:0;"></i>
                    <div>
                        <div style="font-weight:700;font-size:.93rem;"><?php echo e($santri->nama_lengkap); ?></div>
                        <div style="font-size:.76rem;color:var(--text-light);"><?php echo e($santri->id_santri); ?></div>
                    </div>
                </div>

                
                <div style="display:flex;flex-direction:column;align-items:center;min-width:120px;">
                    <div style="font-size:.7rem;color:var(--text-light);margin-bottom:2px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;">Saldo</div>
                    <div style="font-size:1.05rem;font-weight:800;color:<?php echo e($saldoColor); ?>;display:flex;align-items:center;gap:5px;">
                        <span style="width:8px;height:8px;border-radius:50%;background:<?php echo e($saldoDot); ?>;flex-shrink:0;display:inline-block;"></span>
                        Rp <?php echo e(number_format($santri->saldo_terakhir, 0, ',', '.')); ?>

                    </div>
                </div>

                
                <div style="width:1px;height:36px;background:var(--primary-light);flex-shrink:0;"></div>

                
                <div style="display:flex;flex-direction:column;align-items:center;min-width:100px;">
                    <div style="font-size:.7rem;color:var(--text-light);margin-bottom:2px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;">Masuk Bln Ini</div>
                    <div style="font-size:.85rem;font-weight:700;color:#6FBA9D;">
                        + Rp <?php echo e(number_format($santri->pemasukan_bulan, 0, ',', '.')); ?>

                    </div>
                </div>

                
                <div style="display:flex;flex-direction:column;align-items:center;min-width:100px;">
                    <div style="font-size:.7rem;color:var(--text-light);margin-bottom:2px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;">Keluar Bln Ini</div>
                    <div style="font-size:.85rem;font-weight:700;color:#FF8B94;">
                        - Rp <?php echo e(number_format($santri->pengeluaran_bulan, 0, ',', '.')); ?>

                    </div>
                </div>

                
                <div style="width:1px;height:36px;background:var(--primary-light);flex-shrink:0;"></div>

                
                <div style="display:flex;flex-direction:column;align-items:center;min-width:90px;">
                    <div style="font-size:.7rem;color:var(--text-light);margin-bottom:2px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;">Transaksi</div>
                    <span class="badge badge-info" style="font-size:.74rem;"><?php echo e($santri->transaksi_bulan_ini); ?>x bln ini</span>
                    <?php if($santri->transaksi_terakhir_tgl): ?>
                        <div style="font-size:.7rem;color:var(--text-light);margin-top:3px;">
                            terakhir <?php echo e(\Carbon\Carbon::parse($santri->transaksi_terakhir_tgl)->format('d/m/Y')); ?>

                        </div>
                    <?php endif; ?>
                </div>

                
                <div style="display:flex;gap:5px;flex-shrink:0;" onclick="event.stopPropagation()">
                    <a href="<?php echo e(route('admin.uang-saku.create')); ?>?id_santri=<?php echo e($santri->id_santri); ?>"
                       class="btn btn-success btn-sm" title="Tambah Transaksi">
                        <i class="fas fa-plus"></i>
                    </a>
                    <a href="<?php echo e(route('admin.uang-saku.riwayat', $santri->id_santri)); ?>"
                       class="btn btn-primary btn-sm" title="Riwayat Lengkap">
                        <i class="fas fa-history"></i>
                    </a>
                </div>
            </div>

            
            <div id="detail-<?php echo e($santri->id_santri); ?>"
                 style="display:none;border-top:1px solid var(--primary-light);padding:12px 16px;">
                <?php if($santri->transaksi_terbaru->isNotEmpty()): ?>
                    <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                                <th>Saldo</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $santri->transaksi_terbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($tx->tanggal_transaksi->format('d/m/Y')); ?></td>
                                <td>
                                    <?php if($tx->jenis_transaksi === 'pemasukan'): ?>
                                        <span class="badge badge-success"><i class="fas fa-arrow-down"></i> Masuk</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><i class="fas fa-arrow-up"></i> Keluar</span>
                                    <?php endif; ?>
                                </td>
                                <td class="nominal-highlight"><?php echo e($tx->nominal_format); ?></td>
                                <td><div class="content-preview"><?php echo e($tx->keterangan ?? '-'); ?></div></td>
                                <td style="font-weight:600;color:<?php echo e($tx->saldo_sesudah >= 0 ? '#6FBA9D' : '#FF8B94'); ?>;">
                                    Rp <?php echo e(number_format($tx->saldo_sesudah, 0, ',', '.')); ?>

                                </td>
                                <td class="text-center">
                                    <div style="display:flex;gap:4px;justify-content:center;">
                                        <a href="<?php echo e(route('admin.uang-saku.show', $tx->id)); ?>" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>
                                        <a href="<?php echo e(route('admin.uang-saku.edit', $tx->id)); ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                        <form action="<?php echo e(route('admin.uang-saku.destroy', $tx->id)); ?>" method="POST"
                                              style="display:inline;" onsubmit="return confirm('Yakin hapus transaksi ini?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    </div>
                    <?php if($santri->transaksi_terbaru->count() >= 5): ?>
                        <div style="text-align:center;margin-top:10px;">
                            <a href="<?php echo e(route('admin.uang-saku.riwayat', $santri->id_santri)); ?>"
                               class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-right"></i> Lihat Semua Riwayat
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted" style="margin:0;">Belum ada transaksi.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div style="margin-top:14px;"><?php echo e($santriList->links()); ?></div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-wallet"></i>
            <h3>Belum Ada Data</h3>
            <p>Belum ada santri dengan transaksi uang saku.</p>
            <a href="<?php echo e(route('admin.uang-saku.create')); ?>" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Transaksi
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleDetail(id, el) {
    var detail = document.getElementById(id);
    var arrow  = el.querySelector('.toggle-arrow');
    var open   = detail.style.display !== 'none';
    detail.style.display = open ? 'none' : 'block';
    arrow.style.transform = open ? 'rotate(0deg)' : 'rotate(90deg)';
}

function setPreset(type) {
    var form   = document.getElementById('filterForm');
    var dari   = form.querySelector('[name=dari]');
    var sampai = form.querySelector('[name=sampai]');
    var today  = new Date();
    var pad    = n => String(n).padStart(2, '0');
    var ymd    = d => d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());

    if (type === 'today') {
        dari.value   = ymd(today);
        sampai.value = ymd(today);
    } else if (type === 'month') {
        var first = new Date(today.getFullYear(), today.getMonth(), 1);
        var last  = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        dari.value   = ymd(first);
        sampai.value = ymd(last);
    }
    form.submit();
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/uang-saku/index.blade.php ENDPATH**/ ?>