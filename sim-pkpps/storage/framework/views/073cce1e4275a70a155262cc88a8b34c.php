


<?php $__env->startSection('title', 'Verifikasi OTP'); ?>

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

.vf-wrap {
  position: relative; width: 100%; min-height: 100vh;
  display: flex; align-items: center; justify-content: center;
  overflow: hidden; font-family: 'DM Sans', sans-serif;
}
.vf-bg {
  position: absolute; inset: 0; z-index: 0;
  background:
    radial-gradient(ellipse 80% 60% at 70% 50%, rgba(111,186,157,.12) 0%, transparent 60%),
    radial-gradient(ellipse 50% 80% at 10% 80%, rgba(111,186,157,.08) 0%, transparent 55%),
    #F8FDFB;
}
.vf-bg::before {
  content: ''; position: absolute; inset: 0;
  background-image:
    linear-gradient(rgba(111,186,157,.055) 1px, transparent 1px),
    linear-gradient(90deg, rgba(111,186,157,.055) 1px, transparent 1px);
  background-size: 48px 48px;
}
.vf-ring { position: absolute; border-radius: 50%; border: 1.5px solid rgba(111,186,157,.18); pointer-events: none; }
.vf-ring.r1 { width:420px; height:420px; top:-110px; right:-110px; }
.vf-ring.r2 { width:200px; height:200px; bottom:-50px; left:60px; border-color:rgba(111,186,157,.14); }
.vf-dot { position:absolute; border-radius:50%; background:#6FBA9D; pointer-events:none; }
.vf-dot.d1 { width:8px; height:8px; top:22%; right:18%; opacity:.14; }
.vf-dot.d2 { width:11px; height:11px; top:55%; left:15%; opacity:.08; }

.vf-layout {
  position: relative; z-index: 2;
  display: flex; align-items: center;
  width: 100%; max-width: 1100px;
  padding: 40px 60px; gap: 80px;
  animation: vfIn .6s ease both;
}
@keyframes vfIn {
  from { opacity:0; transform:translateY(20px); }
  to   { opacity:1; transform:translateY(0); }
}
.vf-brand      { flex: 0 0 340px; order: 1; }
.vf-form-panel { flex: 1; max-width: 460px; order: 2; }

/* Brand */
.vf-logo { width:72px; height:72px; margin-bottom:20px; border-radius:16px; box-shadow:0 4px 20px rgba(111,186,157,.2); object-fit:contain; background:#fff; }
.vf-eyebrow {
  display:inline-flex; align-items:center; gap:8px;
  font-size:.68rem; font-weight:700; letter-spacing:2px;
  text-transform:uppercase; color:#6FBA9D; margin-bottom:18px;
}
.vf-eyebrow::before { content:''; display:inline-block; width:22px; height:2px; background:#6FBA9D; border-radius:2px; }
.vf-title {
  font-family:'DM Serif Display',serif;
  font-size:3.2rem; line-height:1.05; color:#0F2118; margin-bottom:6px;
}
.vf-title em { font-style:italic; color:#5EA98C; }
.vf-sub { font-size:.9rem; font-weight:500; color:#8AADA0; margin-bottom:32px; line-height:1.6; }
.vf-divider { width:44px; height:3px; background:linear-gradient(90deg,#6FBA9D,#A8D8C6); border-radius:3px; margin-bottom:24px; }
.vf-desc { font-size:.81rem; color:#8AADA0; line-height:1.8; max-width:290px; margin-bottom:32px; }

/* Steps */
.vf-steps { display:flex; flex-direction:column; gap:14px; }
.vf-step { display:flex; align-items:flex-start; gap:12px; }
.vf-step-num {
  width:28px; height:28px; border-radius:50%;
  background:#D6EDE5; color:#5EA98C;
  font-size:.72rem; font-weight:800;
  display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.vf-step-num.done { background:linear-gradient(135deg,#6FBA9D,#5EA98C); color:#fff; }
.vf-step-num.active { background:linear-gradient(135deg,#6FBA9D,#5EA98C); color:#fff; box-shadow:0 0 0 4px rgba(111,186,157,.2); }
.vf-step-text { font-size:.78rem; color:#2A4235; font-weight:500; line-height:1.5; padding-top:3px; }
.vf-step-text small { display:block; color:#8AADA0; font-weight:400; font-size:.72rem; }

/* Card */
.vf-card {
  background: #fff; border-radius: 24px;
  padding: 42px 38px;
  box-shadow:
    0 0 0 1px rgba(111,186,157,.1),
    0 4px 6px rgba(15,33,24,.03),
    0 20px 44px rgba(15,33,24,.08);
  position: relative; overflow: hidden;
}
.vf-card::before {
  content: ''; position:absolute; top:0; left:0; right:0; height:3px;
  background: linear-gradient(90deg, #FFB74D, #FFA726, #FFB74D);
}
.vf-card::after {
  content: ''; position:absolute; bottom:-50px; right:-50px;
  width:140px; height:140px; border-radius:50%;
  background: radial-gradient(circle, rgba(255,183,77,.06) 0%, transparent 70%);
}
.vf-card-icon {
  width:54px; height:54px; border-radius:14px;
  background:linear-gradient(135deg,#FFF3E0,#FFE0B2);
  display:flex; align-items:center; justify-content:center;
  font-size:1.3rem; color:#F57C00; margin-bottom:16px;
}
.vf-card-lbl {
  font-size:.67rem; font-weight:700; letter-spacing:2px;
  text-transform:uppercase; color:#FFB74D; margin-bottom:7px;
}
.vf-card-title {
  font-family:'DM Serif Display',serif;
  font-size:1.85rem; color:#0F2118; line-height:1.1; margin-bottom:5px;
}
.vf-card-desc { font-size:.79rem; color:#8AADA0; line-height:1.6; margin-bottom:10px; }
.vf-email-badge {
  display:inline-flex; align-items:center; gap:6px;
  padding:6px 14px; border-radius:8px;
  background:#EBF7F2; font-size:.78rem; font-weight:600; color:#3D8A6E;
  margin-bottom:22px;
}

/* Alert */
.vf-alert-danger {
  border-radius:10px; padding:10px 13px; font-size:.79rem;
  margin-bottom:18px; background:#FFF3F3; color:#c62828; border-left:3px solid #e53935;
  display:flex; align-items:center; gap:7px;
}
.vf-alert-success {
  border-radius:10px; padding:10px 13px; font-size:.79rem;
  margin-bottom:18px; background:#F0FFF4; color:#2E7D32; border-left:3px solid #43A047;
  display:flex; align-items:center; gap:7px;
}

/* OTP Boxes */
.vf-otp-row {
  display:flex; gap:10px; justify-content:center; margin:22px 0;
}
.vf-otp-box {
  width:50px; height:60px; text-align:center;
  font-size:1.6rem; font-weight:800; color:#0F2118;
  font-family:'DM Sans',sans-serif;
  border:2px solid #D6EDE5; border-radius:12px;
  background:#EBF7F2; outline:none;
  transition:all .2s;
}
.vf-otp-box:focus {
  border-color:#FFB74D; background:#fff;
  box-shadow:0 0 0 4px rgba(255,183,77,.15);
}
.vf-otp-box.filled {
  border-color:#6FBA9D; background:#f0faf5;
}
.vf-otp-box.error {
  border-color:#e53935; background:#FFF3F3;
  animation: vfShake .4s ease;
}
@keyframes vfShake {
  0%,100% { transform:translateX(0); }
  20%,60% { transform:translateX(-4px); }
  40%,80% { transform:translateX(4px); }
}

/* Button */
.vf-btn {
  width:100%; padding:13px;
  background:linear-gradient(135deg, #FFB74D, #FFA726);
  color:#fff; border:none; border-radius:12px;
  font-family:inherit; font-size:.89rem; font-weight:700;
  cursor:pointer; letter-spacing:.3px;
  display:flex; align-items:center; justify-content:center; gap:8px;
  box-shadow:0 4px 18px rgba(255,183,77,.35);
  transition:all .25s;
}
.vf-btn:hover { transform:translateY(-2px); box-shadow:0 8px 26px rgba(255,183,77,.45); }
.vf-btn:active { transform:none; }

/* Resend */
.vf-resend {
  text-align:center; margin-top:20px; font-size:.8rem; color:#8AADA0;
}
.vf-resend a {
  color:#5EA98C; font-weight:700; text-decoration:none; transition:color .2s;
}
.vf-resend a:hover { text-decoration:underline; color:#3D8A6E; }
.vf-resend a.disabled { color:#ccc; pointer-events:none; }
.vf-countdown { font-weight:700; color:#e53935; }

.vf-back {
  display:flex; align-items:center; justify-content:center; gap:6px;
  margin-top:16px; font-size:.78rem; color:#5EA98C; font-weight:600; text-decoration:none;
}
.vf-back:hover { color:#3D8A6E; text-decoration:underline; }

/* Responsive */
@media (max-width: 900px) {
  .vf-layout { gap:48px; padding:32px 36px; }
  .vf-brand { flex:0 0 260px; }
  .vf-title { font-size:2.7rem; }
}
@media (max-width: 720px) {
  body.auth-page { align-items:flex-start !important; overflow-y:auto !important; }
  .vf-wrap { align-items:flex-start; min-height:auto; padding:24px 0 40px; }
  .vf-layout { flex-direction:column; padding:0 20px; gap:28px; }
  .vf-form-panel { order:2; max-width:100%; }
  .vf-brand { order:1; flex:none; text-align:center; }
  .vf-title { font-size:2.2rem; }
  .vf-steps, .vf-desc, .vf-divider { display:none; }
  .vf-sub { margin-bottom:0; }
  .vf-card { padding:28px 20px; }
  .vf-logo { width:56px; height:56px; margin:0 auto 14px; display:block; }
  .vf-otp-box { width:42px; height:50px; font-size:1.3rem; }
}
@media (max-width: 420px) {
  .vf-title { font-size:1.85rem; }
  .vf-card { padding:24px 16px; border-radius:16px; }
  .vf-card-title { font-size:1.5rem; }
  .vf-otp-row { gap:6px; }
  .vf-otp-box { width:38px; height:46px; font-size:1.1rem; border-radius:9px; }
}
@media (min-width: 1280px) {
  .vf-layout { max-width:1160px; padding:40px 80px; }
  .vf-brand { flex:0 0 360px; }
  .vf-title { font-size:3.6rem; }
}
</style>

<div class="vf-wrap">
  <div class="vf-bg"></div>
  <div class="vf-ring r1"></div>
  <div class="vf-ring r2"></div>
  <div class="vf-dot d1"></div>
  <div class="vf-dot d2"></div>

  <div class="vf-layout">

    <!-- ═══ Brand (kiri) ═══ -->
    <div class="vf-brand">
      <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Logo PKPPS" class="vf-logo">
      <div class="vf-eyebrow">Verifikasi</div>
      <h1 class="vf-title">Cek<br><em>Email</em><br>Anda.</h1>
      <p class="vf-sub">PKPPS Riyadlul Jannah</p>
      <div class="vf-divider"></div>
      <p class="vf-desc">Masukkan kode 6 digit yang telah kami kirim. Periksa juga folder spam jika belum menerima email.</p>

      <div class="vf-steps">
        <div class="vf-step">
          <div class="vf-step-num done"><i class="fas fa-check" style="font-size:.6rem"></i></div>
          <div class="vf-step-text" style="color:#8AADA0;">Email terkirim</div>
        </div>
        <div class="vf-step">
          <div class="vf-step-num active">2</div>
          <div class="vf-step-text">Verifikasi kode OTP <small>Masukkan 6 digit kode</small></div>
        </div>
        <div class="vf-step">
          <div class="vf-step-num">3</div>
          <div class="vf-step-text">Buat password baru</div>
        </div>
      </div>
    </div>

    <!-- ═══ Form (kanan) ═══ -->
    <div class="vf-form-panel">
      <div class="vf-card">
        <div class="vf-card-icon">
          <i class="fas fa-shield-alt"></i>
        </div>
        <div class="vf-card-lbl">Langkah 2 dari 3</div>
        <div class="vf-card-title">Masukkan Kode OTP</div>
        <div class="vf-card-desc">Kode verifikasi 6 digit telah dikirim ke:</div>
        <div class="vf-email-badge">
          <i class="fas fa-envelope"></i> <?php echo e($email); ?>

        </div>

        <?php if($errors->any()): ?>
          <div class="vf-alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo e($errors->first()); ?>

          </div>
        <?php endif; ?>

        <?php if(session('success')): ?>
          <div class="vf-alert-success" id="vfSuccessAlert">
            <i class="fas fa-check-circle"></i>
            <?php echo e(session('success')); ?>

          </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('admin.forgot.verify_otp')); ?>" id="otpForm">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="email" value="<?php echo e($email); ?>">
          <input type="hidden" name="otp" id="otpHidden" value="">

          <div class="vf-otp-row" id="otpRow">
            <input type="text" maxlength="1" class="vf-otp-box" data-index="0" inputmode="numeric" autofocus>
            <input type="text" maxlength="1" class="vf-otp-box" data-index="1" inputmode="numeric">
            <input type="text" maxlength="1" class="vf-otp-box" data-index="2" inputmode="numeric">
            <input type="text" maxlength="1" class="vf-otp-box" data-index="3" inputmode="numeric">
            <input type="text" maxlength="1" class="vf-otp-box" data-index="4" inputmode="numeric">
            <input type="text" maxlength="1" class="vf-otp-box" data-index="5" inputmode="numeric">
          </div>

          <button type="submit" class="vf-btn" id="verifyBtn">
            <i class="fas fa-check-circle"></i>
            Verifikasi Kode
          </button>
        </form>

        <div class="vf-resend">
          <p>Tidak menerima kode?</p>
          <form method="POST" action="<?php echo e(route('admin.forgot.resend_otp')); ?>" id="resendForm" style="display:inline;">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="email" value="<?php echo e($email); ?>">
            <a href="#" id="resendLink" class="disabled" onclick="document.getElementById('resendForm').submit(); return false;">
              Kirim Ulang OTP
            </a>
            <span id="timerText" class="vf-countdown"> (60s)</span>
          </form>
        </div>

        <a href="<?php echo e(route('admin.forgot.email_form')); ?>" class="vf-back">
          <i class="fas fa-arrow-left"></i> Ganti Email
        </a>
      </div>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const boxes = document.querySelectorAll('.vf-otp-box');
  const hiddenOtp = document.getElementById('otpHidden');
  const form = document.getElementById('otpForm');

  function updateOtp() {
    let otp = '';
    boxes.forEach(b => otp += b.value);
    hiddenOtp.value = otp;
  }

  boxes.forEach((box, idx) => {
    box.addEventListener('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
      if (this.value && idx < boxes.length - 1) boxes[idx + 1].focus();
      this.classList.toggle('filled', !!this.value);
      this.classList.remove('error');
      updateOtp();
      if (hiddenOtp.value.length === 6) form.submit();
    });

    box.addEventListener('keydown', function(e) {
      if (e.key === 'Backspace' && !this.value && idx > 0) {
        boxes[idx - 1].focus();
        boxes[idx - 1].value = '';
        boxes[idx - 1].classList.remove('filled');
        updateOtp();
      }
    });

    box.addEventListener('paste', function(e) {
      e.preventDefault();
      const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
      if (pasted.length >= 6) {
        for (let i = 0; i < 6; i++) {
          boxes[i].value = pasted[i] || '';
          boxes[i].classList.toggle('filled', !!boxes[i].value);
        }
        boxes[5].focus();
        updateOtp();
        if (hiddenOtp.value.length === 6) form.submit();
      }
    });
  });

  // If there's an error, shake the boxes
  <?php if($errors->any()): ?>
    boxes.forEach(b => b.classList.add('error'));
  <?php endif; ?>

  // Countdown
  let secs = 60;
  const timerText = document.getElementById('timerText');
  const resendLink = document.getElementById('resendLink');
  const cd = setInterval(() => {
    secs--;
    timerText.textContent = ' (' + secs + 's)';
    if (secs <= 0) {
      clearInterval(cd);
      timerText.textContent = '';
      resendLink.classList.remove('disabled');
    }
  }, 1000);

  // Auto-hide success
  const sa = document.getElementById('vfSuccessAlert');
  if (sa) {
    setTimeout(() => {
      sa.style.transition = 'opacity .5s ease';
      sa.style.opacity = '0';
      setTimeout(() => sa.remove(), 500);
    }, 5000);
  }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('auth.auth_layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/auth/verify_otp.blade.php ENDPATH**/ ?>