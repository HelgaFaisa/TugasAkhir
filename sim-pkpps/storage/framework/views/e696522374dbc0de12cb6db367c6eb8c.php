<?php $__env->startSection('content'); ?>
<style>
/* === TABS === */
.dash-tabs { display:flex; gap:4px; flex-wrap:wrap; border-bottom:3px solid var(--primary-color); margin-bottom:0; padding:0; background:#f8faf9; border-radius:12px 12px 0 0; }
.dash-tab { padding:11px 16px; cursor:pointer; font-size:0.83rem; font-weight:600; color:#666; background:transparent; border:none; border-radius:10px 10px 0 0; transition:all .3s; }
.dash-tab:hover { color:var(--primary-dark); background:var(--primary-light); }
.dash-tab.active { color:#fff; background:var(--primary-color); }
.dash-tab i { margin-right:4px; }
.tab-content { display:none; animation:fadeTab .35s ease; }
.tab-content.active { display:block; }
@keyframes fadeTab { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }

/* === SECTION CARD === */
.sc { background:#fff; border-radius:12px; padding:20px; margin-bottom:14px; box-shadow:0 2px 10px rgba(0,0,0,.06); border:1px solid #e8f0ec; }
.sc h4 { margin:0 0 16px; color:var(--primary-dark); font-size:1rem; display:flex; align-items:center; gap:7px; }
.sc h4 .bc { background:var(--primary-light); color:var(--primary-dark); font-size:.72rem; padding:2px 9px; border-radius:20px; }

/* === KPI === */
.kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:14px; margin-bottom:14px; }
.kpi-card { background:#fff; border-radius:12px; padding:14px; position:relative; overflow:hidden; box-shadow:0 3px 12px rgba(0,0,0,.07); border-left:4px solid; transition:.2s; }
.kpi-card:hover { transform:translateY(-2px); box-shadow:0 5px 18px rgba(0,0,0,.11); }
.kpi-card .ki { position:absolute; right:12px; top:50%; transform:translateY(-50%); font-size:2.2rem; opacity:.1; }
.kpi-card .kl { font-size:.75rem; color:#888; text-transform:uppercase; letter-spacing:.4px; margin-bottom:5px; }
.kpi-card .kv { font-size:1.7rem; font-weight:800; line-height:1.1; }
.kpi-card .ks { font-size:.72rem; color:#aaa; margin-top:3px; }
.kpi-teal  { border-color:#6FBA9D; } .kpi-teal  .kv { color:#6FBA9D; }
.kpi-blue  { border-color:#81C6E8; } .kpi-blue  .kv { color:#81C6E8; }
.kpi-purple{ border-color:#B39DDB; } .kpi-purple .kv { color:#B39DDB; }
.kpi-amber { border-color:#FFD56B; } .kpi-amber  .kv { color:#d4a017; }
.kpi-rose  { border-color:#FF8B94; } .kpi-rose   .kv { color:#FF8B94; }

/* === CHART GRID === */
.chart-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:14px; }
.chart-box { background:#fff; border-radius:12px; padding:14px; box-shadow:0 2px 10px rgba(0,0,0,.06); border:1px solid #e8f0ec; }
.chart-box h5 { margin:0 0 12px; color:var(--primary-dark); font-size:.9rem; }
@media(max-width:768px){ .chart-grid{grid-template-columns:1fr;} }

/* === SANTRI CARDS (ringkasan) === */
.santri-filter-bar { display:flex; gap:6px; margin-bottom:14px; align-items:center; flex-wrap:wrap; }
.sfb-btn { padding:6px 16px; border-radius:8px; border:2px solid var(--primary-light); background:#fff; color:var(--primary-dark); font-weight:600; cursor:pointer; font-size:.82rem; transition:.2s; text-decoration:none; display:inline-block; }
.sfb-btn.active, .sfb-btn:hover { background:var(--primary-color); color:#fff; border-color:var(--primary-color); }
.santri-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:12px; }
.santri-card { background:#fff; border-radius:10px; padding:14px; border:1px solid #e8f0ec; box-shadow:0 2px 8px rgba(0,0,0,.05); transition:.2s; }
.santri-card:hover { transform:translateY(-2px); box-shadow:0 4px 14px rgba(0,0,0,.1); }
.santri-card .sc-name { font-weight:700; font-size:.88rem; color:#333; margin-bottom:2px; }
.santri-card .sc-meta { font-size:.72rem; color:#999; margin-bottom:8px; }
.santri-card .sc-pct { font-size:1.3rem; font-weight:800; }
.prog-bar { height:8px; background:#e8e8e8; border-radius:4px; overflow:hidden; margin-top:5px; }
.prog-fill { height:100%; border-radius:4px; transition:width .5s; }
.lihat-semua { display:inline-flex; align-items:center; gap:6px; padding:8px 18px; border-radius:8px; background:var(--primary-light); color:var(--primary-dark); font-weight:600; font-size:.82rem; text-decoration:none; margin-top:12px; transition:.2s; }
.lihat-semua:hover { background:var(--primary-color); color:#fff; }

/* === RANKING === */
.ranking-tabs { display:flex; gap:6px; margin-bottom:14px; flex-wrap:wrap; }
.rt-btn { padding:7px 18px; border-radius:8px; border:2px solid var(--primary-light); background:#fff; color:var(--primary-dark); font-weight:600; cursor:pointer; font-size:.82rem; transition:.2s; }
.rt-btn.active, .rt-btn:hover { background:var(--primary-color); color:#fff; border-color:var(--primary-color); }
.ranking-content { display:none; } .ranking-content.active { display:block; }
.rank-badge { width:30px; height:30px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-weight:800; font-size:.78rem; }
.rank-1 { background:linear-gradient(135deg,#FFD700,#FFA000); color:#fff; }
.rank-2 { background:linear-gradient(135deg,#C0C0C0,#9E9E9E); color:#fff; }
.rank-3 { background:linear-gradient(135deg,#CD7F32,#A0522D); color:#fff; }
.rank-other { background:#f0f0f0; color:#666; }

/* === GANTT === */
.gantt-row { display:flex; align-items:center; margin-bottom:5px; min-height:30px; }
.gantt-label { width:150px; min-width:150px; font-size:.76rem; font-weight:600; color:#444; padding-right:8px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.gantt-bar-wrap { flex:1; background:#f0f0f0; border-radius:5px; height:22px; position:relative; }
.gantt-bar { height:100%; border-radius:5px; display:flex; align-items:center; justify-content:flex-end; padding-right:7px; font-size:.65rem; font-weight:700; color:#fff; min-width:28px; }
.gantt-info { width:110px; min-width:110px; text-align:right; font-size:.7rem; color:#777; padding-left:7px; }

/* === TIMELINE === */
.tl-wrap { position:relative; padding-left:26px; }
.tl-wrap::before { content:''; position:absolute; left:10px; top:0; bottom:0; width:3px; background:linear-gradient(to bottom,var(--primary-color),var(--primary-light)); border-radius:3px; }
.tl-item { position:relative; margin-bottom:12px; padding:9px 12px; background:#f8faf9; border-radius:7px; border-left:3px solid var(--primary-color); }
.tl-item::before { content:''; position:absolute; left:-20px; top:12px; width:10px; height:10px; border-radius:50%; background:var(--primary-color); border:2px solid #fff; box-shadow:0 0 0 2px var(--primary-color); }
.tl-item .tl-sem { font-weight:700; font-size:.8rem; color:var(--primary-dark); }
.tl-item .tl-prog { font-size:.75rem; color:#666; margin-top:2px; }

/* === MISC === */
.growth-pos { color:#2e7d32; font-weight:700; }
.growth-neg { color:#c62828; font-weight:700; }
.growth-zero { color:#999; }
.filter-bar { display:flex; gap:10px; flex-wrap:wrap; align-items:center; padding:14px 18px; background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.06); margin-bottom:14px; border:1px solid #e8f0ec; }
.filter-bar select { padding:7px 11px; border:2px solid #e0e0e0; border-radius:8px; font-size:.84rem; }
.filter-bar select:focus { border-color:var(--primary-color); outline:none; }
.mini-table { width:100%; border-collapse:collapse; font-size:.81rem; }
.mini-table th { background:#f5f8f6; color:#555; font-weight:700; padding:9px 7px; text-align:left; border-bottom:2px solid #e0e0e0; font-size:.76rem; text-transform:uppercase; }
.mini-table td { padding:8px 7px; border-bottom:1px solid #f0f0f0; }
.mini-table tbody tr:hover { background:#f8fdf9; }
.khatam-section { background:linear-gradient(135deg,#e8f5e9,#f1f8e9); border:2px dashed #66bb6a; border-radius:10px; padding:14px; margin-top:14px; }
.khatam-section h5 { color:#2e7d32; margin:0 0 8px; font-size:.88rem; }
.empty-msg { text-align:center; padding:20px; color:#aaa; font-size:.88rem; }
.empty-msg i { font-size:1.8rem; display:block; margin-bottom:8px; opacity:.4; }
.btn-khatam { background:linear-gradient(135deg,#66bb6a,#43a047); color:#fff; border:none; padding:4px 11px; border-radius:6px; font-size:.73rem; cursor:pointer; font-weight:600; transition:.2s; }
.btn-khatam:hover { transform:scale(1.04); }
.btn-batal-khatam { background:#ef5350; color:#fff; border:none; padding:4px 11px; border-radius:6px; font-size:.73rem; cursor:pointer; font-weight:600; }
.bn-alert { background:linear-gradient(135deg,#fff3e0,#fbe9e7); border-left:4px solid #ff7043; border-radius:7px; padding:12px 16px; margin-bottom:10px; display:flex; align-items:center; gap:10px; }
.bn-icon { font-size:1.4rem; color:#ff7043; }
.bn-text { font-size:.83rem; color:#555; }
.completion-cell { text-align:center; font-weight:600; font-size:.76rem; padding:5px 3px !important; }
.completion-cell.high { background:#e8f5e9; color:#2e7d32; }
.completion-cell.mid { background:#fff8e1; color:#f57f17; }
.completion-cell.low { background:#fbe9e7; color:#bf360c; }
.completion-cell.none { background:#f5f5f5; color:#bbb; }
.rapor-btn { display:inline-flex; align-items:center; gap:6px; background:linear-gradient(135deg,var(--primary-color),var(--primary-dark)); color:#fff; padding:8px 16px; border-radius:8px; text-decoration:none; font-size:.81rem; font-weight:600; transition:.2s; border:none; cursor:pointer; }
.rapor-btn:hover { transform:translateY(-2px); color:#fff; }
.summary-card { background:linear-gradient(135deg,#f5f8f6,#fff); border-radius:9px; padding:14px; text-align:center; border:1px solid #e0e0e0; }
.summary-card .sc-val { font-size:1.4rem; font-weight:800; }
.summary-card .sc-label { font-size:.73rem; color:#888; margin-top:3px; }
.summary-card .sc-change { font-size:.78rem; margin-top:5px; }
@media print { .dash-tabs, .filter-bar, .no-print { display:none !important; } .tab-content { display:block !important; } }
</style>

<div class="page-header">
    <h2><i class="fas fa-chart-pie"></i> Dashboard Capaian Al-Qur'an & Hadist</h2>
</div>


<form method="GET" action="<?php echo e(route('admin.capaian.dashboard')); ?>" class="filter-bar no-print">
    <span style="color:#888;font-size:.84rem;"><i class="fas fa-filter"></i> Filter:</span>
    <select name="id_semester" class="form-control" style="min-width:200px;">
        <option value="">Semua Semester</option>
        <?php $__currentLoopData = $semesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($sem->id_semester); ?>" <?php echo e($selectedSemester == $sem->id_semester ? 'selected' : ''); ?>>
                <?php echo e($sem->nama_semester); ?> <?php if($sem->is_active): ?> ★ <?php endif; ?>
            </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="kelas" class="form-control" style="min-width:170px;">
        <option value="">Semua Kelas</option>
        <?php $kelompokGrouped = $kelasModels->groupBy(fn($k) => $k->kelompok->nama_kelompok ?? 'Lainnya'); ?>
        <?php $__currentLoopData = $kelompokGrouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $namaKelompok => $kelasGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <optgroup label="<?php echo e($namaKelompok); ?>">
                <?php $__currentLoopData = $kelasGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $km): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($km->nama_kelas); ?>" <?php echo e($kelas == $km->nama_kelas ? 'selected' : ''); ?>><?php echo e($km->nama_kelas); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </optgroup>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button type="submit" class="btn btn-primary" style="padding:7px 16px;"><i class="fas fa-search"></i> Terapkan</button>
    <?php if($kelas || $selectedSemester != ($semesterAktif->id_semester ?? '')): ?>
        <a href="<?php echo e(route('admin.capaian.dashboard')); ?>" class="btn btn-secondary" style="padding:7px 16px;"><i class="fas fa-redo"></i> Reset</a>
    <?php endif; ?>
</form>


<div class="dash-tabs no-print" id="dashTabs">
    <button class="dash-tab active" data-tab="tab-overview"><i class="fas fa-tachometer-alt"></i> Ringkasan</button>
    <button class="dash-tab" data-tab="tab-ranking"><i class="fas fa-trophy"></i> Ranking Kelas</button>
    <button class="dash-tab" data-tab="tab-materi"><i class="fas fa-book"></i> Analisis Materi</button>
    <button class="dash-tab" data-tab="tab-prediksi"><i class="fas fa-tasks"></i> Kalkulasi Progress</button>
    <button class="dash-tab" data-tab="tab-laporan"><i class="fas fa-file-alt"></i> Laporan</button>
</div>


<div class="tab-content active" id="tab-overview">
    
    <div class="kpi-grid" style="margin-top:14px;">
        <div class="kpi-card kpi-blue">
            <div class="kl">Santri Aktif</div>
            <div class="kv"><?php echo e($totalSantriAktif); ?></div>
            <div class="ks">Sedang belajar</div>
            <i class="fas fa-users ki"></i>
        </div>
        <div class="kpi-card kpi-purple">
            <div class="kl">Rata-rata Progress</div>
            <div class="kv"><?php echo e(number_format($rataRataProgress, 1)); ?>%</div>
            <div class="ks">Keseluruhan</div>
            <i class="fas fa-chart-line ki"></i>
        </div>
        <div class="kpi-card kpi-rose">
            <div class="kl">Santri Khatam</div>
            <div class="kv"><?php echo e($santrisKhatam->count()); ?></div>
            <div class="ks">Semua materi selesai</div>
            <i class="fas fa-graduation-cap ki"></i>
        </div>
    </div>

    
    <div class="kpi-grid">
        <?php $__currentLoopData = $statistikKategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat => $stats): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="kpi-card <?php echo e($kat == "Al-Qur'an" ? 'kpi-teal' : ($kat == 'Hadist' ? 'kpi-blue' : 'kpi-amber')); ?>">
            <div class="kl"><?php echo e($kat); ?></div>
            <div class="kv" style="font-size:1.4rem;"><?php echo e(number_format($stats['avg'], 1)); ?>%</div>
            <div class="ks"><?php echo e($stats['count']); ?> capaian &bull; <?php echo e($stats['selesai']); ?> selesai</div>
            <i class="fas fa-<?php echo e($kat == "Al-Qur'an" ? 'book-quran' : ($kat == 'Hadist' ? 'scroll' : 'book')); ?> ki"></i>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="chart-grid">
        <div class="chart-box">
            <h5><i class="fas fa-chart-line"></i> Progress Rata-rata Per Semester</h5>
            <p style="font-size:.76rem;color:#999;margin:0 0 10px;">Trend progress rata-rata setiap kelas antar semester.</p>
            <canvas id="chartSemesterComparison" style="max-height:260px;"></canvas>
        </div>
        <div class="chart-box">
            <h5><i class="fas fa-chart-bar"></i> Distribusi Progress</h5>
            <canvas id="chartDistribusi" style="max-height:260px;"></canvas>
        </div>
    </div>
</div>


<div class="tab-content" id="tab-ranking">
    <div class="sc" style="margin-top:14px;">
        <h4><i class="fas fa-trophy"></i> Rekap Per Kelas</h4>
        <div class="ranking-tabs">
            <?php $__currentLoopData = $kelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button class="rt-btn <?php echo e($idx === 0 ? 'active' : ''); ?>" data-kelas="<?php echo e($k); ?>">
                    <?php echo e($k); ?> <span style="opacity:.7;font-size:.72rem;">(<?php echo e($rekapKelas[$k]['total_aktif']); ?>)</span>
                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php $__currentLoopData = $kelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="ranking-content <?php echo e($idx === 0 ? 'active' : ''); ?>" id="ranking-<?php echo e($k); ?>">
            <?php if(isset($rekapKelas[$k]['summary'])): ?>
            <?php $s = $rekapKelas[$k]['summary']; ?>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:10px;margin-bottom:14px;">
                <div style="background:#e8f5e9;border-radius:9px;padding:12px;text-align:center;border:1px solid #c8e6c9;">
                    <div style="font-size:1.3rem;font-weight:800;color:#2e7d32;"><?php echo e($s['total_santri']); ?></div>
                    <div style="font-size:.7rem;color:#555;">Total Santri</div>
                </div>
                <div style="background:#e1f5fe;border-radius:9px;padding:12px;text-align:center;border:1px solid #b3e5fc;">
                    <div style="font-size:1.3rem;font-weight:800;color:#0277bd;"><?php echo e($s['avg_progress']); ?>%</div>
                    <div style="font-size:.7rem;color:#555;">Rata-rata Progress</div>
                </div>
                <div style="background:#fff8e1;border-radius:9px;padding:12px;text-align:center;border:1px solid #ffe082;">
                    <div style="font-size:1.3rem;font-weight:800;color:#f57f17;"><?php echo e($s['total_selesai']); ?></div>
                    <div style="font-size:.7rem;color:#555;">Total Materi Selesai</div>
                </div>
                <div style="background:#fbe9e7;border-radius:9px;padding:12px;text-align:center;border:1px solid #ffccbc;">
                    <div style="font-size:1.3rem;font-weight:800;color:#d32f2f;"><?php echo e($s['santri_tuntas']); ?></div>
                    <div style="font-size:.7rem;color:#555;">Santri Tuntas 100%</div>
                </div>
            </div>
            <?php endif; ?>

            <?php if(count($rekapKelas[$k]['ranking']) > 0): ?>
            <div style="overflow-x:auto;">
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th style="width:42px;">#</th>
                            <th>Nama Santri</th>
                            <th style="width:85px;">Progress</th>
                            <th style="width:110px;">Materi</th>
                            <th style="width:150px;">Bar</th>
                            <th style="width:65px;">Al-Qur'an</th>
                            <th style="width:60px;">Hadist</th>
                            <th style="width:65px;">Tambahan</th>
                            <th style="width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $rekapKelas[$k]['ranking']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rIdx => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><span class="rank-badge <?php echo e($rIdx < 3 ? 'rank-'.($rIdx+1) : 'rank-other'); ?>"><?php echo e($rIdx+1); ?></span></td>
                            <td>
                                <strong><?php echo e($r['santri']->nama_lengkap); ?></strong>
                                <br><span style="font-size:.7rem;color:#999;"><?php echo e($r['santri']->nis); ?></span>
                            </td>
                            <td>
                                <span style="font-weight:800;color:<?php echo e($r['avg_progress'] >= 80 ? '#2e7d32' : ($r['avg_progress'] >= 50 ? '#f57f17' : '#c62828')); ?>;">
                                    <?php echo e(number_format($r['avg_progress'],1)); ?>%
                                </span>
                            </td>
                            <td><span style="font-size:.78rem;"><?php echo e($r['selesai']); ?>/<?php echo e($r['total_materi_kelas']); ?></span></td>
                            <td>
                                <div class="prog-bar" style="height:10px;">
                                    <div class="prog-fill" style="width:<?php echo e($r['avg_progress']); ?>%;background:linear-gradient(90deg,<?php echo e($r['avg_progress'] >= 80 ? '#66bb6a,#2e7d32' : ($r['avg_progress'] >= 50 ? '#ffa726,#f57f17' : '#ef5350,#c62828')); ?>);"></div>
                                </div>
                            </td>
                            <td style="text-align:center;font-size:.76rem;font-weight:700;color:<?php echo e($r['alquran'] >= 80 ? '#2e7d32' : ($r['alquran'] >= 50 ? '#f57f17' : '#c62828')); ?>;"><?php echo e(number_format($r['alquran'],0)); ?>%</td>
                            <td style="text-align:center;font-size:.76rem;font-weight:700;color:<?php echo e($r['hadist'] >= 80 ? '#2e7d32' : ($r['hadist'] >= 50 ? '#f57f17' : '#c62828')); ?>;"><?php echo e(number_format($r['hadist'],0)); ?>%</td>
                            <td style="text-align:center;font-size:.76rem;font-weight:700;color:<?php echo e($r['tambahan'] >= 80 ? '#2e7d32' : ($r['tambahan'] >= 50 ? '#f57f17' : '#c62828')); ?>;"><?php echo e(number_format($r['tambahan'],0)); ?>%</td>
                            <td>
                                <?php if($r['is_full_khatam']): ?>
                                    <form method="POST" action="<?php echo e(route('admin.capaian.tandai-khatam', $r['santri']->id_santri)); ?>" style="display:inline;">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn-khatam"
                                            onclick="return confirm('Tandai <?php echo e(addslashes($r['santri']->nama_lengkap)); ?> sebagai Khatam?')">
                                            <i class="fas fa-check"></i> Tandai Lulus
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="<?php echo e(route('admin.capaian.riwayat-santri', $r['santri']->id_santri)); ?>"
                                       style="font-size:.76rem;color:var(--primary-color);">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top:10px;text-align:right;">
                <?php $kelasModelObj = $kelasModels->where('nama_kelas', $k)->first(); ?>
                <a href="<?php echo e(route('admin.capaian.index', array_filter(['id_kelas' => $kelasModelObj?->id, 'id_semester' => $selectedSemester]))); ?>"
                   class="lihat-semua" style="display:inline-flex;">
                    <i class="fas fa-list"></i>
                    Lihat Semua Santri Kelas <?php echo e($k); ?>

                    (<?php echo e($rekapKelas[$k]['total_aktif']); ?> santri)
                </a>
            </div>

            <?php if(count($rekapKelas[$k]['ranking']) >= 2): ?>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px;">
                <div style="background:#e8f5e9;border-radius:7px;padding:10px;">
                    <span style="font-weight:700;color:#2e7d32;font-size:.83rem;"><i class="fas fa-arrow-up"></i> Tertinggi</span>
                    <div style="margin-top:4px;font-size:.8rem;"><?php echo e($rekapKelas[$k]['ranking'][0]['santri']->nama_lengkap); ?> — <strong><?php echo e(number_format($rekapKelas[$k]['ranking'][0]['avg_progress'],1)); ?>%</strong></div>
                </div>
                <div style="background:#fbe9e7;border-radius:7px;padding:10px;">
                    <span style="font-weight:700;color:#c62828;font-size:.83rem;"><i class="fas fa-arrow-down"></i> Terendah</span>
                    <?php $last = end($rekapKelas[$k]['ranking']); ?>
                    <div style="margin-top:4px;font-size:.8rem;"><?php echo e($last['santri']->nama_lengkap); ?> — <strong><?php echo e(number_format($last['avg_progress'],1)); ?>%</strong></div>
                </div>
            </div>
            <?php endif; ?>
            <?php else: ?>
                <div class="empty-msg"><i class="fas fa-inbox"></i>Belum ada data ranking</div>
            <?php endif; ?>

            <?php if($rekapKelas[$k]['khatam']->count() > 0): ?>
            <div class="khatam-section">
                <h5><i class="fas fa-star"></i> Santri Khatam (<?php echo e($rekapKelas[$k]['khatam']->count()); ?>)</h5>
                <div style="display:flex;flex-wrap:wrap;gap:7px;">
                    <?php $__currentLoopData = $rekapKelas[$k]['khatam']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ks): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="background:#fff;border-radius:7px;padding:7px 12px;display:flex;align-items:center;gap:7px;font-size:.8rem;box-shadow:0 1px 4px rgba(0,0,0,.07);">
                        <i class="fas fa-award" style="color:#FFD700;"></i>
                        <span><strong><?php echo e($ks->nama_lengkap); ?></strong> (<?php echo e($ks->nis); ?>)</span>
                        <form method="POST" action="<?php echo e(route('admin.capaian.batal-khatam', $ks->id_santri)); ?>" style="display:inline;margin-left:4px;">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn-batal-khatam"
                                onclick="return confirm('Batalkan status Khatam?')"
                                style="font-size:.66rem;padding:2px 7px;">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>


<div class="tab-content" id="tab-materi">
    <div class="sc" style="margin-top:14px;">
        <h4><i class="fas fa-table"></i> Materi Completion Rate Per Semester</h4>
        <?php if(count($materiCompletionRate) > 0): ?>
        <div style="overflow-x:auto;">
            <table class="mini-table">
                <thead>
                    <tr>
                        <th style="min-width:150px;">Materi</th>
                        <th>Kategori</th>
                        <th>Kelas</th>
                        <?php $__currentLoopData = $allSemestersOrdered; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th style="text-align:center;font-size:.68rem;min-width:85px;"><?php echo e($sem->nama_semester); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $materiCompletionRate; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mcr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><strong><?php echo e($mcr['materi']->nama_kitab); ?></strong></td>
                        <td><?php echo $mcr['materi']->kategori_badge; ?></td>
                        <td><?php echo $mcr['materi']->kelas_badge; ?></td>
                        <?php $__currentLoopData = $allSemestersOrdered; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $rate = $mcr['rates'][$sem->id_semester] ?? null; ?>
                            <td class="completion-cell <?php echo e($rate === null ? 'none' : ($rate >= 70 ? 'high' : ($rate >= 30 ? 'mid' : 'low'))); ?>">
                                <?php echo e($rate !== null ? $rate.'%' : '-'); ?>

                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="empty-msg"><i class="fas fa-book"></i>Belum ada data materi</div>
        <?php endif; ?>
    </div>

    <div class="sc">
        <h4><i class="fas fa-exclamation-triangle" style="color:#ff7043;"></i> Bottleneck Analysis</h4>
        <p style="font-size:.76rem;color:#999;margin:-10px 0 12px;">Materi yang menjadi bottleneck — banyak santri stuck di bawah 50%.</p>
        <?php if(count($bottleneckMateri) > 0): ?>
            <?php $__currentLoopData = array_slice($bottleneckMateri, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($bn['stuck_percentage'] > 0): ?>
                <div class="bn-alert">
                    <div class="bn-icon"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="bn-text">
                        <strong><?php echo e(number_format($bn['stuck_percentage'],0)); ?>%</strong> santri stuck di
                        <strong><?php echo e($bn['materi']->nama_kitab); ?></strong> — rata-rata <strong><?php echo e(number_format($bn['avg_progress'],1)); ?>%</strong>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <table class="mini-table" style="margin-top:12px;">
                <thead>
                    <tr>
                        <th>Materi</th>
                        <th>Kategori</th>
                        <th style="text-align:center;">Total</th>
                        <th style="text-align:center;">Stuck</th>
                        <th style="text-align:center;">% Stuck</th>
                        <th>Avg Progress</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $bottleneckMateri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><strong><?php echo e($bn['materi']->nama_kitab); ?></strong></td>
                        <td><?php echo $bn['materi']->kategori_badge; ?></td>
                        <td style="text-align:center;"><?php echo e($bn['total_santri']); ?></td>
                        <td style="text-align:center;color:#c62828;font-weight:700;"><?php echo e($bn['stuck_santri']); ?></td>
                        <td style="text-align:center;">
                            <span style="background:<?php echo e($bn['stuck_percentage'] >= 50 ? '#fbe9e7' : '#fff8e1'); ?>;color:<?php echo e($bn['stuck_percentage'] >= 50 ? '#c62828' : '#f57f17'); ?>;padding:2px 9px;border-radius:10px;font-weight:700;font-size:.76rem;">
                                <?php echo e(number_format($bn['stuck_percentage'],0)); ?>%
                            </span>
                        </td>
                        <td>
                            <div class="prog-bar" style="width:110px;">
                                <div class="prog-fill" style="width:<?php echo e($bn['avg_progress']); ?>%;background:<?php echo e($bn['avg_progress'] >= 50 ? '#66bb6a' : ($bn['avg_progress'] >= 25 ? '#ffa726' : '#ef5350')); ?>;"></div>
                            </div>
                            <span style="font-size:.7rem;color:#888;"><?php echo e(number_format($bn['avg_progress'],1)); ?>%</span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-msg"><i class="fas fa-check-circle"></i>Tidak ada bottleneck terdeteksi</div>
        <?php endif; ?>
    </div>
</div>


<div class="tab-content" id="tab-prediksi">
    <div class="sc" style="margin-top:14px;">
        <h4><i class="fas fa-tasks"></i> Estimasi Selesai Per Kelas
            <span class="bc"><?php echo e($santrisAktif->count()); ?> santri aktif</span>
        </h4>
        <p style="font-size:.76rem;color:#999;margin:-10px 0 14px;">
            Kalkulasi estimasi semester selesai berdasarkan rata-rata pertumbuhan progress antar semester.
            "Stagnan" = tidak ada pertumbuhan atau menurun.
        </p>

        
        <div class="ranking-tabs" id="progKelasTabBtns">
            <?php $__currentLoopData = $kelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button class="pk-btn rt-btn <?php echo e($idx === 0 ? 'active' : ''); ?>" data-progkelas="<?php echo e($idx); ?>">
                    <?php echo e($k); ?>

                    <span style="opacity:.7;font-size:.72rem;">(<?php echo e(count($projectedByKelas[$k] ?? [])); ?>)</span>
                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php $__currentLoopData = $kelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="prog-kelas-content <?php echo e($idx === 0 ? 'active' : ''); ?>" id="progkelas-<?php echo e($idx); ?>"
             style="<?php echo e($idx !== 0 ? 'display:none;' : ''); ?>">
            <?php if(isset($projectedByKelas[$k]) && count($projectedByKelas[$k]) > 0): ?>
            <div style="margin-bottom:8px;font-size:.78rem;color:#888;">
                <i class="fas fa-users"></i> Menampilkan <strong><?php echo e(count($projectedByKelas[$k])); ?></strong> santri kelas <?php echo e($k); ?>

            </div>
            <?php $__currentLoopData = $projectedByKelas[$k]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="gantt-row">
                <div class="gantt-label" title="<?php echo e($pg['santri']->nama_lengkap); ?>">
                    <?php echo e(\Illuminate\Support\Str::limit($pg['santri']->nama_lengkap, 18)); ?>

                </div>
                <div class="gantt-bar-wrap">
                    <?php
                        $prog = min($pg['current_progress'], 100);
                        $gc   = $prog >= 80 ? '#66bb6a,#2e7d32' : ($prog >= 50 ? '#ffa726,#f57f17' : '#ef5350,#c62828');
                    ?>
                    <div class="gantt-bar" style="width:<?php echo e($prog); ?>%;background:linear-gradient(90deg,<?php echo e($gc); ?>);">
                        <?php echo e(number_format($prog, 0)); ?>%
                    </div>
                </div>
                <div class="gantt-info">
                    <?php if($pg['current_progress'] >= 100): ?>
                        <span style="color:#2e7d32;font-weight:700;"><i class="fas fa-check-circle"></i> Khatam</span>
                    <?php elseif($pg['semesters_to_grad'] !== null): ?>
                        <span>+<?php echo e($pg['semesters_to_grad']); ?> sem</span>
                        <br><span style="font-size:.63rem;color:#999;"><?php echo e($pg['growth_rate'] > 0 ? '+' : ''); ?><?php echo e($pg['growth_rate']); ?>%/sem</span>
                    <?php else: ?>
                        <span style="color:#c62828;font-size:.7rem;"><i class="fas fa-exclamation-triangle"></i> Stagnan</span>
                    <?php endif; ?>
                </div>
                <div style="min-width:70px;padding-left:8px;">
                    <a href="<?php echo e(route('admin.capaian.riwayat-santri', $pg['santri']->id_santri)); ?>"
                       style="font-size:.72rem;color:var(--primary-color);text-decoration:none;">
                        <i class="fas fa-eye"></i> Detail
                    </a>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <div style="margin-top:12px;text-align:right;">
                <?php $kelasModelObj2 = $kelasModels->where('nama_kelas', $k)->first(); ?>
                <a href="<?php echo e(route('admin.capaian.index', array_filter(['id_kelas' => $kelasModelObj2?->id, 'id_semester' => $selectedSemester]))); ?>"
                   class="lihat-semua" style="display:inline-flex;">
                    <i class="fas fa-list"></i> Lihat Detail Capaian Kelas <?php echo e($k); ?>

                </a>
            </div>
            <?php else: ?>
                <div class="empty-msg"><i class="fas fa-inbox"></i>Belum ada data kalkulasi untuk kelas <?php echo e($k); ?></div>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div style="margin-top:14px;padding:9px;background:#f5f8f6;border-radius:7px;font-size:.76rem;color:#666;">
            <i class="fas fa-info-circle" style="color:var(--primary-color);"></i>
            Estimasi dihitung berdasarkan rata-rata pertumbuhan progress per semester dari data historis yang tersedia.
        </div>
    </div>
</div>


<div class="tab-content" id="tab-laporan">
    <?php if($semesterSummary): ?>
    <div class="sc" style="margin-top:14px;">
        <h4><i class="fas fa-clipboard-check"></i> Semester Summary — <?php echo e($semesterSummary['semester']->nama_semester); ?></h4>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:10px;margin-bottom:14px;">
            <div class="summary-card">
                <div class="sc-val" style="color:#6FBA9D;"><?php echo e($semesterSummary['total_santri']); ?></div>
                <div class="sc-label">Total Santri</div>
            </div>
            <div class="summary-card">
                <div class="sc-val" style="color:#81C6E8;"><?php echo e(number_format($semesterSummary['avg_progress'],1)); ?>%</div>
                <div class="sc-label">Rata-rata Progress</div>
                <div class="sc-change <?php echo e($semesterSummary['kenaikan'] >= 0 ? 'growth-pos' : 'growth-neg'); ?>">
                    <?php echo e($semesterSummary['kenaikan'] >= 0 ? '+' : ''); ?><?php echo e(number_format($semesterSummary['kenaikan'],1)); ?>%
                    dari <?php echo e($semesterSummary['prev_semester']?->nama_semester ?? 'N/A'); ?>

                </div>
            </div>
            <div class="summary-card">
                <div class="sc-val" style="color:#2e7d32;"><?php echo e($semesterSummary['santri_khatam']); ?></div>
                <div class="sc-label">Khatam Semua Materi</div>
            </div>
            <div class="summary-card">
                <div class="sc-val" style="color:#c62828;"><?php echo e($semesterSummary['santri_remedial_count']); ?></div>
                <div class="sc-label">Perlu Remedial (&lt;30%)</div>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div>
                <h5 style="color:#2e7d32;font-size:.86rem;margin-bottom:8px;"><i class="fas fa-star"></i> Materi Terbanyak Dikhatamkan</h5>
                <?php $__empty_1 = true; $__currentLoopData = $semesterSummary['materi_khatam']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid #f0f0f0;font-size:.8rem;">
                    <span><?php echo e($mk['materi']->nama_kitab ?? '-'); ?></span>
                    <span style="font-weight:700;color:#2e7d32;"><?php echo e($mk['count']); ?> santri</span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <span style="color:#999;font-size:.8rem;">Belum ada</span>
                <?php endif; ?>
            </div>
            <div>
                <h5 style="color:#c62828;font-size:.86rem;margin-bottom:8px;"><i class="fas fa-exclamation-triangle"></i> Materi Paling Sedikit Progress</h5>
                <?php $__empty_1 = true; $__currentLoopData = $semesterSummary['materi_min']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid #f0f0f0;font-size:.8rem;">
                    <span><?php echo e($mm['materi']->nama_kitab ?? '-'); ?></span>
                    <span style="font-weight:700;color:#c62828;"><?php echo e($mm['avg']); ?>%</span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <span style="color:#999;font-size:.8rem;">Tidak ada data</span>
                <?php endif; ?>
            </div>
        </div>
        <?php if($semesterSummary['santri_remedial_count'] > 0): ?>
        <div style="margin-top:14px;background:#fbe9e7;border-radius:8px;padding:11px;">
            <h5 style="color:#c62828;margin:0 0 7px;font-size:.84rem;"><i class="fas fa-user-times"></i> Santri Perlu Remedial</h5>
            <div style="display:flex;flex-wrap:wrap;gap:5px;font-size:.78rem;">
                <?php $__currentLoopData = $semesterSummary['santri_remedial']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span style="background:#fff;padding:3px 9px;border-radius:6px;border:1px solid #ffcdd2;"><?php echo e($sr->nama_lengkap); ?> (<?php echo e($sr->kelas); ?>)</span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="sc" style="margin-top:14px;">
        <div class="empty-msg"><i class="fas fa-clipboard-check"></i>Pilih semester pada filter untuk melihat laporan</div>
    </div>
    <?php endif; ?>

    <div class="sc">
        <h4><i class="fas fa-file-pdf"></i> Export Rapor Per Santri</h4>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:11px;align-items:end;">
            <div>
                <label style="font-size:.81rem;font-weight:600;color:#555;">Pilih Santri:</label>
                <select id="raporSantri" style="width:100%;padding:7px 11px;border:2px solid #e0e0e0;border-radius:8px;font-size:.84rem;margin-top:3px;">
                    <option value="">-- Pilih Santri --</option>
                    <?php $__currentLoopData = $santrisAktif; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s->id_santri); ?>"><?php echo e($s->nama_lengkap); ?> (<?php echo e($s->kelas); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php $__currentLoopData = $santrisKhatam; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s->id_santri); ?>"><?php echo e($s->nama_lengkap); ?> (<?php echo e($s->kelas); ?>) - Khatam</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label style="font-size:.81rem;font-weight:600;color:#555;">Pilih Semester:</label>
                <select id="raporSemester" style="width:100%;padding:7px 11px;border:2px solid #e0e0e0;border-radius:8px;font-size:.84rem;margin-top:3px;">
                    <option value="">-- Pilih Semester --</option>
                    <?php $__currentLoopData = $semesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($sem->id_semester); ?>"><?php echo e($sem->nama_semester); ?><?php if($sem->is_active): ?> ★ <?php endif; ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <div style="margin-top:12px;">
            <button class="rapor-btn" onclick="exportRapor()"><i class="fas fa-download"></i> Generate Rapor</button>
        </div>
    </div>

    <div style="margin-top:14px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap;" class="no-print">
        <a href="<?php echo e(route('admin.capaian.create')); ?>" class="btn btn-success" style="padding:9px 18px;"><i class="fas fa-plus"></i> Input Capaian Baru</a>
        <a href="<?php echo e(route('admin.capaian.index')); ?>" class="btn btn-primary" style="padding:9px 18px;"><i class="fas fa-list"></i> Daftar Capaian</a>
        <a href="<?php echo e(route('admin.materi.index')); ?>" class="btn btn-info" style="padding:9px 18px;"><i class="fas fa-book"></i> Master Materi</a>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
// Tab switching (main tabs)
document.querySelectorAll('.dash-tab').forEach(t => {
    t.addEventListener('click', function() {
        document.querySelectorAll('.dash-tab').forEach(x => x.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(x => x.classList.remove('active'));
        this.classList.add('active');
        document.getElementById(this.dataset.tab).classList.add('active');
    });
});

// Ranking kelas tabs
document.querySelectorAll('.rt-btn:not(.pk-btn)').forEach(t => {
    t.addEventListener('click', function() {
        document.querySelectorAll('.rt-btn:not(.pk-btn)').forEach(x => x.classList.remove('active'));
        document.querySelectorAll('.ranking-content').forEach(x => x.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('ranking-' + this.dataset.kelas).classList.add('active');
    });
});

// Kalkulasi Progress kelas tabs
document.querySelectorAll('.pk-btn').forEach(t => {
    t.addEventListener('click', function() {
        document.querySelectorAll('.pk-btn').forEach(x => x.classList.remove('active'));
        document.querySelectorAll('.prog-kelas-content').forEach(x => x.style.display = 'none');
        this.classList.add('active');
        const el = document.getElementById('progkelas-' + this.dataset.progkelas);
        if (el) el.style.display = 'block';
    });
});

const clr = {
    teal:'rgba(111,186,157,', blue:'rgba(129,198,232,', amber:'rgba(255,213,107,',
    rose:'rgba(255,139,148,', orange:'rgba(255,171,145,', purple:'rgba(179,157,219,', green:'rgba(102,187,106,'
};
const palette = [clr.teal,clr.blue,clr.amber,clr.rose,clr.purple,clr.orange,clr.green];

// Chart: Distribusi Progress
new Chart(document.getElementById('chartDistribusi'), {
    type: 'bar',
    data: {
        labels: ['0-25%','26-50%','51-75%','76-99%','100%'],
        datasets: [{
            data: [<?php echo e($distribusiProgress['0-25%']); ?>,<?php echo e($distribusiProgress['26-50%']); ?>,<?php echo e($distribusiProgress['51-75%']); ?>,<?php echo e($distribusiProgress['76-99%']); ?>,<?php echo e($distribusiProgress['100%']); ?>],
            backgroundColor: [clr.rose+'0.8)',clr.orange+'0.8)',clr.amber+'0.8)',clr.blue+'0.8)',clr.teal+'0.8)'],
            borderColor: [clr.rose+'1)',clr.orange+'1)',clr.amber+'1)',clr.blue+'1)',clr.teal+'1)'],
            borderWidth: 2, borderRadius: 5
        }]
    },
    options: {
        responsive:true, maintainAspectRatio:true,
        scales:{ y:{ beginAtZero:true, ticks:{stepSize:1} } },
        plugins:{ legend:{display:false} }
    }
});

// Chart: Semester Comparison (digunakan di Tab Ringkasan)
const semLabels = <?php echo json_encode($semesterLabels, 15, 512) ?>;
const semData   = <?php echo json_encode($semesterComparison, 15, 512) ?>;
const lineDatasets = Object.entries(semData).map(([k,v],i) => {
    const c = palette[i % palette.length];
    return { label:k, data:v, borderColor:c+'1)', backgroundColor:c+'0.12)', tension:.4, fill:true, pointRadius:4, borderWidth:2.5 };
});
if(document.getElementById('chartSemesterComparison')) {
    new Chart(document.getElementById('chartSemesterComparison'), {
        type:'line',
        data:{ labels:semLabels, datasets:lineDatasets },
        options:{
            responsive:true, maintainAspectRatio:true,
            scales:{ y:{ beginAtZero:true, max:100, ticks:{callback:v=>v+'%'} } },
            plugins:{
                legend:{ position:'bottom', labels:{padding:12} },
                tooltip:{ callbacks:{label:ctx=>ctx.dataset.label+': '+ctx.parsed.y.toFixed(1)+'%'}, mode:'index', intersect:false }
            }
        }
    });
}

// Export rapor
function exportRapor() {
    const santri   = document.getElementById('raporSantri').value;
    const semester = document.getElementById('raporSemester').value;
    if (!santri || !semester) { alert('Pilih santri dan semester terlebih dahulu.'); return; }
    window.open('<?php echo e(url("admin/capaian/export-rapor")); ?>/'+santri+'/'+semester, '_blank');
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/capaian/dashboard.blade.php ENDPATH**/ ?>