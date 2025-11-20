@extends('layouts.app', ['isAdmin' => true])

@section('title', 'Tambah Santri')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-user-plus"></i> Tambah Santri Baru</h2>
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

    @include('admin.santri.form')
</div>
@endsection