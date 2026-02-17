@extends('layouts.app')

@section('title', 'Detail Pembinaan & Sanksi')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-eye"></i> Detail Pembinaan & Sanksi</h2>
</div>

<div class="content-box">
    <div style="margin-bottom: 30px;">
        <h3 style="color: var(--primary-color); margin-bottom: 15px;">
            <i class="fas fa-info-circle"></i> Informasi
        </h3>
        
        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td style="width: 200px; padding: 10px 0; font-weight: 600;">ID Pembinaan</td>
                <td style="padding: 10px 0;">
                    <span class="badge badge-primary" style="font-size: 1em;">{{ $pembinaan->id_pembinaan }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Judul</td>
                <td style="padding: 10px 0;">
                    <strong style="font-size: 1.1em;">{{ $pembinaan->judul }}</strong>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Urutan Tampilan</td>
                <td style="padding: 10px 0;">
                    <span class="badge badge-info">{{ $pembinaan->urutan }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Status</td>
                <td style="padding: 10px 0;">
                    @if($pembinaan->is_active)
                        <span class="badge badge-success">
                            <i class="fas fa-check-circle"></i> Aktif
                        </span>
                    @else
                        <span class="badge badge-secondary">
                            <i class="fas fa-times-circle"></i> Nonaktif
                        </span>
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Dibuat</td>
                <td style="padding: 10px 0;">
                    {{ $pembinaan->created_at->format('d M Y H:i') }}
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Terakhir Diubah</td>
                <td style="padding: 10px 0;">
                    {{ $pembinaan->updated_at->format('d M Y H:i') }}
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-bottom: 30px;">
        <h3 style="color: var(--primary-color); margin-bottom: 15px;">
            <i class="fas fa-file-alt"></i> Konten
        </h3>
        <div class="content-display" style="background: #ffffff; padding: 30px; border-radius: 8px; border: 2px solid #e9ecef; line-height: 1.8; min-height: 200px;">
            {!! $pembinaan->konten !!}
        </div>
        <p style="margin-top: 10px; color: var(--text-light); font-size: 0.9em;">
            <i class="fas fa-info-circle"></i> Konten di atas akan ditampilkan kepada santri/wali dengan format yang sama.
        </p>
    </div>

    <div class="btn-group">
        <a href="{{ route('admin.pembinaan-sanksi.edit', $pembinaan) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="{{ route('admin.pembinaan-sanksi.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<style>
    /* Styling untuk konten yang ditampilkan */
    .content-display h1, .content-display h2, .content-display h3 {
        color: var(--primary-color);
        margin-top: 20px;
        margin-bottom: 15px;
    }
    
    .content-display h1 {
        font-size: 2em;
        border-bottom: 2px solid var(--primary-color);
        padding-bottom: 10px;
    }
    
    .content-display h2 {
        font-size: 1.5em;
    }
    
    .content-display h3 {
        font-size: 1.2em;
    }
    
    .content-display p {
        margin-bottom: 15px;
        line-height: 1.8;
    }
    
    .content-display ul, .content-display ol {
        margin-left: 30px;
        margin-bottom: 15px;
    }
    
    .content-display li {
        margin-bottom: 8px;
    }
    
    .content-display strong {
        color: #2c3e50;
        font-weight: 600;
    }
    
    .content-display em {
        font-style: italic;
        color: #34495e;
    }
    
    .content-display table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    
    .content-display table td, .content-display table th {
        border: 1px solid #dee2e6;
        padding: 10px;
    }
</style>
@endsection