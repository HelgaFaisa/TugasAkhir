@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-table"></i> Rekap Capaian per Kelas</h2>
</div>

{{-- Filter Section --}}
<div class="content-box" style="margin-bottom: 20px;">
    <form method="GET" action="{{ route('admin.capaian.rekap-kelas') }}" class="filter-form-inline">
        <select name="kelas" class="form-control" style="width: 200px;">
            <option value="Lambatan" {{ $kelas == 'Lambatan' ? 'selected' : '' }}>Kelas Lambatan</option>
            <option value="Cepatan" {{ $kelas == 'Cepatan' ? 'selected' : '' }}>Kelas Cepatan</option>
            <option value="PB" {{ $kelas == 'PB' ? 'selected' : '' }}>Kelas PB</option>
        </select>

        <select name="id_semester" class="form-control" style="width: 250px;">
            <option value="">Semua Semester</option>
            @foreach($semesters as $semester)
                <option value="{{ $semester->id_semester }}" {{ $selectedSemester == $semester->id_semester ? 'selected' : '' }}>
                    {{ $semester->nama_semester }} @if($semester->is_active) ★ @endif
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i> Tampilkan
        </button>

        <button type="button" class="btn btn-success" onclick="exportToExcel()">
            <i class="fas fa-file-excel"></i> Export Excel
        </button>
    </form>
</div>

{{-- Info Box --}}
<div class="info-box" style="margin-bottom: 20px;">
    <i class="fas fa-info-circle"></i>
    <strong>Kelas: {{ $kelas }}</strong> | 
    Total Santri: <strong>{{ count($rekapData) }}</strong> santri
    @if($selectedSemester)
        | Semester: <strong>{{ $semesters->where('id_semester', $selectedSemester)->first()->nama_semester ?? 'Semua' }}</strong>
    @endif
</div>

{{-- Rekap Table --}}
<div class="content-box">
    @if(count($rekapData) > 0)
        <table class="data-table" id="tableRekap">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 5%; vertical-align: middle;">Rank</th>
                    <th rowspan="2" style="width: 10%; vertical-align: middle;">NIS</th>
                    <th rowspan="2" style="width: 20%; vertical-align: middle;">Nama Santri</th>
                    <th rowspan="2" style="width: 10%; vertical-align: middle;">Total Materi</th>
                    <th colspan="3" class="text-center" style="background: linear-gradient(135deg, #E8F7F2, #D4F1E3);">Progress per Kategori (%)</th>
                    <th rowspan="2" style="width: 10%; vertical-align: middle;">Rata-rata</th>
                    <th rowspan="2" style="width: 10%; vertical-align: middle; text-center">Selesai</th>
                </tr>
                <tr>
                    <th class="text-center" style="width: 10%; background: rgba(111, 186, 157, 0.1);">Al-Qur'an</th>
                    <th class="text-center" style="width: 10%; background: rgba(129, 198, 232, 0.1);">Hadist</th>
                    <th class="text-center" style="width: 10%; background: rgba(255, 213, 107, 0.1);">Tambahan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekapData as $index => $data)
                    <tr>
                        <td class="text-center">
                            @if($index < 3)
                                <span style="font-size: 1.3rem;">
                                    @if($index == 0) 🥇
                                    @elseif($index == 1) 🥈
                                    @else 🥉
                                    @endif
                                </span>
                            @else
                                <strong>{{ $index + 1 }}</strong>
                            @endif
                        </td>
                        <td>{{ $data['santri']->nis }}</td>
                        <td>
                            <strong>{{ $data['santri']->nama_lengkap }}</strong>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info">{{ $data['total_materi'] }} materi</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-primary">{{ number_format($data['alquran'], 1) }}%</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-success">{{ number_format($data['hadist'], 1) }}%</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-warning">{{ number_format($data['tambahan'], 1) }}%</span>
                        </td>
                        <td>
                            <div class="progress-bar" style="height: 25px;">
                                <div class="progress-fill" 
                                     style="width: {{ $data['rata_rata'] }}%; 
                                            background: linear-gradient(90deg, 
                                                {{ $data['rata_rata'] >= 75 ? 'var(--success-color), var(--primary-color)' : ($data['rata_rata'] >= 50 ? 'var(--warning-color), var(--accent-peach)' : 'var(--danger-color), var(--secondary-color)') }}); 
                                            display: flex; 
                                            align-items: center; 
                                            justify-content: center; 
                                            color: white; 
                                            font-weight: bold;">
                                    {{ number_format($data['rata_rata'], 1) }}%
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-{{ $data['selesai'] > 0 ? 'success' : 'secondary' }}">
                                {{ $data['selesai'] }} / {{ $data['total_materi'] }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Summary Statistics --}}
        <div style="margin-top: 30px; padding: 20px; background: linear-gradient(135deg, #E8F7F2, #D4F1E3); border-radius: 12px;">
            <h4 style="margin: 0 0 15px 0; color: var(--primary-dark);">
                <i class="fas fa-chart-bar"></i> Statistik Kelas {{ $kelas }}
            </h4>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
                <div style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                    <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Rata-rata Kelas</p>
                    <h3 style="margin: 5px 0; color: var(--primary-color);">
                        {{ number_format(collect($rekapData)->avg('rata_rata'), 1) }}%
                    </h3>
                </div>
                <div style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                    <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Progress Tertinggi</p>
                    <h3 style="margin: 5px 0; color: var(--success-color);">
                        {{ number_format(collect($rekapData)->max('rata_rata'), 1) }}%
                    </h3>
                </div>
                <div style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                    <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Progress Terendah</p>
                    <h3 style="margin: 5px 0; color: var(--danger-color);">
                        {{ number_format(collect($rekapData)->min('rata_rata'), 1) }}%
                    </h3>
                </div>
                <div style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                    <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Total Selesai</p>
                    <h3 style="margin: 5px 0; color: var(--info-color);">
                        {{ collect($rekapData)->sum('selesai') }} materi
                    </h3>
                </div>
            </div>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <h3>Tidak Ada Data</h3>
            <p>Belum ada santri di kelas {{ $kelas }} atau belum ada capaian yang tercatat.</p>
        </div>
    @endif
</div>

<script>
function exportToExcel() {
    // Simple export to CSV
    let csv = [];
    const rows = document.querySelectorAll('#tableRekap tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll('td, th');
        for (let j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }
        csv.push(row.join(','));
    }
    
    const csvFile = new Blob([csv.join('\n')], {type: 'text/csv'});
    const downloadLink = document.createElement('a');
    downloadLink.download = 'rekap_kelas_{{ $kelas }}_{{ date("Y-m-d") }}.csv';
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>
@endsection