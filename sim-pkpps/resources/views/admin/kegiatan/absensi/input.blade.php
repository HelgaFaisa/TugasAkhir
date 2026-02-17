@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-clipboard-check"></i> Input Absensi: {{ $kegiatan->nama_kegiatan }}</h2>
</div>

<div class="content-box" style="margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h3 style="margin: 0; color: var(--primary-color);">{{ $kegiatan->nama_kegiatan }}</h3>
            <p style="margin: 5px 0 0 0; color: var(--text-light);">
                <i class="fas fa-calendar-day"></i> {{ $kegiatan->hari }} | 
                <i class="fas fa-clock"></i> {{ date('H:i', strtotime($kegiatan->waktu_mulai)) }} - {{ date('H:i', strtotime($kegiatan->waktu_selesai)) }} | 
                <i class="fas fa-list-alt"></i> {{ $kegiatan->kategori->nama_kategori }}
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="button" id="btnModeManual" class="btn btn-primary" onclick="setMode('manual')">
                <i class="fas fa-hand-pointer"></i> Mode Manual
            </button>
            <button type="button" id="btnModeRfid" class="btn btn-success" onclick="setMode('rfid')">
                <i class="fas fa-id-card"></i> Mode RFID
            </button>
        </div>
    </div>
</div>

{{-- Info Kelas Kegiatan --}}
<div class="info-box" style="margin-bottom: 20px; border-left: 4px solid var(--primary-color);">
    <i class="fas fa-info-circle"></i>
    @if($kegiatanInfo['is_umum'])
        <strong>Kegiatan Umum</strong> - Diikuti oleh semua santri aktif ({{ $santris->count() }} santri)
    @else
        <strong>Kegiatan Khusus</strong> - Diikuti oleh kelas: 
        <strong style="color: var(--primary-color);">{{ $kegiatanInfo['kelas_list'] }}</strong>
        ({{ $kegiatanInfo['jumlah_kelas'] }} kelas, {{ $santris->count() }} santri)
    @endif
</div>

<!-- MODE MANUAL -->
<div id="modeManual" class="content-box">
    <form action="{{ route('admin.absensi-kegiatan.simpan') }}" method="POST">
        @csrf
        <input type="hidden" name="kegiatan_id" value="{{ $kegiatan->kegiatan_id }}">
        
        <div class="form-group">
            <label for="tanggal">
                <i class="fas fa-calendar form-icon"></i>
                Tanggal Absensi
            </label>
            <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $tanggal }}" required>
        </div>

        <div class="info-box">
            <p><i class="fas fa-info-circle"></i> Pilih status absensi untuk setiap santri. Jika tidak dipilih, akan dianggap <strong>Alpa</strong>.</p>
        </div>

        {{-- Filter Kelas (Manual Mode) --}}
        @if(!$kegiatanInfo['is_umum'] && $kegiatanInfo['jumlah_kelas'] > 1)
        <div class="form-group" style="max-width: 300px;">
            <label for="filterKelas">
                <i class="fas fa-filter form-icon"></i>
                Filter Kelas
            </label>
            <select id="filterKelas" class="form-control">
                <option value="">Semua Kelas</option>
                @foreach($kegiatan->kelasKegiatan as $kelas)
                    <option value="{{ $kelas->nama_kelas }}">{{ $kelas->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">ID Santri</th>
                    <th>Nama Santri</th>
                    <th style="width: 100px;">Kelas</th>
                    <th style="width: 300px; text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($santris as $index => $santri)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $santri->id_santri }}</strong></td>
                    <td>{{ $santri->nama_lengkap }}</td>
                    <td>
                        @php
                            $kelasName = $santri->kelas_name ?? $santri->kelas ?? '-';
                        @endphp
                        <span class="badge badge-secondary">{{ $kelasName }}</span>
                    </td>
                    <td class="text-center">
                        @php
                            $currentStatus = $absensiData[$santri->id_santri] ?? 'Alpa';
                        @endphp
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <label style="margin: 0; cursor: pointer;">
                                <input type="radio" name="absensi[{{ $santri->id_santri }}]" value="Hadir" 
                                    {{ $currentStatus == 'Hadir' ? 'checked' : '' }} required>
                                <span class="badge badge-success">Hadir</span>
                            </label>
                            <label style="margin: 0; cursor: pointer;">
                                <input type="radio" name="absensi[{{ $santri->id_santri }}]" value="Izin" 
                                    {{ $currentStatus == 'Izin' ? 'checked' : '' }}>
                                <span class="badge badge-warning">Izin</span>
                            </label>
                            <label style="margin: 0; cursor: pointer;">
                                <input type="radio" name="absensi[{{ $santri->id_santri }}]" value="Sakit" 
                                    {{ $currentStatus == 'Sakit' ? 'checked' : '' }}>
                                <span class="badge badge-info">Sakit</span>
                            </label>
                            <label style="margin: 0; cursor: pointer;">
                                <input type="radio" name="absensi[{{ $santri->id_santri }}]" value="Alpa" 
                                    {{ $currentStatus == 'Alpa' ? 'checked' : '' }}>
                                <span class="badge badge-danger">Alpa</span>
                            </label>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="btn-group" style="margin-top: 20px;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Absensi
            </button>
            <a href="{{ route('admin.absensi-kegiatan.index') }}" class="btn btn-secondary">
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
        <input type="date" id="tanggalRfid" class="form-control" value="{{ $tanggal }}">
    </div>

    <div class="info-box">
        <p><i class="fas fa-id-card"></i> Tempelkan kartu RFID santri ke reader. Absensi akan otomatis tersimpan sebagai <strong>Hadir</strong>.</p>
    </div>

    <div style="background: linear-gradient(135deg, var(--primary-light) 0%, #D4F1E3 100%); padding: 30px; border-radius: var(--border-radius); text-align: center; margin-bottom: 20px;">
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

    <div class="btn-group" style="margin-top: 20px;">
        <button type="button" class="btn btn-warning" onclick="clearLog()">
            <i class="fas fa-trash"></i> Bersihkan Log
        </button>
        <a href="{{ route('admin.absensi-kegiatan.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<script>
let currentMode = 'manual';
const kegiatanId = '{{ $kegiatan->kegiatan_id }}';

function setMode(mode) {
    currentMode = mode;
    
    if (mode === 'manual') {
        document.getElementById('modeManual').style.display = 'block';
        document.getElementById('modeRfid').style.display = 'none';
        document.getElementById('btnModeManual').classList.add('btn-primary');
        document.getElementById('btnModeManual').classList.remove('btn-secondary');
        document.getElementById('btnModeRfid').classList.remove('btn-success');
        document.getElementById('btnModeRfid').classList.add('btn-secondary');
    } else {
        document.getElementById('modeManual').style.display = 'none';
        document.getElementById('modeRfid').style.display = 'block';
        document.getElementById('btnModeManual').classList.remove('btn-primary');
        document.getElementById('btnModeManual').classList.add('btn-secondary');
        document.getElementById('btnModeRfid').classList.add('btn-success');
        document.getElementById('btnModeRfid').classList.remove('btn-secondary');
        document.getElementById('rfidInput').focus();
    }
}

// RFID Scanner Handler
document.getElementById('rfidInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const rfidUid = this.value.trim();
        
        if (rfidUid) {
            scanRfid(rfidUid);
            this.value = '';
        }
    }
});

