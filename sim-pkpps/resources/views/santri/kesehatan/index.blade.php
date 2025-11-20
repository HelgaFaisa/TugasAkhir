@extends('layouts.app')

@section('title', 'Riwayat Kesehatan')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-heartbeat"></i> Riwayat Kesehatan</h2>
    <p style="margin: 5px 0 0 0; color: var(--text-light);">
        Riwayat kunjungan UKP <strong>{{ $santri->nama_lengkap }}</strong>
    </p>
</div>

{{-- ✅ DISPLAY ERROR VALIDATION --}}
@if($errors->any())
<div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>Error:</strong> {{ $errors->first() }}
</div>
@endif

{{-- ✅ STATISTIK CARDS (BERDASARKAN FILTER) --}}
<div class="row-cards">
    <div class="card card-info">
        <h3><i class="fas fa-notes-medical"></i> Total Kunjungan</h3>
        <div class="card-value">{{ $statistik['total_kunjungan'] }}</div>
        <div class="card-icon"><i class="fas fa-notes-medical"></i></div>
        <p style="margin-top: 10px; font-size: 0.85rem; color: var(--text-light);">
            Periode yang dipilih
        </p>
    </div>
    
    <div class="card card-danger">
        <h3><i class="fas fa-procedures"></i> Sedang Dirawat</h3>
        <div class="card-value">{{ $statistik['sedang_dirawat'] }}</div>
        <div class="card-icon"><i class="fas fa-procedures"></i></div>
        @if($statistik['sedang_dirawat'] > 0)
            <p style="margin-top: 10px; font-size: 0.85rem; color: var(--danger-color);">
                <i class="fas fa-exclamation-circle"></i> Perlu perhatian
            </p>
        @else
            <p style="margin-top: 10px; font-size: 0.85rem; color: var(--text-light);">
                Tidak ada yang dirawat
            </p>
        @endif
    </div>
    
    <div class="card card-success">
        <h3><i class="fas fa-check-circle"></i> Sembuh</h3>
        <div class="card-value">{{ $statistik['sembuh'] }}</div>
        <div class="card-icon"><i class="fas fa-check-circle"></i></div>
        <p style="margin-top: 10px; font-size: 0.85rem; color: var(--text-light);">
            Alhamdulillah
        </p>
    </div>
    
    <div class="card card-warning">
        <h3><i class="fas fa-home"></i> Izin Sakit</h3>
        <div class="card-value">{{ $statistik['izin'] }}</div>
        <div class="card-icon"><i class="fas fa-home"></i></div>
        <p style="margin-top: 10px; font-size: 0.85rem; color: var(--text-light);">
            Izin pulang
        </p>
    </div>
</div>

