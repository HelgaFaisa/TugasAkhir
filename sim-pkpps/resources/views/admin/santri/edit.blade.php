@extends('layouts.app', ['isAdmin' => true])

@section('title', 'Edit Santri: ' . $santri->nama_lengkap)

@section('content')
<div class="page-header">
    <h2><i class="fas fa-user-edit"></i> Edit Santri</h2>
</div>

<div class="content-box">
    <div style="background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%); padding: 15px 20px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #6FBA9D;">
        <p style="margin: 0; color: #2C5F4F;">
            <i class="fas fa-info-circle"></i> 
            <strong>Sedang mengedit data:</strong> {{ $santri->nama_lengkap }} ({{ $santri->id_santri }})
        </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong><i class="fas fa-exclamation-triangle"></i> Terdapat kesalahan:</strong>
            <ul style="margin: 10px 0 0 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('admin.santri.form', ['santri' => $santri])
</div>
@endsection