{{-- resources/views/admin/auth/forgot_password.blade.php --}}
@extends('auth.auth_layout')

@section('title', 'Lupa Password')

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

.fp-wrap {
  position: relative; width: 100%; min-height: 100vh;
  display: flex; align-items: center; justify-content: center;
  overflow: hidden; font-family: 'DM Sans', sans-serif;
}
.fp-bg {
  position: absolute; inset: 0; z-index: 0;
  background:
    radial-gradient(ellipse 80% 60% at 70% 50%, rgba(111,186,157,.12) 0%, transparent 60%),
    radial-gradient(ellipse 50% 80% at 10% 80%, rgba(111,186,157,.08) 0%, transparent 55%),
    #F8FDFB;
}
.fp-bg::before {
  content: ''; position: absolute; inset: 0;
  background-image:
    linear-gradient(rgba(111,186,157,.055) 1px, transparent 1px),
    linear-gradient(90deg, rgba(111,186,157,.055) 1px, transparent 1px);
  background-size: 48px 48px;
}
.fp-ring { position: absolute; border-radius: 50%; border: 1.5px solid rgba(111,186,157,.18); pointer-events: none; }
.fp-ring.r1 { width:420px; height:420px; top:-110px; right:-110px; }
.fp-ring.r2 { width:270px; height:270px; bottom:50px; left:60px; border-color:rgba(111,186,157,.10); }
.fp-dot { position:absolute; border-radius:50%; background:#6FBA9D; pointer-events:none; }
.fp-dot.d1 { width:8px; height:8px; top:22%; right:18%; opacity:.14; }
.fp-dot.d2 { width:11px; height:11px; top:55%; left:15%; opacity:.08; }
.fp-line { position:absolute; height:1px; background:linear-gradient(90deg,transparent,rgba(111,186,157,.14),transparent); pointer-events:none; }
.fp-line.l1 { width:280px; top:28%; left:-60px; transform:rotate(-15deg); }

.fp-layout {
  position: relative; z-index: 2;
  display: flex; align-items: center;
  width: 100%; max-width: 1100px;
  padding: 40px 60px; gap: 80px;
  animation: fpIn .6s ease both;
}
@keyframes fpIn {
  from { opacity:0; transform:translateY(20px); }
  to   { opacity:1; transform:translateY(0); }
}
.fp-brand      { flex: 0 0 340px; order: 1; }
.fp-form-panel { flex: 1; max-width: 430px; order: 2; }

/* Brand */
.fp-logo { width:72px; height:72px; margin-bottom:20px; border-radius:16px; box-shadow:0 4px 20px rgba(111,186,157,.2); object-fit:contain; background:#fff; }
.fp-eyebrow {
  display:inline-flex; align-items:center; gap:8px;
  font-size:.68rem; font-weight:700; letter-spacing:2px;
  text-transform:uppercase; color:#6FBA9D; margin-bottom:18px;
}
.fp-eyebrow::before { content:''; display:inline-block; width:22px; height:2px; background:#6FBA9D; border-radius:2px; }
.fp-title {
  font-family:'DM Serif Display',serif;
  font-size:3.2rem; line-height:1.05; color:#0F2118; margin-bottom:6px;
}
.fp-title em { font-style:italic; color:#5EA98C; }
.fp-sub { font-size:.9rem; font-weight:500; color:#8AADA0; margin-bottom:32px; line-height:1.6; }
.fp-divider { width:44px; height:3px; background:linear-gradient(90deg,#6FBA9D,#A8D8C6); border-radius:3px; margin-bottom:24px; }
.fp-desc { font-size:.81rem; color:#8AADA0; line-height:1.8; max-width:290px; margin-bottom:32px; }

/* Steps */
.fp-steps { display:flex; flex-direction:column; gap:14px; }
.fp-step { display:flex; align-items:flex-start; gap:12px; }
.fp-step-num {
  width:28px; height:28px; border-radius:50%;
  background:linear-gradient(135deg,#6FBA9D,#5EA98C);
  color:#fff; font-size:.72rem; font-weight:800;
  display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.fp-step-num.active { box-shadow:0 0 0 4px rgba(111,186,157,.2); }
.fp-step-text { font-size:.78rem; color:#2A4235; font-weight:500; line-height:1.5; padding-top:3px; }
.fp-step-text small { display:block; color:#8AADA0; font-weight:400; font-size:.72rem; }

/* Card */
.fp-card {
  background: #fff; border-radius: 24px;
  padding: 42px 38px;
  box-shadow:
    0 0 0 1px rgba(111,186,157,.1),
    0 4px 6px rgba(15,33,24,.03),
    0 20px 44px rgba(15,33,24,.08);
  position: relative; overflow: hidden;
}
.fp-card::before {
  content: ''; position:absolute; top:0; left:0; right:0; height:3px;
  background: linear-gradient(90deg, #e57373, #ef9a9a, #e57373);
}
.fp-card::after {
  content: ''; position:absolute; bottom:-50px; right:-50px;
  width:140px; height:140px; border-radius:50%;
  background: radial-gradient(circle, rgba(229,115,115,.06) 0%, transparent 70%);
}
.fp-card-icon {
  width:54px; height:54px; border-radius:14px;
  background:linear-gradient(135deg,#FFEBEE,#FFCDD2);
  display:flex; align-items:center; justify-content:center;
  font-size:1.3rem; color:#e53935; margin-bottom:16px;
}
.fp-card-lbl {
  font-size:.67rem; font-weight:700; letter-spacing:2px;
  text-transform:uppercase; color:#e57373; margin-bottom:7px;
}
.fp-card-title {
  font-family:'DM Serif Display',serif;
  font-size:1.85rem; color:#0F2118; line-height:1.1; margin-bottom:5px;
}
.fp-card-desc { font-size:.79rem; color:#8AADA0; line-height:1.6; margin-bottom:26px; }

/* Alert */
.fp-alert-danger {
  border-radius:10px; padding:10px 13px; font-size:.79rem;
  margin-bottom:18px; background:#FFF3F3; color:#c62828; border-left:3px solid #e53935;
  display:flex; align-items:center; gap:7px;
}
.fp-alert-success {
  border-radius:10px; padding:10px 13px; font-size:.79rem;
  margin-bottom:18px; background:#F0FFF4; color:#2E7D32; border-left:3px solid #43A047;
  display:flex; align-items:center; gap:7px;
}

/* Field */
.fp-field { margin-bottom:18px; }
.fp-lbl { display:block; font-size:.7rem; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:#2A4235; margin-bottom:7px; }
.fp-shell { position:relative; display:flex; align-items:center; }
.fp-shell .fi { position:absolute; left:15px; color:#A8D8C6; font-size:.8rem; pointer-events:none; transition:color .2s; }
.fp-shell input {
  width:100%; padding:12px 15px 12px 40px;
  background:#EBF7F2; border:1.5px solid transparent;
  border-radius:11px; font-family:inherit; font-size:.87rem; color:#0F2118; outline:none;
  transition:all .2s;
}
.fp-shell input::placeholder { color:#8AADA0; font-size:.83rem; }
.fp-shell input:focus { background:#fff; border-color:#e57373; box-shadow:0 0 0 4px rgba(229,115,115,.12); }
.fp-shell .fi.active { color:#e57373; }

/* Button */
.fp-btn {
  width:100%; padding:13px;
  background:linear-gradient(135deg, #e57373, #ef5350);
  color:#fff; border:none; border-radius:12px;
  font-family:inherit; font-size:.89rem; font-weight:700;
  cursor:pointer; letter-spacing:.3px;
  display:flex; align-items:center; justify-content:center; gap:8px;
  box-shadow:0 4px 18px rgba(229,115,115,.35);
  transition:all .25s;
}
.fp-btn:hover { transform:translateY(-2px); box-shadow:0 8px 26px rgba(229,115,115,.45); }
.fp-btn:active { transform:none; }

.fp-back {
  display:flex; align-items:center; justify-content:center; gap:6px;
  margin-top:20px; font-size:.78rem; color:#5EA98C; font-weight:600; text-decoration:none;
  transition:color .2s;
}
.fp-back:hover { color:#3D8A6E; text-decoration:underline; }

/* Responsive */
@media (max-width: 900px) {
  .fp-layout { gap:48px; padding:32px 36px; }
  .fp-brand { flex:0 0 260px; }
  .fp-title { font-size:2.7rem; }
}
@media (max-width: 720px) {
  body.auth-page { align-items:flex-start !important; overflow-y:auto !important; }
  .fp-wrap { align-items:flex-start; min-height:auto; padding:24px 0 40px; }
  .fp-layout { flex-direction:column; padding:0 20px; gap:28px; }
  .fp-form-panel { order:2; max-width:100%; }
  .fp-brand { order:1; flex:none; text-align:center; }
  .fp-title { font-size:2.2rem; }
  .fp-steps, .fp-desc, .fp-divider { display:none; }
  .fp-sub { margin-bottom:0; }
  .fp-card { padding:28px 20px; }
  .fp-logo { width:56px; height:56px; margin:0 auto 14px; display:block; }
}
@media (max-width: 420px) {
  .fp-title { font-size:1.85rem; }
  .fp-card { padding:24px 16px; border-radius:16px; }
  .fp-card-title { font-size:1.5rem; }
}
@media (min-width: 1280px) {
  .fp-layout { max-width:1160px; padding:40px 80px; }
  .fp-brand { flex:0 0 360px; }
  .fp-title { font-size:3.6rem; }
}
</style>

<div class="fp-wrap">
  <div class="fp-bg"></div>
  <div class="fp-ring r1"></div>
  <div class="fp-ring r2"></div>
  <div class="fp-dot d1"></div>
  <div class="fp-dot d2"></div>
  <div class="fp-line l1"></div>

  <div class="fp-layout">

    <!-- ═══ Brand (kiri) ═══ -->
    <div class="fp-brand">
      <img src="{{ asset('images/logo.png') }}" alt="Logo PKPPS" class="fp-logo">
      <div class="fp-eyebrow">Reset Akses</div>
      <h1 class="fp-title">Lupa<br><em>Password?</em></h1>
      <p class="fp-sub">Jangan khawatir, kami bantu pulihkan.</p>
      <div class="fp-divider"></div>
      <p class="fp-desc">Ikuti langkah berikut untuk mengatur ulang password akun Super Admin Anda.</p>

      <div class="fp-steps">
        <div class="fp-step">
          <div class="fp-step-num active">1</div>
          <div class="fp-step-text">Masukkan email terdaftar <small>Kami kirim kode OTP 6 digit</small></div>
        </div>
        <div class="fp-step">
          <div class="fp-step-num">2</div>
          <div class="fp-step-text">Verifikasi kode OTP <small>Cek email masuk / spam</small></div>
        </div>
        <div class="fp-step">
          <div class="fp-step-num">3</div>
          <div class="fp-step-text">Buat password baru <small>Minimal 8 karakter</small></div>
        </div>
      </div>
    </div>

    <!-- ═══ Form (kanan) ═══ -->
    <div class="fp-form-panel">
      <div class="fp-card">
        <div class="fp-card-icon">
          <i class="fas fa-envelope-open-text"></i>
        </div>
        <div class="fp-card-lbl">Langkah 1 dari 3</div>
        <div class="fp-card-title">Masukkan Email</div>
        <div class="fp-card-desc">Masukkan email Super Admin yang terdaftar di sistem. Kami akan mengirim kode OTP ke email tersebut.</div>

        @if ($errors->any())
          <div class="fp-alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->first() }}
          </div>
        @endif

        @if(session('success'))
          <div class="fp-alert-success" id="fpSuccessAlert">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
          </div>
        @endif

        <form method="POST" action="{{ route('admin.forgot.send_otp') }}">
          @csrf

          <div class="fp-field">
            <label class="fp-lbl">Email Super Admin</label>
            <div class="fp-shell">
              <i class="fas fa-envelope fi" id="ico-email"></i>
              <input type="email" id="email" name="email"
                     value="{{ old('email') }}"
                     placeholder="contoh@email.com"
                     required autofocus
                     onfocus="document.getElementById('ico-email').classList.add('active')"
                     onblur="document.getElementById('ico-email').classList.remove('active')">
            </div>
          </div>

          <button type="submit" class="fp-btn">
            <i class="fas fa-paper-plane"></i>
            Kirim Kode OTP
          </button>
        </form>

        <a href="{{ route('admin.login') }}" class="fp-back">
          <i class="fas fa-arrow-left"></i> Kembali ke Login
        </a>
      </div>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const sa = document.getElementById('fpSuccessAlert');
  if (sa) {
    setTimeout(() => {
      sa.style.transition = 'opacity .5s ease';
      sa.style.opacity = '0';
      setTimeout(() => sa.remove(), 500);
    }, 5000);
  }
});
</script>
@endsection
