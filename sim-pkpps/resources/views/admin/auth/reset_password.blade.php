{{-- resources/views/admin/auth/reset_password.blade.php --}}
@extends('auth.auth_layout')

@section('title', 'Reset Password')

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

.rp-wrap {
  position: relative; width: 100%; min-height: 100vh;
  display: flex; align-items: center; justify-content: center;
  overflow: hidden; font-family: 'DM Sans', sans-serif;
}
.rp-bg {
  position: absolute; inset: 0; z-index: 0;
  background:
    radial-gradient(ellipse 80% 60% at 70% 50%, rgba(111,186,157,.12) 0%, transparent 60%),
    radial-gradient(ellipse 50% 80% at 10% 80%, rgba(111,186,157,.08) 0%, transparent 55%),
    #F8FDFB;
}
.rp-bg::before {
  content: ''; position: absolute; inset: 0;
  background-image:
    linear-gradient(rgba(111,186,157,.055) 1px, transparent 1px),
    linear-gradient(90deg, rgba(111,186,157,.055) 1px, transparent 1px);
  background-size: 48px 48px;
}
.rp-ring { position: absolute; border-radius: 50%; border: 1.5px solid rgba(111,186,157,.18); pointer-events: none; }
.rp-ring.r1 { width:420px; height:420px; top:-110px; right:-110px; }
.rp-ring.r2 { width:200px; height:200px; bottom:-50px; left:60px; border-color:rgba(111,186,157,.14); }
.rp-dot { position:absolute; border-radius:50%; background:#6FBA9D; pointer-events:none; }
.rp-dot.d1 { width:8px; height:8px; top:22%; right:18%; opacity:.14; }
.rp-dot.d2 { width:11px; height:11px; top:55%; left:15%; opacity:.08; }

.rp-layout {
  position: relative; z-index: 2;
  display: flex; align-items: center;
  width: 100%; max-width: 1100px;
  padding: 40px 60px; gap: 80px;
  animation: rpIn .6s ease both;
}
@keyframes rpIn {
  from { opacity:0; transform:translateY(20px); }
  to   { opacity:1; transform:translateY(0); }
}
.rp-brand      { flex: 0 0 340px; order: 1; }
.rp-form-panel { flex: 1; max-width: 460px; order: 2; }

/* Brand */
.rp-logo { width:72px; height:72px; margin-bottom:20px; border-radius:16px; box-shadow:0 4px 20px rgba(111,186,157,.2); object-fit:contain; background:#fff; }
.rp-eyebrow {
  display:inline-flex; align-items:center; gap:8px;
  font-size:.68rem; font-weight:700; letter-spacing:2px;
  text-transform:uppercase; color:#6FBA9D; margin-bottom:18px;
}
.rp-eyebrow::before { content:''; display:inline-block; width:22px; height:2px; background:#6FBA9D; border-radius:2px; }
.rp-title {
  font-family:'DM Serif Display',serif;
  font-size:3.2rem; line-height:1.05; color:#0F2118; margin-bottom:6px;
}
.rp-title em { font-style:italic; color:#5EA98C; }
.rp-sub { font-size:.9rem; font-weight:500; color:#8AADA0; margin-bottom:32px; line-height:1.6; }
.rp-divider { width:44px; height:3px; background:linear-gradient(90deg,#6FBA9D,#A8D8C6); border-radius:3px; margin-bottom:24px; }
.rp-desc { font-size:.81rem; color:#8AADA0; line-height:1.8; max-width:290px; margin-bottom:32px; }

/* Steps */
.rp-steps { display:flex; flex-direction:column; gap:14px; margin-bottom:28px; }
.rp-step { display:flex; align-items:flex-start; gap:12px; }
.rp-step-num {
  width:28px; height:28px; border-radius:50%;
  background:#D6EDE5; color:#5EA98C;
  font-size:.72rem; font-weight:800;
  display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.rp-step-num.done { background:linear-gradient(135deg,#6FBA9D,#5EA98C); color:#fff; }
.rp-step-num.active { background:linear-gradient(135deg,#6FBA9D,#5EA98C); color:#fff; box-shadow:0 0 0 4px rgba(111,186,157,.2); }
.rp-step-text { font-size:.78rem; color:#2A4235; font-weight:500; line-height:1.5; padding-top:3px; }
.rp-step-text small { display:block; color:#8AADA0; font-weight:400; font-size:.72rem; }

/* Password rules */
.rp-rules { margin-bottom:24px; }
.rp-rules-title { font-size:.72rem; font-weight:700; letter-spacing:.5px; color:#2A4235; margin-bottom:10px; text-transform:uppercase; }
.rp-rule {
  display:flex; align-items:center; gap:8px;
  font-size:.76rem; color:#8AADA0; font-weight:500;
  padding:4px 0; transition:color .2s;
}
.rp-rule i { font-size:.65rem; width:16px; text-align:center; }
.rp-rule.pass { color:#2E7D32; }
.rp-rule.pass i { color:#43A047; }
.rp-rule.fail { color:#c62828; }
.rp-rule.fail i { color:#e53935; }

/* Card */
.rp-card {
  background: #fff; border-radius: 24px;
  padding: 42px 38px;
  box-shadow:
    0 0 0 1px rgba(111,186,157,.1),
    0 4px 6px rgba(15,33,24,.03),
    0 20px 44px rgba(15,33,24,.08);
  position: relative; overflow: hidden;
}
.rp-card::before {
  content: ''; position:absolute; top:0; left:0; right:0; height:3px;
  background: linear-gradient(90deg, #6FBA9D, #A8D8C6, #6FBA9D);
}
.rp-card::after {
  content: ''; position:absolute; bottom:-50px; right:-50px;
  width:140px; height:140px; border-radius:50%;
  background: radial-gradient(circle, rgba(111,186,157,.06) 0%, transparent 70%);
}
.rp-card-icon {
  width:54px; height:54px; border-radius:14px;
  background:linear-gradient(135deg,#E8F5E9,#C8E6C9);
  display:flex; align-items:center; justify-content:center;
  font-size:1.3rem; color:#2E7D32; margin-bottom:16px;
}
.rp-card-lbl {
  font-size:.67rem; font-weight:700; letter-spacing:2px;
  text-transform:uppercase; color:#6FBA9D; margin-bottom:7px;
}
.rp-card-title {
  font-family:'DM Serif Display',serif;
  font-size:1.85rem; color:#0F2118; line-height:1.1; margin-bottom:5px;
}
.rp-card-desc { font-size:.79rem; color:#8AADA0; line-height:1.6; margin-bottom:26px; }

/* Alert */
.rp-alert-danger {
  border-radius:10px; padding:10px 13px; font-size:.79rem;
  margin-bottom:18px; background:#FFF3F3; color:#c62828; border-left:3px solid #e53935;
  display:flex; align-items:center; gap:7px;
}
.rp-alert-success {
  border-radius:10px; padding:10px 13px; font-size:.79rem;
  margin-bottom:18px; background:#F0FFF4; color:#2E7D32; border-left:3px solid #43A047;
  display:flex; align-items:center; gap:7px;
}

/* Fields */
.rp-field { margin-bottom:16px; }
.rp-lbl { display:block; font-size:.7rem; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:#2A4235; margin-bottom:7px; }
.rp-shell { position:relative; display:flex; align-items:center; }
.rp-shell .fi { position:absolute; left:15px; color:#A8D8C6; font-size:.8rem; pointer-events:none; transition:color .2s; }
.rp-shell input {
  width:100%; padding:12px 50px 12px 40px;
  background:#EBF7F2; border:1.5px solid transparent;
  border-radius:11px; font-family:inherit; font-size:.87rem; color:#0F2118; outline:none;
  transition:all .2s;
}
.rp-shell input::placeholder { color:#8AADA0; font-size:.83rem; }
.rp-shell input:focus { background:#fff; border-color:#6FBA9D; box-shadow:0 0 0 4px rgba(111,186,157,.12); }
.rp-shell .fi.active { color:#6FBA9D; }
.rp-show {
  position:absolute; right:13px;
  background:none; border:none; font-size:.68rem; font-weight:800;
  letter-spacing:.8px; color:#5EA98C; cursor:pointer; font-family:inherit;
}
.rp-show:hover { color:#3D8A6E; }

/* Strength bar */
.rp-strength {
  display:flex; gap:4px; margin-top:8px;
}
.rp-bar { height:4px; flex:1; border-radius:3px; background:#D6EDE5; transition:background .3s; }
.rp-str-text { font-size:.72rem; margin-top:4px; font-weight:600; transition:color .3s; }

/* Match */
.rp-match { font-size:.72rem; margin-top:6px; font-weight:600; }

/* Button */
.rp-btn {
  width:100%; padding:13px;
  background:linear-gradient(135deg, #6FBA9D, #5EA98C);
  color:#fff; border:none; border-radius:12px;
  font-family:inherit; font-size:.89rem; font-weight:700;
  cursor:pointer; letter-spacing:.3px; margin-top:4px;
  display:flex; align-items:center; justify-content:center; gap:8px;
  box-shadow:0 4px 18px rgba(94,169,140,.35);
  transition:all .25s;
}
.rp-btn:hover { transform:translateY(-2px); box-shadow:0 8px 26px rgba(94,169,140,.45); }
.rp-btn:active { transform:none; }
.rp-btn:disabled {
  background:#D6EDE5; color:#8AADA0; cursor:not-allowed;
  box-shadow:none; transform:none;
}

.rp-back {
  display:flex; align-items:center; justify-content:center; gap:6px;
  margin-top:20px; font-size:.78rem; color:#5EA98C; font-weight:600; text-decoration:none;
}
.rp-back:hover { color:#3D8A6E; text-decoration:underline; }

/* Responsive */
@media (max-width: 900px) {
  .rp-layout { gap:48px; padding:32px 36px; }
  .rp-brand { flex:0 0 260px; }
  .rp-title { font-size:2.7rem; }
}
@media (max-width: 720px) {
  body.auth-page { align-items:flex-start !important; overflow-y:auto !important; }
  .rp-wrap { align-items:flex-start; min-height:auto; padding:24px 0 40px; }
  .rp-layout { flex-direction:column; padding:0 20px; gap:28px; }
  .rp-form-panel { order:2; max-width:100%; }
  .rp-brand { order:1; flex:none; text-align:center; }
  .rp-title { font-size:2.2rem; }
  .rp-steps, .rp-desc, .rp-divider, .rp-rules { display:none; }
  .rp-sub { margin-bottom:0; }
  .rp-card { padding:28px 20px; }
  .rp-logo { width:56px; height:56px; margin:0 auto 14px; display:block; }
}
@media (max-width: 420px) {
  .rp-title { font-size:1.85rem; }
  .rp-card { padding:24px 16px; border-radius:16px; }
  .rp-card-title { font-size:1.5rem; }
}
@media (min-width: 1280px) {
  .rp-layout { max-width:1160px; padding:40px 80px; }
  .rp-brand { flex:0 0 360px; }
  .rp-title { font-size:3.6rem; }
}
</style>

<div class="rp-wrap">
  <div class="rp-bg"></div>
  <div class="rp-ring r1"></div>
  <div class="rp-ring r2"></div>
  <div class="rp-dot d1"></div>
  <div class="rp-dot d2"></div>

  <div class="rp-layout">

    <!-- ═══ Brand (kiri) ═══ -->
    <div class="rp-brand">
      <img src="{{ asset('images/logo.png') }}" alt="Logo PKPPS" class="rp-logo">
      <div class="rp-eyebrow">Langkah Terakhir</div>
      <h1 class="rp-title">Buat<br>Password<br><em>Baru.</em></h1>
      <p class="rp-sub">PKPPS Riyadlul Jannah</p>
      <div class="rp-divider"></div>

      <div class="rp-steps">
        <div class="rp-step">
          <div class="rp-step-num done"><i class="fas fa-check" style="font-size:.6rem"></i></div>
          <div class="rp-step-text" style="color:#8AADA0;">Email terkirim</div>
        </div>
        <div class="rp-step">
          <div class="rp-step-num done"><i class="fas fa-check" style="font-size:.6rem"></i></div>
          <div class="rp-step-text" style="color:#8AADA0;">OTP terverifikasi</div>
        </div>
        <div class="rp-step">
          <div class="rp-step-num active">3</div>
          <div class="rp-step-text">Buat password baru <small>Ikuti ketentuan di bawah</small></div>
        </div>
      </div>

      {{-- Ketentuan Password --}}
      <div class="rp-rules">
        <div class="rp-rules-title"><i class="fas fa-info-circle"></i> Ketentuan Password</div>
        <div class="rp-rule" id="rule-length">
          <i class="fas fa-circle"></i> Minimal 8 karakter
        </div>
        <div class="rp-rule" id="rule-upper">
          <i class="fas fa-circle"></i> Mengandung huruf besar (A-Z)
        </div>
        <div class="rp-rule" id="rule-lower">
          <i class="fas fa-circle"></i> Mengandung huruf kecil (a-z)
        </div>
        <div class="rp-rule" id="rule-number">
          <i class="fas fa-circle"></i> Mengandung angka (0-9)
        </div>
        <div class="rp-rule" id="rule-special">
          <i class="fas fa-circle"></i> Mengandung simbol (!@#$%...)
        </div>
      </div>
    </div>

    <!-- ═══ Form (kanan) ═══ -->
    <div class="rp-form-panel">
      <div class="rp-card">
        <div class="rp-card-icon">
          <i class="fas fa-lock-open"></i>
        </div>
        <div class="rp-card-lbl">Langkah 3 dari 3</div>
        <div class="rp-card-title">Password Baru</div>
        <div class="rp-card-desc">Buat password baru yang kuat. Password harus memenuhi semua ketentuan keamanan berikut.</div>

        {{-- Ketentuan Password (visible di mobile saja) --}}
        <div class="rp-rules-mobile" style="display:none; margin-bottom:20px;">
          <div class="rp-rules-title" style="font-size:.68rem;"><i class="fas fa-info-circle"></i> Ketentuan Password</div>
          <div class="rp-rule" id="rule-length-m"><i class="fas fa-circle"></i> Minimal 8 karakter</div>
          <div class="rp-rule" id="rule-upper-m"><i class="fas fa-circle"></i> Huruf besar (A-Z)</div>
          <div class="rp-rule" id="rule-lower-m"><i class="fas fa-circle"></i> Huruf kecil (a-z)</div>
          <div class="rp-rule" id="rule-number-m"><i class="fas fa-circle"></i> Angka (0-9)</div>
          <div class="rp-rule" id="rule-special-m"><i class="fas fa-circle"></i> Simbol (!@#$%...)</div>
        </div>

        @if ($errors->any())
          <div class="rp-alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->first() }}
          </div>
        @endif

        @if(session('success'))
          <div class="rp-alert-success" id="rpSuccessAlert">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
          </div>
        @endif

        <form method="POST" action="{{ route('admin.forgot.reset_password') }}" id="resetForm">
          @csrf
          <input type="hidden" name="email" value="{{ $email }}">

          <div class="rp-field">
            <label class="rp-lbl">Password Baru</label>
            <div class="rp-shell">
              <i class="fas fa-lock fi" id="ico-pw"></i>
              <input type="password" id="password" name="password"
                     placeholder="Buat password yang kuat"
                     required autofocus
                     onfocus="document.getElementById('ico-pw').classList.add('active')"
                     onblur="document.getElementById('ico-pw').classList.remove('active')">
              <button type="button" class="rp-show" id="tglPw">SHOW</button>
            </div>
            <div class="rp-strength">
              <div class="rp-bar" id="bar1"></div>
              <div class="rp-bar" id="bar2"></div>
              <div class="rp-bar" id="bar3"></div>
              <div class="rp-bar" id="bar4"></div>
            </div>
            <div class="rp-str-text" id="strText"></div>
          </div>

          <div class="rp-field">
            <label class="rp-lbl">Konfirmasi Password</label>
            <div class="rp-shell">
              <i class="fas fa-lock-open fi" id="ico-pc"></i>
              <input type="password" id="password_confirmation" name="password_confirmation"
                     placeholder="Ulangi password baru"
                     required
                     onfocus="document.getElementById('ico-pc').classList.add('active')"
                     onblur="document.getElementById('ico-pc').classList.remove('active')">
              <button type="button" class="rp-show" id="tglPc">SHOW</button>
            </div>
            <div class="rp-match" id="matchText"></div>
          </div>

          <button type="submit" class="rp-btn" id="submitBtn" disabled>
            <i class="fas fa-save"></i>
            Ubah Password
          </button>
        </form>

        <a href="{{ route('admin.login') }}" class="rp-back">
          <i class="fas fa-arrow-left"></i> Kembali ke Login
        </a>
      </div>
    </div>

  </div>
</div>

<style>
  @media (max-width: 720px) {
    .rp-rules-mobile { display:block !important; }
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const pw = document.getElementById('password');
  const pc = document.getElementById('password_confirmation');
  const bars = ['bar1','bar2','bar3','bar4'].map(id => document.getElementById(id));
  const strText = document.getElementById('strText');
  const matchText = document.getElementById('matchText');
  const submitBtn = document.getElementById('submitBtn');

  // Toggle show/hide
  function setupToggle(btnId, inputId) {
    const btn = document.getElementById(btnId);
    const inp = document.getElementById(inputId);
    if (btn && inp) {
      btn.addEventListener('click', () => {
        const isP = inp.type === 'password';
        inp.type = isP ? 'text' : 'password';
        btn.textContent = isP ? 'HIDE' : 'SHOW';
      });
    }
  }
  setupToggle('tglPw', 'password');
  setupToggle('tglPc', 'password_confirmation');

  // Rule checker
  function updateRule(id, pass) {
    const el = document.getElementById(id);
    if (!el) return;
    const icon = el.querySelector('i');
    if (pass) {
      el.classList.add('pass');
      el.classList.remove('fail');
      icon.className = 'fas fa-check-circle';
    } else {
      el.classList.remove('pass');
      el.classList.add('fail');
      icon.className = 'fas fa-circle';
    }
  }

  function checkRules(val) {
    const rules = {
      'length': val.length >= 8,
      'upper': /[A-Z]/.test(val),
      'lower': /[a-z]/.test(val),
      'number': /[0-9]/.test(val),
      'special': /[^A-Za-z0-9]/.test(val),
    };

    // Update both desktop and mobile rules
    Object.entries(rules).forEach(([key, pass]) => {
      updateRule('rule-' + key, pass);
      updateRule('rule-' + key + '-m', pass);
    });

    return Object.values(rules).filter(Boolean).length;
  }

  // Strength
  pw.addEventListener('input', function() {
    const val = this.value;
    const passed = checkRules(val);

    // Reset bars
    bars.forEach(b => b.style.background = '#D6EDE5');

    if (val.length === 0) {
      strText.textContent = '';
      validateForm();
      return;
    }

    let color, label;
    if (passed <= 2) { color = '#e53935'; label = 'Lemah'; }
    else if (passed <= 3) { color = '#FB8C00'; label = 'Sedang'; }
    else if (passed <= 4) { color = '#FFB74D'; label = 'Cukup Baik'; }
    else { color = '#6FBA9D'; label = 'Kuat'; }

    for (let i = 0; i < Math.min(passed, 4); i++) bars[i].style.background = color;
    strText.textContent = label;
    strText.style.color = color;

    checkMatch();
    validateForm();
  });

  // Match
  function checkMatch() {
    if (!pc.value) { matchText.textContent = ''; return; }
    if (pw.value === pc.value) {
      matchText.textContent = '✓ Password cocok';
      matchText.style.color = '#6FBA9D';
    } else {
      matchText.textContent = '✗ Password tidak cocok';
      matchText.style.color = '#e53935';
    }
  }
  pc.addEventListener('input', function() { checkMatch(); validateForm(); });

  // Validate form
  function validateForm() {
    const val = pw.value;
    const allPass = val.length >= 8
      && /[A-Z]/.test(val)
      && /[a-z]/.test(val)
      && /[0-9]/.test(val)
      && /[^A-Za-z0-9]/.test(val)
      && pw.value === pc.value
      && pc.value.length > 0;

    submitBtn.disabled = !allPass;
  }

  // Auto-hide success
  const sa = document.getElementById('rpSuccessAlert');
  if (sa) {
    setTimeout(() => {
      sa.style.transition = 'opacity .5s ease';
      sa.style.opacity = '0';
      setTimeout(() => sa.remove(), 500);
    }, 5000);
  }

  // Init rules to neutral
  if (!pw.value) {
    ['length','upper','lower','number','special'].forEach(key => {
      ['rule-'+key, 'rule-'+key+'-m'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.classList.remove('pass','fail'); }
      });
    });
  }
});
</script>
@endsection
