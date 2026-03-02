


<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-clipboard-check"></i> Input Absensi: <?php echo e($kegiatan->nama_kegiatan); ?></h2>
</div>

<div class="content-box" style="margin-bottom: 14px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 11px;">
        <div>
            <h3 style="margin: 0; color: var(--primary-color);"><?php echo e($kegiatan->nama_kegiatan); ?></h3>
            <p style="margin: 5px 0 0 0; color: var(--text-light);">
                <i class="fas fa-calendar-day"></i> <?php echo e($kegiatan->hari); ?> |
                <i class="fas fa-clock"></i> <?php echo e(date('H:i', strtotime($kegiatan->waktu_mulai))); ?> - <?php echo e(date('H:i', strtotime($kegiatan->waktu_selesai))); ?> |
                <i class="fas fa-list-alt"></i> <?php echo e($kegiatan->kategori->nama_kategori); ?>

            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="button" id="btnModeManual" class="btn btn-primary" onclick="setMode('manual')">
                <i class="fas fa-hand-pointer"></i> Mode Manual
            </button>
            <button type="button" id="btnModeRfid" class="btn btn-secondary" onclick="setMode('rfid')">
                <i class="fas fa-id-card"></i> Mode RFID
            </button>
        </div>
    </div>
</div>


<div class="info-box" style="margin-bottom: 14px; border-left: 4px solid var(--primary-color);">
    <i class="fas fa-info-circle"></i>
    <?php if($kegiatanInfo['is_umum']): ?>
        <strong>Kegiatan Umum</strong> - Diikuti oleh semua santri aktif (<?php echo e($santris->count()); ?> santri)
    <?php else: ?>
        <strong>Kegiatan Khusus</strong> - Diikuti oleh kelas:
        <strong style="color: var(--primary-color);"><?php echo e($kegiatanInfo['kelas_list']); ?></strong>
        (<?php echo e($kegiatanInfo['jumlah_kelas']); ?> kelas, <?php echo e($santris->count()); ?> santri)
    <?php endif; ?>
</div>


<?php
    $sudahAdaData = count($absensiData) > 0;
?>
<?php if($sudahAdaData): ?>
    <div class="alert alert-info" style="margin-bottom: 14px;">
        <i class="fas fa-edit"></i>
        <strong>Mode Edit</strong> - Data absensi untuk tanggal ini sudah ada (<?php echo e(count($absensiData)); ?> santri).
        Anda dapat mengubah status absensi lalu klik Simpan.
    </div>
<?php endif; ?>

