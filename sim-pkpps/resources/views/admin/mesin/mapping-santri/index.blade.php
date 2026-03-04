{{-- resources/views/admin/mesin/mapping-santri/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Mapping ID Fingerprint')
@section('content')

<div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
    <h2><i class="fas fa-link"></i> Mapping ID Fingerprint</h2>
    <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger"><i class="fas fa-times-circle"></i> {{ session('error') }}</div>
@endif

{{-- Auto-import dari INFO.XLS --}}
<div class="content-box" style="margin-bottom:14px">
    <h4 style="margin:0 0 6px;font-size:15px">
        <i class="fas fa-magic" style="color:#166534"></i> Auto-Import dari INFO.XLS
    </h4>
    <p style="margin:0 0 12px;color:#6B7280;font-size:13px">
        Upload INFO.XLS dari mesin Eppos sistem otomatis cocokkan nama dan buat mapping.
        Nama yang tidak cocok otomatis ke dropdown untuk dipilih manual.
    </p>
    <form action="{{ route('admin.mesin.mapping-santri.import-info') }}"
          method="POST" enctype="multipart/form-data"
          style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        @csrf
        <input type="file" name="file_info" accept=".xls,.xlsx" required
               class="form-control" style="max-width:320px">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-magic"></i> Auto-Import
        </button>
    </form>
</div>

{{-- Statistik --}}
@php
    $total      = $mappings->count();
    $terpetakan = $mappings->filter(fn($m) => !empty($m->id_santri))->count();
    $belum      = $total - $terpetakan;
@endphp

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:14px">
    <div style="background:#F9FAFB;border:1px solid #E5E7EB;border-radius:10px;padding:14px;text-align:center">
        <div style="font-size:28px;font-weight:700;color:#1F2937">{{ $total }}</div>
        <div style="font-size:12px;color:#6B7280">Total ID Mesin</div>
    </div>
    <div style="background:#DCFCE7;border:1px solid #BBF7D0;border-radius:10px;padding:14px;text-align:center">
        <div style="font-size:28px;font-weight:700;color:#166534">{{ $terpetakan }}</div>
        <div style="font-size:12px;color:#166534">Terpetakan</div>
    </div>
    <div style="background:{{ $belum > 0 ? '#FEE2E2' : '#DCFCE7' }};border:1px solid {{ $belum > 0 ? '#FECACA' : '#BBF7D0' }};border-radius:10px;padding:14px;text-align:center">
        <div style="font-size:28px;font-weight:700;color:{{ $belum > 0 ? '#991B1B' : '#166534' }}">{{ $belum }}</div>
        <div style="font-size:12px;color:{{ $belum > 0 ? '#991B1B' : '#166534' }}">
            {{ $belum > 0 ? 'Belum Dipetakan' : 'Semua Terpetakan' }}
        </div>
    </div>
</div>

{{-- Peringatan jika ada yang belum --}}
@if($belum > 0)
<div style="background:#FEF9C3;border:1px solid #FDE68A;border-left:4px solid #F59E0B;
            border-radius:8px;padding:12px 16px;margin-bottom:14px;font-size:13px">
    <strong>âš  {{ $belum }} ID mesin belum dipetakan ke santri.</strong>
    Data scan santri tersebut tidak akan tersimpan saat import absensi.
    Pilih santri yang sesuai dari dropdown di bawah.
</div>
@endif

{{-- Tabel Mapping --}}
<div class="content-box" style="padding:0;overflow:hidden;margin-bottom:14px">
    <div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:70px;text-align:center">ID Mesin</th>
                <th>Nama di Mesin</th>
                <th style="width:90px">Dept/Kel</th>
                <th>Santri Web yang Dipetakan</th>
                <th style="width:110px;text-align:center">Status</th>
                <th style="width:70px;text-align:center">Hapus</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mappings as $m)
            <tr style="background:{{ empty($m->id_santri) ? '#FFFBEB' : 'white' }}">

                {{-- ID Mesin --}}
                <td style="text-align:center">
                    <strong style="font-family:monospace;font-size:15px;color:#1D4ED8">
                        {{ $m->id_mesin }}
                    </strong>
                </td>

                {{-- Nama di Mesin --}}
                <td>
                    <div style="font-weight:600;color:#1F2937">{{ $m->nama_mesin ?? '-' }}</div>
                    @if($m->catatan)
                        <div style="font-size:11px;color:#9CA3AF">{{ $m->catatan }}</div>
                    @endif
                </td>

                {{-- Dept --}}
                <td style="color:#6B7280;font-size:12px">{{ $m->dept_mesin ?? '-' }}</td>

                {{-- Dropdown Santri --}}
                <td>
                    <form action="{{ route('admin.mesin.mapping-santri.update', $m->id) }}"
                          method="POST" style="margin:0">
                        @csrf @method('PUT')
                        <div style="display:flex;align-items:center;gap:8px">
                            <select name="id_santri"
                                    class="form-control"
                                    style="font-size:13px;
                                           border-color:{{ empty($m->id_santri) ? '#FCA5A5' : '#D1D5DB' }};
                                           background:{{ empty($m->id_santri) ? '#FFF5F5' : 'white' }}"
                                    onchange="this.form.submit()">
                                <option value="">-- Pilih Santri --</option>
                                @foreach($santris as $s)
                                <option value="{{ $s->id_santri }}"
                                    {{ $m->id_santri == $s->id_santri ? 'selected' : '' }}>
                                    {{ $s->nama_lengkap }}
                                </option>
                                @endforeach
                            </select>
                            @if(!empty($m->id_santri))
                                <i class="fas fa-check-circle" style="color:#22C55E;font-size:16px" title="Sudah dipetakan"></i>
                            @else
                                <i class="fas fa-exclamation-circle" style="color:#EF4444;font-size:16px" title="Belum dipetakan"></i>
                            @endif
                        </div>
                    </form>
                </td>

                {{-- Status --}}
                <td style="text-align:center">
                    @if(!empty($m->id_santri))
                        <span style="background:#DCFCE7;color:#166534;border-radius:12px;
                                     padding:3px 10px;font-size:11px;font-weight:700">
                            Terpetakan
                        </span>
                    @else
                        <span style="background:#FEE2E2;color:#991B1B;border-radius:12px;
                                     padding:3px 10px;font-size:11px;font-weight:700">
                            âš  Belum
                        </span>
                    @endif
                </td>

                {{-- Hapus --}}
                <td style="text-align:center">
                    <form action="{{ route('admin.mesin.mapping-santri.destroy', $m->id) }}"
                          method="POST"
                          onsubmit="return confirm('Hapus mapping ID Mesin {{ $m->id_mesin }} ({{ $m->nama_mesin }})?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="btn btn-sm btn-danger"
                                style="padding:4px 10px">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:40px;color:#9CA3AF">
                    <i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:8px"></i>
                    Belum ada mapping. Upload INFO.XLS di atas untuk mulai.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

{{-- Tambah Manual --}}
<div class="content-box">
    <h4 style="margin:0 0 6px;font-size:15px">
        <i class="fas fa-plus-circle" style="color:#1D4ED8"></i> Tambah Mapping Manual
    </h4>
    <p style="margin:0 0 12px;color:#6B7280;font-size:13px">
        Untuk santri yang baru daftar ke mesin setelah INFO.XLS diekspor,
        atau santri yang nama di mesin sangat berbeda dari nama di sistem.
    </p>
    <form action="{{ route('admin.mesin.mapping-santri.store') }}" method="POST">
        @csrf
        <div style="display:grid;grid-template-columns:120px 160px 1fr auto;gap:10px;align-items:end">
            <div class="form-group" style="margin:0">
                <label style="font-size:12px;font-weight:600">ID Mesin <span style="color:red">*</span></label>
                <input type="text" name="id_mesin" class="form-control"
                       placeholder="cth: 8" required value="{{ old('id_mesin') }}"
                       style="font-family:monospace;font-size:15px;font-weight:700">
                @error('id_mesin')
                    <p style="color:#EF4444;font-size:11px;margin:3px 0 0">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group" style="margin:0">
                <label style="font-size:12px;font-weight:600">Nama di Mesin</label>
                <input type="text" name="nama_mesin" class="form-control"
                       placeholder="cth: ilham" value="{{ old('nama_mesin') }}">
            </div>
            <div class="form-group" style="margin:0">
                <label style="font-size:12px;font-weight:600">Santri Web</label>
                <select name="id_santri" class="form-control">
                    <option value="">-- Pilih Santri (bisa diisi nanti) --</option>
                    @foreach($santris as $s)
                    <option value="{{ $s->id_santri }}"
                        {{ old('id_santri') == $s->id_santri ? 'selected' : '' }}>
                        {{ $s->nama_lengkap }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary" style="white-space:nowrap;padding:9px 18px">
                    <i class="fas fa-plus"></i> Tambah
                </button>
            </div>
        </div>
    </form>
</div>

@endsection