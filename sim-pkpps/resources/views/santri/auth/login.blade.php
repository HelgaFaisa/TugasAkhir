{{-- resources/views/santri/auth/login.blade.php --}}
@extends('auth.auth_layout')

@section('title', 'Login Santri')

@section('auth-content')

<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body.auth-page {
  background: #F8FDFB !important;
  font-family: 'DM Sans', sans-serif !important;
  padding: 0 !important;
  align-items: stretch !important;
  min-height: 100vh !important;
}
.auth-container {
  width: 100vw !important; max-width: 100vw !important;
  min-height: 100vh !important; background: transparent !important;
  padding: 0 !important; border-radius: 0 !important; box-shadow: none !important;
}

/* ── Wrapper & Background ── */
.sl-wrap {
  position: relative; width: 100%; min-height: 100vh;
  display: flex; align-items: center; justify-content: center;
  overflow: hidden; font-family: 'DM Sans', sans-serif;
}
.sl-bg {
  position: absolute; inset: 0; z-index: 0;
  background:
    radial-gradient(ellipse 80% 60% at 70% 50%, rgba(111,186,157,.12) 0%, transparent 60%),
    radial-gradient(ellipse 50% 80% at 10% 80%, rgba(111,186,157,.08) 0%, transparent 55%),
    #F8FDFB;
}
.sl-bg::before {
  content: ''; position: absolute; inset: 0;
  background-image:
    linear-gradient(rgba(111,186,157,.055) 1px, transparent 1px),
    linear-gradient(90deg, rgba(111,186,157,.055) 1px, transparent 1px);
  background-size: 48px 48px;
}