function scanRfid(rfidUid) {
    const tanggal = document.getElementById('tanggalRfid').value;
    const statusEl = document.getElementById('rfidStatus');
    
    statusEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    statusEl.style.color = 'var(--warning-color)';
    
    fetch('{{ route("admin.absensi-kegiatan.scan-rfid") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            rfid_uid: rfidUid,
            kegiatan_id: kegiatanId,
            tanggal: tanggal
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            statusEl.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
            statusEl.style.color = 'var(--success-color)';
            addLogEntry(data.data, 'success');
            playSound('success');
        } else {
            statusEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
            statusEl.style.color = 'var(--danger-color)';
            playSound('error');
        }
        
        setTimeout(() => {
            statusEl.innerHTML = '<i class="fas fa-wifi"></i> Siap Scan RFID';
            statusEl.style.color = 'var(--primary-color)';
        }, 2000);
    })
    .catch(error => {
        statusEl.innerHTML = '<i class="fas fa-times-circle"></i> Koneksi error';
        statusEl.style.color = 'var(--danger-color)';
        console.error('Error:', error);
        
        setTimeout(() => {
            statusEl.innerHTML = '<i class="fas fa-wifi"></i> Siap Scan RFID';
            statusEl.style.color = 'var(--primary-color)';
        }, 2000);
    });
}

function addLogEntry(data, type) {
    const logContent = document.getElementById('rfidLogContent');
    
    if (logContent.querySelector('p')) {
        logContent.innerHTML = '';
    }
    
    const entry = document.createElement('div');
    entry.style.cssText = 'padding: 12px; margin-bottom: 10px; border-radius: 8px; background: ' + 
        (type === 'success' ? 'linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%)' : 'linear-gradient(135deg, #FFE8EA 0%, #FFD5D8 100%)') + 
        '; border-left: 4px solid ' + (type === 'success' ? 'var(--success-color)' : 'var(--danger-color)');
    
    entry.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <strong>${data.nama}</strong> (${data.id_santri})
                <div style="font-size: 0.85rem; color: var(--text-light); margin-top: 3px;">
                    Kelas: ${data.kelas} | Waktu: ${data.waktu}
                </div>
            </div>
            <span class="badge badge-success"><i class="fas fa-check"></i> Hadir</span>
        </div>
    `;
    
    logContent.insertBefore(entry, logContent.firstChild);
}

function clearLog() {
    if (confirm('Yakin ingin membersihkan log?')) {
        document.getElementById('rfidLogContent').innerHTML = '<p style="text-align: center; color: var(--text-light);">Belum ada absensi...</p>';
    }
}

function playSound(type) {
    // Bisa ditambahkan audio feedback
    const audio = new Audio(type === 'success' ? '/sounds/success.mp3' : '/sounds/error.mp3');
    audio.play().catch(() => {}); // Ignore errors
}

// Auto-focus kembali ke input RFID jika kehilangan fokus
setInterval(() => {
    if (currentMode === 'rfid' && document.activeElement !== document.getElementById('rfidInput')) {
        document.getElementById('rfidInput').focus();
    }
}, 1000);

// Filter Kelas functionality (Manual Mode)
const filterKelasEl = document.getElementById('filterKelas');
if (filterKelasEl) {
    filterKelasEl.addEventListener('change', function() {
        const selectedKelas = this.value.toLowerCase();
        const rows = document.querySelectorAll('#modeManual tbody tr');
        
        rows.forEach(row => {
            const kelasCell = row.querySelector('td:nth-child(4)'); // Kolom kelas
            if (kelasCell) {
                const kelasText = kelasCell.textContent.toLowerCase();
                if (!selectedKelas || kelasText.includes(selectedKelas)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    });
}
</script>
@endsection