<!-- MODE MANUAL -->
<div id="modeManual" class="content-box">
    <form action="<?php echo e(route('admin.absensi-kegiatan.simpan')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="kegiatan_id" value="<?php echo e($kegiatan->kegiatan_id); ?>">

        <div class="filter-form-inline" style="margin-bottom: 14px; gap: 12px;">
            <div class="filter-form-inline" style="gap: 8px;">
                <label style="font-weight: 600; white-space: nowrap; margin: 0;">
                    <i class="fas fa-calendar"></i> Tanggal:
                </label>
                <input type="date" name="tanggal" id="tanggal" class="form-control"
                       value="<?php echo e($tanggal); ?>" required style="max-width: 170px;">
            </div>

            <div class="filter-form-inline" style="gap: 8px;">
                <label style="font-weight: 600; white-space: nowrap; margin: 0;">
                    <i class="fas fa-school"></i> Pilih Kelas:
                </label>
                <select id="kelasFilter" class="form-control" onchange="filterKelas(this.value)" style="max-width: 220px;">
                    <option value="semua">-- Tampilkan Semua Kelas --</option>
                    <?php $__currentLoopData = $santriGrouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelasNama => $santriKelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($kelasNama); ?>"><?php echo e($kelasNama); ?> (<?php echo e($santriKelas->count()); ?> santri)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div style="margin-left: auto; display: flex; gap: 8px; flex-wrap: wrap;">
                <button type="button" class="btn btn-sm btn-info" onclick="setAllStatus('Hadir')">
                    <i class="fas fa-check-double"></i> Semua Hadir
                </button>
                <button type="button" class="btn btn-sm" style="background: #FF9800; color: white;" onclick="setAllStatus('Terlambat')">
                    <i class="fas fa-clock"></i> Semua Terlambat
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="setAllStatus('Alpa')">
                    <i class="fas fa-times"></i> Semua Alpa
                </button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="clearAllStatus()">
                    <i class="fas fa-eraser"></i> Kosongkan
                </button>
            </div>
        </div>

        <div class="info-box" style="margin-bottom: 14px;">
            <p style="margin: 0;"><i class="fas fa-info-circle"></i> Pilih kelas terlebih dahulu untuk menampilkan daftar santri. Santri tanpa pilihan status akan <strong>dilewati</strong>. Santri yang <strong>sedang pulang</strong> otomatis ditandai.</p>
        </div>

        
        <?php $__currentLoopData = $santriGrouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelasNama => $santriKelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $hadirCount = 0;
            $totalKelas = $santriKelas->count();
            foreach ($santriKelas as $s) {
                $st = $absensiData[$s->id_santri] ?? null;
                if ($st === 'Hadir') $hadirCount++;
            }
            $sudahInputKelas = false;
            foreach ($santriKelas as $s) {
                if (isset($absensiData[$s->id_santri])) { $sudahInputKelas = true; break; }
            }
        ?>
        <div class="kelas-group" data-kelas="<?php echo e($kelasNama); ?>" style="margin-bottom: 20px; display: none;">
            <div style="background: var(--primary-light); padding: 10px 14px; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h4 style="margin: 0; color: var(--primary-color); font-size: 0.95rem;">
                    <i class="fas fa-users"></i> <?php echo e($kelasNama); ?>

                </h4>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <?php if($sudahInputKelas): ?>
                        <span class="badge badge-info"><i class="fas fa-edit"></i> <?php echo e($hadirCount); ?>/<?php echo e($totalKelas); ?> hadir</span>
                    <?php endif; ?>
                    <span class="badge badge-primary"><?php echo e($totalKelas); ?> santri</span>
                </div>
            </div>
            <table class="data-table" style="margin-top: 0; border-top-left-radius: 0; border-top-right-radius: 0;">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 100px;">ID Santri</th>
                        <th>Nama Santri</th>
                        <th style="width: 420px; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $santriKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isPulang = in_array($santri->id_santri, $santriSedangPulang ?? []);
                        $currentStatus = $absensiData[$santri->id_santri] ?? ($isPulang ? 'Pulang' : '');
                    ?>
                    <tr <?php if($isPulang): ?> style="background: #FFF8E1; opacity: 0.85;" <?php endif; ?>>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><strong><?php echo e($santri->id_santri); ?></strong></td>
                        <td>
                            <?php echo e($santri->nama_lengkap); ?>

                            <?php if($isPulang): ?>
                                <span class="badge" style="background: #FFF3E0; color: #E65100; font-size: 0.75rem; margin-left: 6px;">
                                    <i class="fas fa-home"></i> Sedang Pulang
                                </span>
                            <?php endif; ?>
                            <?php if(isset($absensiData[$santri->id_santri])): ?>
                                <span class="badge badge-secondary" style="font-size: 0.7rem; margin-left: 4px;">
                                    <i class="fas fa-edit"></i> Sudah input
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if($isPulang): ?>
                                <input type="hidden" name="absensi[<?php echo e($santri->id_santri); ?>]" value="Pulang" class="absensi-input">
                                <span class="badge" style="background: #FFF3E0; color: #E65100; padding: 6px 14px; font-size: 0.85rem;">
                                    <i class="fas fa-home"></i> Pulang
                                </span>
                            <?php else: ?>
                                <div style="display: flex; gap: 5px; justify-content: center; flex-wrap: wrap;">
                                    <label style="margin: 0; cursor: pointer;">
                                        <input type="radio" name="absensi[<?php echo e($santri->id_santri); ?>]" value="Hadir"
                                            <?php echo e($currentStatus == 'Hadir' ? 'checked' : ''); ?> class="absensi-radio absensi-input">
                                        <span class="badge badge-success">Hadir</span>
                                    </label>
                                    <label style="margin: 0; cursor: pointer;">
                                        <input type="radio" name="absensi[<?php echo e($santri->id_santri); ?>]" value="Terlambat"
                                            <?php echo e($currentStatus == 'Terlambat' ? 'checked' : ''); ?> class="absensi-radio absensi-input">
                                        <span class="badge" style="background: #FF9800; color: white;">Terlambat</span>
                                    </label>
                                    <label style="margin: 0; cursor: pointer;">
                                        <input type="radio" name="absensi[<?php echo e($santri->id_santri); ?>]" value="Izin"
                                            <?php echo e($currentStatus == 'Izin' ? 'checked' : ''); ?> class="absensi-radio absensi-input">
                                        <span class="badge badge-warning">Izin</span>
                                    </label>
                                    <label style="margin: 0; cursor: pointer;">
                                        <input type="radio" name="absensi[<?php echo e($santri->id_santri); ?>]" value="Sakit"
                                            <?php echo e($currentStatus == 'Sakit' ? 'checked' : ''); ?> class="absensi-radio absensi-input">
                                        <span class="badge badge-info">Sakit</span>
                                    </label>
                                    <label style="margin: 0; cursor: pointer;">
                                        <input type="radio" name="absensi[<?php echo e($santri->id_santri); ?>]" value="Alpa"
                                            <?php echo e($currentStatus == 'Alpa' ? 'checked' : ''); ?> class="absensi-radio absensi-input">
                                        <span class="badge badge-danger">Alpa</span>
                                    </label>
                                    <?php if($currentStatus): ?>
                                    <label style="margin: 0; cursor: pointer;" title="Hapus pilihan">
                                        <button type="button" class="btn btn-sm" style="padding: 2px 8px; font-size: 0.75rem; background: #f1f1f1;" onclick="clearRadio('<?php echo e($santri->id_santri); ?>')">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </label>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div id="noKelasSelected" class="empty-state" style="padding: 40px 20px;">
            <i class="fas fa-hand-pointer"></i>
            <h3>Pilih Kelas Terlebih Dahulu</h3>
            <p>Silakan pilih kelas pada dropdown di atas untuk menampilkan daftar santri yang akan diabsen.</p>
        </div>

        <div class="btn-group" style="margin-top: 14px;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> <?php echo e($sudahAdaData ? 'Update Absensi' : 'Simpan Absensi'); ?>

            </button>
            <a href="<?php echo e(route('admin.kegiatan.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<!-- MODE RFID -->
