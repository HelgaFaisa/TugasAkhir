{{-- resources/views/santri/kesehatan/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Riwayat Kesehatan')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-file-medical"></i> Detail Riwayat Kesehatan</h2>
</div>

<div class="content-box">
    <div class="detail-header">
        <div>
            <h3 style="margin: 0;">Detail Kunjungan UKP</h3>
            <p style="margin: 4px 0 0 0; color: var(--text-light); font-size: 0.88rem;">
                ID: <strong>{{ $kesehatanSantri->id_kesehatan }}</strong>
                &bull; Dicatat: {{ $kesehatanSantri->created_at->locale('id')->isoFormat('D MMM Y') }}
            </p>
        </div>
        <a href="{{ route('santri.kesehatan.index') }}" class="btn btn-secondary btn-sm hover-lift">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <hr style="border: none; border-top: 2px solid var(--primary-light); margin: 20px 0;">

    {{-- ── STATUS BANNER ── --}}
    @php
        $statusColor = match($kesehatanSantri->status) { 'dirawat' => '#E74C3C', 'sembuh' => '#6FBA9D', default => '#F39C12' };
        $statusBg    = match($kesehatanSantri->status) { 'dirawat' => 'linear-gradient(135deg,#FFE8EA,#FFD5D8)', 'sembuh' => 'linear-gradient(135deg,#E8F7F2,#D4F1E3)', default => 'linear-gradient(135deg,#FFF8E1,#FFF3CD)' };
        $statusIcon  = match($kesehatanSantri->status) { 'dirawat' => 'fa-procedures', 'sembuh' => 'fa-check-circle', default => 'fa-home' };
        $statusLabel = match($kesehatanSantri->status) { 'dirawat' => 'Sedang Dirawat', 'sembuh' => 'Sudah Sembuh', default => 'Izin Pulang' };
    @endphp

    <div style="background: {{ $statusBg }}; border-radius: 14px; border-left: 5px solid {{ $statusColor }}; padding: 20px; margin-bottom: 22px; display: flex; align-items: center; gap: 18px;">
        <div style="width: 64px; height: 64px; background: {{ $statusColor }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 14px rgba(0,0,0,0.15);">
            <i class="fas {{ $statusIcon }}" style="font-size: 1.8rem; color: white;"></i>
        </div>
        <div style="flex: 1;">
            <p style="margin: 0; font-size: 0.78rem; color: var(--text-light); font-weight: 700; letter-spacing: 0.5px;">STATUS KESEHATAN</p>
            <p style="margin: 2px 0 0 0; font-size: 1.5rem; font-weight: 700; color: {{ $statusColor }};">{{ $statusLabel }}</p>
        </div>
        <div style="text-align: center; background: rgba(255,255,255,0.75); padding: 12px 20px; border-radius: 10px; flex-shrink: 0;">
            <p style="margin: 0; font-size: 0.72rem; color: var(--text-light);">Lama Dirawat</p>
            <p style="margin: 0; font-size: 2.2rem; font-weight: 700; color: {{ $statusColor }}; line-height: 1.1;">{{ $kesehatanSantri->lama_dirawat }}</p>
            <p style="margin: 0; font-size: 0.72rem; color: var(--text-light);">hari</p>
        </div>
    </div>

    {{-- ── TIMELINE PERAWATAN ── --}}
    <div class="detail-section">
        <h4><i class="fas fa-calendar-alt"></i> Timeline Perawatan</h4>

        <div style="position: relative; padding-left: 38px; margin: 18px 0 4px;">
            {{-- Garis timeline --}}
            <div style="position: absolute; left: 12px; top: 14px; bottom: 14px; width: 3px; background: linear-gradient(180deg, #6FBA9D 0%, {{ $kesehatanSantri->tanggal_keluar ? $statusColor : '#ddd' }} 100%); border-radius: 2px;"></div>

            {{-- MASUK --}}
            <div style="position: relative; margin-bottom: 20px;">
                <div style="position: absolute; left: -31px; width: 22px; height: 22px; background: #6FBA9D; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(111,186,157,0.5); top: 50%; transform: translateY(-50%);"></div>
                <div style="background: linear-gradient(135deg, #E8F7F2, #F4FAF8); padding: 14px 16px; border-radius: 10px; border-left: 4px solid #6FBA9D;">
                    <p style="margin: 0 0 2px 0; font-size: 0.72rem; font-weight: 700; color: var(--text-light); letter-spacing: 0.5px;">MASUK UKP</p>
                    <p style="margin: 0; font-size: 1.2rem; font-weight: 700; color: #2C5F4F;">
                        {{ $kesehatanSantri->tanggal_masuk->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    </p>
                    <p style="margin: 3px 0 0 0; font-size: 0.82rem; color: var(--text-light);">
                        <i class="fas fa-clock"></i> {{ $kesehatanSantri->tanggal_masuk->diffForHumans() }}
                    </p>
                </div>
            </div>

            {{-- KELUAR --}}
            <div style="position: relative;">
                <div style="position: absolute; left: -31px; width: 22px; height: 22px; background: {{ $kesehatanSantri->tanggal_keluar ? $statusColor : '#ccc' }}; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.1); top: 50%; transform: translateY(-50%);"></div>
                @if($kesehatanSantri->tanggal_keluar)
                    <div style="background: {{ $statusBg }}; padding: 14px 16px; border-radius: 10px; border-left: 4px solid {{ $statusColor }};">
                        <p style="margin: 0 0 2px 0; font-size: 0.72rem; font-weight: 700; color: var(--text-light); letter-spacing: 0.5px;">KELUAR UKP</p>
                        <p style="margin: 0; font-size: 1.2rem; font-weight: 700; color: {{ $statusColor }};">
                            {{ $kesehatanSantri->tanggal_keluar->locale('id')->isoFormat('dddd, D MMMM Y') }}
                        </p>
                        <p style="margin: 3px 0 0 0; font-size: 0.82rem; color: var(--text-light);">
                            <i class="fas fa-clock"></i> {{ $kesehatanSantri->tanggal_keluar->diffForHumans() }}
                        </p>
                    </div>
                @else
                    <div style="background: #F5F5F5; padding: 14px 16px; border-radius: 10px; border-left: 4px solid #ccc;">
                        <p style="margin: 0 0 2px 0; font-size: 0.72rem; font-weight: 700; color: var(--text-light); letter-spacing: 0.5px;">KELUAR UKP</p>
                        <p style="margin: 0; font-size: 1rem; color: #E74C3C; font-weight: 600;">
                            <i class="fas fa-hourglass-half"></i> Belum Keluar — Masih Dalam Perawatan
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── INFORMASI MEDIS ── --}}
    <div class="detail-section">
        <h4><i class="fas fa-notes-medical"></i> Informasi Medis</h4>
        <table class="detail-table">
            <tr>
                <th width="180"><i class="fas fa-stethoscope"></i> Keluhan</th>
                <td><strong style="color: var(--danger-color);">{{ $kesehatanSantri->keluhan }}</strong></td>
            </tr>
            @if($kesehatanSantri->catatan)
            <tr>
                <th><i class="fas fa-clipboard-list"></i> Catatan Petugas</th>
                <td style="white-space: pre-wrap;">{{ $kesehatanSantri->catatan }}</td>
            </tr>
            @endif
            <tr>
                <th><i class="fas fa-clock"></i> Lama Dirawat</th>
                <td>
                    <span class="badge badge-info" style="font-size: 0.9rem; padding: 5px 10px;">
                        {{ $kesehatanSantri->lama_dirawat }} hari
                    </span>
                    @if($kesehatanSantri->status === 'dirawat')
                        <span class="badge badge-danger badge-sm" style="margin-left: 6px;">Masih berjalan</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- ── INFO BOX STATUS ── --}}
    @if($kesehatanSantri->status === 'dirawat')
        <div style="background: linear-gradient(135deg,#FFE8EA,#FFD5D8); padding: 14px 16px; border-radius: 10px; border-left: 4px solid #E74C3C; margin-bottom: 14px;">
            <p style="margin: 0; color: #C0392B; font-weight: 600;">
                <i class="fas fa-exclamation-triangle"></i>
                Kamu masih dalam perawatan UKP. Ikuti instruksi petugas kesehatan dan istirahat yang cukup.
            </p>
        </div>
    @elseif($kesehatanSantri->status === 'sembuh')
        <div style="background: linear-gradient(135deg,#E8F7F2,#D4F1E3); padding: 14px 16px; border-radius: 10px; border-left: 4px solid #6FBA9D; margin-bottom: 14px;">
            <p style="margin: 0; color: #2C5F4F; font-weight: 600;">
                <i class="fas fa-check-circle"></i>
                Alhamdulillah, kamu sudah sembuh! Jaga pola hidup sehat agar tidak sakit lagi.
            </p>
        </div>
    @elseif($kesehatanSantri->status === 'izin')
        <div style="background: linear-gradient(135deg,#FFF8E1,#FFF3CD); padding: 14px 16px; border-radius: 10px; border-left: 4px solid #F39C12; margin-bottom: 14px;">
            <p style="margin: 0; color: #7D5A00; font-weight: 600;">
                <i class="fas fa-home"></i>
                Kamu mendapat izin pulang untuk pemulihan. Semoga cepat sehat!
            </p>
        </div>
    @endif

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('santri.kesehatan.index') }}" class="btn btn-primary hover-lift">
            <i class="fas fa-list"></i> Lihat Semua Riwayat
        </a>
    </div>
