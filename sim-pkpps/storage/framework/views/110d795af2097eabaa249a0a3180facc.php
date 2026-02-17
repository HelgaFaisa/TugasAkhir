

<?php $__env->startSection('content'); ?>
<style>
/* ===== ENHANCED DASHBOARD STYLES ===== */
.dashboard-kegiatan {
    --primary: #6FBAA5;
    --success: #28a745;
    --warning: #ffc107;
    --danger: #dc3545;
    --info: #17a2b8;
}

/* KPI Cards */
.kpi-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.kpi-card {
    background: linear-gradient(135deg, var(--primary), #5AA88D);
    color: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
    transition: transform 0.3s;
}

.kpi-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.kpi-card.success { background: linear-gradient(135deg, #28a745, #218838); }
.kpi-card.warning { background: linear-gradient(135deg, #ffc107, #e0a800); }
.kpi-card.info { background: linear-gradient(135deg, #17a2b8, #138496); }

.kpi-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    pointer-events: none;
}

.kpi-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.kpi-card-icon {
    font-size: 2.5rem;
    opacity: 0.3;
}

.kpi-card-value {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
}

.kpi-card-label {
    font-size: 0.9rem;
    opacity: 0.9;
    margin-top: 5px;
}

.kpi-comparison {
    font-size: 0.85rem;
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.kpi-comparison.up { color: #d4edda; }
.kpi-comparison.down { color: #f8d7da; }

/* Filter Tabs Horizontal */
.filter-tabs-horizontal {
    background: white;
    padding: 15px 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    margin-bottom: 25px;
}

.tabs-container {
    display: flex;
    gap: 10px;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.tab-item {
    padding: 10px 20px;
    border: none;
    background: none;
    cursor: pointer;
    font-weight: 500;
    color: #6c757d;
    border-bottom: 3px solid transparent;
    transition: all 0.3s;
    white-space: nowrap;
}

.tab-item:hover {
    color: var(--primary);
}

.tab-item.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
}

.tab-item .badge {
    margin-left: 5px;
    font-size: 0.75rem;
}

/* Date Filter Section */
.date-filter-section {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}

.date-filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.date-filter-group label {
    font-weight: 500;
    white-space: nowrap;
    margin: 0;
}

/* Insights Panel */
.insights-panel {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    padding: 20px;
    margin-bottom: 25px;
}

.insights-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
    color: var(--primary);
}

.insights-header i {
    font-size: 1.5rem;
}

.insight-item {
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}

.insight-item.warning {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
}

.insight-item.success {
    background: #d4edda;
    border-left: 4px solid #28a745;
}

.insight-item.info {
    background: #d1ecf1;
    border-left: 4px solid #17a2b8;
}

.insight-item.danger {
    background: #f8d7da;
    border-left: 4px solid #dc3545;
}

.insight-content {
    flex: 1;
}

.insight-message {
    font-weight: 600;
    margin-bottom: 3px;
}

.insight-detail {
    font-size: 0.85rem;
    opacity: 0.8;
}

/* Main Content Layout */
.main-content-layout {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 25px;
}

/* Kegiatan Cards */
.kegiatan-timeline {
    display: grid;
    gap: 20px;
}

.kegiatan-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    padding: 20px;
    transition: all 0.3s ease;
    border-left: 4px solid var(--primary);
}

.kegiatan-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.kegiatan-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
    gap: 15px;
    flex-wrap: wrap;
}

.kegiatan-info {
    flex: 1;
}

.kegiatan-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.kegiatan-meta {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    font-size: 0.9rem;
    color: #6c757d;
}

.kegiatan-meta i {
    margin-right: 5px;
    color: var(--primary);
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    white-space: nowrap;
}

.status-belum {
    background: #e9ecef;
    color: #6c757d;
}

.status-berlangsung {
    background: #d4edda;
    color: #155724;
    animation: pulse 2s infinite;
}

.status-selesai {
    background: #d1ecf1;
    color: #0c5460;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.progress-section {
    margin: 15px 0;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.progress-label {
    font-weight: 500;
    color: #2c3e50;
}

.progress-value {
    font-weight: 700;
}

.progress-bar-container {
    height: 24px;
    background: #e9ecef;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
}

.progress-bar {
    height: 100%;
    border-radius: 12px;
    transition: width 0.6s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
    color: white;
}

.progress-success {
    background: linear-gradient(90deg, #28a745, #20c997);
}

.progress-warning {
    background: linear-gradient(90deg, #ffc107, #fd7e14);
}

.progress-orange {
    background: linear-gradient(90deg, #fd7e14, #dc3545);
}

.progress-danger {
    background: linear-gradient(90deg, #dc3545, #c82333);
}

.kegiatan-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 15px;
}

/* Heatmap Calendar */
.heatmap-calendar {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    padding: 20px;
    position: sticky;
    top: 20px;
}

.heatmap-header {
    font-weight: 600;
    margin-bottom: 15px;
    color: var(--primary);
    display: flex;
    align-items: center;
    gap: 8px;
}

.heatmap-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 3px;
    margin-bottom: 8px;
}

.heatmap-day-label {
    text-align: center;
    font-size: 0.75rem;
    font-weight: 600;
    color: #6c757d;
}

.heatmap-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 3px;
}

.heatmap-cell {
    aspect-ratio: 1;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: 600;
    color: #2c3e50;
    position: relative;
}

.heatmap-cell:hover {
    transform: scale(1.1);
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
    z-index: 10;
}

.heatmap-level-0 { background: #e5e7eb; color: #9ca3af; }
.heatmap-level-1 { background: #ef4444; color: white; }
.heatmap-level-2 { background: #fbbf24; color: white; }
.heatmap-level-3 { background: #34d399; color: white; }
.heatmap-level-4 { background: #10b981; color: white; }

.heatmap-cell.today {
    border: 2px solid var(--primary);
    box-shadow: 0 0 0 3px rgba(111, 186, 165, 0.3);
}

.heatmap-legend {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e9ecef;
}

.heatmap-legend-title {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 8px;
}

.heatmap-legend-items {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.7rem;
}

.heatmap-legend-item {
    display: flex;
    align-items: center;
    gap: 4px;
}

.heatmap-legend-box {
    width: 12px;
    height: 12px;
    border-radius: 2px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.empty-state i {
    font-size: 4rem;
    color: #cbd5e0;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.empty-state p {
    color: #6c757d;
    margin-bottom: 20px;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    animation: fadeIn 0.3s;
}

.modal.active {
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: white;
    border-radius: 12px;
    max-width: 700px;
    width: 90%;
    max-height: 85vh;
    overflow-y: auto;
    position: relative;
    animation: slideUp 0.3s;
}

.modal-header {
    padding: 20px;
    border-bottom: 2px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    background: white;
    z-index: 10;
}

.modal-header h3 {
    margin: 0;
    color: #2c3e50;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #6c757d;
    transition: color 0.3s;
}

.modal-close:hover {
    color: #2c3e50;
}

.modal-body {
    padding: 20px;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { transform: translateY(50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

/* Responsive */
@media (max-width: 1024px) {
    .main-content-layout {
        grid-template-columns: 1fr;
    }
    
    .heatmap-calendar {
        position: static;
    }
}

@media (max-width: 768px) {
    .kpi-cards {
        grid-template-columns: 1fr;
    }
    
    .tabs-container {
        flex-wrap: nowrap;
        overflow-x: auto;
    }
    
    .date-filter-section {
        flex-direction: column;
        align-items: stretch;
    }
    
    .kegiatan-card-header {
        flex-direction: column;
    }
}
</style>

<div class="dashboard-kegiatan">
    <div class="page-header">
        <h2><i class="fas fa-tachometer-alt"></i> Dashboard Kegiatan Santri</h2>
    </div>
    
    <p style="color: #6c757d; margin-top: 5px; margin-bottom: 20px;">
        <?php echo e($selectedDate->locale('id')->isoFormat('dddd, D MMMM Y')); ?>

    </p>

    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <div class="kpi-cards">
        <div class="kpi-card">
            <div class="kpi-card-header">
                <div>
                    <div class="kpi-card-label">Total Kegiatan</div>
                    <div class="kpi-card-value"><?php echo e($totalKegiatanHariIni); ?></div>
                    <div class="kpi-comparison <?php echo e($comparisonTotal >= 0 ? 'up' : 'down'); ?>">
                        <i class="fas fa-<?php echo e($comparisonTotal >= 0 ? 'arrow-up' : 'arrow-down'); ?>"></i>
                        <?php echo e(abs($comparisonTotal)); ?> vs minggu lalu
                    </div>
                </div>
                <i class="fas fa-calendar-day kpi-card-icon"></i>
            </div>
        </div>
        
        <div class="kpi-card success">
            <div class="kpi-card-header">
                <div>
                    <div class="kpi-card-label">Kegiatan Selesai</div>
                    <div class="kpi-card-value"><?php echo e($kegiatanSelesai); ?></div>
                    <div class="kpi-card-label" style="margin-top: 10px;">dari <?php echo e($totalKegiatanHariIni); ?> kegiatan</div>
                </div>
                <i class="fas fa-check-circle kpi-card-icon"></i>
            </div>
        </div>
        
        <div class="kpi-card warning">
            <div class="kpi-card-header">
                <div>
                    <div class="kpi-card-label">Rata-rata Kehadiran</div>
                    <div class="kpi-card-value"><?php echo e($avgKehadiran); ?>%</div>
                    <div class="kpi-comparison <?php echo e($comparisonAvg >= 0 ? 'up' : 'down'); ?>">
                        <i class="fas fa-<?php echo e($comparisonAvg >= 0 ? 'arrow-up' : 'arrow-down'); ?>"></i>
                        <?php echo e(abs($comparisonAvg)); ?>% vs minggu lalu
                    </div>
                </div>
                <i class="fas fa-chart-line kpi-card-icon"></i>
            </div>
        </div>
        
        <div class="kpi-card info">
            <div class="kpi-card-header">
                <div>
                    <div class="kpi-card-label">Sedang Berlangsung</div>
                    <div class="kpi-card-value"><?php echo e($kegiatanBerlangsung); ?></div>
                    <?php if($kegiatanBerlangsung > 0): ?>
                        <div class="kpi-card-label" style="margin-top: 10px;">
                            <i class="fas fa-circle" style="color: #90ee90; animation: pulse 1.5s infinite;"></i> Live Now
                        </div>
                    <?php else: ?>
                        <div class="kpi-card-label" style="margin-top: 10px;">Tidak ada kegiatan</div>
                    <?php endif; ?>
                </div>
                <i class="fas fa-clock kpi-card-icon"></i>
            </div>
        </div>
    </div>

    
    <div class="filter-tabs-horizontal">
        <div class="tabs-container">
            <button class="tab-item <?php echo e(!$selectedKelasId ? 'active' : ''); ?>" 
                    onclick="filterByKelas('')">
                <i class="fas fa-layer-group"></i> Semua Kegiatan
                <span class="badge badge-primary"><?php echo e($kegiatanHariIni->count()); ?></span>
            </button>
        </div>
        
        
        <div class="date-filter-section">
            <form method="GET" action="<?php echo e(route('admin.kegiatan.index')); ?>" id="filterForm" style="display: contents;">
                <input type="hidden" name="kelas" id="kelasInput" value="<?php echo e($selectedKelasId); ?>">
                
                <div class="date-filter-group">
                    <label><i class="fas fa-filter"></i> Filter Kelas:</label>
                    <select name="kelas" class="form-control" onchange="this.form.submit()" style="max-width: 250px;">
                        <option value="">-- Semua Kelas --</option>
                        <option value="umum" <?php echo e($selectedKelasId === 'umum' ? 'selected' : ''); ?>>
                            🌐 Kegiatan Umum
                        </option>
                        
                        <?php
                            $kelompokGroups = $kelasList->groupBy('kelompok.nama_kelompok');
                        ?>
                        
                        <?php $__currentLoopData = $kelompokGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelompokNama => $kelasGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <optgroup label="📚 <?php echo e($kelompokNama); ?>">
                                <?php $__currentLoopData = $kelasGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($kelas->id); ?>" <?php echo e($selectedKelasId == $kelas->id ? 'selected' : ''); ?>>
                                        <?php echo e($kelas->nama_kelas); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </optgroup>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                
                <div class="date-filter-group">
                    <label><i class="fas fa-calendar"></i> Tanggal:</label>
                    <input type="date" name="tanggal" class="form-control" 
                           value="<?php echo e($selectedDate->format('Y-m-d')); ?>" 
                           onchange="this.form.submit()" 
                           style="max-width: 180px;">
                </div>
                
                <div class="date-filter-group">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="setToday()">
                        <i class="fas fa-calendar-day"></i> Hari Ini
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="prevDay()">
                        <i class="fas fa-chevron-left"></i> Kemarin
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="nextDay()">
                        Besok <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                
                <div style="margin-left: auto; display: flex; gap: 10px;">
                    <a href="<?php echo e(route('admin.kegiatan.jadwal')); ?>" class="btn btn-info">
                        <i class="fas fa-list"></i> Semua Jadwal
                    </a>
                    <div style="display: flex; gap: 10px; align-items: center;">
                     <a href="<?php echo e(route('admin.absensi-kegiatan.index')); ?>" class="btn btn-info">
                    <i class="fas fa-clipboard-check"></i> Absensi Kegiatan
                </a>
                    <a href="<?php echo e(route('admin.kegiatan.create')); ?>" class="btn btn-success">
                        <i class="fas fa-plus"></i> Tambah Kegiatan
                    </a>
                </div>
            </form>
        </div>
    </div>

    
    <?php if(count($insights) > 0): ?>
    <div class="insights-panel">
        <div class="insights-header">
            <i class="fas fa-lightbulb"></i>
            <h5 style="margin: 0;">Insight Hari Ini</h5>
        </div>
        <?php $__currentLoopData = $insights; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $insight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="insight-item <?php echo e($insight['type']); ?>">
            <div class="insight-content">
                <div class="insight-message">
                    <i class="fas fa-<?php echo e($insight['icon']); ?>"></i> <?php echo e($insight['message']); ?>

                </div>
                <div class="insight-detail"><?php echo e($insight['detail']); ?></div>
            </div>
            <?php if($insight['action_url']): ?>
                <a href="<?php echo e($insight['action_url']); ?>" class="btn btn-sm btn-<?php echo e($insight['type']); ?>">
                    <?php echo e($insight['action_text']); ?>

                </a>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

    
    <div class="main-content-layout">
        
        <div>
            <?php if($kegiatanHariIni->count() > 0): ?>
                <div class="kegiatan-timeline">
                    <?php $__currentLoopData = $kegiatanHariIni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kegiatan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="kegiatan-card" style="border-left-color: <?php echo e($kegiatan->kategori->warna ?? '#6FBAA5'); ?>">
                            <div class="kegiatan-card-header">
                                <div class="kegiatan-info">
                                    <h3 class="kegiatan-title">
                                        <i class="fas <?php echo e($kegiatan->kategori->icon ?? 'fa-calendar'); ?>" 
                                           style="color: <?php echo e($kegiatan->kategori->warna ?? '#6FBAA5'); ?>"></i>
                                        <?php echo e($kegiatan->nama_kegiatan); ?>

                                    </h3>
                                    <div class="kegiatan-meta">
                                        <span>
                                            <i class="fas fa-clock"></i>
                                            <?php echo e(date('H:i', strtotime($kegiatan->waktu_mulai))); ?> - 
                                            <?php echo e(date('H:i', strtotime($kegiatan->waktu_selesai))); ?>

                                        </span>
                                        <span style="background-color: <?php echo e($kegiatan->kategori->warna ?? '#6FBAA5'); ?>22; 
                                                     color: <?php echo e($kegiatan->kategori->warna ?? '#6FBAA5'); ?>; 
                                                     padding: 4px 10px; border-radius: 6px;">
                                            <i class="fas <?php echo e($kegiatan->kategori->icon ?? 'fa-tag'); ?>"></i>
                                            <?php echo e($kegiatan->kategori->nama_kategori); ?>

                                        </span>
                                        <?php if($kegiatan->materi): ?>
                                            <span>
                                                <i class="fas fa-book"></i>
                                                <?php echo e(Str::limit($kegiatan->materi, 40)); ?>

                                            </span>
                                        <?php endif; ?>
                                        <span>
                                            <i class="fas fa-layer-group"></i>
                                            <?php if($kegiatan->kelasKegiatan->isEmpty()): ?>
                                                <span style="background: #17a2b8; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">
                                                    Kegiatan Umum
                                                </span>
                                            <?php else: ?>
                                                <?php echo e($kegiatan->kelasKegiatan->pluck('nama_kelas')->implode(', ')); ?>

                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                                <span class="status-badge status-<?php echo e($kegiatan->status_kegiatan); ?>">
                                    <?php if($kegiatan->status_kegiatan == 'belum'): ?>
                                        <i class="fas fa-hourglass-start"></i> Belum Dimulai
                                    <?php elseif($kegiatan->status_kegiatan == 'berlangsung'): ?>
                                        <i class="fas fa-play-circle"></i> Berlangsung
                                    <?php else: ?>
                                        <i class="fas fa-check-circle"></i> Selesai
                                    <?php endif; ?>
                                </span>
                            </div>
                            
                            
                            <div class="progress-section">
                                <div class="progress-header">
                                    <span class="progress-label">
                                        <i class="fas fa-users"></i> Kehadiran
                                    </span>
                                    <span class="progress-value">
                                        <?php echo e($kegiatan->total_hadir); ?>/<?php echo e($kegiatan->total_absensi > 0 ? $kegiatan->total_absensi : $totalSantriAktif); ?> 
                                        (<?php echo e($kegiatan->persen_kehadiran); ?>%)
                                    </span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar 
                                        <?php if($kegiatan->persen_kehadiran >= 85): ?> progress-success
                                        <?php elseif($kegiatan->persen_kehadiran >= 70): ?> progress-warning
                                        <?php elseif($kegiatan->persen_kehadiran >= 50): ?> progress-orange
                                        <?php else: ?> progress-danger
                                        <?php endif; ?>"
                                        style="width: <?php echo e($kegiatan->persen_kehadiran); ?>%">
                                        <?php echo e($kegiatan->persen_kehadiran); ?>%
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class="kegiatan-actions">
                                <a href="<?php echo e(route('admin.absensi-kegiatan.input', $kegiatan->kegiatan_id)); ?>?tanggal=<?php echo e($selectedDate->format('Y-m-d')); ?>" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-clipboard-check"></i> Input Absensi
                                </a>
                                <button class="btn btn-sm btn-info" 
                                        onclick="showDetailModal('<?php echo e($kegiatan->kegiatan_id); ?>', '<?php echo e($selectedDate->format('Y-m-d')); ?>')">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </button>
                                <a href="<?php echo e(route('admin.riwayat-kegiatan.index')); ?>?kegiatan_id=<?php echo e($kegiatan->kegiatan_id); ?>&tanggal=<?php echo e($selectedDate->format('Y-m-d')); ?>" 
                                   class="btn btn-sm btn-secondary">
                                    <i class="fas fa-chart-bar"></i> Rekap
                                </a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h3>Tidak Ada Kegiatan Dijadwalkan</h3>
                    <p>Tidak ada kegiatan untuk <?php echo e($selectedKelasId ? 'kelas ini' : 'hari ini'); ?> pada <?php echo e($selectedDate->locale('id')->isoFormat('dddd, D MMMM Y')); ?></p>
                    <a href="<?php echo e(route('admin.kegiatan.create')); ?>" class="btn btn-success">
                        <i class="fas fa-plus"></i> Buat Kegiatan Baru
                    </a>
                </div>
            <?php endif; ?>
        </div>

        
        <div>
            <div class="heatmap-calendar">
                <div class="heatmap-header">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Kalender Kehadiran</span>
                </div>
                
                
                <div style="margin-bottom: 15px; display: flex; gap: 10px; align-items: center;">
                    <button onclick="changeHeatmapMonth(-1)" class="btn btn-sm btn-outline-secondary" style="padding: 4px 8px;">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <select id="heatmapMonth" onchange="updateHeatmap()" class="form-control form-control-sm" style="flex: 1;">
                        <?php for($m = 1; $m <= 12; $m++): ?>
                            <option value="<?php echo e($m); ?>" <?php echo e($m == now()->month ? 'selected' : ''); ?>>
                                <?php echo e(\Carbon\Carbon::create(null, $m, 1)->locale('id')->isoFormat('MMMM')); ?>

                            </option>
                        <?php endfor; ?>
                    </select>
                    <select id="heatmapYear" onchange="updateHeatmap()" class="form-control form-control-sm" style="width: 80px;">
                        <?php for($y = now()->year - 2; $y <= now()->year + 1; $y++): ?>
                            <option value="<?php echo e($y); ?>" <?php echo e($y == now()->year ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                        <?php endfor; ?>
                    </select>
                    <button onclick="changeHeatmapMonth(1)" class="btn btn-sm btn-outline-secondary" style="padding: 4px 8px;">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                
                <div style="text-align: center; font-weight: 600; color: var(--primary); margin-bottom: 10px;" id="heatmapMonthName">
                    <?php echo e(now()->locale('id')->isoFormat('MMMM Y')); ?>

                </div>
                
                <div class="heatmap-days">
                    <div class="heatmap-day-label">Sen</div>
                    <div class="heatmap-day-label">Sel</div>
                    <div class="heatmap-day-label">Rab</div>
                    <div class="heatmap-day-label">Kam</div>
                    <div class="heatmap-day-label">Jum</div>
                    <div class="heatmap-day-label">Sab</div>
                    <div class="heatmap-day-label">Ahd</div>
                </div>
                
                <div class="heatmap-grid" id="heatmapGrid">
                    <?php $__currentLoopData = $heatmapData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="heatmap-cell heatmap-level-<?php echo e($day['level']); ?> <?php echo e($day['is_today'] ? 'today' : ''); ?>"
                             data-date="<?php echo e($day['date']); ?>"
                             data-percentage="<?php echo e($day['percentage']); ?>"
                             onclick="goToDate('<?php echo e($day['date']); ?>')"
                             title="<?php echo e(\Carbon\Carbon::parse($day['date'])->locale('id')->isoFormat('dddd, D MMM Y')); ?>: <?php echo e($day['percentage']); ?>%">
                            <?php echo e(\Carbon\Carbon::parse($day['date'])->format('j')); ?>

                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                <div class="heatmap-legend">
                    <div class="heatmap-legend-title">Legend:</div>
                    <div class="heatmap-legend-items">
                        <div class="heatmap-legend-item">
                            <div class="heatmap-legend-box heatmap-level-4"></div>
                            <span>>90%</span>
                        </div>
                        <div class="heatmap-legend-item">
                            <div class="heatmap-legend-box heatmap-level-3"></div>
                            <span>80-90%</span>
                        </div>
                        <div class="heatmap-legend-item">
                            <div class="heatmap-legend-box heatmap-level-2"></div>
                            <span>70-80%</span>
                        </div>
                        <div class="heatmap-legend-item">
                            <div class="heatmap-legend-box heatmap-level-1"></div>
                            <span><70%</span>
                        </div>
                        <div class="heatmap-legend-item">
                            <div class="heatmap-legend-box heatmap-level-0"></div>
                            <span>No data</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="detailModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-info-circle"></i> Detail Absensi Kegiatan</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="modalBody">
            <div style="text-align: center; padding: 40px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary);"></i>
                <p style="margin-top: 10px;">Memuat data...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Filter by Kelas
function filterByKelas(kelasId) {
    document.getElementById('kelasInput').value = kelasId;
    document.getElementById('filterForm').submit();
}

// Date Navigation
function setToday() {
    document.querySelector('input[name="tanggal"]').value = '<?php echo e(now()->format('Y-m-d')); ?>';
    document.getElementById('filterForm').submit();
}

function prevDay() {
    let currentDate = new Date('<?php echo e($selectedDate->format('Y-m-d')); ?>');
    currentDate.setDate(currentDate.getDate() - 1);
    document.querySelector('input[name="tanggal"]').value = currentDate.toISOString().split('T')[0];
    document.getElementById('filterForm').submit();
}

function nextDay() {
    let currentDate = new Date('<?php echo e($selectedDate->format('Y-m-d')); ?>');
    currentDate.setDate(currentDate.getDate() + 1);
    document.querySelector('input[name="tanggal"]').value = currentDate.toISOString().split('T')[0];
    document.getElementById('filterForm').submit();
}

function goToDate(date) {
    document.querySelector('input[name="tanggal"]').value = date;
    document.getElementById('filterForm').submit();
}

// Show Detail Modal
function showDetailModal(kegiatanId, tanggal) {
    const modal = document.getElementById('detailModal');
    const modalBody = document.getElementById('modalBody');
    
    modal.classList.add('active');
    
    // Fetch detail via AJAX - using correct route
    const url = `<?php echo e(route('admin.kegiatan.detail-modal', ':id')); ?>?tanggal=${tanggal}`.replace(':id', kegiatanId);
    fetch(url)
        .then(response => response.text())
        .then(html => {
            modalBody.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = `
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #dc3545;"></i>
                    <h4 style="margin: 20px 0 10px; color: #dc3545;">Gagal Memuat Data</h4>
                    <p style="color: #6c757d;">Terjadi kesalahan saat memuat detail absensi.</p>
                    <button class="btn btn-primary" onclick="closeModal()">Tutup</button>
                </div>
            `;
        });
}

function closeModal() {
    document.getElementById('detailModal').classList.remove('active');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('detailModal');
    if (event.target == modal) {
        closeModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});

// Heatmap Calendar Functions
function changeHeatmapMonth(delta) {
    const monthSelect = document.getElementById('heatmapMonth');
    const yearSelect = document.getElementById('heatmapYear');
    
    let month = parseInt(monthSelect.value) + delta;
    let year = parseInt(yearSelect.value);
    
    if (month > 12) {
        month = 1;
        year++;
    } else if (month < 1) {
        month = 12;
        year--;
    }
    
    monthSelect.value = month;
    yearSelect.value = year;
    updateHeatmap();
}

function updateHeatmap() {
    const month = document.getElementById('heatmapMonth').value;
    const year = document.getElementById('heatmapYear').value;
    const kelasId = document.getElementById('kelasInput').value;
    
    // Update month name display
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    document.getElementById('heatmapMonthName').textContent = monthNames[month - 1] + ' ' + year;
    
    // Fetch heatmap data via AJAX
    fetch(`<?php echo e(route('admin.kegiatan.index')); ?>?heatmap=1&month=${month}&year=${year}&kelas=${kelasId}`)
        .then(response => response.json())
        .then(data => {
            const grid = document.getElementById('heatmapGrid');
            grid.innerHTML = '';
            
            // Add empty cells for the first week
            const firstDay = new Date(year, month - 1, 1).getDay();
            const startDay = firstDay === 0 ? 6 : firstDay - 1; // Convert Sunday (0) to 6
            
            for (let i = 0; i < startDay; i++) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'heatmap-cell';
                emptyCell.style.visibility = 'hidden';
                grid.appendChild(emptyCell);
            }
            
            // Add calendar cells
            data.heatmapData.forEach(day => {
                const cell = document.createElement('div');
                const date = new Date(day.date);
                const dateNum = date.getDate();
                const today = new Date();
                const isToday = date.toDateString() === today.toDateString();
                
                cell.className = `heatmap-cell heatmap-level-${day.level} ${isToday ? 'today' : ''}`;
                cell.setAttribute('data-date', day.date);
                cell.setAttribute('data-percentage', day.percentage);
                cell.setAttribute('title', day.title);
                cell.onclick = () => goToDate(day.date);
                cell.textContent = dateNum;
                
                grid.appendChild(cell);
            });
        })
        .catch(error => {
            console.error('Error loading heatmap:', error);
        });
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kegiatan/data/dashboard.blade.php ENDPATH**/ ?>