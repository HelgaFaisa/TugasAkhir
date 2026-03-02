{{-- resources/views/admin/pembayaran-spp/generate.blade.php --}}
@extends('layouts.app')

@section('title', 'Generate SPP Massal')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-cogs"></i> Generate SPP Massal</h2>
</div>

<div class="content-box">
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Informasi:</strong> Fitur ini akan membuat data pembayaran SPP untuk <strong>semua santri aktif</strong> dalam periode yang ditentukan. 
        Data yang sudah ada akan dilewati (tidak duplikat).
    </div>

    <form action="{{ route('admin.pembayaran-spp.generate') }}" method="POST" onsubmit="return confirmGenerate()">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Bulan -->
            <div class="form-group">
                <label><i class="fas fa-calendar form-icon"></i> Bulan <span style="color: red;">*</span></label>
                <select name="bulan" class="form-control @error('bulan') is-invalid @enderror" required>
                    <option value="">-- Pilih Bulan --</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ old('bulan', date('n')) == $i ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                        </option>
                    @endfor
                </select>
                @error('bulan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Tahun -->
            <div class="form-group">
                <label><i class="fas fa-calendar-alt form-icon"></i> Tahun <span style="color: red;">*</span></label>
                <input type="number" 
                       name="tahun" 
                       class="form-control @error('tahun') is-invalid @enderror" 
                       value="{{ old('tahun', date('Y')) }}" 
                       min="2020" 
                       max="2100"
                       required>
                @error('tahun')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Nominal -->
            <div class="form-group">
                <label><i class="fas fa-money-bill-wave form-icon"></i> Nominal per Santri (Rp) <span style="color: red;">*</span></label>
                <input type="number" 
                       name="nominal" 
                       id="input-nominal"
                       class="form-control @error('nominal') is-invalid @enderror" 
                       value="{{ old('nominal') }}" 
                       placeholder="Contoh: 250000"
                       min="0" 
                       step="1000"
                       required>
                @error('nominal')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text" id="nominal-helper">
                    <span id="nominal-display" style="color: var(--primary-color); font-weight: 600;">Masukkan nominal pembayaran</span>
                </small>
            </div>

            <!-- Batas Bayar -->
            <div class="form-group">
                <label><i class="fas fa-clock form-icon"></i> Batas Bayar <span style="color: red;">*</span></label>
                <input type="date" 
                       name="batas_bayar" 
                       class="form-control @error('batas_bayar') is-invalid @enderror" 
                       value="{{ old('batas_bayar', date('Y-m-10')) }}"
                       required>
                @error('batas_bayar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Preview Info Santri -->
        <div class="info-box" style="background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%); border-color: var(--success-color);">
            <p style="margin: 0; line-height: 1.8;">
                <i class="fas fa-users"></i> 
                Data SPP akan dibuat untuk <strong>{{ \App\Models\Santri::where('status', 'Aktif')->count() }} santri</strong> dengan status Aktif.<br>
                <i class="fas fa-exclamation-triangle"></i> 
                Pastikan data sudah benar sebelum melanjutkan. Proses ini tidak dapat dibatalkan.
            </p>
        </div>

        <!-- Periode Yang Sudah Di-Generate -->
        @php
            $periodeGenerated = \App\Models\PembayaranSpp::selectRaw('DISTINCT bulan, tahun')
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc')
                ->get();
        @endphp

        @if($periodeGenerated->count() > 0)
        <div class="info-box" style="background: linear-gradient(135deg, #FFF8E1 0%, #FFF3CD 100%); border-color: var(--warning-color);">
            <p style="margin: 0 0 10px 0; font-weight: 600; color: var(--text-color);">
                <i class="fas fa-info-circle"></i> Periode Yang Sudah Di-Generate:
            </p>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                @foreach($periodeGenerated as $periode)
                    @php
                        $bulanNama = DateTime::createFromFormat('!m', $periode->bulan)->format('F');
                    @endphp
                    <span style="background: white; padding: 6px 12px; border-radius: 5px; font-size: 0.9rem; border: 1px solid #ddd;">
                        {{ $bulanNama }} {{ $periode->tahun }}
                    </span>
                @endforeach
            </div>
            <p style="margin: 10px 0 0 0; font-size: 0.85rem; color: var(--text-light);">
                <i class="fas fa-lightbulb"></i> Jika Anda generate periode yang sudah ada, data akan di-skip (tidak duplikat).
            </p>
        </div>
        @endif

        <!-- Buttons -->
        <div style="display: flex; gap: 10px; margin-top: 25px;">
            <button type="submit" class="btn btn-success hover-shadow" id="btn-generate">
                <i class="fas fa-cogs"></i> Generate SPP
            </button>
            <a href="{{ route('admin.pembayaran-spp.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: white; padding: 22px; border-radius: 15px; text-align: center; max-width: 400px;">
        <div style="border: 4px solid var(--primary-light); border-top: 4px solid var(--primary-color); border-radius: 50%; width: 60px; height: 60px; animation: spin 1s linear infinite; margin: 0 auto 20px;"></div>
        <h3 style="color: var(--primary-color); margin-bottom: 10px;">Generating SPP...</h3>
        <p style="color: var(--text-light); margin: 0;">Mohon tunggu, proses sedang berjalan.</p>
    </div>
</div>

@push('styles')
<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush

@push('scripts')
<script>
// Daftar periode yang sudah di-generate
const periodeGenerated = @json($periodeGenerated->map(function($p) {
    return ['bulan' => $p->bulan, 'tahun' => $p->tahun];
}));

// Format nominal display saat user mengetik
document.getElementById('input-nominal').addEventListener('input', function() {
    const value = parseInt(this.value) || 0;
    const displayElement = document.getElementById('nominal-display');
    
    if (value > 0) {
        const formatted = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value);
        displayElement.textContent = formatted;
        displayElement.style.color = 'var(--primary-color)';
    } else {
        displayElement.textContent = 'Masukkan nominal pembayaran';
        displayElement.style.color = 'var(--text-light)';
    }
});

// Check apakah periode sudah pernah di-generate
function checkPeriode(bulan, tahun) {
    return periodeGenerated.some(p => p.bulan == bulan && p.tahun == tahun);
}

// Confirm before generate
function confirmGenerate() {
    const bulan = document.querySelector('select[name="bulan"]');
    const tahun = document.querySelector('input[name="tahun"]');
    const nominal = document.querySelector('input[name="nominal"]');
    const batasBayar = document.querySelector('input[name="batas_bayar"]');
    
    // Validasi nominal harus diisi
    if (!nominal.value || parseInt(nominal.value) <= 0) {
        alert('Nominal pembayaran harus diisi!');
        nominal.focus();
        return false;
    }
    
    const bulanValue = parseInt(bulan.value);
    const tahunValue = parseInt(tahun.value);
    const bulanText = bulan.options[bulan.selectedIndex].text;
    const nominalFormatted = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(nominal.value);
    
    const jumlahSantri = {{ \App\Models\Santri::where('status', 'Aktif')->count() }};
    const totalNominal = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(nominal.value * jumlahSantri);
    
    // Cek apakah periode sudah pernah di-generate
    const sudahDiGenerate = checkPeriode(bulanValue, tahunValue);
    let warningText = '';
    
    if (sudahDiGenerate) {
        warningText = `\nâš ï¸ PERINGATAN: Periode ${bulanText} ${tahunValue} sudah pernah di-generate!\nData yang sudah ada akan di-skip (tidak akan duplikat).\n`;
    }
    
    const message = `Anda akan generate SPP untuk semua santri aktif dengan detail:\n\n` +
                   `Periode: ${bulanText} ${tahunValue}\n` +
                   `Nominal per Santri: ${nominalFormatted}\n` +
                   `Batas Bayar: ${new Date(batasBayar.value).toLocaleDateString('id-ID')}\n` +
                   `Jumlah Santri: ${jumlahSantri} santri\n` +
                   `Total Nominal: ${totalNominal}\n` +
                   warningText +
                   `\nLanjutkan?`;
    
    if (confirm(message)) {
        // Show loading overlay
        document.getElementById('loading-overlay').style.display = 'flex';
        document.getElementById('btn-generate').disabled = true;
        return true;
    }
    return false;
}
</script>
@endpush
@endsection