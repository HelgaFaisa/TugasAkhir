@extends('auth.auth_layout')

@section('title', 'Register Admin')

@section('auth-content')
<div class="auth-header">
    <div class="logo-circle">
        <i class="fas fa-lock-open fa-2x"></i>
    </div>
    <h2>Pendaftaran Akun Admin</h2>
    <p>Mohon gunakan email dan password yang kuat untuk keamanan sistem.</p>
</div>

{{-- Tampilkan error dari validator --}}
@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.register') }}" class="data-form">
    @csrf

    <div class="form-group">
        <label for="email"><i class="fas fa-envelope form-icon"></i> Email Admin</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="password"><i class="fas fa-key form-icon"></i> Password</label>
        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation"><i class="fas fa-lock form-icon"></i> Konfirmasi Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
    </div>

    <div class="form-group action-group">
        <button type="submit" class="btn btn-success btn-full hover-shadow">
            <i class="fas fa-paper-plane"></i> Daftarkan Admin
        </button>
    </div>

    <p style="text-align: center; font-size: 0.9rem; margin-top: 20px;">
        Sudah punya akun? <a href="{{ route('admin.login') }}" class="link-primary">Login di sini</a>
    </p>
</form>
@endsection