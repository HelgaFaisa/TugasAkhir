


<?php $__env->startSection('content'); ?>
<?php
    // -- Day-tab calculations --
    $isoDay       = $selectedDate->dayOfWeekIso;
    $monOfWeek    = $selectedDate->copy()->subDays($isoDay - 1);
    $hariMapTab   = [
        'Senin' => 'Senin','Selasa' => 'Selasa','Rabu' => 'Rabu',
        'Kamis' => 'Kamis','Jumat' => 'Jumat','Sabtu' => 'Sabtu','Minggu' => 'Ahad'
    ];
    $activeTab = $hariMapTab[$selectedDate->locale('id')->isoFormat('dddd')] ?? 'Senin';
    $todayHari = $hariMapTab[now()->locale('id')->isoFormat('dddd')] ?? 'Senin';

    $tabHari = [
        ['nama' => 'Senin',  'offset' => 0],
        ['nama' => 'Selasa', 'offset' => 1],
        ['nama' => 'Rabu',   'offset' => 2],
        ['nama' => 'Kamis',  'offset' => 3],
        ['nama' => 'Jumat',  'offset' => 4],
        ['nama' => 'Sabtu',  'offset' => 5],
        ['nama' => 'Minggu',   'offset' => 6],
    ];

    $kelompokGroups = $kelasList->groupBy('kelompok.nama_kelompok');
?>




<div class="page-header" style="display:flex; align-items:center; justify-content:space-between;">
    <h2><i class="fas fa-tachometer-alt"></i> Dashboard Absensi</h2>

    <div style="display:flex; gap:8px;">
        <a href="<?php echo e(route('admin.mesin.mapping-santri.index')); ?>"
           class="btn btn-sm btn-secondary">
            <i class="fas fa-link"></i> Mapping Fingerprint
        </a>
        <a href="<?php echo e(route('admin.mesin.import.index')); ?>"
           class="btn btn-sm btn-success"
           style="background:#0F7B6C; border-color:#0F7B6C;">
            <i class="fas fa-file-import"></i> Import
        </a>
    </div>
</div>

<p style="color: var(--text-light); margin-top: 5px; margin-bottom: 14px;">
    <?php echo e($selectedDate->locale('id')->isoFormat('dddd, D MMMM Y')); ?>

</p>

