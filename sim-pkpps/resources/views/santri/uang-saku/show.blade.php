{{-- resources/views/santri/uang-saku/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Transaksi Uang Saku')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-file-invoice"></i> Detail Transaksi Uang Saku</h2>
</div>

<div class="content-box">
    <div class="detail-header">
        <div>
            <h3><i class="fas fa-receipt"></i> Informasi Transaksi</h3>
            <p style="color: var(--text-light); margin: 0;">Detail lengkap transaksi uang saku</p>
        </div>
        <div>
            <a href="{{ route('santri.uang-saku.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <hr style="border: none; border-top: 2px solid #E8F7F2; margin: 25px 0;">

    {{-- DATA TRANSAKSI --}}
    <div class="detail-section">
        <h4><i class="fas fa-info-circle"></i> Data Transaksi</h4>
        <table class="detail-table">
            <tr>
                <th width="200"><i class="fas fa-hashtag"></i> ID Transaksi</th>
                <td><strong>{{ $transaksi->id_uang_saku }}</strong></td>
            </tr>
            <tr>
                <th><i class="fas fa-calendar"></i> Tanggal Transaksi</th>
                <td>{{ $transaksi->tanggal_transaksi->isoFormat('dddd, D MMMM YYYY') }}</td>
            </tr>
            <tr>
                <th><i class="fas fa-exchange-alt"></i> Jenis Transaksi</th>
                <td>
                    @if($transaksi->jenis_transaksi === 'pemasukan')
                        <span class="badge badge-success" style="font-size: 0.9rem; padding: 6px 12px;">
                            <i class="fas fa-arrow-down"></i> Pemasukan
                        </span>
                    @else
                        <span class="badge badge-danger" style="font-size: 0.9rem; padding: 6px 12px;">
                            <i class="fas fa-arrow-up"></i> Pengeluaran
                        </span>
                    @endif
                </td>
            </tr>
            <tr>
                <th><i class="fas fa-money-bill-wave"></i> Nominal</th>
                <td>
                    <span style="font-size: 1.4rem; font-weight: 700; color: {{ $transaksi->jenis_transaksi === 'pemasukan' ? '#6FBA9D' : '#FF8B94' }};">
                        {{ $transaksi->nominal_format }}
                    </span>
                </td>
            </tr>
            <tr>
                <th><i class="fas fa-sticky-note"></i> Keterangan</th>
                <td>{{ $transaksi->keterangan ?? '-' }}</td>
            </tr>
            <tr>
                <th><i class="fas fa-clock"></i> Dicatat Pada</th>
                <td>{{ $transaksi->created_at->format('d/m/Y H:i:s') }} WIB</td>
            </tr>
        </table>
    </div>

    {{-- RINCIAN SALDO --}}
    <div class="detail-section">
        <h4><i class="fas fa-calculator"></i> Rincian Saldo</h4>
        <table class="detail-table">
            <tr>
                <th width="200"><i class="fas fa-wallet"></i> Saldo Sebelum</th>
                <td><strong>Rp {{ number_format($transaksi->saldo_sebelum, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <th>
                    <i class="fas fa-{{ $transaksi->jenis_transaksi === 'pemasukan' ? 'plus' : 'minus' }}-circle"></i>
                    {{ $transaksi->jenis_transaksi === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran' }}
                </th>
                <td style="color: {{ $transaksi->jenis_transaksi === 'pemasukan' ? '#6FBA9D' : '#FF8B94' }}; font-weight: 700;">
                    {{ $transaksi->jenis_transaksi === 'pemasukan' ? '+' : '-' }} {{ $transaksi->nominal_format }}
                </td>
            </tr>
            <tr style="background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%);">
                <th><i class="fas fa-wallet"></i> Saldo Sesudah</th>
                <td>
                    <strong style="font-size: 1.2rem; color: {{ $transaksi->saldo_sesudah >= 0 ? '#6FBA9D' : '#FF8B94' }};">
                        {{ $transaksi->saldo_sesudah_format }}
                    </strong>
                </td>
            </tr>
        </table>
    </div>

    {{-- VISUALISASI PERUBAHAN SALDO --}}
    <div class="detail-section">
        <h4><i class="fas fa-chart-line"></i> Visualisasi Perubahan Saldo</h4>

        <div class="grid-2col" style="margin-bottom: 20px;">
            <div style="background: linear-gradient(135deg, #E8F7F2, #D4F1E3); padding: 16px; border-radius: 12px; text-align: center;">
                <i class="fas fa-arrow-down" style="font-size: 2rem; color: #6FBA9D; margin-bottom: 8px;"></i>
                <p style="margin: 0 0 4px 0; color: var(--text-light); font-size: 0.85rem;">Nominal Pemasukan</p>
                <p style="font-size: 1.4rem; font-weight: 700; color: #6FBA9D; margin: 0;">
                    {{ $transaksi->jenis_transaksi === 'pemasukan' ? $transaksi->nominal_format : 'Rp 0' }}
                </p>
            </div>
            <div style="background: linear-gradient(135deg, #FFE8EA, #FFD5D8); padding: 16px; border-radius: 12px; text-align: center;">
                <i class="fas fa-arrow-up" style="font-size: 2rem; color: #FF8B94; margin-bottom: 8px;"></i>
                <p style="margin: 0 0 4px 0; color: var(--text-light); font-size: 0.85rem;">Nominal Pengeluaran</p>
                <p style="font-size: 1.4rem; font-weight: 700; color: #FF8B94; margin: 0;">
                    {{ $transaksi->jenis_transaksi === 'pengeluaran' ? $transaksi->nominal_format : 'Rp 0' }}
                </p>
            </div>
        </div>

        {{-- Progress bar perubahan saldo --}}
        @php
            $maxSaldo = max($transaksi->saldo_sebelum, $transaksi->saldo_sesudah, 1);
            $pctSebelum  = ($transaksi->saldo_sebelum / $maxSaldo) * 100;
            $pctSesudah  = ($transaksi->saldo_sesudah / $maxSaldo) * 100;
            $colorSesudah = $transaksi->jenis_transaksi === 'pemasukan' ? '#6FBA9D' : '#FF8B94';
        @endphp

        <div style="background: white; padding: 20px; border-radius: 12px; border: 2px solid #E8F7F2;">
            <p style="font-weight: 600; margin: 0 0 12px 0;"><i class="fas fa-wallet"></i> Perubahan Saldo</p>

            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                <span style="font-size: 0.9rem; color: var(--text-light);">Saldo Sebelum</span>
                <strong>Rp {{ number_format($transaksi->saldo_sebelum, 0, ',', '.') }}</strong>
            </div>
            <div style="height: 36px; background: #e0e0e0; border-radius: 18px; overflow: hidden; margin-bottom: 16px;">
                <div style="height: 100%; width: {{ max($pctSebelum, 3) }}%; background: linear-gradient(90deg, var(--primary-color), #4a9e7f); border-radius: 18px;"></div>
            </div>

            <div style="text-align: center; margin-bottom: 16px;">
                <i class="fas fa-arrow-{{ $transaksi->jenis_transaksi === 'pemasukan' ? 'up' : 'down' }}"
                   style="font-size: 1.4rem; color: {{ $colorSesudah }};"></i>
                <p style="margin: 4px 0 0 0; font-size: 0.85rem; color: var(--text-light);">
                    Saldo {{ $transaksi->jenis_transaksi === 'pemasukan' ? 'bertambah' : 'berkurang' }}
                    <strong style="color: {{ $colorSesudah }};">{{ $transaksi->nominal_format }}</strong>
                </p>
            </div>

            <div style="height: 36px; background: #e0e0e0; border-radius: 18px; overflow: hidden; margin-bottom: 6px;">
                <div style="height: 100%; width: {{ max($pctSesudah, 3) }}%; background: linear-gradient(90deg, {{ $colorSesudah }}, {{ $transaksi->jenis_transaksi === 'pemasukan' ? '#4CAF50' : '#e07080' }}); border-radius: 18px;"></div>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="font-size: 0.9rem; color: var(--text-light);">Saldo Sesudah</span>
                <strong style="color: {{ $colorSesudah }}; font-size: 1.05rem;">
                    Rp {{ number_format($transaksi->saldo_sesudah, 0, ',', '.') }}
                </strong>
            </div>
        </div>
    </div>

    <div style="margin-top: 22px; text-align: center;">
        <a href="{{ route('santri.uang-saku.index') }}" class="btn btn-primary">
            <i class="fas fa-list"></i> Lihat Semua Riwayat
        </a>
    </div>
</div>
@endsection