<div id="modeRfid" class="content-box" style="display: none;">
    <div class="form-group">
        <label for="tanggalRfid">
            <i class="fas fa-calendar form-icon"></i>
            Tanggal Absensi
        </label>
        <input type="date" id="tanggalRfid" class="form-control" value="<?php echo e($tanggal); ?>">
    </div>

    <div class="info-box">
        <p><i class="fas fa-id-card"></i> Tempelkan kartu RFID santri ke reader. Absensi akan otomatis tersimpan sebagai <strong>Hadir</strong>.</p>
    </div>

    <div style="background: linear-gradient(135deg, var(--primary-light) 0%, #D4F1E3 100%); padding: 22px; border-radius: var(--border-radius); text-align: center; margin-bottom: 14px;">
        <div id="rfidStatus" style="font-size: 1.5rem; font-weight: 600; color: var(--primary-color); margin-bottom: 15px;">
            <i class="fas fa-wifi"></i> Siap Scan RFID
        </div>
        <input type="text" id="rfidInput" placeholder="Fokus di sini untuk scan RFID..."
            style="width: 100%; padding: 15px; font-size: 1.2rem; border: 3px solid var(--primary-color); border-radius: var(--border-radius-sm); text-align: center;"
            autofocus>
    </div>

    <div id="rfidLog" style="max-height: 400px; overflow-y: auto; background: white; border-radius: var(--border-radius-sm); padding: 15px; border: 1px solid var(--primary-light);">
        <h4 style="margin: 0 0 15px 0; color: var(--primary-color);"><i class="fas fa-history"></i> Log Absensi</h4>
        <div id="rfidLogContent">
            <p style="text-align: center; color: var(--text-light);">Belum ada absensi...</p>
        </div>
    </div>

    <div class="btn-group" style="margin-top: 14px;">
        <button type="button" class="btn btn-warning" onclick="clearLog()">
            <i class="fas fa-trash"></i> Bersihkan Log
        </button>
        <a href="<?php echo e(route('admin.kegiatan.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<script>
// -- Kelas Filter --
function filterKelas(value) {
    var groups = document.querySelectorAll('.kelas-group');
    var emptyMsg = document.getElementById('noKelasSelected');

    if (value === 'semua') {
        for (var i = 0; i < groups.length; i++) {
            groups[i].style.display = 'block';
            toggleGroupInputs(groups[i], true);
        }
        emptyMsg.style.display = 'none';
    } else {
        var found = false;
        for (var i = 0; i < groups.length; i++) {
            if (groups[i].getAttribute('data-kelas') === value) {
                groups[i].style.display = 'block';
                toggleGroupInputs(groups[i], true);
                found = true;
            } else {
                groups[i].style.display = 'none';
                toggleGroupInputs(groups[i], false);
            }
        }
        emptyMsg.style.display = found ? 'none' : 'block';
    }
}

// Enable/disable all inputs in a kelas group so hidden groups don't submit
function toggleGroupInputs(group, enabled) {
    var inputs = group.querySelectorAll('.absensi-input');
    for (var i = 0; i < inputs.length; i++) {
        inputs[i].disabled = !enabled;
    }
}

// -- Set All Status (for visible groups only) --
function setAllStatus(status) {
    var groups = document.querySelectorAll('.kelas-group');
    for (var i = 0; i < groups.length; i++) {
        if (groups[i].style.display !== 'none') {
            var radios = groups[i].querySelectorAll('input.absensi-radio[value="' + status + '"]');
            for (var j = 0; j < radios.length; j++) {
                radios[j].checked = true;
            }
        }
    }
}

