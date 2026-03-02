{{-- resources/views/admin/auth/register.blade.php --}}
@extends('auth.auth_layout')

@section('title', 'Register Admin')

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

.rv2-wrap {
  position: relative; width: 100%; min-height: 100vh;
  display: flex; align-items: center; justify-content: center;
  overflow: hidden; font-family: 'DM Sans', sans-serif;
}

.rv2-bg {
  position: absolute; inset: 0; z-index: 0;
  background:
    radial-gradient(ellipse 80% 60% at 30% 50%, rgba(111,186,157,.12) 0%, transparent 60%),
    radial-gradient(ellipse 50% 80% at 90% 20%, rgba(111,186,157,.08) 0%, transparent 55%),
    #F8FDFB;
}
.rv2-bg::before {
  content: ''; position: absolute; inset: 0;
  background-image:
    linear-gradient(rgba(111,186,157,.055) 1px, transparent 1px),
    linear-gradient(90deg, rgba(111,186,157,.055) 1px, transparent 1px);
  background-size: 48px 48px;
}

.rv2-ring {
  position: absolute; border-radius: 50%;
  border: 1.5px solid rgba(111,186,157,.18); pointer-events: none;
}
.rv2-ring.r1 { width:420px; height:420px; top:-110px; left:-110px; }
.rv2-ring.r2 { width:270px; height:270px; top:50px; left:60px; border-color:rgba(111,186,157,.10); }
.rv2-ring.r3 { width:200px; height:200px; bottom:-50px; right:60px; border-color:rgba(111,186,157,.14); }
.rv2-dot { position:absolute; border-radius:50%; background:#6FBA9D; pointer-events:none; }
.rv2-dot.d1 { width:8px; height:8px; top:22%; left:18%; opacity:.14; }
.rv2-dot.d2 { width:5px; height:5px; bottom:40%; left:30%; opacity:.09; }
.rv2-dot.d3 { width:11px; height:11px; top:55%; right:15%; opacity:.08; }
.rv2-line { position:absolute; height:1px; background:linear-gradient(90deg,transparent,rgba(111,186,157,.14),transparent); pointer-events:none; }
.rv2-line.l1 { width:280px; top:28%; right:-60px; transform:rotate(15deg); }
.rv2-line.l2 { width:220px; bottom:30%; left:-40px; transform:rotate(-18deg); }

.rv2-layout {
  position: relative; z-index: 2;
  display: flex; align-items: center;
  width: 100%; max-width: 1100px;
  padding: 40px 60px; gap: 80px;
  animation: rv2In .6s ease both;
}
@keyframes rv2In {
  from { opacity:0; transform:translateY(20px); }
  to   { opacity:1; transform:translateY(0); }
}

/* Form kiri dulu di register */
.rv2-form-panel { flex: 1; max-width: 430px; order: 1; }
.rv2-brand      { flex: 0 0 320px; order: 2; }

/* CARD */
.rv2-card {
  background: #fff; border-radius: 24px;
  padding: 42px 38px;
  box-shadow:
    0 0 0 1px rgba(111,186,157,.1),
    0 4px 6px rgba(15,33,24,.03),
    0 20px 44px rgba(15,33,24,.08);
  position: relative; overflow: hidden;
}
.rv2-card::before {
  content: ''; position:absolute; top:0; left:0; right:0; height:3px;
  background: linear-gradient(90deg, #6FBA9D, #A8D8C6, #6FBA9D);
}
.rv2-card::after {
  content: ''; position:absolute; bottom:-50px; left:-50px;
  width:140px; height:140px; border-radius:50%;
  background: radial-gradient(circle, rgba(111,186,157,.06) 0%, transparent 70%);
}

.rv2-card-lbl {
  font-size:.67rem; font-weight:700; letter-spacing:2px;
  text-transform:uppercase; color:#6FBA9D; margin-bottom:7px;
}
.rv2-card-title {
  font-family:'DM Serif Display',serif;
  font-size:1.85rem; color:#0F2118; line-height:1.1; margin-bottom:5px;
}
.rv2-card-desc { font-size:.79rem; color:#8AADA0; line-height:1.6; margin-bottom:26px; }

/* Alert */
.rv2-alert {
  border-radius:10px; padding:10px 13px; font-size:.79rem;
  margin-bottom:18px; background:#FFF3F3; color:#c62828; border-left:3px solid #e53935;
}
.rv2-alert p { display:flex; align-items:center; gap:7px; margin:2px 0; }

/* Fields */
.rv2-field { margin-bottom:15px; }
.rv2-lbl {
  display:block; font-size:.7rem; font-weight:700;
  letter-spacing:.8px; text-transform:uppercase; color:#2A4235; margin-bottom:7px;
}
.rv2-shell { position:relative; display:flex; align-items:center; }
.rv2-shell .fi { position:absolute; left:15px; color:#A8D8C6; font-size:.8rem; pointer-events:none; transition:color .2s; }
.rv2-shell input {
  width:100%; padding:12px 15px 12px 40px;
  background:#EBF7F2; border:1.5px solid transparent;
  border-radius:11px; font-family:inherit; font-size:.87rem; color:#0F2118; outline:none;
  transition:all .2s;
}
.rv2-shell input::placeholder { color:#8AADA0; font-size:.83rem; }
.rv2-shell input:focus {
  background:#fff; border-color:#6FBA9D;
  box-shadow:0 0 0 4px rgba(111,186,157,.12);
}
.rv2-show {
  position:absolute; right:13px;
  background:none; border:none; font-size:.68rem; font-weight:800;
  letter-spacing:.8px; color:#5EA98C; cursor:pointer; font-family:inherit;
}
.rv2-show:hover { color:#3D8A6E; }
.rv2-ferr { font-size:.72rem; color:#e53935; margin-top:4px; padding-left:3px; }

/* Strength */
.rv2-strength { display:flex; gap:4px; margin-top:7px; }
.rv2-bar { height:3px; flex:1; border-radius:3px; background:#D6EDE5; transition:background .3s; }
.rv2-bar.w { background:#e53935; }
.rv2-bar.m { background:#FB8C00; }
.rv2-bar.s { background:#6FBA9D; }

/* Buttons */
.rv2-btn {
  width:100%; padding:13px;
  background:linear-gradient(135deg, #6FBA9D, #5EA98C);
  color:#fff; border:none; border-radius:12px;
  font-family:inherit; font-size:.89rem; font-weight:700;
  cursor:pointer; letter-spacing:.3px; margin-top:6px;
  display:flex; align-items:center; justify-content:center; gap:8px;
  box-shadow:0 4px 18px rgba(94,169,140,.35);
  transition:all .25s;
}
.rv2-btn:hover { transform:translateY(-2px); box-shadow:0 8px 26px rgba(94,169,140,.45); }
.rv2-btn:active { transform:none; }

.rv2-foot { text-align:center; font-size:.77rem; color:#8AADA0; margin-top:20px; }
.rv2-foot a { color:#5EA98C; font-weight:700; text-decoration:none; }
.rv2-foot a:hover { text-decoration:underline; }

/* Brand */
.rv2-logo { width:72px; height:72px; margin-bottom:20px; border-radius:16px; box-shadow:0 4px 20px rgba(111,186,157,.2); object-fit:contain; background:#fff; }
.rv2-eyebrow {
  display:inline-flex; align-items:center; gap:8px;
  font-size:.68rem; font-weight:700; letter-spacing:2px;
  text-transform:uppercase; color:#6FBA9D; margin-bottom:18px;
}
.rv2-eyebrow::before {
  content:''; display:inline-block; width:22px; height:2px;
  background:#6FBA9D; border-radius:2px;
}
.rv2-title {
  font-family:'DM Serif Display',serif;
  font-size:3.2rem; line-height:1.05; color:#0F2118; margin-bottom:6px;
}
.rv2-title em { font-style:italic; color:#5EA98C; }
.rv2-sub { font-size:.9rem; font-weight:500; color:#8AADA0; margin-bottom:32px; line-height:1.6; }
.rv2-divider { width:44px; height:3px; background:linear-gradient(90deg,#6FBA9D,#A8D8C6); border-radius:3px; margin-bottom:24px; }
.rv2-desc { font-size:.81rem; color:#8AADA0; line-height:1.8; max-width:270px; margin-bottom:32px; }
.rv2-features { display:flex; flex-direction:column; gap:11px; }
.rv2-feat { display:flex; align-items:center; gap:11px; font-size:.79rem; color:#2A4235; font-weight:500; }
.rv2-feat-ico {
  width:30px; height:30px; border-radius:8px; background:#EBF7F2;
  display:flex; align-items:center; justify-content:center;
  color:#3D8A6E; font-size:.73rem; flex-shrink:0;
}

/* Responsive */
@media (max-width: 900px) {
  .rv2-layout { gap:48px; padding:32px 36px; }
  .rv2-brand { flex:0 0 260px; }
  .rv2-title { font-size:2.7rem; }
}
@media (max-width: 720px) {
  body.auth-page { align-items:flex-start !important; overflow-y:auto !important; }
  .rv2-wrap { align-items:flex-start; min-height:auto; padding:24px 0 40px; }
  .rv2-layout { flex-direction:column; padding:0 20px; gap:28px; }
  .rv2-form-panel { order:2; max-width:100%; }
  .rv2-brand { order:1; flex:none; }
  .rv2-title { font-size:2.2rem; }
  .rv2-logo { width:56px; height:56px; margin:0 auto 14px; display:block; }
  .rv2-features, .rv2-desc, .rv2-divider { display:none; }
  .rv2-sub { margin-bottom:0; }
  .rv2-card { padding:28px 20px; }
  .rv2-ring.r1 { width:260px; height:260px; top:-70px; left:-70px; }
  .rv2-ring.r2 { display:none; }
}
@media (max-width: 420px) {
  .rv2-title { font-size:1.85rem; }
  .rv2-card { padding:24px 16px; border-radius:16px; }
  .rv2-card-title { font-size:1.5rem; }
}
@media (min-width: 1280px) {
  .rv2-layout { max-width:1160px; padding:40px 80px; }
  .rv2-brand { flex:0 0 360px; }
  .rv2-title { font-size:3.6rem; }
}
</style>

<div class="rv2-wrap">
  <div class="rv2-bg"></div>
  <div class="rv2-ring r1"></div>
  <div class="rv2-ring r2"></div>
  <div class="rv2-ring r3"></div>
  <div class="rv2-dot d1"></div>
  <div class="rv2-dot d2"></div>
  <div class="rv2-dot d3"></div>
  <div class="rv2-line l1"></div>
  <div class="rv2-line l2"></div>

  <div class="rv2-layout">

    <!-- Form (kiri) -->
    <div class="rv2-form-panel">
      <div class="rv2-card">
        <div class="rv2-card-lbl">Pendaftaran Admin</div>
        <div class="rv2-card-title">Buat Akun Baru</div>
        <div class="rv2-card-desc">Isi data berikut untuk mendaftarkan akun admin Anda.</div>

        @if ($errors->any())
          <div class="rv2-alert">
            @foreach ($errors->all() as $error)
              <p><i class="fas fa-circle-exclamation"></i> {{ $error }}</p>
            @endforeach
          </div>
        @endif

        <form method="POST" action="{{ route('admin.register') }}">
          @csrf

          <div class="rv2-field">
            <label class="rv2-lbl">Email Admin</label>
            <div class="rv2-shell">
              <i class="fas fa-envelope fi" id="ico-e"></i>
              <input type="email" id="email" name="email"
                     value="{{ old('email') }}"
                     placeholder="nama@institusi.com"
                     autocomplete="email" required autofocus
                     onfocus="document.getElementById('ico-e').style.color='#6FBA9D'"
                     onblur="document.getElementById('ico-e').style.color=''">
            </div>
            @error('email')<div class="rv2-ferr">{{ $message }}</div>@enderror
          </div>

          <div class="rv2-field">
            <label class="rv2-lbl">Password</label>
            <div class="rv2-shell">
              <i class="fas fa-lock fi" id="ico-pw"></i>
              <input type="password" id="password" name="password"
                     placeholder="Buat password yang kuat"
                     oninput="rv2Str(this.value)" required
                     onfocus="document.getElementById('ico-pw').style.color='#6FBA9D'"
                     onblur="document.getElementById('ico-pw').style.color=''">
              <button type="button" class="rv2-show" id="rv2TglBtn">SHOW</button>
            </div>
            <div class="rv2-strength">
              <div class="rv2-bar" id="rv2b1"></div>
              <div class="rv2-bar" id="rv2b2"></div>
              <div class="rv2-bar" id="rv2b3"></div>
              <div class="rv2-bar" id="rv2b4"></div>
            </div>
            @error('password')<div class="rv2-ferr">{{ $message }}</div>@enderror
          </div>

          <div class="rv2-field" style="margin-bottom:22px;">
            <label class="rv2-lbl">Konfirmasi Password</label>
            <div class="rv2-shell">
              <i class="fas fa-lock-open fi" id="ico-c"></i>
              <input type="password" id="password_confirmation"
                     name="password_confirmation"
                     placeholder="Ulangi password Anda" required
                     onfocus="document.getElementById('ico-c').style.color='#6FBA9D'"
                     onblur="document.getElementById('ico-c').style.color=''">
            </div>
          </div>

          <button type="submit" class="rv2-btn">
            <i class="fas fa-user-plus"></i>
            Daftarkan Akun Admin
          </button>

          <div class="rv2-foot">
            Sudah punya akun? <a href="{{ route('admin.login') }}">Login di sini</a>
          </div>
        </form>
      </div>
    </div>

    <!-- Brand (kanan) -->
    <div class="rv2-brand">
      <img src="{{ asset('images/logo.png') }}" alt="Logo PKPPS" class="rv2-logo">
      <div class="rv2-eyebrow">Bergabung Sekarang</div>
      <h1 class="rv2-title">Bergabung<br>Bersama<br><em>Kami.</em></h1>
      <p class="rv2-sub">PKPPS Riyadlul Jannah</p>
      <div class="rv2-divider"></div>
      <p class="rv2-desc">Daftarkan akun admin baru dengan aman. Gunakan email dan password kuat untuk menjaga keamanan sistem pesantren.</p>
      <div class="rv2-features">
        <div class="rv2-feat">
          <div class="rv2-feat-ico"><i class="fas fa-envelope"></i></div>
          <span>Gunakan email institusi yang valid</span>
        </div>
        <div class="rv2-feat">
          <div class="rv2-feat-ico"><i class="fas fa-key"></i></div>
          <span>Password minimal 8 karakter campuran</span>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const btn = document.getElementById('rv2TglBtn');
  const pw  = document.getElementById('password');
  if (btn && pw) {
    btn.addEventListener('click', () => {
      const isP = pw.type === 'password';
      pw.type = isP ? 'text' : 'password';
      btn.textContent = isP ? 'HIDE' : 'SHOW';
    });
  }
});
function rv2Str(v) {
  const bars = ['rv2b1','rv2b2','rv2b3','rv2b4'].map(id => document.getElementById(id));
  bars.forEach(b => b.className = 'rv2-bar');
  let s = 0;
  if (v.length >= 6) s++;
  if (v.length >= 10) s++;
  if (/[A-Z]/.test(v) && /[0-9]/.test(v)) s++;
  if (/[^A-Za-z0-9]/.test(v)) s++;
  const cls = s <= 1 ? 'w' : s <= 2 ? 'm' : 's';
  for (let i = 0; i < s; i++) bars[i].classList.add(cls);
}
</script>

@endsection