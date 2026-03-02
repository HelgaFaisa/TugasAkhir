<?php $__env->startSection('title', 'Login Admin'); ?>

<?php $__env->startSection('auth-content'); ?>

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
.lg-wrap {
  position: relative; width: 100%; min-height: 100vh;
  display: flex; align-items: center; justify-content: center;
  overflow: hidden; font-family: 'DM Sans', sans-serif;
}
.lg-bg {
  position: absolute; inset: 0; z-index: 0;
  background:
    radial-gradient(ellipse 80% 60% at 70% 50%, rgba(111,186,157,.12) 0%, transparent 60%),
    radial-gradient(ellipse 50% 80% at 10% 80%, rgba(111,186,157,.08) 0%, transparent 55%),
    #F8FDFB;
}
.lg-bg::before {
  content: ''; position: absolute; inset: 0;
  background-image:
    linear-gradient(rgba(111,186,157,.055) 1px, transparent 1px),
    linear-gradient(90deg, rgba(111,186,157,.055) 1px, transparent 1px);
  background-size: 48px 48px;
}

/* Decorations */
.lg-ring { position: absolute; border-radius: 50%; border: 1.5px solid rgba(111,186,157,.18); pointer-events: none; }
.lg-ring.r1 { width:420px; height:420px; top:-110px; right:-110px; }
.lg-ring.r2 { width:270px; height:270px; bottom:50px; right:60px; border-color:rgba(111,186,157,.10); }
.lg-ring.r3 { width:200px; height:200px; bottom:-50px; left:60px; border-color:rgba(111,186,157,.14); }
.lg-dot { position:absolute; border-radius:50%; background:#6FBA9D; pointer-events:none; }
.lg-dot.d1 { width:8px; height:8px; top:22%; right:18%; opacity:.14; }
.lg-dot.d2 { width:5px; height:5px; bottom:40%; right:30%; opacity:.09; }
.lg-dot.d3 { width:11px; height:11px; top:55%; left:15%; opacity:.08; }
.lg-line { position:absolute; height:1px; background:linear-gradient(90deg,transparent,rgba(111,186,157,.14),transparent); pointer-events:none; }
.lg-line.l1 { width:280px; top:28%; left:-60px; transform:rotate(-15deg); }
.lg-line.l2 { width:220px; bottom:30%; right:-40px; transform:rotate(18deg); }

/* Layout */
.lg-layout {
  position: relative; z-index: 2;
  display: flex; align-items: center;
  width: 100%; max-width: 1100px;
  padding: 40px 60px; gap: 80px;
  animation: lgIn .6s ease both;
}
@keyframes lgIn {
  from { opacity:0; transform:translateY(20px); }
  to   { opacity:1; transform:translateY(0); }
}

/* Brand left, Form right */
.lg-brand      { flex: 0 0 340px; order: 1; }
.lg-form-panel { flex: 1; max-width: 430px; order: 2; }

/* ── Brand section ── */
.lg-logo { width:72px; height:72px; margin-bottom:20px; border-radius:16px; box-shadow:0 4px 20px rgba(111,186,157,.2); object-fit:contain; background:#fff; }
.lg-eyebrow {
  display:inline-flex; align-items:center; gap:8px;
  font-size:.68rem; font-weight:700; letter-spacing:2px;
  text-transform:uppercase; color:#6FBA9D; margin-bottom:18px;
}
.lg-eyebrow::before {
  content:''; display:inline-block; width:22px; height:2px;
  background:#6FBA9D; border-radius:2px;
}
.lg-title {
  font-family:'DM Serif Display',serif;
  font-size:3.2rem; line-height:1.05; color:#0F2118; margin-bottom:6px;
}
.lg-title em { font-style:italic; color:#5EA98C; }
.lg-sub { font-size:.9rem; font-weight:500; color:#8AADA0; margin-bottom:32px; line-height:1.6; }
.lg-divider { width:44px; height:3px; background:linear-gradient(90deg,#6FBA9D,#A8D8C6); border-radius:3px; margin-bottom:24px; }
.lg-desc { font-size:.81rem; color:#8AADA0; line-height:1.8; max-width:290px; margin-bottom:32px; }
.lg-features { display:flex; flex-direction:column; gap:11px; }
.lg-feat { display:flex; align-items:center; gap:11px; font-size:.79rem; color:#2A4235; font-weight:500; }
.lg-feat-ico {
  width:30px; height:30px; border-radius:8px; background:#EBF7F2;
  display:flex; align-items:center; justify-content:center;
  color:#3D8A6E; font-size:.73rem; flex-shrink:0;
}

/* ── Card ── */
.lg-card {
  background: #fff; border-radius: 24px;
  padding: 42px 38px;
  box-shadow:
    0 0 0 1px rgba(111,186,157,.1),
    0 4px 6px rgba(15,33,24,.03),
    0 20px 44px rgba(15,33,24,.08);
  position: relative; overflow: hidden;
}
.lg-card::before {
  content: ''; position:absolute; top:0; left:0; right:0; height:3px;
  background: linear-gradient(90deg, #6FBA9D, #A8D8C6, #6FBA9D);
}
.lg-card::after {
  content: ''; position:absolute; bottom:-50px; right:-50px;
  width:140px; height:140px; border-radius:50%;
  background: radial-gradient(circle, rgba(111,186,157,.06) 0%, transparent 70%);
}
.lg-card-lbl {
  font-size:.67rem; font-weight:700; letter-spacing:2px;
  text-transform:uppercase; color:#6FBA9D; margin-bottom:7px;
}
.lg-card-title {
  font-family:'DM Serif Display',serif;
  font-size:1.85rem; color:#0F2118; line-height:1.1; margin-bottom:5px;
}
.lg-card-desc { font-size:.79rem; color:#8AADA0; line-height:1.6; margin-bottom:26px; }

/* Alert */
.lg-alert-danger {
  border-radius:10px; padding:10px 13px; font-size:.79rem;
  margin-bottom:18px; background:#FFF3F3; color:#c62828; border-left:3px solid #e53935;
  display:flex; align-items:center; gap:7px;
}
.lg-alert-success {
  border-radius:10px; padding:10px 13px; font-size:.79rem;
  margin-bottom:18px; background:#F0FFF4; color:#2E7D32; border-left:3px solid #43A047;
  display:flex; align-items:center; gap:7px;
}

/* Fields */
.lg-field { margin-bottom:15px; }
.lg-lbl {
  display:block; font-size:.7rem; font-weight:700;
  letter-spacing:.8px; text-transform:uppercase; color:#2A4235; margin-bottom:7px;
}
.lg-shell { position:relative; display:flex; align-items:center; }
.lg-shell .fi { position:absolute; left:15px; color:#A8D8C6; font-size:.8rem; pointer-events:none; transition:color .2s; }
.lg-shell input {
  width:100%; padding:12px 15px 12px 40px;
  background:#EBF7F2; border:1.5px solid transparent;
  border-radius:11px; font-family:inherit; font-size:.87rem; color:#0F2118; outline:none;
  transition:all .2s;
}
.lg-shell input::placeholder { color:#8AADA0; font-size:.83rem; }
.lg-shell input:focus {
  background:#fff; border-color:#6FBA9D;
  box-shadow:0 0 0 4px rgba(111,186,157,.12);
}
.lg-shell .fi.active { color:#6FBA9D; }
.lg-show {
  position:absolute; right:13px;
  background:none; border:none; font-size:.68rem; font-weight:800;
  letter-spacing:.8px; color:#5EA98C; cursor:pointer; font-family:inherit;
}
.lg-show:hover { color:#3D8A6E; }

/* Remember + Forgot row */
.lg-options {
  display:flex; align-items:center; justify-content:space-between;
  margin-bottom:18px; font-size:.78rem;
}
.lg-remember { display:flex; align-items:center; gap:7px; color:#2A4235; font-weight:500; cursor:pointer; }
.lg-remember input[type="checkbox"] {
  width:16px; height:16px; accent-color:#6FBA9D; cursor:pointer;
}
.lg-forgot { color:#e57373; font-weight:600; text-decoration:none; transition:color .2s; }
.lg-forgot:hover { color:#c62828; text-decoration:underline; }

/* Buttons */
.lg-btn {
  width:100%; padding:13px;
  background:linear-gradient(135deg, #6FBA9D, #5EA98C);
  color:#fff; border:none; border-radius:12px;
  font-family:inherit; font-size:.89rem; font-weight:700;
  cursor:pointer; letter-spacing:.3px; margin-top:6px;
  display:flex; align-items:center; justify-content:center; gap:8px;
  box-shadow:0 4px 18px rgba(94,169,140,.35);
  transition:all .25s;
}
.lg-btn:hover { transform:translateY(-2px); box-shadow:0 8px 26px rgba(94,169,140,.45); }
.lg-btn:active { transform:none; }

.lg-foot { text-align:center; font-size:.77rem; color:#8AADA0; margin-top:20px; }
.lg-foot a { color:#5EA98C; font-weight:700; text-decoration:none; }
.lg-foot a:hover { text-decoration:underline; }

/* Santri separator */
.lg-sep {
  display:flex; align-items:center; gap:12px; margin-top:22px;
  font-size:.7rem; color:#B8D4C8; letter-spacing:1px; text-transform:uppercase; font-weight:600;
}
.lg-sep::before, .lg-sep::after {
  content:''; flex:1; height:1px; background:linear-gradient(90deg,transparent,#D6EDE5,transparent);
}
.lg-santri-link {
  display:flex; align-items:center; justify-content:center; gap:8px;
  margin-top:12px; padding:10px;
  background:#EBF7F2; border:1.5px solid transparent; border-radius:10px;
  font-size:.8rem; font-weight:600; color:#3D8A6E; text-decoration:none;
  transition:all .2s;
}
.lg-santri-link:hover {
  border-color:#6FBA9D; background:#fff; box-shadow:0 0 0 3px rgba(111,186,157,.1);
}

/* Responsive */
@media (max-width: 900px) {
  .lg-layout { gap:48px; padding:32px 36px; }
  .lg-brand { flex:0 0 260px; }
  .lg-title { font-size:2.7rem; }
}
@media (max-width: 720px) {
  body.auth-page { align-items:flex-start !important; overflow-y:auto !important; }
  .lg-wrap { align-items:flex-start; min-height:auto; padding:24px 0 40px; }
  .lg-layout { flex-direction:column; padding:0 20px; gap:28px; }
  .lg-form-panel { order:2; max-width:100%; }
  .lg-brand { order:1; flex:none; text-align:center; }
  .lg-title { font-size:2.2rem; }
  .lg-features, .lg-desc, .lg-divider { display:none; }
  .lg-sub { margin-bottom:0; }
  .lg-card { padding:28px 20px; }
  .lg-ring.r1 { width:260px; height:260px; top:-70px; right:-70px; }
  .lg-ring.r2 { display:none; }
  .lg-logo { width:56px; height:56px; margin:0 auto 14px; display:block; }
}
@media (max-width: 420px) {
  .lg-title { font-size:1.85rem; }
  .lg-card { padding:24px 16px; border-radius:16px; }
  .lg-card-title { font-size:1.5rem; }
  .lg-options { flex-direction:column; align-items:flex-start; gap:10px; }
}
@media (min-width: 1280px) {
  .lg-layout { max-width:1160px; padding:40px 80px; }
  .lg-brand { flex:0 0 360px; }
  .lg-title { font-size:3.6rem; }
}
</style>

<div class="lg-wrap">
  <div class="lg-bg"></div>
  <div class="lg-ring r1"></div>
  <div class="lg-ring r2"></div>
  <div class="lg-ring r3"></div>
  <div class="lg-dot d1"></div>
  <div class="lg-dot d2"></div>
  <div class="lg-dot d3"></div>
  <div class="lg-line l1"></div>
  <div class="lg-line l2"></div>

  <div class="lg-layout">

    <!-- ═══ Brand (kiri) ═══ -->
    <div class="lg-brand">
      <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Logo PKPPS" class="lg-logo">
      <div class="lg-eyebrow">Selamat Datang</div>
      <h1 class="lg-title">Masuk ke<br>Panel<br><em>Admin.</em></h1>
      <p class="lg-sub">PKPPS Riyadlul Jannah</p>
      <div class="lg-divider"></div>
      <p class="lg-desc">Kelola data santri, absensi, keuangan, dan seluruh aktivitas pesantren dalam satu sistem terpadu.</p>
      <div class="lg-features">
        <div class="lg-feat">
          <div class="lg-feat-ico"><i class="fas fa-chart-line"></i></div>
          <span>Dashboard monitoring real-time</span>
        </div>
        <div class="lg-feat">
          <div class="lg-feat-ico"><i class="fas fa-shield-alt"></i></div>
          <span>Akses aman berbasis role</span>
        </div>
        <div class="lg-feat">
          <div class="lg-feat-ico"><i class="fas fa-mobile-alt"></i></div>
          <span>Terintegrasi aplikasi mobile wali</span>
        </div>
      </div>
    </div>

    <!-- ═══ Form (kanan) ═══ -->
    <div class="lg-form-panel">
      <div class="lg-card">
        <div class="lg-card-lbl">Login Admin</div>
        <div class="lg-card-title">Masuk Akun</div>
        <div class="lg-card-desc">Masukkan username dan password untuk mengakses panel admin.</div>

        <?php if($errors->any()): ?>
          <div class="lg-alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo e($errors->first()); ?>

          </div>
        <?php endif; ?>

        <?php if(session('success')): ?>
          <div class="lg-alert-success" id="lgSuccessAlert">
            <i class="fas fa-check-circle"></i>
            <?php echo e(session('success')); ?>

          </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('admin.login')); ?>" id="adminLoginForm">
          <?php echo csrf_field(); ?>

          <div class="lg-field">
            <label class="lg-lbl">Username</label>
            <div class="lg-shell">
              <i class="fas fa-user fi" id="ico-u"></i>
              <input type="text" id="username" name="username"
                     value="<?php echo e(old('username')); ?>"
                     placeholder="Masukkan username admin"
                     autocomplete="username" required autofocus
                     onfocus="document.getElementById('ico-u').classList.add('active')"
                     onblur="document.getElementById('ico-u').classList.remove('active')">
            </div>
          </div>

          <div class="lg-field">
            <label class="lg-lbl">Password</label>
            <div class="lg-shell">
              <i class="fas fa-lock fi" id="ico-p"></i>
              <input type="password" id="password" name="password"
                     placeholder="Masukkan password"
                     autocomplete="current-password" required
                     onfocus="document.getElementById('ico-p').classList.add('active')"
                     onblur="document.getElementById('ico-p').classList.remove('active')">
              <button type="button" class="lg-show" id="lgTglBtn">SHOW</button>
            </div>
          </div>

          <div class="lg-options">
            <label class="lg-remember">
              <input type="checkbox" name="remember" id="remember"> Ingat Saya
            </label>
            <a href="<?php echo e(route('admin.forgot.email_form')); ?>" class="lg-forgot">
              <i class="fas fa-key"></i> Lupa Password?
            </a>
          </div>

          <button type="submit" class="lg-btn">
            <i class="fas fa-sign-in-alt"></i>
            Login
          </button>

          <div class="lg-foot">
            Admin baru? <a href="<?php echo e(route('admin.register')); ?>">Daftar Sekarang</a>
          </div>

          <div class="lg-sep">atau</div>
          <a href="<?php echo e(route('santri.login')); ?>" class="lg-santri-link">
            <i class="fas fa-user-graduate"></i> Login sebagai Santri / Wali
          </a>
        </form>
      </div>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Toggle password
  const btn = document.getElementById('lgTglBtn');
  const pw  = document.getElementById('password');
  if (btn && pw) {
    btn.addEventListener('click', () => {
      const isP = pw.type === 'password';
      pw.type = isP ? 'text' : 'password';
      btn.textContent = isP ? 'HIDE' : 'SHOW';
    });
  }

  // CSRF check
  const form = document.getElementById('adminLoginForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      const csrf = document.querySelector('input[name="_token"]');
      if (!csrf || !csrf.value || csrf.value.length < 40) {
        e.preventDefault();
        alert('Session expired. Halaman akan dimuat ulang.');
        window.location.reload();
        return false;
      }
    });
  }

  // Clear error on input
  const alertBox = document.querySelector('.lg-alert-danger');
  if (alertBox) {
    ['username','password'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.addEventListener('input', () => alertBox.style.display = 'none');
    });
  }

  // Auto-hide success
  const sa = document.getElementById('lgSuccessAlert');
  if (sa) {
    setTimeout(() => {
      sa.style.transition = 'opacity .5s ease';
      sa.style.opacity = '0';
      setTimeout(() => sa.remove(), 500);
    }, 5000);
  }

  // Focus management
  const u = document.getElementById('username');
  const p = document.getElementById('password');
  if (u && !u.value) u.focus();
  if (u && p) {
    u.addEventListener('keypress', e => {
      if (e.key === 'Enter') { e.preventDefault(); p.focus(); }
    });
  }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('auth.auth_layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/auth/login.blade.php ENDPATH**/ ?>