// -- Clear radio selection for a specific santri --
function clearRadio(santriId) {
    var radios = document.querySelectorAll('input[name="absensi[' + santriId + ']"]');
    for (var i = 0; i < radios.length; i++) {
        radios[i].checked = false;
    }
}

// -- Clear all selections in visible groups --
function clearAllStatus() {
    var groups = document.querySelectorAll('.kelas-group');
    for (var i = 0; i < groups.length; i++) {
        if (groups[i].style.display !== 'none') {
            var radios = groups[i].querySelectorAll('input.absensi-radio');
            for (var j = 0; j < radios.length; j++) {
                radios[j].checked = false;
            }
        }
    }
}

// -- Mode Switch --
var currentMode = 'manual';
var kegiatanId = '<?php echo e($kegiatan->kegiatan_id); ?>';

function setMode(mode) {
    currentMode = mode;
    var modeManualEl = document.getElementById('modeManual');
    var modeRfidEl = document.getElementById('modeRfid');
    var btnManual = document.getElementById('btnModeManual');
    var btnRfid = document.getElementById('btnModeRfid');

    if (mode === 'manual') {
        modeManualEl.style.display = 'block';
        modeRfidEl.style.display = 'none';
        btnManual.className = 'btn btn-primary';
        btnRfid.className = 'btn btn-secondary';
    } else {
        modeManualEl.style.display = 'none';
        modeRfidEl.style.display = 'block';
        btnManual.className = 'btn btn-secondary';
        btnRfid.className = 'btn btn-success';
        document.getElementById('rfidInput').focus();
    }
}

// -- RFID Scanner --
document.getElementById('rfidInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        var rfidUid = this.value.trim();
        if (rfidUid) {
            scanRfid(rfidUid);
            this.value = '';
        }
    }
});

function scanRfid(rfidUid) {
    var tanggal = document.getElementById('tanggalRfid').value;
    var statusEl = document.getElementById('rfidStatus');

    statusEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    statusEl.style.color = 'var(--warning-color)';

    fetch('<?php echo e(route("admin.absensi-kegiatan.scan-rfid")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({
            rfid_uid: rfidUid,
            kegiatan_id: kegiatanId,
            tanggal: tanggal
        })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            statusEl.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
            statusEl.style.color = 'var(--success-color)';
            addLogEntry(data.data, 'success');
        } else {
            statusEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
            statusEl.style.color = 'var(--danger-color)';
        }

        setTimeout(function() {
            statusEl.innerHTML = '<i class="fas fa-wifi"></i> Siap Scan RFID';
            statusEl.style.color = 'var(--primary-color)';
        }, 2000);
    })
    .catch(function(error) {
        statusEl.innerHTML = '<i class="fas fa-times-circle"></i> Koneksi error';
        statusEl.style.color = 'var(--danger-color)';
        console.error('Error:', error);

        setTimeout(function() {
            statusEl.innerHTML = '<i class="fas fa-wifi"></i> Siap Scan RFID';
            statusEl.style.color = 'var(--primary-color)';
        }, 2000);
    });
}

function addLogEntry(data, type) {
    var logContent = document.getElementById('rfidLogContent');

    if (logContent.querySelector('p')) {
        logContent.innerHTML = '';
    }

    var entry = document.createElement('div');
    entry.style.cssText = 'padding: 12px; margin-bottom: 10px; border-radius: 8px; background: ' +
        (type === 'success' ? 'linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%)' : 'linear-gradient(135deg, #FFE8EA 0%, #FFD5D8 100%)') +
        '; border-left: 4px solid ' + (type === 'success' ? 'var(--success-color)' : 'var(--danger-color)');

    entry.innerHTML = '<div style="display: flex; justify-content: space-between; align-items: center;">' +
        '<div><strong>' + data.nama + '</strong> (' + data.id_santri + ')' +
        '<div style="font-size: 0.85rem; color: var(--text-light); margin-top: 3px;">Kelas: ' + data.kelas + ' | Waktu: ' + data.waktu + '</div></div>' +
        '<span class="badge badge-success"><i class="fas fa-check"></i> Hadir</span></div>';

    logContent.insertBefore(entry, logContent.firstChild);
}

function clearLog() {
    if (confirm('Yakin ingin membersihkan log?')) {
        document.getElementById('rfidLogContent').innerHTML = '<p style="text-align: center; color: var(--text-light);">Belum ada absensi...</p>';
    }
}

// -- Auto-focus RFID input --
setInterval(function() {
    if (currentMode === 'rfid') {
        var rfidInput = document.getElementById('rfidInput');
        if (document.activeElement !== rfidInput) {
            rfidInput.focus();
        }
    }
}, 1000);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kegiatan/absensi/input.blade.php ENDPATH**/ ?>