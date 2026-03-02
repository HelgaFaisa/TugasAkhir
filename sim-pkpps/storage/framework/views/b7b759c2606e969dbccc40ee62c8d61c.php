


<?php $__env->startSection('title', 'Input Capaian'); ?>

<?php $__env->startSection('content'); ?>
<style>
.input-hero {
    background: linear-gradient(135deg, #1b5e20, #2e7d32, #43a047);
    border-radius: 14px;
    padding: 22px 24px;
    color: #fff;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 16px;
    position: relative;
    overflow: hidden;
}
.input-hero::after {
    content: '✏️';
    position: absolute;
    right: 20px;
    font-size: 4rem;
    opacity: 0.15;
}
.input-hero h2 { margin: 0 0 4px; font-size: 1.2rem; }
.input-hero p  { margin: 0; opacity: 0.8; font-size: .84rem; }

.deadline-banner {
    background: linear-gradient(135deg, #fff3e0, #ffe0b2);
    border: 2px solid #ffa726;
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: .84rem;
    color: #555;
}
.deadline-banner i { font-size: 1.4rem; color: #e65100; flex-shrink: 0; }

.materi-card {
    background: #fff;
    border-radius: 12px;
    padding: 16px 18px;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 14px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all .2s;
}
.materi-card:hover { border-color: var(--primary-color); transform: translateX(3px); }
.materi-card.selected { border-color: var(--primary-color); background: #f0faf4; }
.materi-card.done { border-color: #66bb6a; background: #f1f8e9; }
.materi-badge { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
.materi-info { flex: 1; }
.materi-info h4 { margin: 0 0 3px; font-size: .9rem; }
.materi-info small { color: #999; font-size: .75rem; }

.form-section {
    background: #fff;
    border-radius: 14px;
    padding: 22px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    margin-bottom: 14px;
    border: 1px solid #e8f0ec;
}
.form-section h4 { margin: 0 0 16px; color: var(--primary-dark); font-size: .95rem; }

.prog-preview {
    background: linear-gradient(135deg, #e8f5e9, #f1f8e9);
    border-radius: 10px;
    padding: 14px;
    margin-top: 12px;
    display: none;
}
.prog-preview.show { display: block; }
.prog-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 10px; }
.prog-stat { background: #fff; border-radius: 8px; padding: 10px; text-align: center; }
.prog-stat .pv { font-size: 1.2rem; font-weight: 800; color: var(--primary-color); }
.prog-stat .pl { font-size: .7rem; color: #999; margin-top: 2px; }
.prog-bar { height: 10px; background: #e0e0e0; border-radius: 10px; overflow: hidden; }
.prog-fill { height: 100%; border-radius: 10px; background: linear-gradient(90deg, var(--primary-color), var(--success-color)); transition: width .5s; }

.method-tabs { display: flex; gap: 6px; margin-bottom: 14px; flex-wrap: wrap; }
.mt-btn { padding: 7px 14px; border: 2px solid #e0e0e0; background: #fff; border-radius: 8px; font-size: .78rem; font-weight: 600; cursor: pointer; transition: .2s; color: #666; }
.mt-btn.active { border-color: var(--primary-color); background: var(--primary-light); color: var(--primary-dark); }
.method-panel { display: none; }
.method-panel.active { display: block; }
</style>


<div class="input-hero">
    <div>
        <h2><i class="fas fa-pencil-alt"></i> Input Capaian Mandiri</h2>
        <p>
            <?php echo e($santri->nama_lengkap); ?> &bull;
            <?php echo e($santri->kelasPrimary?->kelas?->nama_kelas ?? '-'); ?>

            <?php if($semesterAktif): ?>
                &bull; Semester: <strong><?php echo e($semesterAktif->nama_semester); ?></strong>
            <?php endif; ?>
        </p>
    </div>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success" style="border-radius:10px;"><i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="alert alert-danger" style="border-radius:10px;"><i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?></div>
<?php endif; ?>


<?php if(!empty($accessConfig['catatan'])): ?>
<div class="deadline-banner">
    <i class="fas fa-bell"></i>
    <div>
        <strong>Informasi dari Admin:</strong> <?php echo e($accessConfig['catatan']); ?>

        <?php if($sisaWaktu): ?>
            &bull; <strong>Sisa waktu: <?php echo e($sisaWaktu); ?></strong>
        <?php endif; ?>
    </div>
</div>
<?php elseif($sisaWaktu): ?>
<div class="deadline-banner">
    <i class="fas fa-hourglass-half"></i>
    <div>Waktu input masih tersedia: <strong><?php echo e($sisaWaktu); ?></strong></div>
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1.5fr;gap:18px;">


<div>
    <div class="form-section">
        <h4><i class="fas fa-book"></i> Pilih Materi</h4>

        <?php
            $materiByKat = $materiOptions->groupBy('kategori');
        ?>

        <?php $__currentLoopData = [
            ["Al-Qur'an", 'fas fa-book-quran', 'var(--success-color)', '#e8f5e9'],
            ['Hadist', 'fas fa-scroll', 'var(--info-color)', '#e3f2fd'],
            ['Materi Tambahan', 'fas fa-book', 'var(--warning-color)', '#fffde7'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$kat, $icon, $color, $bg]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(isset($materiByKat[$kat]) && $materiByKat[$kat]->count() > 0): ?>
        <div style="margin-bottom:14px;">
            <div style="font-size:.77rem;font-weight:700;color:<?php echo e($color); ?>;margin-bottom:6px;display:flex;align-items:center;gap:5px;">
                <i class="<?php echo e($icon); ?>"></i> <?php echo e($kat); ?>

            </div>
            <?php $__currentLoopData = $materiByKat[$kat]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $materi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $existPct = $existingCapaians[$materi->id_materi] ?? null;
            ?>
            <div class="materi-card <?php echo e($existPct >= 100 ? 'done' : ''); ?>"
                 onclick="selectMateri('<?php echo e($materi->id_materi); ?>', this)"
                 data-materi-id="<?php echo e($materi->id_materi); ?>"
                 id="materi-card-<?php echo e($materi->id_materi); ?>">
                <div class="materi-badge" style="background:<?php echo e($bg); ?>;color:<?php echo e($color); ?>;">
                    <i class="<?php echo e($icon); ?>"></i>
                </div>
                <div class="materi-info">
                    <h4><?php echo e($materi->nama_kitab); ?></h4>
                    <small>Hal. <?php echo e($materi->halaman_mulai); ?>–<?php echo e($materi->halaman_akhir); ?> &bull; <?php echo e($materi->total_halaman); ?> hal</small>
                    <?php if($existPct !== null): ?>
                    <div style="margin-top:4px;">
                        <span style="font-size:.72rem;font-weight:700;color:<?php echo e($existPct >= 100 ? '#2e7d32' : '#f57f17'); ?>;">
                            <i class="fas fa-<?php echo e($existPct >= 100 ? 'check-circle' : 'edit'); ?>"></i>
                            <?php echo e(number_format($existPct, 0)); ?>% — <?php echo e($existPct >= 100 ? 'Khatam' : 'Sudah diisi'); ?>

                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if($materiOptions->isEmpty()): ?>
        <div style="text-align:center;color:#aaa;padding:20px;">
            <i class="fas fa-book" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.4;"></i>
            Tidak ada materi untuk kelas Anda
        </div>
        <?php endif; ?>
    </div>
</div>


<div>
    
    <div id="formPlaceholder" class="form-section" style="text-align:center;color:#aaa;min-height:200px;display:flex;flex-direction:column;align-items:center;justify-content:center;">
        <i class="fas fa-hand-point-left" style="font-size:2rem;opacity:.4;margin-bottom:10px;"></i>
        <p style="margin:0;font-size:.85rem;">Pilih materi di sebelah kiri untuk mulai input capaian</p>
    </div>

    
    <div id="formInput" style="display:none;">
        <div class="form-section">
            <h4 id="formTitle"><i class="fas fa-edit"></i> Input Capaian</h4>

            <form method="POST" action="<?php echo e(route('santri.capaian.input.store')); ?>" id="santriCapaianForm">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id_materi" id="id_materi">
                <input type="hidden" name="id_semester" value="<?php echo e($semesterAktif?->id_semester); ?>">

                
                <div style="background:#f8fdf9;border-radius:9px;padding:12px;margin-bottom:14px;font-size:.83rem;">
                    <div id="info-nama" style="font-weight:700;color:var(--primary-dark);margin-bottom:3px;"></div>
                    <div id="info-meta" style="color:#888;"></div>
                </div>

                
                <div style="margin-bottom:10px;">
                    <div style="font-size:.78rem;font-weight:700;color:#555;margin-bottom:6px;">Metode Input:</div>
                    <div class="method-tabs">
                        <button type="button" class="mt-btn active" onclick="switchMethod(1, this)"><i class="fas fa-keyboard"></i> Range Text</button>
                        <button type="button" class="mt-btn" onclick="switchMethod(2, this)"><i class="fas fa-th"></i> Visual Grid</button>
                        <button type="button" class="mt-btn" onclick="switchMethod(3, this)"><i class="fas fa-bolt"></i> Quick Input</button>
                    </div>
                </div>

                
                <div class="method-panel active" id="method1">
                    <div class="form-group" style="margin-bottom:8px;">
                        <input type="text" id="halaman_selesai" name="halaman_selesai"
                               class="form-control" placeholder="Contoh: 1-10, 16-21, 40"
                               style="font-size:.85rem;" oninput="onRangeInput(this.value)">
                        <small style="color:#999;font-size:.73rem;">Range dengan tanda (-) dan pisahkan koma (,)</small>
                    </div>
                    <button type="button" class="btn btn-info btn-sm" onclick="previewRange()">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                </div>

                
                <div class="method-panel" id="method2">
                    <div style="margin-bottom:8px;display:flex;gap:6px;">
                        <button type="button" class="btn btn-sm btn-success" onclick="selectAllGrid()"><i class="fas fa-check-square"></i> Semua</button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="clearGrid()"><i class="fas fa-times"></i> Reset</button>
                    </div>
                    <div id="pageGrid" style="display:grid;grid-template-columns:repeat(8,1fr);gap:5px;max-height:280px;overflow-y:auto;"></div>
                </div>

                
                <div class="method-panel" id="method3">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                        <span style="font-size:.83rem;">Halaman 1 sampai</span>
                        <input type="number" id="quickVal" class="form-control" style="width:90px;font-size:.83rem;" min="1" placeholder="...">
                        <button type="button" class="btn btn-primary btn-sm" onclick="applyQuick()">Terapkan</button>
                    </div>
                    <button type="button" class="btn btn-sm btn-success" onclick="selectAllQuick()"><i class="fas fa-check-double"></i> Semua Halaman</button>
                </div>

                
                <div class="prog-preview" id="progPreview">
                    <div class="prog-row">
                        <div class="prog-stat"><div class="pv" id="prevHal">0</div><div class="pl">Hal Selesai</div></div>
                        <div class="prog-stat"><div class="pv" id="prevTot">0</div><div class="pl">Total Hal</div></div>
                        <div class="prog-stat"><div class="pv" id="prevPct" style="color:var(--success-color);">0%</div><div class="pl">Progress</div></div>
                    </div>
                    <div class="prog-bar"><div class="prog-fill" id="prevBar" style="width:0%;"></div></div>
                </div>

                
                <div class="form-group" style="margin:12px 0 10px;">
                    <label style="font-size:.78rem;font-weight:600;color:#555;display:block;margin-bottom:3px;">
                        <i class="fas fa-sticky-note"></i> Catatan (opsional)
                    </label>
                    <textarea name="catatan" class="form-control" rows="2"
                              style="font-size:.83rem;" placeholder="Catatan tambahan..."></textarea>
                </div>

                <div class="form-group" style="margin-bottom:14px;">
                    <label style="font-size:.78rem;font-weight:600;color:#555;display:block;margin-bottom:3px;">
                        <i class="fas fa-calendar-day"></i> Tanggal Input <span style="color:red;">*</span>
                    </label>
                    <input type="date" name="tanggal_input" class="form-control"
                           value="<?php echo e(date('Y-m-d')); ?>" style="font-size:.83rem;" required>
                </div>

                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-success" style="flex:1;">
                        <i class="fas fa-save"></i> Simpan Capaian
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetForm()">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>

<div style="margin-top:10px;">
    <a href="<?php echo e(route('santri.capaian.index')); ?>" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali ke Capaian
    </a>
</div>

<script>
// ===== STATE =====
let totalHal = 0, halamanMulai = 1, halamanAkhir = 1;
let selectedPages = new Set();
let currentMethod = 1;

// Data materi dari PHP
const materiData = {
    <?php $__currentLoopData = $materiOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    '<?php echo e($m->id_materi); ?>': {
        nama: '<?php echo e(addslashes($m->nama_kitab)); ?>',
        kategori: '<?php echo e(addslashes($m->kategori)); ?>',
        total: <?php echo e($m->total_halaman); ?>,
        mulai: <?php echo e($m->halaman_mulai); ?>,
        akhir: <?php echo e($m->halaman_akhir); ?>,
    },
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
};

const existingCapaians = {
    <?php $__currentLoopData = $existingCapaians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idMateri => $pct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    // (only id_materi:halaman_selesai is needed but pct is enough for display)
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
};

// ===== SELECT MATERI =====
function selectMateri(idMateri, el) {
    // Remove selection from all cards
    document.querySelectorAll('.materi-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');

    const m = materiData[idMateri];
    if (!m) return;

    // Set form
    document.getElementById('id_materi').value = idMateri;
    document.getElementById('formTitle').innerHTML = `<i class="fas fa-edit"></i> Input: ${m.nama}`;
    document.getElementById('info-nama').textContent = m.nama;
    document.getElementById('info-meta').textContent = `${m.kategori} — Hal. ${m.mulai}–${m.akhir} (${m.total} halaman)`;

    totalHal = m.total;
    halamanMulai = m.mulai;
    halamanAkhir = m.akhir;

    // Reset pages
    selectedPages = new Set();
    generateGrid();
    document.getElementById('halaman_selesai').value = '';
    hidePreview();

    // Load existing
    loadExisting(idMateri);

    document.getElementById('formPlaceholder').style.display = 'none';
    document.getElementById('formInput').style.display = 'block';
}

// ===== LOAD EXISTING CAPAIAN =====
function loadExisting(idMateri) {
    const idSemester = document.querySelector('[name="id_semester"]').value;
    if (!idSemester) return;

    fetch('<?php echo e(route("santri.capaian.input.ajax.detail")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({ id_materi: idMateri, id_semester: idSemester })
    })
    .then(r => r.json())
    .then(data => {
        if (data.existing_capaian && data.existing_capaian.halaman_selesai) {
            const hs = data.existing_capaian.halaman_selesai;
            document.getElementById('halaman_selesai').value = hs;
            selectedPages = parseRange(hs);
            updateGridDisplay();
            updatePreview();
            if (data.existing_capaian.catatan) {
                document.querySelector('[name="catatan"]').value = data.existing_capaian.catatan;
            }
        }
    })
    .catch(() => {});
}

// ===== METHOD SWITCH =====
function switchMethod(n, btn) {
    currentMethod = n;
    document.querySelectorAll('.method-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.mt-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('method' + n).classList.add('active');
    btn.classList.add('active');
    if (n === 2) updateGridDisplay();
    syncRange();
}

// ===== RANGE TEXT =====
function onRangeInput(val) {
    if (!val.trim()) { selectedPages.clear(); hidePreview(); return; }
    selectedPages = parseRange(val);
    updatePreview();
}

function previewRange() {
    const val = document.getElementById('halaman_selesai').value.trim();
    if (!val) { alert('Masukkan range halaman dulu!'); return; }
    selectedPages = parseRange(val);
    updateGridDisplay();
    updatePreview();
}

// ===== GRID =====
function generateGrid() {
    const grid = document.getElementById('pageGrid');
    grid.innerHTML = '';
    for (let i = halamanMulai; i <= halamanAkhir; i++) {
        const box = document.createElement('div');
        box.id = 'pg-' + i;
        box.textContent = i;
        box.style.cssText = 'padding:6px;border:1.5px solid #ddd;border-radius:6px;text-align:center;cursor:pointer;font-size:.7rem;font-weight:600;background:#fff;transition:all .15s;';
        box.onclick = () => togglePage(i);
        grid.appendChild(box);
    }
}

function togglePage(i) {
    if (selectedPages.has(i)) selectedPages.delete(i);
    else selectedPages.add(i);
    updateGridDisplay();
    updatePreview();
    syncRange();
}

function updateGridDisplay() {
    for (let i = halamanMulai; i <= halamanAkhir; i++) {
        const box = document.getElementById('pg-' + i);
        if (!box) continue;
        if (selectedPages.has(i)) {
            box.style.background = 'linear-gradient(135deg,var(--primary-color),var(--success-color))';
            box.style.borderColor = 'var(--primary-color)';
            box.style.color = '#fff';
        } else {
            box.style.background = '#fff';
            box.style.borderColor = '#ddd';
            box.style.color = '#333';
        }
    }
}

function selectAllGrid() {
    selectedPages.clear();
    for (let i = halamanMulai; i <= halamanAkhir; i++) selectedPages.add(i);
    updateGridDisplay();
    updatePreview();
    syncRange();
}

function clearGrid() {
    selectedPages.clear();
    updateGridDisplay();
    hidePreview();
    syncRange();
}

// ===== QUICK =====
function applyQuick() {
    const v = parseInt(document.getElementById('quickVal').value);
    if (!v || v < 1) { alert('Masukkan nilai!'); return; }
    selectedPages.clear();
    for (let i = halamanMulai; i <= Math.min(halamanMulai + v - 1, halamanAkhir); i++) selectedPages.add(i);
    updatePreview();
    syncRange();
}

function selectAllQuick() {
    selectedPages.clear();
    for (let i = halamanMulai; i <= halamanAkhir; i++) selectedPages.add(i);
    updatePreview();
    syncRange();
}

// ===== SYNC RANGE INPUT =====
function syncRange() {
    if (selectedPages.size === 0) {
        document.getElementById('halaman_selesai').value = '';
        return;
    }
    const sorted = [...selectedPages].sort((a,b) => a-b);
    document.getElementById('halaman_selesai').value = toRangeString(sorted);
}

// ===== HELPERS =====
function parseRange(str) {
    const pages = new Set();
    str.split(',').forEach(part => {
        part = part.trim();
        if (part.includes('-')) {
            const [s, e] = part.split('-').map(n => parseInt(n.trim()));
            for (let i = s; i <= e; i++) { if (i >= halamanMulai && i <= halamanAkhir) pages.add(i); }
        } else {
            const n = parseInt(part);
            if (n >= halamanMulai && n <= halamanAkhir) pages.add(n);
        }
    });
    return pages;
}

function toRangeString(arr) {
    if (!arr.length) return '';
    const ranges = [];
    let s = arr[0], e = arr[0];
    for (let i = 1; i < arr.length; i++) {
        if (arr[i] === e + 1) { e = arr[i]; }
        else { ranges.push(s === e ? `${s}` : `${s}-${e}`); s = e = arr[i]; }
    }
    ranges.push(s === e ? `${s}` : `${s}-${e}`);
    return ranges.join(',');
}

function updatePreview() {
    const jml = selectedPages.size;
    const pct = totalHal > 0 ? ((jml / totalHal) * 100).toFixed(1) : 0;
    document.getElementById('prevHal').textContent = jml;
    document.getElementById('prevTot').textContent = totalHal;
    document.getElementById('prevPct').textContent = pct + '%';
    document.getElementById('prevBar').style.width = pct + '%';
    document.getElementById('progPreview').classList.add('show');
}

function hidePreview() {
    document.getElementById('progPreview').classList.remove('show');
}

function resetForm() {
    selectedPages.clear();
    document.getElementById('halaman_selesai').value = '';
    document.querySelectorAll('.materi-card').forEach(c => c.classList.remove('selected'));
    document.getElementById('formPlaceholder').style.display = 'flex';
    document.getElementById('formInput').style.display = 'none';
    hidePreview();
}

// Form validation
document.getElementById('santriCapaianForm')?.addEventListener('submit', function(e) {
    const hs = document.getElementById('halaman_selesai').value.trim();
    if (!hs) {
        e.preventDefault();
        alert('Silakan input halaman yang sudah selesai!');
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/capaian/input.blade.php ENDPATH**/ ?>