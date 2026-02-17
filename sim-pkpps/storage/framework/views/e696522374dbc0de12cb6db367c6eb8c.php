

<?php $__env->startSection('content'); ?>

<style>
/* === TABS === */
.dash-tabs { display: flex; gap: 4px; flex-wrap: wrap; border-bottom: 3px solid var(--primary-color); margin-bottom: 0; padding: 0; background: #f8faf9; border-radius: 12px 12px 0 0; }
.dash-tab { padding: 12px 18px; cursor: pointer; font-size: 0.85rem; font-weight: 600; color: #666; background: transparent; border: none; border-radius: 10px 10px 0 0; transition: all 0.3s; position: relative; }
.dash-tab:hover { color: var(--primary-dark); background: var(--primary-light); }
.dash-tab.active { color: #fff; background: var(--primary-color); }
.dash-tab i { margin-right: 5px; }
.tab-content { display: none; animation: fadeTab 0.4s ease; }
.tab-content.active { display: block; }
@keyframes fadeTab { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

/* === SECTION CARD === */
.section-card { background: #fff; border-radius: 12px; padding: 24px; margin-bottom: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #e8f0ec; }
.section-card h4 { margin: 0 0 18px 0; color: var(--primary-dark); font-size: 1.05rem; display: flex; align-items: center; gap: 8px; }
.section-card h4 .badge-count { background: var(--primary-light); color: var(--primary-dark); font-size: 0.75rem; padding: 2px 10px; border-radius: 20px; }

/* === KPI CARDS === */
.kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 20px; }
.kpi-card { background: #fff; border-radius: 14px; padding: 20px; position: relative; overflow: hidden; box-shadow: 0 3px 15px rgba(0,0,0,0.07); border-left: 5px solid; transition: transform 0.2s, box-shadow 0.2s; }
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.12); }
.kpi-card .kpi-icon { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); font-size: 2.5rem; opacity: 0.12; }
.kpi-card .kpi-label { font-size: 0.8rem; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
.kpi-card .kpi-value { font-size: 1.8rem; font-weight: 800; line-height: 1.1; }
.kpi-card .kpi-sub { font-size: 0.75rem; color: #aaa; margin-top: 4px; }
.kpi-card.kpi-teal { border-color: #6FBA9D; } .kpi-card.kpi-teal .kpi-value { color: #6FBA9D; }
.kpi-card.kpi-blue { border-color: #81C6E8; } .kpi-card.kpi-blue .kpi-value { color: #81C6E8; }
.kpi-card.kpi-amber { border-color: #FFD56B; } .kpi-card.kpi-amber .kpi-value { color: #d4a017; }
.kpi-card.kpi-rose { border-color: #FF8B94; } .kpi-card.kpi-rose .kpi-value { color: #FF8B94; }
.kpi-card.kpi-purple { border-color: #B39DDB; } .kpi-card.kpi-purple .kpi-value { color: #B39DDB; }

/* === CHART GRID === */
.chart-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
.chart-box { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #e8f0ec; }
.chart-box h5 { margin: 0 0 15px 0; color: var(--primary-dark); font-size: 0.95rem; }
@media (max-width: 768px) { .chart-grid { grid-template-columns: 1fr; } }

/* === HEATMAP === */
.heatmap-wrap { overflow-x: auto; }
.heatmap-table { border-collapse: separate; border-spacing: 2px; width: 100%; font-size: 0.7rem; }
.heatmap-table th { padding: 6px 4px; font-weight: 600; color: #555; text-align: center; font-size: 0.65rem; white-space: nowrap; max-width: 80px; overflow: hidden; text-overflow: ellipsis; background: #f5f5f5; border-radius: 4px; }
.heatmap-table td { text-align: center; padding: 5px 3px; border-radius: 4px; font-weight: 700; color: #fff; min-width: 36px; transition: transform 0.15s; cursor: default; }
.heatmap-table td:hover { transform: scale(1.15); z-index: 2; position: relative; }
.heatmap-table td.hm-name { text-align: left; color: #333; font-weight: 600; font-size: 0.72rem; white-space: nowrap; background: transparent !important; min-width: 120px; }
.hm-0 { background: #ef5350; } .hm-25 { background: #ff7043; } .hm-50 { background: #ffa726; } .hm-75 { background: #66bb6a; } .hm-100 { background: #2e7d32; }
.heatmap-legend { display: flex; gap: 12px; margin-top: 12px; font-size: 0.75rem; align-items: center; flex-wrap: wrap; }
.heatmap-legend .hl-item { display: flex; align-items: center; gap: 4px; }
.heatmap-legend .hl-box { width: 16px; height: 16px; border-radius: 3px; }

/* === GANTT CHART === */
.gantt-wrap { overflow-x: auto; }
.gantt-row { display: flex; align-items: center; margin-bottom: 6px; min-height: 32px; }
.gantt-label { width: 160px; min-width: 160px; font-size: 0.78rem; font-weight: 600; color: #444; padding-right: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.gantt-bar-wrap { flex: 1; background: #f0f0f0; border-radius: 6px; height: 24px; position: relative; overflow: visible; }
.gantt-bar { height: 100%; border-radius: 6px; display: flex; align-items: center; justify-content: flex-end; padding-right: 8px; font-size: 0.68rem; font-weight: 700; color: #fff; transition: width 0.8s ease; min-width: 30px; }
.gantt-marker { position: absolute; top: -2px; bottom: -2px; width: 3px; background: #333; border-radius: 2px; z-index: 2; }
.gantt-marker::after { content: attr(data-label); position: absolute; top: -16px; left: 50%; transform: translateX(-50%); font-size: 0.6rem; color: #333; white-space: nowrap; font-weight: 600; }
.gantt-info { width: 120px; min-width: 120px; text-align: right; font-size: 0.72rem; color: #777; padding-left: 8px; }

/* === RANKING === */
.ranking-tabs { display: flex; gap: 6px; margin-bottom: 16px; }
.ranking-tab { padding: 8px 20px; border-radius: 8px; border: 2px solid var(--primary-light); background: #fff; color: var(--primary-dark); font-weight: 600; cursor: pointer; font-size: 0.85rem; transition: all 0.2s; }
.ranking-tab.active, .ranking-tab:hover { background: var(--primary-color); color: #fff; border-color: var(--primary-color); }
.ranking-content { display: none; } .ranking-content.active { display: block; }
.rank-badge { width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem; }
.rank-1 { background: linear-gradient(135deg, #FFD700, #FFA000); color: #fff; }
.rank-2 { background: linear-gradient(135deg, #C0C0C0, #9E9E9E); color: #fff; }
.rank-3 { background: linear-gradient(135deg, #CD7F32, #A0522D); color: #fff; }
.rank-other { background: #f0f0f0; color: #666; }

/* === TIMELINE === */
.timeline { position: relative; padding-left: 28px; }
.timeline::before { content: ''; position: absolute; left: 12px; top: 0; bottom: 0; width: 3px; background: linear-gradient(to bottom, var(--primary-color), var(--primary-light)); border-radius: 3px; }
.timeline-item { position: relative; margin-bottom: 16px; padding: 10px 14px; background: #f8faf9; border-radius: 8px; border-left: 3px solid var(--primary-color); }
.timeline-item::before { content: ''; position: absolute; left: -22px; top: 14px; width: 12px; height: 12px; border-radius: 50%; background: var(--primary-color); border: 3px solid #fff; box-shadow: 0 0 0 2px var(--primary-color); }
.timeline-item .tl-sem { font-weight: 700; font-size: 0.82rem; color: var(--primary-dark); }
.timeline-item .tl-progress { font-size: 0.78rem; color: #666; margin-top: 3px; }

/* === MISC === */
.growth-pos { color: #2e7d32; font-weight: 700; } .growth-neg { color: #c62828; font-weight: 700; } .growth-zero { color: #999; }
.filter-bar { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; padding: 16px 20px; background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); margin-bottom: 20px; border: 1px solid #e8f0ec; }
.filter-bar select, .filter-bar input { padding: 8px 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 0.85rem; transition: border-color 0.2s; }
.filter-bar select:focus, .filter-bar input:focus { border-color: var(--primary-color); outline: none; }
.mini-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
.mini-table th { background: #f5f8f6; color: #555; font-weight: 700; padding: 10px 8px; text-align: left; border-bottom: 2px solid #e0e0e0; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.3px; }
.mini-table td { padding: 9px 8px; border-bottom: 1px solid #f0f0f0; }
.mini-table tbody tr:hover { background: #f8fdf9; }
.prog-bar { height: 8px; background: #e8e8e8; border-radius: 4px; overflow: hidden; }
.prog-fill { height: 100%; border-radius: 4px; transition: width 0.6s ease; }
.khatam-section { background: linear-gradient(135deg, #e8f5e9, #f1f8e9); border: 2px dashed #66bb6a; border-radius: 12px; padding: 16px; margin-top: 16px; }
.khatam-section h5 { color: #2e7d32; margin: 0 0 10px 0; font-size: 0.9rem; }
.empty-msg { text-align: center; padding: 30px; color: #aaa; font-size: 0.9rem; }
.empty-msg i { font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.4; }
.btn-khatam { background: linear-gradient(135deg, #66bb6a, #43a047); color: #fff; border: none; padding: 5px 12px; border-radius: 6px; font-size: 0.75rem; cursor: pointer; font-weight: 600; transition: all 0.2s; }
.btn-khatam:hover { transform: scale(1.05); box-shadow: 0 2px 8px rgba(76,175,80,0.4); }
.btn-batal-khatam { background: #ef5350; color: #fff; border: none; padding: 5px 12px; border-radius: 6px; font-size: 0.75rem; cursor: pointer; font-weight: 600; }
.completion-cell { text-align: center; font-weight: 600; font-size: 0.78rem; padding: 6px 4px !important; }
.completion-cell.high { background: #e8f5e9; color: #2e7d32; }
.completion-cell.mid { background: #fff8e1; color: #f57f17; }
.completion-cell.low { background: #fbe9e7; color: #bf360c; }
.completion-cell.none { background: #f5f5f5; color: #bbb; }
.bottleneck-alert { background: linear-gradient(135deg, #fff3e0, #fbe9e7); border-left: 4px solid #ff7043; border-radius: 8px; padding: 14px 18px; margin-bottom: 12px; display: flex; align-items: center; gap: 12px; }
.bottleneck-alert .bn-icon { font-size: 1.6rem; color: #ff7043; }
.bottleneck-alert .bn-text { font-size: 0.85rem; color: #555; }
.bottleneck-alert .bn-text strong { color: #e64a19; }
.rapor-btn { display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: #fff; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.82rem; font-weight: 600; transition: all 0.2s; border: none; cursor: pointer; }
.rapor-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(111,186,157,0.4); color: #fff; text-decoration: none; }
.summary-card { background: linear-gradient(135deg, #f5f8f6, #fff); border-radius: 10px; padding: 16px; text-align: center; border: 1px solid #e0e0e0; }
.summary-card .sc-val { font-size: 1.5rem; font-weight: 800; }
.summary-card .sc-label { font-size: 0.75rem; color: #888; margin-top: 4px; }
.summary-card .sc-change { font-size: 0.8rem; margin-top: 6px; }
@media print { .dash-tabs, .filter-bar, .no-print { display: none !important; } .tab-content { display: block !important; } }
</style>

<div class="page-header">
    <h2><i class="fas fa-chart-pie"></i> Dashboard Capaian Al-Qur'an & Hadist</h2>
</div>


<form method="GET" action="<?php echo e(route('admin.capaian.dashboard')); ?>" class="filter-bar no-print">
    <div style="display:flex;align-items:center;gap:6px;color:#888;font-size:0.85rem;"><i class="fas fa-filter"></i> Filter:</div>
    <select name="id_semester" class="form-control" style="min-width:200px;">
        <option value="">Semua Semester</option>
        <?php $__currentLoopData = $semesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($sem->id_semester); ?>" <?php echo e($selectedSemester == $sem->id_semester ? 'selected' : ''); ?>>
                <?php echo e($sem->nama_semester); ?> <?php if($sem->is_active): ?> ★ <?php endif; ?>
            </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="kelas" class="form-control" style="min-width:180px;">
        <option value="">Semua Kelas</option>
        <?php
            $kelompokGrouped = $kelasModels->groupBy(fn($k) => $k->kelompok->nama_kelompok ?? 'Lainnya');
        ?>
        <?php $__currentLoopData = $kelompokGrouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $namaKelompok => $kelasGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <optgroup label="<?php echo e($namaKelompok); ?>">
                <?php $__currentLoopData = $kelasGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $km): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($km->nama_kelas); ?>" <?php echo e($kelas == $km->nama_kelas ? 'selected' : ''); ?>><?php echo e($km->nama_kelas); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </optgroup>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button type="submit" class="btn btn-primary" style="padding:8px 18px;"><i class="fas fa-search"></i> Terapkan</button>
    <?php if($kelas || $selectedSemester != ($semesterAktif->id_semester ?? '')): ?>
        <a href="<?php echo e(route('admin.capaian.dashboard')); ?>" class="btn btn-secondary" style="padding:8px 18px;"><i class="fas fa-redo"></i> Reset</a>
    <?php endif; ?>
</form>


<div class="dash-tabs no-print" id="dashTabs">
    <button class="dash-tab active" data-tab="tab-overview"><i class="fas fa-tachometer-alt"></i> Ringkasan</button>
    <button class="dash-tab" data-tab="tab-ranking"><i class="fas fa-trophy"></i> Ranking Kelas</button>
    <button class="dash-tab" data-tab="tab-semester"><i class="fas fa-chart-line"></i> Trend Semester</button>
    <button class="dash-tab" data-tab="tab-materi"><i class="fas fa-book"></i> Analisis Materi</button>
    <button class="dash-tab" data-tab="tab-prediksi"><i class="fas fa-magic"></i> Prediksi</button>
    <button class="dash-tab" data-tab="tab-laporan"><i class="fas fa-file-alt"></i> Laporan</button>
</div>


<div class="tab-content active" id="tab-overview">
    
    <div class="kpi-grid" style="margin-top:20px;">
        <div class="kpi-card kpi-teal">
            <div class="kpi-label">Total Capaian</div>
            <div class="kpi-value"><?php echo e($totalCapaian); ?></div>
            <div class="kpi-sub">Data tercatat</div>
            <i class="fas fa-clipboard-list kpi-icon"></i>
        </div>
        <div class="kpi-card kpi-blue">
            <div class="kpi-label">Santri Aktif</div>
            <div class="kpi-value"><?php echo e($totalSantriAktif); ?></div>
            <div class="kpi-sub">Sedang belajar</div>
            <i class="fas fa-users kpi-icon"></i>
        </div>
        <div class="kpi-card kpi-purple">
            <div class="kpi-label">Rata-rata Progress</div>
            <div class="kpi-value"><?php echo e(number_format($rataRataProgress, 1)); ?>%</div>
            <div class="kpi-sub">Keseluruhan</div>
            <i class="fas fa-chart-line kpi-icon"></i>
        </div>
        <div class="kpi-card kpi-amber">
            <div class="kpi-label">Materi Selesai</div>
            <div class="kpi-value"><?php echo e($capaianSelesai); ?></div>
            <div class="kpi-sub">100% khatam</div>
            <i class="fas fa-trophy kpi-icon"></i>
        </div>
        <div class="kpi-card kpi-rose">
            <div class="kpi-label">Santri Khatam</div>
            <div class="kpi-value"><?php echo e($santrisKhatam->count()); ?></div>
            <div class="kpi-sub">Semua materi selesai</div>
            <i class="fas fa-graduation-cap kpi-icon"></i>
        </div>
    </div>

    
    <div class="kpi-grid">
        <?php $__currentLoopData = $statistikKategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat => $stats): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="kpi-card <?php echo e($kat == 'Al-Qur\'an' ? 'kpi-teal' : ($kat == 'Hadist' ? 'kpi-blue' : 'kpi-amber')); ?>">
            <div class="kpi-label"><?php echo e($kat); ?></div>
            <div class="kpi-value" style="font-size:1.4rem;"><?php echo e(number_format($stats['avg'], 1)); ?>%</div>
            <div class="kpi-sub"><?php echo e($stats['count']); ?> capaian &bull; <?php echo e($stats['selesai']); ?> selesai</div>
            <i class="fas fa-<?php echo e($kat == 'Al-Qur\'an' ? 'book-quran' : ($kat == 'Hadist' ? 'scroll' : 'book')); ?> kpi-icon"></i>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="chart-grid">
        <div class="chart-box">
            <h5><i class="fas fa-chart-pie"></i> Progress per Kategori</h5>
            <canvas id="chartKategori" style="max-height:280px;"></canvas>
        </div>
        <div class="chart-box">
            <h5><i class="fas fa-chart-bar"></i> Distribusi Progress</h5>
            <canvas id="chartDistribusi" style="max-height:280px;"></canvas>
        </div>
    </div>
</div>


<div class="tab-content" id="tab-ranking">
    <div class="section-card" style="margin-top:20px;">
        <h4><i class="fas fa-trophy"></i> Rekap Per Kelas dengan Status Khatam</h4>

        <div class="ranking-tabs">
            <?php $__currentLoopData = $kelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button class="ranking-tab <?php echo e($idx === 0 ? 'active' : ''); ?>" data-kelas="<?php echo e($k); ?>">
                    <?php echo e($k); ?> <span style="opacity:0.7;font-size:0.75rem;">(<?php echo e($rekapKelas[$k]['total_aktif']); ?>)</span>
                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php $__currentLoopData = $kelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="ranking-content <?php echo e($idx === 0 ? 'active' : ''); ?>" id="ranking-<?php echo e($k); ?>">
            
            <?php if(isset($rekapKelas[$k]['summary'])): ?>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:16px;">
                <div style="background:linear-gradient(135deg,#e8f5e9,#f1f8e9);border-radius:10px;padding:14px;text-align:center;border:1px solid #c8e6c9;">
                    <div style="font-size:1.4rem;font-weight:800;color:#2e7d32;"><?php echo e($rekapKelas[$k]['summary']['total_santri']); ?></div>
                    <div style="font-size:0.72rem;color:#555;margin-top:2px;">Total Santri Aktif</div>
                </div>
                <div style="background:linear-gradient(135deg,#e1f5fe,#e0f2f1);border-radius:10px;padding:14px;text-align:center;border:1px solid #b3e5fc;">
                    <div style="font-size:1.4rem;font-weight:800;color:#0277bd;"><?php echo e($rekapKelas[$k]['summary']['avg_progress']); ?>%</div>
                    <div style="font-size:0.72rem;color:#555;margin-top:2px;">Rata-rata Progress</div>
                </div>
                <div style="background:linear-gradient(135deg,#fff8e1,#fff3e0);border-radius:10px;padding:14px;text-align:center;border:1px solid #ffe082;">
                    <div style="font-size:1.4rem;font-weight:800;color:#f57f17;"><?php echo e($rekapKelas[$k]['summary']['total_selesai']); ?></div>
                    <div style="font-size:0.72rem;color:#555;margin-top:2px;">Total Materi Selesai</div>
                </div>
                <div style="background:linear-gradient(135deg,#fbe9e7,#ffebee);border-radius:10px;padding:14px;text-align:center;border:1px solid #ffccbc;">
                    <div style="font-size:1.4rem;font-weight:800;color:#d32f2f;"><?php echo e($rekapKelas[$k]['summary']['santri_tuntas']); ?></div>
                    <div style="font-size:0.72rem;color:#555;margin-top:2px;">Santri Tuntas (100%)</div>
                </div>
            </div>
            <?php endif; ?>

            <?php if(count($rekapKelas[$k]['ranking']) > 0): ?>
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Nama Santri</th>
                            <th style="width:90px;">Progress</th>
                            <th style="width:120px;">Materi</th>
                            <th style="width:180px;">Progress Bar</th>
                            <th style="width:70px;">Al-Qur'an</th>
                            <th style="width:70px;">Hadist</th>
                            <th style="width:70px;">Tambahan</th>
                            <th style="width:110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $rekapKelas[$k]['ranking']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rIdx => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <?php if($rIdx < 3): ?>
                                    <span class="rank-badge rank-<?php echo e($rIdx + 1); ?>"><?php echo e($rIdx + 1); ?></span>
                                <?php else: ?>
                                    <span class="rank-badge rank-other"><?php echo e($rIdx + 1); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo e($r['santri']->nama_lengkap); ?></strong>
                                <br><span style="font-size:0.72rem;color:#999;"><?php echo e($r['santri']->nis); ?></span>
                            </td>
                            <td>
                                <span style="font-weight:800;color:<?php echo e($r['avg_progress'] >= 80 ? '#2e7d32' : ($r['avg_progress'] >= 50 ? '#f57f17' : '#c62828')); ?>;">
                                    <?php echo e(number_format($r['avg_progress'], 1)); ?>%
                                </span>
                            </td>
                            <td><span style="font-size:0.8rem;"><?php echo e($r['selesai']); ?>/<?php echo e($r['total_materi_kelas']); ?> selesai</span></td>
                            <td>
                                <div class="prog-bar" style="height:12px;">
                                    <div class="prog-fill" style="width:<?php echo e($r['avg_progress']); ?>%;background:linear-gradient(90deg,<?php echo e($r['avg_progress'] >= 80 ? '#66bb6a,#2e7d32' : ($r['avg_progress'] >= 50 ? '#ffa726,#f57f17' : '#ef5350,#c62828')); ?>);"></div>
                                </div>
                            </td>
                            <td style="text-align:center;">
                                <span style="font-size:0.78rem;font-weight:700;color:<?php echo e($r['alquran'] >= 80 ? '#2e7d32' : ($r['alquran'] >= 50 ? '#f57f17' : '#c62828')); ?>;">
                                    <?php echo e(number_format($r['alquran'], 0)); ?>%
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <span style="font-size:0.78rem;font-weight:700;color:<?php echo e($r['hadist'] >= 80 ? '#2e7d32' : ($r['hadist'] >= 50 ? '#f57f17' : '#c62828')); ?>;">
                                    <?php echo e(number_format($r['hadist'], 0)); ?>%
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <span style="font-size:0.78rem;font-weight:700;color:<?php echo e($r['tambahan'] >= 80 ? '#2e7d32' : ($r['tambahan'] >= 50 ? '#f57f17' : '#c62828')); ?>;">
                                    <?php echo e(number_format($r['tambahan'], 0)); ?>%
                                </span>
                            </td>
                            <td>
                                <?php if($r['is_full_khatam']): ?>
                                    <form method="POST" action="<?php echo e(route('admin.capaian.tandai-khatam', $r['santri']->id_santri)); ?>" style="display:inline;">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn-khatam" onclick="return confirm('Tandai <?php echo e($r['santri']->nama_lengkap); ?> sebagai Khatam?')">
                                            <i class="fas fa-check"></i> Tandai Lulus
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="<?php echo e(route('admin.capaian.riwayat-santri', $r['santri']->id_santri)); ?>" style="font-size:0.78rem;color:var(--primary-color);text-decoration:none;">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>

                
                <?php if(count($rekapKelas[$k]['ranking']) >= 3): ?>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                    <div style="background:#e8f5e9;border-radius:8px;padding:12px;">
                        <span style="font-weight:700;color:#2e7d32;font-size:0.85rem;"><i class="fas fa-arrow-up"></i> Tertinggi</span>
                        <div style="margin-top:6px;font-size:0.82rem;">
                            <?php echo e($rekapKelas[$k]['ranking'][0]['santri']->nama_lengkap); ?> — <strong><?php echo e(number_format($rekapKelas[$k]['ranking'][0]['avg_progress'], 1)); ?>%</strong>
                        </div>
                    </div>
                    <div style="background:#fbe9e7;border-radius:8px;padding:12px;">
                        <span style="font-weight:700;color:#c62828;font-size:0.85rem;"><i class="fas fa-arrow-down"></i> Terendah</span>
                        <div style="margin-top:6px;font-size:0.82rem;">
                            <?php $last = end($rekapKelas[$k]['ranking']); ?>
                            <?php echo e($last['santri']->nama_lengkap); ?> — <strong><?php echo e(number_format($last['avg_progress'], 1)); ?>%</strong>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="empty-msg"><i class="fas fa-inbox"></i>Belum ada data ranking untuk kelas <?php echo e($k); ?></div>
            <?php endif; ?>

            
            <?php if($rekapKelas[$k]['khatam']->count() > 0): ?>
            <div class="khatam-section">
                <h5><i class="fas fa-star"></i> Santri Khatam (<?php echo e($rekapKelas[$k]['khatam']->count()); ?>)</h5>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    <?php $__currentLoopData = $rekapKelas[$k]['khatam']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ks): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="background:#fff;border-radius:8px;padding:8px 14px;display:flex;align-items:center;gap:8px;font-size:0.82rem;box-shadow:0 1px 4px rgba(0,0,0,0.08);">
                            <i class="fas fa-award" style="color:#FFD700;"></i>
                            <span><strong><?php echo e($ks->nama_lengkap); ?></strong> (<?php echo e($ks->nis); ?>)</span>
                            <form method="POST" action="<?php echo e(route('admin.capaian.batal-khatam', $ks->id_santri)); ?>" style="display:inline;margin-left:6px;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn-batal-khatam" onclick="return confirm('Batalkan status Khatam?')" style="font-size:0.68rem;padding:3px 8px;">
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


<div class="tab-content" id="tab-semester">
    <div class="chart-grid" style="margin-top:20px;">
        
        <div class="chart-box" style="grid-column:1/3;">
            <h5><i class="fas fa-chart-line"></i> Comparison Chart — Progress Rata-rata Per Semester</h5>
            <p style="font-size:0.78rem;color:#999;margin:0 0 12px 0;">Trend progress rata-rata setiap kelas antar semester. Evaluasi apakah semester ini lebih baik dari sebelumnya.</p>
            <canvas id="chartSemesterComparison" style="max-height:320px;"></canvas>
        </div>
    </div>

    
    <div class="section-card">
        <h4><i class="fas fa-chart-bar"></i> Semester-over-Semester Growth <span class="badge-count"><?php echo e(count($sosGrowth)); ?> santri</span></h4>
        <p style="font-size:0.78rem;color:#999;margin:-10px 0 14px 0;">Perbandingan pertumbuhan progress tiap santri antar semester. Identifikasi yang stagnan atau menurun.</p>

        <?php if(count($sosGrowth) > 0): ?>
        <div style="overflow-x:auto;">
            <table class="mini-table">
                <thead>
                    <tr>
                        <th style="min-width:140px;">Santri</th>
                        <th>Kelas</th>
                        <?php $__currentLoopData = $allSemestersOrdered; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th style="text-align:center;font-size:0.7rem;min-width:100px;"><?php echo e($sem->nama_semester); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $sosGrowth; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><strong><?php echo e($sg['nama']); ?></strong></td>
                        <td><span class="badge badge-secondary" style="font-size:0.72rem;"><?php echo e($sg['kelas']); ?></span></td>
                        <?php $__currentLoopData = $sg['progress']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $prog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td style="text-align:center;">
                            <div style="font-weight:700;font-size:0.82rem;"><?php echo e($prog); ?>%</div>
                            <?php if($i > 0): ?>
                                <?php $g = $sg['growth'][$i]; ?>
                                <div class="<?php echo e($g > 0 ? 'growth-pos' : ($g < 0 ? 'growth-neg' : 'growth-zero')); ?>" style="font-size:0.7rem;">
                                    <?php echo e($g > 0 ? '+' : ''); ?><?php echo e($g); ?>%
                                </div>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="empty-msg"><i class="fas fa-chart-bar"></i>Belum ada data pertumbuhan</div>
        <?php endif; ?>
    </div>

    
    <div class="chart-box" style="margin-top:0;">
        <h5><i class="fas fa-signal"></i> Growth Chart — Top 10 Santri</h5>
        <canvas id="chartSosGrowth" style="max-height:300px;"></canvas>
    </div>
</div>


<div class="tab-content" id="tab-materi">
    
    <div class="section-card" style="margin-top:20px;">
        <h4><i class="fas fa-table"></i> Materi Completion Rate Per Semester</h4>
        <p style="font-size:0.78rem;color:#999;margin:-10px 0 14px 0;">Persentase santri yang menyelesaikan tiap materi per semester. Prediksi kapan semua santri selesai.</p>

        <?php if(count($materiCompletionRate) > 0): ?>
        <div style="overflow-x:auto;">
            <table class="mini-table">
                <thead>
                    <tr>
                        <th style="min-width:160px;">Materi</th>
                        <th>Kategori</th>
                        <th>Kelas</th>
                        <?php $__currentLoopData = $allSemestersOrdered; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th style="text-align:center;font-size:0.7rem;min-width:90px;"><?php echo e($sem->nama_semester); ?></th>
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
                                <?php echo e($rate !== null ? $rate . '%' : '-'); ?>

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

    
    <div class="section-card">
        <h4><i class="fas fa-exclamation-triangle" style="color:#ff7043;"></i> Bottleneck Analysis</h4>
        <p style="font-size:0.78rem;color:#999;margin:-10px 0 14px 0;">Materi yang menjadi "bottleneck" — banyak santri stuck di bawah 50%.</p>

        <?php if(count($bottleneckMateri) > 0): ?>
            <?php $__currentLoopData = array_slice($bottleneckMateri, 0, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($bn['stuck_percentage'] > 0): ?>
                <div class="bottleneck-alert">
                    <div class="bn-icon"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="bn-text">
                        <strong><?php echo e(number_format($bn['stuck_percentage'], 0)); ?>%</strong> santri stuck di materi
                        <strong><?php echo e($bn['materi']->nama_kitab); ?></strong> (<?php echo e($bn['materi']->kategori); ?>)
                        — <?php echo e($bn['stuck_santri']); ?> dari <?php echo e($bn['total_santri']); ?> santri, rata-rata progress <strong><?php echo e(number_format($bn['avg_progress'], 1)); ?>%</strong>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <table class="mini-table" style="margin-top:14px;">
                <thead>
                    <tr>
                        <th>Materi</th>
                        <th>Kategori</th>
                        <th style="text-align:center;">Total Santri</th>
                        <th style="text-align:center;">Stuck (&lt;50%)</th>
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
                            <span style="background:<?php echo e($bn['stuck_percentage'] >= 50 ? '#fbe9e7' : '#fff8e1'); ?>;color:<?php echo e($bn['stuck_percentage'] >= 50 ? '#c62828' : '#f57f17'); ?>;padding:2px 10px;border-radius:12px;font-weight:700;font-size:0.78rem;">
                                <?php echo e(number_format($bn['stuck_percentage'], 0)); ?>%
                            </span>
                        </td>
                        <td>
                            <div class="prog-bar" style="width:120px;">
                                <div class="prog-fill" style="width:<?php echo e($bn['avg_progress']); ?>%;background:<?php echo e($bn['avg_progress'] >= 50 ? '#66bb6a' : ($bn['avg_progress'] >= 25 ? '#ffa726' : '#ef5350')); ?>;"></div>
                            </div>
                            <span style="font-size:0.72rem;color:#888;"><?php echo e(number_format($bn['avg_progress'], 1)); ?>%</span>
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
    
    <div class="section-card" style="margin-top:20px;">
        <h4><i class="fas fa-history"></i> Historical Progress Tracker</h4>
        <p style="font-size:0.78rem;color:#999;margin:-10px 0 14px 0;">Timeline progress tiap santri per semester dalam bentuk milestone.</p>

        <?php if(count($projectedGraduation) > 0): ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:16px;">
            <?php $__currentLoopData = array_slice($projectedGraduation, 0, 12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="background:#f8faf9;border-radius:10px;padding:14px;border:1px solid #e8f0ec;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                    <div>
                        <strong style="font-size:0.88rem;"><?php echo e($pg['santri']->nama_lengkap); ?></strong>
                        <div style="font-size:0.72rem;color:#999;"><?php echo e($pg['santri']->kelas); ?> &bull; <?php echo e($pg['santri']->nis); ?></div>
                    </div>
                    <div style="font-weight:800;font-size:1.1rem;color:<?php echo e($pg['current_progress'] >= 80 ? '#2e7d32' : ($pg['current_progress'] >= 50 ? '#f57f17' : '#c62828')); ?>;">
                        <?php echo e(number_format($pg['current_progress'], 0)); ?>%
                    </div>
                </div>
                <div class="timeline">
                    <?php $__currentLoopData = $pg['history']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="timeline-item">
                        <div class="tl-sem"><?php echo e($h['sem']); ?></div>
                        <div class="tl-progress">
                            Progress: <strong><?php echo e(number_format($h['avg'], 1)); ?>%</strong>
                            <div class="prog-bar" style="margin-top:4px;">
                                <div class="prog-fill" style="width:<?php echo e($h['avg']); ?>%;background:linear-gradient(90deg,var(--primary-color),#2e7d32);"></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
            <div class="empty-msg"><i class="fas fa-history"></i>Belum ada data historis</div>
        <?php endif; ?>
    </div>

    
    <div class="section-card">
        <h4><i class="fas fa-calendar-alt"></i> Projected Graduation Timeline</h4>
        <p style="font-size:0.78rem;color:#999;margin:-10px 0 14px 0;">Prediksi kapan santri akan lulus (100% semua materi) berdasarkan pace semester sebelumnya.</p>

        <?php if(count($projectedGraduation) > 0): ?>
        <div class="gantt-wrap">
            <?php $__currentLoopData = $projectedGraduation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="gantt-row">
                <div class="gantt-label" title="<?php echo e($pg['santri']->nama_lengkap); ?>"><?php echo e(\Illuminate\Support\Str::limit($pg['santri']->nama_lengkap, 20)); ?></div>
                <div class="gantt-bar-wrap">
                    <?php
                        $prog = min($pg['current_progress'], 100);
                        $gradColor = $prog >= 80 ? '#66bb6a,#2e7d32' : ($prog >= 50 ? '#ffa726,#f57f17' : '#ef5350,#c62828');
                    ?>
                    <div class="gantt-bar" style="width:<?php echo e($prog); ?>%;background:linear-gradient(90deg,<?php echo e($gradColor); ?>);">
                        <?php echo e(number_format($prog, 0)); ?>%
                    </div>
                    <?php if($pg['semesters_to_grad'] !== null && $pg['semesters_to_grad'] > 0): ?>
                        <div class="gantt-marker" style="left:calc(100% - 2px);" data-label="Prediksi +<?php echo e($pg['semesters_to_grad']); ?> sem"></div>
                    <?php endif; ?>
                </div>
                <div class="gantt-info">
                    <?php if($pg['current_progress'] >= 100): ?>
                        <span style="color:#2e7d32;font-weight:700;"><i class="fas fa-check-circle"></i> Khatam</span>
                    <?php elseif($pg['semesters_to_grad'] !== null): ?>
                        <span style="color:#555;">+<?php echo e($pg['semesters_to_grad']); ?> semester</span>
                        <br><span style="font-size:0.65rem;color:#999;"><?php echo e($pg['growth_rate'] > 0 ? '+' : ''); ?><?php echo e($pg['growth_rate']); ?>%/sem</span>
                    <?php else: ?>
                        <span style="color:#c62828;font-size:0.72rem;"><i class="fas fa-exclamation-triangle"></i> Stagnan</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div style="margin-top:14px;padding:10px;background:#f5f8f6;border-radius:8px;font-size:0.78rem;color:#666;">
            <i class="fas fa-info-circle" style="color:var(--primary-color);"></i>
            <strong>Catatan:</strong> Prediksi berdasarkan rata-rata pertumbuhan progress per semester. Santri "Stagnan" = tidak ada pertumbuhan atau menurun.
        </div>
        <?php else: ?>
            <div class="empty-msg"><i class="fas fa-calendar-alt"></i>Belum ada data untuk prediksi</div>
        <?php endif; ?>
    </div>
</div>


<div class="tab-content" id="tab-laporan">
    
    <?php if($semesterSummary): ?>
    <div class="section-card" style="margin-top:20px;">
        <h4><i class="fas fa-clipboard-check"></i> Semester Summary Report — <?php echo e($semesterSummary['semester']->nama_semester); ?></h4>

        
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(150px, 1fr));gap:12px;margin-bottom:20px;">
            <div class="summary-card">
                <div class="sc-val" style="color:#6FBA9D;"><?php echo e($semesterSummary['total_santri']); ?></div>
                <div class="sc-label">Total Santri</div>
            </div>
            <div class="summary-card">
                <div class="sc-val" style="color:#81C6E8;"><?php echo e(number_format($semesterSummary['avg_progress'], 1)); ?>%</div>
                <div class="sc-label">Rata-rata Progress</div>
                <div class="sc-change <?php echo e($semesterSummary['kenaikan'] >= 0 ? 'growth-pos' : 'growth-neg'); ?>">
                    <?php echo e($semesterSummary['kenaikan'] >= 0 ? '+' : ''); ?><?php echo e(number_format($semesterSummary['kenaikan'], 1)); ?>% dari <?php echo e($semesterSummary['prev_semester'] ? $semesterSummary['prev_semester']->nama_semester : 'N/A'); ?>

                </div>
            </div>
            <div class="summary-card">
                <div class="sc-val" style="color:#2e7d32;"><?php echo e($semesterSummary['santri_khatam']); ?></div>
                <div class="sc-label">Naik Kelas / Khatam</div>
            </div>
            <div class="summary-card">
                <div class="sc-val" style="color:#c62828;"><?php echo e($semesterSummary['santri_remedial_count']); ?></div>
                <div class="sc-label">Perlu Remedial (&lt;30%)</div>
            </div>
        </div>

        
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            
            <div>
                <h5 style="color:#2e7d32;font-size:0.88rem;margin-bottom:10px;"><i class="fas fa-star"></i> Materi Paling Banyak Dikhatamkan</h5>
                <?php if($semesterSummary['materi_khatam']->count() > 0): ?>
                    <?php $__currentLoopData = $semesterSummary['materi_khatam']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f0f0f0;font-size:0.82rem;">
                        <span><?php echo e($mk['materi']->nama_kitab ?? '-'); ?></span>
                        <span style="font-weight:700;color:#2e7d32;"><?php echo e($mk['count']); ?> santri</span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <span style="color:#999;font-size:0.82rem;">Belum ada materi yang dikhatamkan</span>
                <?php endif; ?>
            </div>

            
            <div>
                <h5 style="color:#c62828;font-size:0.88rem;margin-bottom:10px;"><i class="fas fa-exclamation-triangle"></i> Materi Paling Sedikit Progress</h5>
                <?php if($semesterSummary['materi_min']->count() > 0): ?>
                    <?php $__currentLoopData = $semesterSummary['materi_min']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f0f0f0;font-size:0.82rem;">
                        <span><?php echo e($mm['materi']->nama_kitab ?? '-'); ?></span>
                        <span style="font-weight:700;color:#c62828;"><?php echo e($mm['avg']); ?>%</span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <span style="color:#999;font-size:0.82rem;">Tidak ada data</span>
                <?php endif; ?>
            </div>
        </div>

        
        <?php if($semesterSummary['santri_remedial_count'] > 0): ?>
        <div style="margin-top:16px;background:#fbe9e7;border-radius:8px;padding:12px;">
            <h5 style="color:#c62828;margin:0 0 8px 0;font-size:0.85rem;"><i class="fas fa-user-times"></i> Santri Perlu Remedial</h5>
            <div style="display:flex;flex-wrap:wrap;gap:6px;font-size:0.8rem;">
                <?php $__currentLoopData = $semesterSummary['santri_remedial']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span style="background:#fff;padding:4px 10px;border-radius:6px;border:1px solid #ffcdd2;"><?php echo e($sr->nama_lengkap); ?> (<?php echo e($sr->kelas); ?>)</span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="section-card" style="margin-top:20px;">
        <div class="empty-msg"><i class="fas fa-clipboard-check"></i>Pilih semester pada filter untuk melihat laporan</div>
    </div>
    <?php endif; ?>

    
    <div class="section-card">
        <h4><i class="fas fa-file-pdf"></i> Export Rapor Per Santri</h4>
        <p style="font-size:0.78rem;color:#999;margin:-10px 0 14px 0;">Generate rapor per santri per semester dengan progress, perbandingan, dan catatan. Buka halaman rapor lalu cetak (Ctrl+P) sebagai PDF.</p>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;align-items:end;">
            <div>
                <label style="font-size:0.82rem;font-weight:600;color:#555;">Pilih Santri:</label>
                <select id="raporSantri" style="width:100%;padding:8px 12px;border:2px solid #e0e0e0;border-radius:8px;font-size:0.85rem;margin-top:4px;">
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
                <label style="font-size:0.82rem;font-weight:600;color:#555;">Pilih Semester:</label>
                <select id="raporSemester" style="width:100%;padding:8px 12px;border:2px solid #e0e0e0;border-radius:8px;font-size:0.85rem;margin-top:4px;">
                    <option value="">-- Pilih Semester --</option>
                    <?php $__currentLoopData = $semesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($sem->id_semester); ?>"><?php echo e($sem->nama_semester); ?><?php if($sem->is_active): ?> ★ <?php endif; ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <div style="margin-top:14px;">
            <button class="rapor-btn" onclick="exportRapor()">
                <i class="fas fa-download"></i> Generate Rapor
            </button>
        </div>
    </div>

    
    <div style="margin-top:20px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap;" class="no-print">
        <a href="<?php echo e(route('admin.capaian.create')); ?>" class="btn btn-success" style="padding:10px 20px;">
            <i class="fas fa-plus"></i> Input Capaian Baru
        </a>
        <a href="<?php echo e(route('admin.capaian.index')); ?>" class="btn btn-primary" style="padding:10px 20px;">
            <i class="fas fa-list"></i> Daftar Capaian
        </a>
        <a href="<?php echo e(route('admin.materi.index')); ?>" class="btn btn-info" style="padding:10px 20px;">
            <i class="fas fa-book"></i> Master Materi
        </a>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
// === TAB SWITCHING ===
document.querySelectorAll('.dash-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.dash-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        document.getElementById(this.dataset.tab).classList.add('active');
    });
});

// === RANKING KELAS TABS ===
document.querySelectorAll('.ranking-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.ranking-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.ranking-content').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('ranking-' + this.dataset.kelas).classList.add('active');
    });
});

// === COLORS ===
const clr = {
    teal: 'rgba(111,186,157,', blue: 'rgba(129,198,232,', amber: 'rgba(255,213,107,',
    rose: 'rgba(255,139,148,', orange: 'rgba(255,171,145,', purple: 'rgba(179,157,219,',
    green: 'rgba(102,187,106,',
};

// === CHART 1: DOUGHNUT — Kategori ===
new Chart(document.getElementById('chartKategori'), {
    type: 'doughnut',
    data: {
        labels: ['Al-Qur\'an', 'Hadist', 'Materi Tambahan'],
        datasets: [{
            data: [<?php echo e($statistikKategori['Al-Qur\'an']['avg']); ?>, <?php echo e($statistikKategori['Hadist']['avg']); ?>, <?php echo e($statistikKategori['Materi Tambahan']['avg']); ?>],
            backgroundColor: [clr.teal+'0.8)', clr.blue+'0.8)', clr.amber+'0.8)'],
            borderColor: [clr.teal+'1)', clr.blue+'1)', clr.amber+'1)'],
            borderWidth: 2, hoverOffset: 8
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: true, cutout: '55%',
        plugins: { legend: { position: 'bottom', labels: { padding: 15, font: { size: 12 } } },
            tooltip: { callbacks: { label: ctx => ctx.label + ': ' + ctx.parsed.toFixed(1) + '%' } }
        }
    }
});

// === CHART 2: BAR — Distribusi ===
new Chart(document.getElementById('chartDistribusi'), {
    type: 'bar',
    data: {
        labels: ['0-25%', '26-50%', '51-75%', '76-99%', '100%'],
        datasets: [{
            label: 'Jumlah',
            data: [<?php echo e($distribusiProgress['0-25%']); ?>, <?php echo e($distribusiProgress['26-50%']); ?>, <?php echo e($distribusiProgress['51-75%']); ?>, <?php echo e($distribusiProgress['76-99%']); ?>, <?php echo e($distribusiProgress['100%']); ?>],
            backgroundColor: [clr.rose+'0.8)', clr.orange+'0.8)', clr.amber+'0.8)', clr.blue+'0.8)', clr.teal+'0.8)'],
            borderColor: [clr.rose+'1)', clr.orange+'1)', clr.amber+'1)', clr.blue+'1)', clr.teal+'1)'],
            borderWidth: 2, borderRadius: 6
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
        plugins: { legend: { display: false } }
    }
});

// === CHART 3: LINE — Semester Comparison ===
const semLabels = <?php echo json_encode($semesterLabels, 15, 512) ?>;
const semData = <?php echo json_encode($semesterComparison, 15, 512) ?>;
// Dynamic line colors - cycle through palette for any kelas names
const lineColorPalette = [
    {bg: clr.teal, br: clr.teal},
    {bg: clr.blue, br: clr.blue},
    {bg: clr.amber, br: clr.amber},
    {bg: clr.rose, br: clr.rose},
    {bg: clr.purple, br: clr.purple},
    {bg: clr.orange, br: clr.orange},
    {bg: clr.green, br: clr.green},
];
const lineDatasets = [];
let colorIdx = 0;
for (const [k, vals] of Object.entries(semData)) {
    const c = lineColorPalette[colorIdx % lineColorPalette.length];
    colorIdx++;
    lineDatasets.push({
        label: k, data: vals,
        borderColor: c.br + '1)', backgroundColor: c.bg + '0.15)',
        tension: 0.4, fill: true, pointRadius: 5, pointHoverRadius: 7,
        pointBackgroundColor: c.br + '1)', borderWidth: 3
    });
}
if (document.getElementById('chartSemesterComparison')) {
    new Chart(document.getElementById('chartSemesterComparison'), {
        type: 'line',
        data: { labels: semLabels, datasets: lineDatasets },
        options: {
            responsive: true, maintainAspectRatio: true,
            scales: { y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } } },
            plugins: {
                legend: { position: 'bottom', labels: { padding: 15 } },
                tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + ctx.parsed.y.toFixed(1) + '%' } }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });
}

// === CHART 4: BAR — SoS Growth Top 10 ===
const sosData = <?php echo json_encode(array_slice($sosGrowth, 0, 10)) ?>;
if (document.getElementById('chartSosGrowth') && sosData.length > 0) {
    const sosLabels = sosData.map(s => s.nama.length > 15 ? s.nama.substring(0,15)+'...' : s.nama);
    const sosDatasets = [];
    semLabels.forEach((sem, i) => {
        const colors = [clr.teal, clr.blue, clr.amber, clr.rose, clr.purple, clr.orange, clr.green];
        const c = colors[i % colors.length];
        sosDatasets.push({
            label: sem,
            data: sosData.map(s => s.progress[i] || 0),
            backgroundColor: c + '0.7)',
            borderColor: c + '1)',
            borderWidth: 1, borderRadius: 3
        });
    });
    new Chart(document.getElementById('chartSosGrowth'), {
        type: 'bar',
        data: { labels: sosLabels, datasets: sosDatasets },
        options: {
            responsive: true, maintainAspectRatio: true,
            scales: { y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } }, x: { ticks: { font: { size: 10 } } } },
            plugins: {
                legend: { position: 'bottom', labels: { padding: 10, font: { size: 11 } } },
                tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + ctx.parsed.y.toFixed(1) + '%' } }
            }
        }
    });
}

// === EXPORT RAPOR ===
function exportRapor() {
    const santri = document.getElementById('raporSantri').value;
    const semester = document.getElementById('raporSemester').value;
    if (!santri || !semester) {
        alert('Pilih santri dan semester terlebih dahulu.');
        return;
    }
    const url = '<?php echo e(url("admin/capaian/export-rapor")); ?>/' + santri + '/' + semester;
    window.open(url, '_blank');
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/capaian/dashboard.blade.php ENDPATH**/ ?>