/* Decorations */
.sl-ring { position: absolute; border-radius: 50%; border: 1.5px solid rgba(111,186,157,.18); pointer-events: none; }
.sl-ring.r1 { width:420px; height:420px; top:-110px; right:-110px; }
.sl-ring.r2 { width:270px; height:270px; bottom:50px; right:60px; border-color:rgba(111,186,157,.10); }
.sl-ring.r3 { width:200px; height:200px; bottom:-50px; left:60px; border-color:rgba(111,186,157,.14); }
.sl-dot { position:absolute; border-radius:50%; background:#6FBA9D; pointer-events:none; }
.sl-dot.d1 { width:8px; height:8px; top:22%; right:18%; opacity:.14; }
.sl-dot.d2 { width:5px; height:5px; bottom:40%; right:30%; opacity:.09; }
.sl-dot.d3 { width:11px; height:11px; top:55%; left:15%; opacity:.08; }
.sl-line { position:absolute; height:1px; background:linear-gradient(90deg,transparent,rgba(111,186,157,.14),transparent); pointer-events:none; }
.sl-line.l1 { width:280px; top:28%; left:-60px; transform:rotate(-15deg); }
.sl-line.l2 { width:220px; bottom:30%; right:-40px; transform:rotate(18deg); }

/* Layout */
.sl-layout {
  position: relative; z-index: 2;
  display: flex; align-items: center;
  width: 100%; max-width: 1100px;
  padding: 40px 60px; gap: 80px;
  animation: slIn .6s ease both;
}
@keyframes slIn {
  from { opacity:0; transform:translateY(20px); }
  to   { opacity:1; transform:translateY(0); }
}

/* Brand left, Form right */
.sl-brand      { flex: 0 0 340px; order: 1; }
.sl-form-panel { flex: 1; max-width: 430px; order: 2; }

/* ── Brand section ── */
.sl-logo { width:72px; height:72px; margin-bottom:20px; border-radius:16px; box-shadow:0 4px 20px rgba(111,186,157,.2); object-fit:contain; background:#fff; }
.sl-eyebrow {
  display:inline-flex; align-items:center; gap:8px;
  font-size:.68rem; font-weight:700; letter-spacing:2px;
  text-transform:uppercase; color:#6FBA9D; margin-bottom:18px;
}
.sl-eyebrow::before {
  content:''; display:inline-block; width:22px; height:2px;
  background:#6FBA9D; border-radius:2px;
}
.sl-title {
  font-family:'DM Serif Display',serif;
  font-size:3.2rem; line-height:1.05; color:#0F2118; margin-bottom:6px;
}
.sl-title em { font-style:italic; color:#5EA98C; }
.sl-sub { font-size:.9rem; font-weight:500; color:#8AADA0; margin-bottom:32px; line-height:1.6; }
.sl-divider { width:44px; height:3px; background:linear-gradient(90deg,#6FBA9D,#A8D8C6); border-radius:3px; margin-bottom:24px; }
.sl-desc { font-size:.81rem; color:#8AADA0; line-height:1.8; max-width:290px; margin-bottom:32px; }
.sl-features { display:flex; flex-direction:column; gap:11px; }
.sl-feat { display:flex; align-items:center; gap:11px; font-size:.79rem; color:#2A4235; font-weight:500; }
.sl-feat-ico {
  width:30px; height:30px; border-radius:8px; background:#EBF7F2;
  display:flex; align-items:center; justify-content:center;
  color:#3D8A6E; font-size:.73rem; flex-shrink:0;
}

/* ── Card ── */
.sl-card {
  background: #fff; border-radius: 24px;
  padding: 42px 38px;
  box-shadow:
    0 0 0 1px rgba(111,186,157,.1),
    0 4px 6px rgba(15,33,24,.03),
    0 20px 44px rgba(15,33,24,.08);
  position: relative; overflow: hidden;
}
.sl-card::before {
  content: ''; position:absolute; top:0; left:0; right:0; height:3px;
  background: linear-gradient(90deg, #6FBA9D, #A8D8C6, #6FBA9D);
}
.sl-card::after {
  content: ''; position:absolute; bottom:-50px; right:-50px;
  width:140px; height:140px; border-radius:50%;
  background: radial-gradient(circle, rgba(111,186,157,.06) 0%, transparent 70%);
}

.sl-card-lbl {
  font-size:.67rem; font-weight:700; letter-spacing:2px;
  text-transform:uppercase; color:#6FBA9D; margin-bottom:7px;
}
.sl-card-title {
  font-family:'DM Serif Display',serif;
  font-size:1.85rem; color:#0F2118; line-height:1.1; margin-bottom:5px;
}
.sl-card-desc { font-size:.79rem; color:#8AADA0; line-height:1.6; margin-bottom:26px; }

/* Alert */
.sl-alert {
  border-radius:10px; padding:10px 13px; font-size:.79rem;
  margin-bottom:18px; border-left:3px solid;
}
.sl-alert.danger { background:#FFF3F3; color:#c62828; border-color:#e53935; }
.sl-alert.success { background:#F0FFF4; color:#2E7D32; border-color:#43A047; }
.sl-alert p { display:flex; align-items:center; gap:7px; margin:2px 0; }

/* Fields */
.sl-field { margin-bottom:18px; }
.sl-lbl {
  display:block; font-size:.7rem; font-weight:700;
  letter-spacing:.8px; text-transform:uppercase; color:#2A4235; margin-bottom:7px;
}
.sl-shell { position:relative; display:flex; align-items:center; }
.sl-shell .fi { position:absolute; left:15px; color:#A8D8C6; font-size:.8rem; pointer-events:none; transition:color .2s; }
.sl-shell input {
  width:100%; padding:12px 15px 12px 40px;
  background:#EBF7F2; border:1.5px solid transparent;
  border-radius:11px; font-family:inherit; font-size:.87rem; color:#0F2118; outline:none;
  transition:all .2s;
}
.sl-shell input::placeholder { color:#8AADA0; font-size:.83rem; }
.sl-shell input:focus {
  background:#fff; border-color:#6FBA9D;
  box-shadow:0 0 0 4px rgba(111,186,157,.12);
}
.sl-show {
  position:absolute; right:13px;
  background:none; border:none; font-size:.68rem; font-weight:800;
  letter-spacing:.8px; color:#5EA98C; cursor:pointer; font-family:inherit;
}
.sl-show:hover { color:#3D8A6E; }
.sl-ferr { font-size:.72rem; color:#e53935; margin-top:4px; padding-left:3px; }

/* Remember */
.sl-remember {
  display:flex; align-items:center; gap:9px;
  margin-bottom:20px; cursor:pointer;
}
.sl-remember input[type="checkbox"] {
  width:16px; height:16px; accent-color:#6FBA9D; cursor:pointer;
  border-radius:4px;
}
.sl-remember span {
  font-size:.8rem; color:#5A7E6E; font-weight:500; user-select:none;
}

/* Button */
.sl-btn {
  width:100%; padding:13px;
  background:linear-gradient(135deg, #6FBA9D, #5EA98C);
  color:#fff; border:none; border-radius:12px;
  font-family:inherit; font-size:.89rem; font-weight:700;
  cursor:pointer; letter-spacing:.3px; margin-top:4px;
  display:flex; align-items:center; justify-content:center; gap:8px;
  box-shadow:0 4px 18px rgba(94,169,140,.35);
  transition:all .25s;
}
.sl-btn:hover { transform:translateY(-2px); box-shadow:0 8px 26px rgba(94,169,140,.45); }
.sl-btn:active { transform:none; }

.sl-foot { text-align:center; font-size:.77rem; color:#8AADA0; margin-top:20px; }
.sl-foot a { color:#5EA98C; font-weight:700; text-decoration:none; }
.sl-foot a:hover { text-decoration:underline; }

.sl-note {
  text-align:center; font-size:.73rem; color:#A8C4B8; margin-top:14px;
  line-height:1.5;
}
.sl-note i { margin-right:4px; }

/* ── Responsive ── */
@media (max-width: 900px) {
  .sl-layout { gap:48px; padding:32px 36px; }
  .sl-brand { flex:0 0 260px; }
  .sl-title { font-size:2.7rem; }
}
@media (max-width: 720px) {
  body.auth-page { align-items:flex-start !important; overflow-y:auto !important; }
  .sl-wrap { align-items:flex-start; min-height:auto; padding:24px 0 40px; }
  .sl-layout { flex-direction:column; padding:0 20px; gap:28px; }
  .sl-form-panel { order:2; max-width:100%; }
  .sl-brand { order:1; flex:none; text-align:center; }
  .sl-title { font-size:2.2rem; }
  .sl-logo { width:56px; height:56px; margin:0 auto 14px; display:block; }
  .sl-features, .sl-desc, .sl-divider { display:none; }
  .sl-sub { margin-bottom:0; }
  .sl-card { padding:28px 20px; }
  .sl-ring.r1 { width:260px; height:260px; top:-70px; right:-70px; }
  .sl-ring.r2 { display:none; }
}
@media (max-width: 420px) {
  .sl-title { font-size:1.85rem; }
  .sl-card { padding:24px 16px; border-radius:16px; }
  .sl-card-title { font-size:1.5rem; }
}
@media (min-width: 1280px) {
  .sl-layout { max-width:1160px; padding:40px 80px; }
  .sl-brand { flex:0 0 360px; }
  .sl-title { font-size:3.6rem; }
}
</style>

<div class="sl-wrap">
  <div class="sl-bg"></div>
  <div class="sl-ring r1"></div>
  <div class="sl-ring r2"></div>
  <div class="sl-ring r3"></div>
  <div class="sl-dot d1"></div>
  <div class="sl-dot d2"></div>
  <div class="sl-dot d3"></div>
  <div class="sl-line l1"></div>
  <div class="sl-line l2"></div>

  <div class="sl-layout">

    <!-- Brand (kiri) -->
    <div class="sl-brand">
      <img src="{{ asset('images/logo.png') }}" alt="Logo PKPPS" class="sl-logo">
      <div class="sl-eyebrow">Portal Santri</div>
      <h1 class="sl-title">Welcome Back<br><em>SIM Santri</em></h1>
      <p class="sl-sub">PKPPS Riyadlul Jannah</p>
      <div class="sl-divider"></div>
      <p class="sl-desc">Akses nilai, absensi, jadwal, dan seluruh progres pembelajaran santri secara mudah dan aman.</p>
      <div class="sl-features">
        <div class="sl-feat">
          <div class="sl-feat-ico"><i class="fas fa-user-graduate"></i></div>
          <span>Pantau progres akademik santri</span>
        </div>
        <div class="sl-feat">
          <div class="sl-feat-ico"><i class="fas fa-calendar-check"></i></div>
          <span>Lihat jadwal & absensi harian</span>
        </div>
        <div class="sl-feat">
          <div class="sl-feat-ico"><i class="fas fa-chart-line"></i></div>
          <span>Laporan capaian & perkembangan</span>
        </div>
      </div>
    </div>

    <!-- Form (kanan) -->
    <div class="sl-form-panel">
      <div class="sl-card">
        <div class="sl-card-lbl">Login Santri</div>
        <div class="sl-card-title">Masuk ke Akun</div>
        <div class="sl-card-desc">Gunakan username dan password yang diberikan oleh admin pesantren.</div>

        @if ($errors->any())
          <div class="sl-alert danger">
            @foreach ($errors->all() as $error)
              <p><i class="fas fa-circle-exclamation"></i> {{ $error }}</p>
            @endforeach
          </div>
        @endif

        @if (session('success'))
          <div class="sl-alert success">
            <p><i class="fas fa-check-circle"></i> {{ session('success') }}</p>
          </div>
        @endif

        <form method="POST" action="{{ route('santri.login') }}">
          @csrf

          <div class="sl-field">
            <label class="sl-lbl">Username / ID Santri</label>
            <div class="sl-shell">
              <i class="fas fa-user fi" id="sl-ico-u"></i>
              <input type="text" id="username" name="username"
                     value="{{ old('username') }}"
                     placeholder="Masukkan username Anda"
                     autocomplete="username" required autofocus
                     onfocus="document.getElementById('sl-ico-u').style.color='#6FBA9D'"
                     onblur="document.getElementById('sl-ico-u').style.color=''">
            </div>
            @error('username')<div class="sl-ferr">{{ $message }}</div>@enderror
          </div>

          <div class="sl-field">
            <label class="sl-lbl">Password</label>
            <div class="sl-shell">
              <i class="fas fa-lock fi" id="sl-ico-pw"></i>
              <input type="password" id="password" name="password"
                     placeholder="Masukkan password Anda"
                     autocomplete="current-password" required
                     onfocus="document.getElementById('sl-ico-pw').style.color='#6FBA9D'"
                     onblur="document.getElementById('sl-ico-pw').style.color=''">
              <button type="button" class="sl-show" id="slTglBtn">SHOW</button>
            </div>
            @error('password')<div class="sl-ferr">{{ $message }}</div>@enderror
          </div>

          <label class="sl-remember">
            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <span>Ingat Saya</span>
          </label>

          <button type="submit" class="sl-btn">
            <i class="fas fa-sign-in-alt"></i>
            Masuk
          </button>

          {{-- <div class="sl-foot">
            Login sebagai Admin? <a href="{{ route('admin.login') }}">Klik di sini</a>
          </div> --}}

          <div class="sl-note">
            <i class="fas fa-info-circle"></i>
            Lupa akun? Silakan hubungi admin pesantren untuk bantuan.
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Toggle password
  const btn = document.getElementById('slTglBtn');
  const pw  = document.getElementById('password');
  if (btn && pw) {
    btn.addEventListener('click', () => {
      const isP = pw.type === 'password';
      pw.type = isP ? 'text' : 'password';
      btn.textContent = isP ? 'HIDE' : 'SHOW';
    });
  }

  // Auto-hide success alert
  const successAlert = document.querySelector('.sl-alert.success');
  if (successAlert) {
    setTimeout(() => {
      successAlert.style.transition = 'opacity .5s ease';
      successAlert.style.opacity = '0';
      setTimeout(() => successAlert.remove(), 500);
    }, 4000);
  }
});
</script>

@endsection