</div>

{{-- ── RIWAYAT LAIN ── --}}
@if($riwayatLain->count() > 0)
<div class="content-box" style="margin-top: 14px;">
    <h4 style="color: var(--primary-color); margin: 0 0 14px 0;">
        <i class="fas fa-history"></i> Riwayat Kesehatan Lainnya
    </h4>
    <div style="display: flex; flex-direction: column; gap: 9px;">
        @foreach($riwayatLain as $item)
        @php
            $bc = match($item->status) { 'dirawat' => '#E74C3C', 'sembuh' => '#6FBA9D', default => '#F39C12' };
        @endphp
        <a href="{{ route('santri.kesehatan.show', $item->id) }}"
           style="display:flex; align-items:center; gap:12px; padding:12px 14px; background:white; border-radius:8px; border-left:3px solid {{ $bc }}; text-decoration:none; box-shadow:0 1px 4px rgba(0,0,0,0.06); transition:background 0.15s;"
           onmouseover="this.style.background='#F8FBF9';"
           onmouseout="this.style.background='white';">
            <span class="badge badge-{{ $item->status_badge_color }}" style="flex-shrink:0;">{{ ucfirst($item->status) }}</span>
            <span style="flex:1; color:var(--text-color); font-size:0.9rem;">{{ Str::limit($item->keluhan, 55) }}</span>
            <span style="font-size:0.78rem; color:var(--text-light); flex-shrink:0;">{{ $item->tanggal_masuk_formatted }}</span>
            <i class="fas fa-chevron-right" style="color:var(--text-light); font-size:0.75rem; flex-shrink:0;"></i>
        </a>
        @endforeach
    </div>
</div>
@endif
@endsection