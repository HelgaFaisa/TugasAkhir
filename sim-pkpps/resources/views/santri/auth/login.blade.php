<!-- resources/views/santri/auth/login.blade.php -->
@extends('auth.auth_layout')

@section('title', 'Login Santri')

@section('auth-content')
<div class="auth-header">
    <h2><i class="fas fa-user-graduate"></i> Login Santri/Wali</h2>
    <p>Akses progres dan laporan santri.</p>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('santri.login') }}" class="data-form">
    @csrf

    <div class="form-group">
        <label for="username">Username (ID Santri/Wali)</label>
        <input type="text" id="username" name="username" value="{{ old('username') }}" class="form-control" required autofocus>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control" required>
    </div>
    
    <div class="form-group" style="display:flex; align-items: center;">
        <input type="checkbox" name="remember" id="remember" style="width: auto; margin-right: 10px;">
        <label for="remember" style="font-weight: normal; margin-bottom: 0;">Ingat Saya</label>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-full">
            <i class="fas fa-sign-in-alt"></i> Masuk
        </button>
    </div>

    <p style="text-align: center; font-size: 0.9rem; margin-top: 15px;">
        Lupa akun? Hubungi Admin.
    </p>
</form>
@endsection
