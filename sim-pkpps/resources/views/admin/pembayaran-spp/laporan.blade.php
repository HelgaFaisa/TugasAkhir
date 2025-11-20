{{-- resources/views/admin/pembayaran-spp/laporan.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Pembayaran SPP')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-file-pdf"></i> Laporan Pembayaran SPP</h2>
</div>

<div class="row-cards">
    <!-- Laporan Semua Data -->
    <div class="card card-primary hover-lift">
        <h3>Laporan Semua Data</h3>
        <p style="margin: 10px 0; color: var(--text-light);">
            Cetak laporan semua pembayaran SPP atau dengan filter tertentu
        </p>
        <form action="{{ route('admin.pembayaran-spp.cetak-laporan') }}" method="GET" target="_blank">
            <div class="form-group">
                <label><i class="fas fa-calendar form-icon"></i> Bulan (Opsional)</label>
                <select name="bulan" class="form-control">
                    <option value="">Semua Bulan</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}">{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                    @endfor
                </select>
            </div>

            <div class="form-group">
                <label><i class="fas fa-calendar-alt form-icon"></i> Tahun (Opsional)</label>
                <select name="tahun" class="form-control">
                    <option value="">Semua Tahun</option>
                    @for($year = date('Y'); $year >= 2020; $year--)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
            </div>

            <div class="form-group">
                <label><i class="fas fa-info-circle form-icon"></i> Status (Opsional)</label>
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="Lunas">Lunas</option>
                    <option value="Belum Lunas">Belum Lunas</option>
                    <option value="Telat">Telat</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary hover-shadow" style="width: 100%;">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
        </form>
    </div>

    <!-- Laporan Per Santri -->
    <div class="card card-success hover-lift">
        <h3>Laporan Per Santri</h3>
        <p style="margin: 10px 0; color: var(--text-light);">
            Cetak laporan pembayaran SPP untuk santri tertentu
        </p>
        <form action="{{ route('admin.pembayaran-spp.cetak-laporan-santri', ':id') }}" method="GET" id="form-santri" target="_blank">
            <div class="form-group">
                <label><i class="fas fa-user form-icon"></i> Pilih Santri</label>
                <select name="id_santri" id="select-santri" class="form-control" required>
                    <option value="">-- Pilih Santri --</option>
                    @foreach(\App\Models\Santri::orderBy('nama_lengkap')->get() as $santri)
                        <option value="{{ $santri->id_santri }}">
                            {{ $santri->nama_lengkap }} ({{ $santri->id_santri }})
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success hover-shadow" style="width: 100%;">
                <i class="fas fa-print"></i> Cetak Laporan Santri
            </button>
        </form>
    </div>
</div>

<div class="content-box" style="margin-top: 25px;">
    <a href="{{ route('admin.pembayaran-spp.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

@push('scripts')
<script>
// Update form action saat santri dipilih
document.getElementById('select-santri').addEventListener('change', function() {
    const form = document.getElementById('form-santri');
    const selectedId = this.value;
    if (selectedId) {
        form.action = "{{ route('admin.pembayaran-spp.cetak-laporan-santri', ':id') }}".replace(':id', selectedId);
    }
});
</script>
@endpush
@endsection