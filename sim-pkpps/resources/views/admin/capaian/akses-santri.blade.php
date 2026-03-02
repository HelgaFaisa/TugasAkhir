{{-- resources/views/admin/capaian/akses-santri.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
.access-hero {
    border-radius: 14px;
    padding: 24px 28px;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 20px;
    position: relative;
    overflow: hidden;
}
.access-hero.open  { background: linear-gradient(135deg, #e8f5e9, #c8e6c9); border: 2px solid #66bb6a; }
.access-hero.closed { background: linear-gradient(135deg, #fff3e0, #ffe0b2); border: 2px solid #ffa726; }
.access-hero .ah-icon { font-size: 3.5rem; flex-shrink: 0; }
.access-hero h3 { margin: 0 0 5px; font-size: 1.15rem; }
.access-hero p  { margin: 0; font-size: 0.85rem; color: #555; }

.status-badge-big {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 8px 22px; border-radius: 25px; font-weight: 800;
    font-size: 1rem; margin-bottom: 10px;
}
.status-badge-big.open   { background: #2e7d32; color: #fff; }
.status-badge-big.closed { background: #e65100; color: #fff; }

.info-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 12px;
    margin-bottom: 18px;
}
.info-box {
    background: #fff;
    border-radius: 10px;
    padding: 14px 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border-left: 4px solid;
}
.info-box .ib-label { font-size: 0.72rem; color: #999; text-transform: uppercase; margin-bottom: 4px; }
.info-box .ib-val   { font-size: 0.92rem; font-weight: 700; color: #333; }

.form-card {
    background: #fff;
    border-radius: 12px;
    padding: 22px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    margin-bottom: 16px;
    border: 1px solid #e8f0ec;
}
.form-card h4 { margin: 0 0 16px; color: var(--primary-dark); font-size: 1rem; }

.toggle-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 28px; border-radius: 10px; border: none;
    font-weight: 700; font-size: 0.9rem; cursor: pointer;
    transition: all .2s;
}
.toggle-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,0.15); }
.toggle-btn.open-btn  { background: linear-gradient(135deg, #43a047, #2e7d32); color: #fff; }
.toggle-btn.close-btn { background: linear-gradient(135deg, #ef5350, #c62828); color: #fff; }

.countdown-bar {
    height: 10px; background: #f0f0f0;
    border-radius: 20px; overflow: hidden; margin-top: 8px;
}
.countdown-fill {
    height: 100%; border-radius: 20px;
    background: linear-gradient(90deg, #66bb6a, #2e7d32);
    transition: width .5s;
}
</style>

<div class="page-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
    <h2><i class="fas fa-unlock-alt"></i> Kelola Akses Input Capaian Santri</h2>
    <a href="{{ route('admin.capaian.index') }}" class="btn btn-secondary" style="padding:7px 16px;">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

{{-- Alert --}}
@if(session('success'))
<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif

{{-- ===== STATUS HERO ===== --}}
<div class="access-hero {{ $isOpen ? 'open' : 'closed' }}">
    <div class="ah-icon">{{ $isOpen ? '🔓' : '🔒' }}</div>
    <div style="flex:1;">
        <div class="status-badge-big {{ $isOpen ? 'open' : 'closed' }}">
            <i class="fas fa-{{ $isOpen ? 'lock-open' : 'lock' }}"></i>
            {{ $isOpen ? 'AKSES DIBUKA' : 'AKSES DITUTUP' }}
        </div>
        <h3>
            @if($isOpen)
                Santri sedang bisa menginputkan capaian mereka
            @else
                Santri belum bisa menginputkan capaian
            @endif
        </h3>
        <p>
            @if($isOpen)
                Dibuka oleh <strong>{{ $config['opened_by'] ?? '-' }}</strong>
                pada {{ $config['opened_at'] ? \Carbon\Carbon::parse($config['opened_at'])->isoFormat('D MMM YYYY, HH:mm') : '-' }}
                @if($config['id_semester'])
                    &bull; Semester: <strong>{{ \App\Models\Semester::where('id_semester', $config['id_semester'])->value('nama_semester') ?? '-' }}</strong>
                @else
                    &bull; Semua semester diizinkan
                @endif
                @if($sisaWaktu)
                    &bull; Sisa waktu: <strong>{{ $sisaWaktu }}</strong>
                @endif
            @else
                @if($config['closed_at'])
                    Ditutup pada {{ \Carbon\Carbon::parse($config['closed_at'])->isoFormat('D MMM YYYY, HH:mm') }}
                @else
                    Belum pernah dibuka
                @endif
            @endif
        </p>
        @if(!empty($config['catatan']))
        <p style="margin-top:6px;font-style:italic;"><i class="fas fa-sticky-note"></i> "{{ $config['catatan'] }}"</p>
        @endif
    </div>
</div>

{{-- ===== INFO STATS ===== --}}
<div class="info-row">
    <div class="info-box" style="border-color:#66bb6a;">
        <div class="ib-label">Status</div>
        <div class="ib-val" style="color:{{ $isOpen ? '#2e7d32' : '#c62828' }};">
            <i class="fas fa-{{ $isOpen ? 'check-circle' : 'times-circle' }}"></i>
            {{ $isOpen ? 'Terbuka' : 'Tertutup' }}
        </div>
    </div>
    <div class="info-box" style="border-color:#81C6E8;">
        <div class="ib-label">Dibuka Oleh</div>
        <div class="ib-val">{{ $config['opened_by'] ?? '-' }}</div>
    </div>
    <div class="info-box" style="border-color:#FFD56B;">
        <div class="ib-label">Semester</div>
        <div class="ib-val">
            @if($config['id_semester'])
                {{ \App\Models\Semester::where('id_semester', $config['id_semester'])->value('nama_semester') ?? '-' }}
            @else
                Semua Semester
            @endif
        </div>
    </div>
    <div class="info-box" style="border-color:#B39DDB;">
        <div class="ib-label">Auto-Close</div>
        <div class="ib-val">
            @if($config['auto_close_at'])
                {{ \Carbon\Carbon::parse($config['auto_close_at'])->isoFormat('D MMM HH:mm') }}
            @else
                Manual
            @endif
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">

{{-- ===== FORM BUKA AKSES ===== --}}
<div class="form-card">
    <h4 style="color:#2e7d32;"><i class="fas fa-lock-open"></i> Buka Akses Input Capaian</h4>

    <form method="POST" action="{{ route('admin.capaian.akses-santri.buka') }}">
        @csrf

        <div class="form-group" style="margin-bottom:12px;">
            <label style="font-size:.83rem;font-weight:600;color:#555;display:block;margin-bottom:4px;">
                <i class="fas fa-calendar-alt"></i> Semester yang Dibuka
            </label>
            <select name="id_semester" class="form-control" style="font-size:.84rem;">
                <option value="">-- Semua Semester --</option>
                @foreach($semesters as $sem)
                    <option value="{{ $sem->id_semester }}"
                        {{ ($semesterAktif && $sem->id_semester == $semesterAktif->id_semester) ? 'selected' : '' }}>
                        {{ $sem->nama_semester }}{{ $sem->is_active ? ' ★ (Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
            <small style="color:#999;font-size:.74rem;">Kosongkan = santri bisa input di semua semester</small>
        </div>

        <div class="form-group" style="margin-bottom:12px;">
            <label style="font-size:.83rem;font-weight:600;color:#555;display:block;margin-bottom:4px;">
                <i class="fas fa-clock"></i> Durasi Otomatis Tutup (opsional)
            </label>
            <div style="display:flex;align-items:center;gap:8px;">
                <input type="number" name="durasi_jam" min="1" max="720"
                       class="form-control" style="width:110px;font-size:.84rem;"
                       placeholder="cth: 24">
                <span style="font-size:.84rem;color:#555;">jam</span>
            </div>
            <small style="color:#999;font-size:.74rem;">Kosongkan = harus ditutup manual oleh admin</small>
        </div>

        <div class="form-group" style="margin-bottom:16px;">
            <label style="font-size:.83rem;font-weight:600;color:#555;display:block;margin-bottom:4px;">
                <i class="fas fa-sticky-note"></i> Catatan untuk Santri (opsional)
            </label>
            <input type="text" name="catatan" class="form-control" style="font-size:.84rem;"
                   placeholder="cth: Deadline input: Jumat 17.00 WIB" maxlength="255">
        </div>

        <button type="submit" class="toggle-btn open-btn">
            <i class="fas fa-lock-open"></i> Buka Akses Sekarang
        </button>
    </form>
</div>

{{-- ===== TUTUP AKSES ===== --}}
<div class="form-card">
    <h4 style="color:#c62828;"><i class="fas fa-lock"></i> Tutup Akses Input Capaian</h4>

    @if($isOpen)
    <div style="background:#fbe9e7;border-radius:9px;padding:14px;margin-bottom:16px;">
        <p style="margin:0;font-size:.84rem;color:#555;">
            <i class="fas fa-info-circle" style="color:#ef5350;"></i>
            Saat ini akses <strong>sedang dibuka</strong>. Klik tombol di bawah untuk menutup akses input capaian santri segera.
        </p>
        @if($sisaWaktu)
        <p style="margin:8px 0 0;font-size:.8rem;color:#777;">
            <i class="fas fa-hourglass-half"></i> Sisa waktu auto-close: <strong>{{ $sisaWaktu }}</strong>
        </p>
        @endif
    </div>

    <form method="POST" action="{{ route('admin.capaian.akses-santri.tutup') }}"
          onsubmit="return confirm('Yakin ingin menutup akses input capaian santri?')">
        @csrf
        <button type="submit" class="toggle-btn close-btn">
            <i class="fas fa-lock"></i> Tutup Akses Sekarang
        </button>
    </form>
    @else
    <div style="background:#f5f5f5;border-radius:9px;padding:14px;text-align:center;color:#aaa;">
        <i class="fas fa-lock" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
        Akses saat ini sudah tertutup.<br>
        <span style="font-size:.8rem;">Gunakan form di sebelah kiri untuk membuka akses.</span>
    </div>
    @endif
</div>

</div>

{{-- ===== PANDUAN ===== --}}
<div class="form-card" style="background:#f0f9ff;border:1px solid #b3e0ff;">
    <h4 style="color:#0277bd;"><i class="fas fa-info-circle"></i> Alur Penggunaan</h4>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;font-size:.83rem;color:#555;">
        <div style="display:flex;gap:10px;">
            <div style="background:#1565c0;color:#fff;width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;flex-shrink:0;">1</div>
            <div><strong>Admin membuka akses</strong><br>Pilih semester & opsional durasi waktu lalu klik "Buka Akses".</div>
        </div>
        <div style="display:flex;gap:10px;">
            <div style="background:#2e7d32;color:#fff;width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;flex-shrink:0;">2</div>
            <div><strong>Santri input capaian</strong><br>Santri login ke web-nya dan bisa input capaian sesuai materi kelasnya.</div>
        </div>
        <div style="display:flex;gap:10px;">
            <div style="background:#e65100;color:#fff;width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;flex-shrink:0;">3</div>
            <div><strong>Data langsung masuk</strong><br>Data capaian santri langsung terlihat di dashboard admin & riwayat santri.</div>
        </div>
        <div style="display:flex;gap:10px;">
            <div style="background:#6a1b9a;color:#fff;width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;flex-shrink:0;">4</div>
            <div><strong>Admin menutup akses</strong><br>Setelah selesai, tutup akses manual atau biarkan auto-close berjalan.</div>
        </div>
    </div>
</div>
@endsection