{{-- ✅ FILTER TANGGAL (DI ATAS CARDS) --}}
<div class="content-box" style="margin-bottom: 20px;">
    <form method="GET" action="{{ route('santri.kesehatan.index') }}" id="filterForm">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
            {{-- Tanggal Dari --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label style="margin-bottom: 8px; display: block;">
                    <i class="fas fa-calendar-alt form-icon"></i> Tanggal Dari
                </label>
                <input type="date" 
                       name="tanggal_dari" 
                       class="form-control" 
                       value="{{ $tanggalDari->format('Y-m-d') }}"
                       max="{{ date('Y-m-d') }}">
            </div>
            
            {{-- Tanggal Sampai --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label style="margin-bottom: 8px; display: block;">
                    <i class="fas fa-calendar-check form-icon"></i> Tanggal Sampai
                </label>
                <input type="date" 
                       name="tanggal_sampai" 
                       class="form-control" 
                       value="{{ $tanggalSampai->format('Y-m-d') }}"
                       max="{{ date('Y-m-d') }}">
            </div>
            
            {{-- Status Filter --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label style="margin-bottom: 8px; display: block;">
                    <i class="fas fa-filter form-icon"></i> Status
                </label>
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            {{-- Buttons --}}
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary hover-lift" style="flex: 1;">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('santri.kesehatan.index') }}" class="btn btn-secondary hover-lift" style="flex: 1;">
                    <i class="fas fa-sync"></i> Reset
                </a>
            </div>
        </div>
    </form>
    
    {{-- Info Periode --}}
    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--primary-light);">
        <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">
            <i class="fas fa-info-circle"></i> 
            Menampilkan data periode: 
            <strong style="color: var(--primary-color);">
                {{ $tanggalDari->locale('id')->isoFormat('D MMMM Y') }} - {{ $tanggalSampai->locale('id')->isoFormat('D MMMM Y') }}
            </strong>
            ({{ $tanggalDari->diffInDays($tanggalSampai) + 1 }} hari)
        </p>
    </div>
</div>

{{-- Riwayat Kesehatan --}}
@if($riwayatKesehatan->isEmpty())
    <div class="empty-state" style="margin-top: 20px;">
        <i class="fas fa-notes-medical"></i>
        <h3>Tidak Ada Data</h3>
        <p>Tidak ada riwayat kesehatan pada periode yang dipilih.</p>
        <a href="{{ route('santri.kesehatan.index') }}" class="btn btn-primary" style="margin-top: 15px;">
            <i class="fas fa-sync"></i> Lihat Semua Data
        </a>
    </div>
@else
    <div class="content-box" style="margin-top: 20px;">
        <h3 style="margin: 0 0 15px 0; color: var(--primary-color);">
            <i class="fas fa-list"></i> Daftar Riwayat ({{ $riwayatKesehatan->total() }} data)
        </h3>
        
        <div style="display: flex; flex-direction: column; gap: 15px;">
            @foreach($riwayatKesehatan as $item)
            <a href="{{ route('santri.kesehatan.show', $item->id) }}" 
               style="display: flex; gap: 15px; padding: 15px; background: linear-gradient(135deg, #FFFFFF 0%, #FEFFFE 100%); border-radius: var(--border-radius-sm); border-left: 4px solid 
               @if($item->status == 'dirawat') var(--danger-color)
               @elseif($item->status == 'sembuh') var(--success-color)
               @else var(--warning-color) @endif
               ; text-decoration: none; transition: var(--transition-base); position: relative;"
               onmouseover="this.style.boxShadow='var(--shadow-md)'; this.style.transform='translateX(5px)';"
               onmouseout="this.style.boxShadow='none'; this.style.transform='translateX(0)';">
                
                {{-- Icon Status --}}
                <div style="flex-shrink: 0; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: 
                    @if($item->status == 'dirawat') linear-gradient(135deg, #FFE8EA, #FFD5D8)
                    @elseif($item->status == 'sembuh') linear-gradient(135deg, #E8F7F2, #D4F1E3)
                    @else linear-gradient(135deg, #FFF8E1, #FFF3CD) @endif
                    ;">
                    <i class="fas 
                        @if($item->status == 'dirawat') fa-procedures
                        @elseif($item->status == 'sembuh') fa-check-circle
                        @else fa-home @endif
                        " style="font-size: 1.8rem; color: 
                        @if($item->status == 'dirawat') var(--danger-color)
                        @elseif($item->status == 'sembuh') var(--success-color)
                        @else var(--warning-color) @endif
                        ;"></i>
                </div>
                
                {{-- Konten --}}
                <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                            <h3 style="margin: 0; font-size: 1rem; font-weight: 600; color: var(--text-color);">
                                {{ $item->keluhan }}
                            </h3>
                            <span class="badge badge-{{ $item->status_badge_color }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </div>
                        
                        <p style="margin: 0 0 8px 0; font-size: 0.9rem; color: var(--text-light);">
                            <i class="fas fa-code"></i> {{ $item->id_kesehatan }}
                        </p>
                    </div>
                    
                    <div style="display: flex; flex-wrap: wrap; gap: 15px; font-size: 0.85rem; color: var(--text-light);">
                        <span>
                            <i class="fas fa-calendar-plus"></i> Masuk: {{ $item->tanggal_masuk_formatted }}
                        </span>
                        @if($item->tanggal_keluar)
                            <span>
                                <i class="fas fa-calendar-check"></i> Keluar: {{ $item->tanggal_keluar_formatted }}
                            </span>
                            <span class="badge badge-info badge-sm">
                                <i class="fas fa-clock"></i> {{ $item->lama_dirawat }} hari
                            </span>
                        @else
                            <span class="badge badge-danger badge-sm">
                                <i class="fas fa-procedures"></i> Masih dirawat ({{ $item->lama_dirawat }} hari)
                            </span>
                        @endif
                    </div>
                </div>
                
                {{-- Arrow --}}
                <div style="flex-shrink: 0; display: flex; align-items: center;">
                    <i class="fas fa-chevron-right" style="color: var(--text-light);"></i>
                </div>
            </a>
            @endforeach
        </div>
        
        {{-- Pagination --}}
        <div style="margin-top: 25px;">
            {{ $riwayatKesehatan->links() }}
        </div>
    </div>
@endif

{{-- Info Box --}}
<div class="info-box" style="margin-top: 20px;">
    <i class="fas fa-info-circle"></i>
    <strong>Info:</strong> Gunakan filter tanggal untuk melihat riwayat kesehatan pada periode tertentu. 
    Jika tidak difilter, data yang ditampilkan adalah untuk bulan berjalan.
</div>

{{-- Quick Actions --}}
<div style="margin-top: 20px; text-align: center;">
    <a href="{{ route('santri.dashboard') }}" class="btn btn-secondary hover-lift">
        <i class="fas fa-home"></i> Kembali ke Dashboard
    </a>
</div>

{{-- ✅ JAVASCRIPT UNTUK AUTO SUBMIT SAAT TANGGAL BERUBAH (OPTIONAL) --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tanggalDari = document.querySelector('input[name="tanggal_dari"]');
    const tanggalSampai = document.querySelector('input[name="tanggal_sampai"]');
    
    // Validasi tanggal
    tanggalDari.addEventListener('change', function() {
        if (tanggalSampai.value && tanggalSampai.value < this.value) {
            alert('Tanggal sampai tidak boleh lebih kecil dari tanggal dari!');
            this.value = tanggalSampai.value;
        }
    });
    
    tanggalSampai.addEventListener('change', function() {
        if (tanggalDari.value && this.value < tanggalDari.value) {
            alert('Tanggal sampai tidak boleh lebih kecil dari tanggal dari!');
            this.value = tanggalDari.value;
        }
    });
});
</script>
@endsection