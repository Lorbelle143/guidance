<?php
require_once __DIR__.'/../config/config.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
if (isAuthenticated()) {
    isStudent() ? redirect('../student/student_dashboard.php') : redirect('../admin/admin_dashboard.php');
}
$pageTitle = 'Create Account — NBSC GCO';
require_once __DIR__.'/../includes/header.php';
$flash = getFlash();
?>
<style>
/* ── Register page — dark blue glassmorphism ── */
.reg-wrap {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 32px 16px;
  background: linear-gradient(145deg, #0d1b3e 0%, #0f2460 30%, #1a3a8f 60%, #1e3fa8 100%);
  position: relative;
  overflow: hidden;
}

/* Subtle radial glow blobs */
.reg-wrap::before {
  content: '';
  position: absolute;
  top: -20%;
  left: -10%;
  width: 60%;
  height: 60%;
  background: radial-gradient(circle, rgba(99,102,241,.25) 0%, transparent 70%);
  pointer-events: none;
}
.reg-wrap::after {
  content: '';
  position: absolute;
  bottom: -15%;
  right: -10%;
  width: 50%;
  height: 50%;
  background: radial-gradient(circle, rgba(59,130,246,.2) 0%, transparent 70%);
  pointer-events: none;
}

/* Stars */
.reg-star {
  position: absolute;
  width: 2px;
  height: 2px;
  background: rgba(255,255,255,.7);
  border-radius: 50%;
  animation: twinkle ease-in-out infinite;
}

/* Scrollable form box */
.reg-box {
  position: relative;
  z-index: 10;
  width: 100%;
  max-width: 460px;
  max-height: 92vh;
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: rgba(255,255,255,.15) transparent;
  padding: 4px;
}
.reg-box::-webkit-scrollbar { width: 4px; }
.reg-box::-webkit-scrollbar-track { background: transparent; }
.reg-box::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 4px; }

/* Heading */
.reg-title {
  font-size: 2rem;
  font-weight: 800;
  color: #fff;
  letter-spacing: -.03em;
  margin-bottom: 6px;
  animation: fadeUp .5s ease both;
}
.reg-sub {
  font-size: .9rem;
  color: rgba(148,163,184,.85);
  margin-bottom: 28px;
  animation: fadeUp .5s .08s ease both;
}

/* Field label */
.reg-lbl {
  display: block;
  font-size: .875rem;
  font-weight: 600;
  color: rgba(255,255,255,.9);
  margin-bottom: 8px;
  letter-spacing: .01em;
}

/* Glass input wrapper */
.reg-iw {
  position: relative;
  margin-bottom: 18px;
}
.reg-iw .ico {
  position: absolute;
  inset-y: 0;
  left: 0;
  padding-left: 16px;
  display: flex;
  align-items: center;
  pointer-events: none;
  color: rgba(148,163,184,.7);
}
.reg-iw .ico svg { width: 18px; height: 18px; }
.reg-iw .eye-btn {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: rgba(148,163,184,.6);
  padding: 2px;
  transition: color .15s;
  display: flex;
  align-items: center;
}
.reg-iw .eye-btn:hover { color: rgba(255,255,255,.8); }
.reg-iw .eye-btn svg { width: 17px; height: 17px; }

.reg-inp {
  width: 100%;
  padding: 14px 44px;
  background: rgba(255,255,255,.08);
  border: 1.5px solid rgba(255,255,255,.14);
  border-radius: 12px;
  font-size: .9rem;
  color: #fff;
  outline: none;
  transition: all .2s;
  font-family: 'Inter', sans-serif;
  backdrop-filter: blur(8px);
}
.reg-inp::placeholder { color: rgba(148,163,184,.55); }
.reg-inp:focus {
  border-color: rgba(99,102,241,.7);
  background: rgba(255,255,255,.12);
  box-shadow: 0 0 0 3px rgba(99,102,241,.18);
}

/* Photo upload */
.reg-photo-label {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 13px 16px;
  background: rgba(255,255,255,.06);
  border: 1.5px dashed rgba(255,255,255,.2);
  border-radius: 12px;
  cursor: pointer;
  transition: all .2s;
  margin-bottom: 18px;
}
.reg-photo-label:hover {
  border-color: rgba(99,102,241,.6);
  background: rgba(99,102,241,.1);
}
.reg-photo-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: rgba(255,255,255,.1);
  border: 2px solid rgba(255,255,255,.15);
  overflow: hidden;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}