<?php if(session('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>




<div class="kpi-grid-kegiatan">
    <div class="kpi-kegiatan bg-primary">
        <i class="fas fa-calendar-day kpi-icon"></i>
        <div class="kpi-value"><?php echo e($totalKegiatanHariIni); ?></div>
        <div class="kpi-label">Total Kegiatan</div>
        <div class="kpi-sub">
            <i class="fas fa-<?php echo e($comparisonTotal >= 0 ? 'arrow-up' : 'arrow-down'); ?>"></i>
            <?php echo e(abs($comparisonTotal)); ?> vs minggu lalu
        </div>
    </div>

    <div class="kpi-kegiatan bg-success">
        <i class="fas fa-check-circle kpi-icon"></i>
        <div class="kpi-value"><?php echo e($kegiatanSelesai); ?></div>
        <div class="kpi-label">Kegiatan Selesai</div>
        <div class="kpi-sub">dari <?php echo e($totalKegiatanHariIni); ?> kegiatan</div>
    </div>

    <div class="kpi-kegiatan bg-warning">
        <i class="fas fa-chart-line kpi-icon"></i>
        <div class="kpi-value"><?php echo e($avgKehadiran); ?>%</div>
        <div class="kpi-label">Rata-rata Kehadiran</div>
        <div class="kpi-sub">
            <i class="fas fa-<?php echo e($comparisonAvg >= 0 ? 'arrow-up' : 'arrow-down'); ?>"></i>
            <?php echo e(abs($comparisonAvg)); ?>% vs minggu lalu
        </div>
    </div>

    <div class="kpi-kegiatan bg-info">
        <i class="fas fa-clock kpi-icon"></i>
        <div class="kpi-value"><?php echo e($kegiatanBerlangsung); ?></div>
        <div class="kpi-label">Sedang Berlangsung</div>
        <?php if($kegiatanBerlangsung > 0): ?>
            <div class="kpi-sub"><i class="fas fa-circle" style="color: #90ee90;"></i> Live Now</div>
        <?php else: ?>
            <div class="kpi-sub">Tidak ada kegiatan</div>
        <?php endif; ?>
    </div>
</div>




<div class="content-box">
    <div class="day-tabs">
        <?php $__currentLoopData = $tabHari; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $tabDate    = $monOfWeek->copy()->addDays($tab['offset']);
                $tabDateStr = $tabDate->format('Y-m-d');
                $isActive   = ($activeTab === $tab['nama']);
                $isToday    = ($todayHari === $tab['nama'] && now()->format('W') === $selectedDate->format('W'));
                $params     = array_merge(
                    request()->only(['kelas', 'kategori_id']),
                    ['tanggal' => $tabDateStr]
                );
            ?>
            <a href="<?php echo e(route('admin.kegiatan.index', $params)); ?>"
               class="day-tab <?php echo e($isActive ? 'active' : ''); ?> <?php echo e($isToday ? 'today-tab' : ''); ?>">
                <span class="day-name"><?php echo e($tab['nama']); ?></span>
                <span class="day-date"><?php echo e($tabDate->format('d/m')); ?></span>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    
    
    <form method="GET" action="<?php echo e(route('admin.kegiatan.index')); ?>" id="filterForm" class="filter-form-inline" style="margin-top: 10px;">
        <input type="hidden" name="kelas" id="kelasInput" value="<?php echo e($selectedKelasId); ?>">

        <div class="filter-form-inline" style="gap: 8px;">
            <label style="font-weight: 600; white-space: nowrap; margin: 0;">
                <i class="fas fa-calendar"></i> Tanggal:
            </label>
            <input type="date" name="tanggal" class="form-control"
                   value="<?php echo e($selectedDate->format('Y-m-d')); ?>"
                   onchange="this.form.submit()" style="max-width: 170px;">
        </div>

        <div class="filter-form-inline" style="gap: 8px;">
            <label style="font-weight: 600; white-space: nowrap; margin: 0;">
                <i class="fas fa-tags"></i> Kategori:
            </label>
            <select name="kategori_id" class="form-control" onchange="this.form.submit()" style="max-width: 180px;">
                <option value="">Semua Kategori</option>
                <?php $__currentLoopData = $kategoris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($kat->kategori_id); ?>" <?php echo e(request('kategori_id') == $kat->kategori_id ? 'selected' : ''); ?>>
                        <?php echo e($kat->nama_kategori); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="filter-form-inline" style="gap: 8px;">
            <label style="font-weight: 600; white-space: nowrap; margin: 0;">
                <i class="fas fa-school"></i> Kelas:
            </label>
            <select name="kelas" class="form-control" onchange="this.form.submit()" style="max-width: 200px;">
                <option value="">Semua Kelas</option>
                <option value="umum" <?php echo e($selectedKelasId === 'umum' ? 'selected' : ''); ?>>Kegiatan Umum</option>
                <?php $__currentLoopData = $kelompokGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelompokNama => $kelasGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <optgroup label="<?php echo e($kelompokNama); ?>">
                        <?php $__currentLoopData = $kelasGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($kelas->id); ?>" <?php echo e($selectedKelasId == $kelas->id ? 'selected' : ''); ?>>
                                <?php echo e($kelas->nama_kelas); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </optgroup>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <button type="button" class="btn btn-sm btn-primary" onclick="setToday()">
            <i class="fas fa-calendar-day"></i> Hari Ini
        </button>
        <button type="button" class="btn btn-sm btn-secondary" onclick="prevDay()">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button type="button" class="btn btn-sm btn-secondary" onclick="nextDay()">
            <i class="fas fa-chevron-right"></i>
        </button>

        
    </form>
</div>




<?php if(count($insights) > 0): ?>
<div class="content-box" style="margin-top: 14px;">
    <h4 style="margin: 0 0 12px; color: var(--primary-color);">
        <i class="fas fa-lightbulb"></i> Insight Hari Ini
    </h4>
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




<div class="layout-kegiatan" style="margin-top: 14px;">

    
    <div>
        <?php if($kegiatanHariIni->count() > 0): ?>
            <div class="kegiatan-list">
                <?php $__currentLoopData = $kegiatanHariIni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kegiatan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="kegiatan-card">
                        <div class="kegiatan-card-header">
                            <div class="kegiatan-info">
                                <h3 class="kegiatan-title">
                                    <i class="fas fa-calendar" style="color: var(--primary-color);"></i>
                                    <?php echo e($kegiatan->nama_kegiatan); ?>

                                </h3>
                                <div class="kegiatan-meta">
                                    <span>
                                        <i class="fas fa-clock"></i>
                                        <?php echo e(date('H:i', strtotime($kegiatan->waktu_mulai))); ?> &ndash;
                                        <?php echo e(date('H:i', strtotime($kegiatan->waktu_selesai))); ?>

                                    </span>
                                    <span class="badge badge-info">
                                        <i class="fas fa-tag"></i>
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
                                            <span class="badge badge-secondary">Kegiatan Umum</span>
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

                        
                        <?php
                            $persen = $kegiatan->persen_kehadiran;
                            $pClass = $persen >= 85 ? 'p-success' : ($persen >= 70 ? 'p-warning' : ($persen >= 50 ? 'p-orange' : 'p-danger'));
                            $denominator = $kegiatan->total_absensi > 0 ? $kegiatan->total_absensi : $totalSantriAktif;
                        ?>
                        <div class="kegiatan-progress">
                            <div class="kegiatan-progress-header">
                                <span style="font-weight: 500;">
                                    <i class="fas fa-users"></i> Kehadiran
                                </span>
                                <span style="font-weight: 700;">
                                    <?php echo e($kegiatan->total_hadir); ?>/<?php echo e($denominator); ?>

                                    (<?php echo e($persen); ?>%)
                                </span>
                            </div>
                            <div class="kegiatan-progress-bar">
                                <div class="kegiatan-progress-fill <?php echo e($pClass); ?>"
                                     data-width="<?php echo e($persen); ?>">
                                    <?php echo e($persen); ?>%
                                </div>
                            </div>
                        </div>

                        
                        <div class="kegiatan-actions">
                            <a href="<?php echo e(route('admin.absensi-kegiatan.input', $kegiatan->kegiatan_id)); ?>?tanggal=<?php echo e($selectedDate->format('Y-m-d')); ?>"
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-clipboard-check"></i> Input Absensi
                            </a>
                            <button type="button" class="btn btn-sm btn-info"
                                    data-id="<?php echo e($kegiatan->kegiatan_id); ?>"
                                    data-tanggal="<?php echo e($selectedDate->format('Y-m-d')); ?>"
                                    onclick="showDetailModal(this.getAttribute('data-id'), this.getAttribute('data-tanggal'))">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </button>
                            <a href="<?php echo e(route('admin.absensi-kegiatan.rekap', $kegiatan->kegiatan_id)); ?>?tanggal=<?php echo e($selectedDate->format('Y-m-d')); ?>"
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
                <p>Tidak ada kegiatan untuk
                    <?php echo e($selectedKelasId ? 'kelas ini' : 'hari ini'); ?>

                    pada <?php echo e($selectedDate->locale('id')->isoFormat('dddd, D MMMM Y')); ?>

                </p>
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

            
            <div class="filter-form-inline" style="margin-bottom: 12px;">
                <button type="button" class="btn btn-sm btn-secondary" style="padding: 4px 8px;"
                        onclick="changeHeatmapMonth(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <select id="heatmapMonth" onchange="updateHeatmap()" class="form-control" style="flex: 1;">
                    <?php for($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo e($m); ?>" <?php echo e($m == now()->month ? 'selected' : ''); ?>>
                            <?php echo e(\Carbon\Carbon::create(null, $m, 1)->locale('id')->isoFormat('MMMM')); ?>

                        </option>
                    <?php endfor; ?>
                </select>
                <select id="heatmapYear" onchange="updateHeatmap()" class="form-control" style="width: 80px;">
                    <?php for($y = now()->year - 2; $y <= now()->year + 1; $y++): ?>
                        <option value="<?php echo e($y); ?>" <?php echo e($y == now()->year ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                    <?php endfor; ?>
                </select>
                <button type="button" class="btn btn-sm btn-secondary" style="padding: 4px 8px;"
                        onclick="changeHeatmapMonth(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <div style="text-align: center; font-weight: 600; color: var(--primary-color); margin-bottom: 10px;"
                 id="heatmapMonthName">
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
                        <span>&gt;90%</span>
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
                        <span>&lt;70%</span>
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




<div id="detailModal" class="modal-kegiatan">
    <div class="modal-kegiatan-panel">
        <div class="modal-kegiatan-head">
            <h3><i class="fas fa-info-circle"></i> Detail Absensi Kegiatan</h3>
            <button class="modal-kegiatan-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-kegiatan-body" id="modalBody">
            <div style="text-align: center; padding: 22px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary-color);"></i>
                <p style="margin-top: 10px;">Memuat data...</p>
            </div>
        </div>
    </div>
</div>




<script>
// -- Date Navigation --
function setToday() {
    var dateInput = document.querySelector('input[name="tanggal"]');
    var now = new Date();
    var y = now.getFullYear();
    var m = ('0' + (now.getMonth() + 1)).slice(-2);
    var d = ('0' + now.getDate()).slice(-2);
    dateInput.value = y + '-' + m + '-' + d;
    document.getElementById('filterForm').submit();
}

function prevDay() {
    var currentDate = new Date('<?php echo e($selectedDate->format("Y-m-d")); ?>');
    currentDate.setDate(currentDate.getDate() - 1);
    document.querySelector('input[name="tanggal"]').value = currentDate.toISOString().split('T')[0];
    document.getElementById('filterForm').submit();
}

function nextDay() {
    var currentDate = new Date('<?php echo e($selectedDate->format("Y-m-d")); ?>');
    currentDate.setDate(currentDate.getDate() + 1);
    document.querySelector('input[name="tanggal"]').value = currentDate.toISOString().split('T')[0];
    document.getElementById('filterForm').submit();
}

function goToDate(date) {
    document.querySelector('input[name="tanggal"]').value = date;
    document.getElementById('filterForm').submit();
}

// -- Show Detail Modal (AJAX) --
function showDetailModal(kegiatanId, tanggal) {
    var modal = document.getElementById('detailModal');
    var modalBody = document.getElementById('modalBody');
    modal.classList.add('active');

    var baseUrl = '<?php echo e(route("admin.kegiatan.detail-modal", ":id")); ?>';
    var url = baseUrl.replace(':id', kegiatanId) + '?tanggal=' + tanggal;

    fetch(url)
        .then(function(response) { return response.text(); })
        .then(function(html) { modalBody.innerHTML = html; })
        .catch(function() {
            modalBody.innerHTML =
                '<div style="text-align:center;padding:22px;">' +
                '<i class="fas fa-exclamation-circle" style="font-size:2.2rem;color:var(--danger-color);"></i>' +
                '<h4 style="margin:20px 0 10px;color:var(--danger-color);">Gagal Memuat Data</h4>' +
                '<p style="color:var(--text-light);">Terjadi kesalahan saat memuat detail absensi.</p>' +
                '<button class="btn btn-primary" onclick="closeModal()">Tutup</button>' +
                '</div>';
        });
}

function closeModal() {
    document.getElementById('detailModal').classList.remove('active');
}

// -- Close modal on backdrop / Escape --
window.onclick = function(event) {
    var modal = document.getElementById('detailModal');
    if (event.target === modal) { closeModal(); }
};
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') { closeModal(); }
});

// -- Progress bar width animation --
document.querySelectorAll('.kegiatan-progress-fill[data-width]').forEach(function(el) {
    el.style.width = el.getAttribute('data-width') + '%';
});

// -- Heatmap: delegated click for cells --
document.getElementById('heatmapGrid').addEventListener('click', function(e) {
    var cell = e.target.closest('.heatmap-cell');
    if (cell && cell.dataset.date) {
        goToDate(cell.dataset.date);
    }
});

// -- Heatmap: month navigation --
function changeHeatmapMonth(delta) {
    var monthSelect = document.getElementById('heatmapMonth');
    var yearSelect  = document.getElementById('heatmapYear');
    var month = parseInt(monthSelect.value) + delta;
    var year  = parseInt(yearSelect.value);

    if (month > 12) { month = 1; year++; }
    else if (month < 1) { month = 12; year--; }

    monthSelect.value = month;
    yearSelect.value  = year;
    updateHeatmap();
}

// -- Heatmap: AJAX reload --
function updateHeatmap() {
    var month   = document.getElementById('heatmapMonth').value;
    var year    = document.getElementById('heatmapYear').value;
    var kelasId = document.getElementById('kelasInput').value;

    var monthNames = [
        'Januari','Februari','Maret','April','Mei','Juni',
        'Juli','Agustus','September','Oktober','November','Desember'
    ];
    document.getElementById('heatmapMonthName').textContent = monthNames[month - 1] + ' ' + year;

    var url = '<?php echo e(route("admin.kegiatan.index")); ?>' +
              '?heatmap=1&month=' + month + '&year=' + year + '&kelas=' + kelasId;

    fetch(url)
        .then(function(response) { return response.json(); })
        .then(function(data) {
            var grid = document.getElementById('heatmapGrid');
            grid.innerHTML = '';

            // -- Empty cells for first week alignment --
            var firstDay = new Date(year, month - 1, 1).getDay();
            var startDay = firstDay === 0 ? 6 : firstDay - 1;
            var i;
            for (i = 0; i < startDay; i++) {
                var empty = document.createElement('div');
                empty.className = 'heatmap-cell';
                empty.style.visibility = 'hidden';
                grid.appendChild(empty);
            }

            // -- Render calendar cells --
            data.heatmapData.forEach(function(day) {
                var cell = document.createElement('div');
                var date = new Date(day.date);
                var today = new Date();
                var isToday = date.toDateString() === today.toDateString();

                cell.className = 'heatmap-cell heatmap-level-' + day.level + (isToday ? ' today' : '');
                cell.setAttribute('data-date', day.date);
                cell.setAttribute('data-percentage', day.percentage);
                cell.setAttribute('title', day.title);
                cell.onclick = function() { goToDate(day.date); };
                cell.textContent = date.getDate();
                grid.appendChild(cell);
            });
        })
        .catch(function(err) {
            console.error('Error loading heatmap:', err);
        });
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kegiatan/data/dashboard.blade.php ENDPATH**/ ?>