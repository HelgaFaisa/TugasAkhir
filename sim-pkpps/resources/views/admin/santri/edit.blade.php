@extends('layouts.app', ['isAdmin' => true])

@section('title', 'Edit Santri: ' . $santri->nama_lengkap)

@section('content')
<div class="page-header">
    <h2><i class="fas fa-user-edit"></i> Edit Data Santri</h2>
</div>

<div class="content-box">
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

    {{-- Info Box: Data Santri yang Sedang Diedit --}}
    <div style="background: linear-gradient(135deg, #E3F2FD 0%, #D1E9F9 100%); padding: 20px; border-radius: 8px; border-left: 4px solid #81C6E8; margin-bottom: 25px;">
        <div style="display: flex; align-items: center; gap: 15px;">
            @if($santri->foto)
                <img src="{{ asset('storage/' . $santri->foto) }}" 
                     alt="Foto {{ $santri->nama_lengkap }}" 
                     style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 3px solid #81C6E8;"
                     loading="lazy">
            @else
                <div style="width: 60px; height: 60px; border-radius: 50%; background: #81C6E8; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.5rem;">
                    {{ strtoupper(substr($santri->nama_lengkap, 0, 1)) }}
                </div>
            @endif
            
            <div>
                <p style="margin: 0; color: #2D4A7C; font-size: 0.9rem;">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Sedang mengedit data:</strong>
                </p>
                <p style="margin: 5px 0 0 0; color: #2D4A7C; font-weight: 600; font-size: 1.1rem;">
                    {{ $santri->nama_lengkap }} ({{ $santri->id_santri }})
                </p>
            </div>
        </div>
    </div>

    @include('admin.santri.form')
</div>
@endsection