.reg-photo-avatar svg { width: 22px; height: 22px; color: rgba(255,255,255,.4); }
.reg-photo-avatar img { width: 100%; height: 100%; object-fit: cover; display: none; }
.reg-photo-text { font-size: .875rem; font-weight: 600; color: rgba(255,255,255,.8); }
.reg-photo-hint { font-size: .75rem; color: rgba(148,163,184,.65); margin-top: 2px; }

/* 2-col grid */
.reg-grid2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

/* Submit button */
.reg-btn {
  width: 100%;
  padding: 14px;
  background: linear-gradient(135deg, #6366f1, #4f46e5 50%, #3b82f6);
  background-size: 200% auto;
  color: #fff;
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all .3s;
  box-shadow: 0 4px 20px rgba(99,102,241,.45);
  font-family: 'Inter', sans-serif;
  letter-spacing: .01em;
  margin-bottom: 20px;
}
.reg-btn:hover {
  background-position: right center;
  box-shadow: 0 8px 28px rgba(99,102,241,.6);
  transform: translateY(-2px);
}

/* OR divider */
.reg-or {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
  color: rgba(148,163,184,.5);
  font-size: .78rem;
  font-weight: 600;
  letter-spacing: .08em;
  text-transform: uppercase;
}
.reg-or::before, .reg-or::after {
  content: '';
  flex: 1;
  height: 1px;
  background: rgba(255,255,255,.1);
}

/* Sign in link */
.reg-signin {
  text-align: center;
  font-size: .875rem;
  color: rgba(148,163,184,.8);
  margin-bottom: 20px;
}
.reg-signin a {
  color: #fff;
  font-weight: 700;
  text-decoration: none;
  transition: color .15s;
}
.reg-signin a:hover { color: #a5b4fc; }

/* Back to home */
.reg-back {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  font-size: .82rem;
  color: rgba(148,163,184,.6);
  text-decoration: none;
  transition: color .15s;
  padding-top: 16px;
  border-top: 1px solid rgba(255,255,255,.08);
}
.reg-back:hover { color: rgba(255,255,255,.85); }
.reg-back svg { width: 14px; height: 14px; }

/* Flash alert */
.reg-alert {
  padding: 12px 16px;
  border-radius: 10px;
  font-size: .875rem;
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-bottom: 20px;
  border: 1px solid;
}
.reg-alert svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }
.reg-alert.error { background: rgba(239,68,68,.12); border-color: rgba(239,68,68,.3); color: #fca5a5; }
.reg-alert.success { background: rgba(16,185,129,.12); border-color: rgba(16,185,129,.3); color: #6ee7b7; }

/* Animations */
@keyframes fadeUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
@keyframes twinkle { 0%,100%{opacity:.15;transform:scale(1)} 50%{opacity:.8;transform:scale(1.5)} }

.reg-field { animation: fadeUp .45s ease both; }
</style>

<div class="reg-wrap">
  <!-- Stars -->
  <div class="reg-star" style="top:8%;left:12%;animation-duration:2.3s"></div>
  <div class="reg-star" style="top:15%;left:78%;animation-duration:3.5s;animation-delay:1.1s"></div>
  <div class="reg-star" style="top:28%;left:45%;animation-duration:2.9s;animation-delay:.7s"></div>
  <div class="reg-star" style="top:70%;left:20%;animation-duration:4s;animation-delay:1.9s"></div>
  <div class="reg-star" style="top:82%;left:65%;animation-duration:2.6s;animation-delay:.4s"></div>
  <div class="reg-star" style="top:55%;left:88%;animation-duration:3.8s;animation-delay:2.1s"></div>
  <div class="reg-star" style="top:40%;left:5%;animation-duration:3.2s;animation-delay:.9s"></div>
  <div class="reg-star" style="top:92%;left:40%;animation-duration:2.7s;animation-delay:1.6s"></div>

  <div class="reg-box">

    <!-- Heading -->
    <h1 class="reg-title">Create account</h1>
    <p class="reg-sub">Register with your student information</p>

    <?php if ($flash): ?>
    <div class="reg-alert <?= $flash['type']==='error' ? 'error' : 'success' ?>">
      <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
      <span><?= sanitize($flash['message']) ?></span>
    </div>
    <?php endif; ?>

    <form action="student_register_process.php" method="POST" enctype="multipart/form-data" id="regForm">
      <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

      <!-- Student ID -->
      <div class="reg-field" style="animation-delay:.1s">
        <label class="reg-lbl">Student ID *</label>
        <div class="reg-iw">
          <div class="ico">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
          </div>
          <input type="text" name="student_id" class="reg-inp" placeholder="e.g. 2024-001" required autofocus>
        </div>
      </div>

      <!-- Last + First Name -->
      <div class="reg-grid2 reg-field" style="animation-delay:.15s">
        <div>
          <label class="reg-lbl">Last Name *</label>
          <div class="reg-iw" style="margin-bottom:0">
            <div class="ico">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <input type="text" name="last_name" class="reg-inp" placeholder="Last name" required>
          </div>
        </div>
        <div>
          <label class="reg-lbl">First Name *</label>
          <div class="reg-iw" style="margin-bottom:0">
            <div class="ico">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <input type="text" name="first_name" class="reg-inp" placeholder="First name" required>
          </div>
        </div>
      </div>
      <div style="margin-bottom:18px"></div>

      <!-- Middle Name -->
      <div class="reg-field" style="animation-delay:.2s">
        <label class="reg-lbl">Middle Name</label>
        <div class="reg-iw">
          <div class="ico">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          </div>
          <input type="text" name="middle_name" class="reg-inp" placeholder="Middle name (optional)">
        </div>
      </div>

      <!-- Personal Email -->
      <div class="reg-field" style="animation-delay:.25s">
        <label class="reg-lbl">Personal Email</label>
        <div class="reg-iw">
          <div class="ico">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          </div>
          <input type="email" name="email" class="reg-inp" placeholder="yourname@gmail.com">
        </div>
      </div>

      <!-- Profile Photo -->
      <div class="reg-field" style="animation-delay:.3s">
        <label class="reg-lbl">Profile Photo <span style="font-size:.75rem;color:rgba(148,163,184,.6);font-weight:400">(optional — you can add this later)</span></label>
        <label for="photo" class="reg-photo-label">
          <div class="reg-photo-avatar" id="photo-avatar">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" id="photo-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <img id="photo-prev" src="" alt="">
          </div>
          <div>
            <div class="reg-photo-text" id="photo-text">Click to upload photo</div>
            <div class="reg-photo-hint">JPG, PNG, GIF — max 5MB</div>
          </div>
        </label>
        <input type="file" name="photo" id="photo" accept="image/*" style="display:none">
      </div>

      <!-- Password -->
      <div class="reg-field" style="animation-delay:.35s">
        <label class="reg-lbl">Password *</label>
        <div class="reg-iw">
          <div class="ico">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
          </div>
          <input type="password" name="password" id="pw1" class="reg-inp" placeholder="Create a password" required minlength="6">
          <button type="button" class="eye-btn" onclick="togglePw('pw1')">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
          </button>
        </div>
      </div>

      <!-- Confirm Password -->
      <div class="reg-field" style="animation-delay:.4s">
        <label class="reg-lbl">Confirm Password *</label>
        <div class="reg-iw">
          <div class="ico">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <input type="password" name="confirm_password" id="pw2" class="reg-inp" placeholder="Confirm your password" required minlength="6">
          <button type="button" class="eye-btn" onclick="togglePw('pw2')">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
          </button>
        </div>
      </div>

      <!-- Submit -->
      <div class="reg-field" style="animation-delay:.45s">
        <button type="submit" class="reg-btn">Create Account</button>
      </div>

    </form>

    <!-- OR divider -->
    <div class="reg-or">OR</div>

    <!-- Sign in link -->
    <div class="reg-signin">
      Already have an account? <a href="login.php">Sign in here</a>
    </div>

    <!-- Back to home -->
    <a href="../index.php" class="reg-back">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      Back to Home
    </a>

  </div><!-- end reg-box -->
</div>

<script>
function togglePw(id) {
    const i = document.getElementById(id);
    i.type = i.type === 'password' ? 'text' : 'password';
}

document.getElementById('photo').addEventListener('change', function() {
    const f = this.files[0];
    if (!f) return;
    const r = new FileReader();
    r.onload = e => {
        const prev = document.getElementById('photo-prev');
        const icon = document.getElementById('photo-icon');
        prev.src = e.target.result;
        prev.style.display = 'block';
        icon.style.display = 'none';
        document.getElementById('photo-text').textContent = f.name;
    };
    r.readAsDataURL(f);
});

document.getElementById('regForm').addEventListener('submit', function(e) {
    if (document.getElementById('pw1').value !== document.getElementById('pw2').value) {
        e.preventDefault();
        alert('Passwords do not match!');
    }
});
</script>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
