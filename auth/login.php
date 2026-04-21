<?php
require_once __DIR__.'/../config/config.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';

if (isAuthenticated()) {
    isStudent() ? redirect('../student/student_dashboard.php') : redirect('../admin/admin_dashboard.php');
}

$pageTitle = 'Sign In — NBSC Guidance Office';
require_once __DIR__.'/../includes/header.php';
$flash = getFlash();
?>
<div class="auth-wrap" style="background-image:url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcROE3tdSJOhol7z2c9L5Y6Sawh5ZmEU7GT8Dg&s')">
  <div class="auth-overlay" style="background:linear-gradient(135deg,rgba(30,27,75,.88),rgba(30,58,138,.78),rgba(15,23,42,.88))"></div>
  <div class="ptcl pt1"></div><div class="ptcl pt2"></div><div class="ptcl pt3"></div>
  <div class="ptcl pt4"></div><div class="ptcl pt5"></div><div class="ptcl pt6"></div>
  <div class="ptcl pt7"></div><div class="ptcl pt8"></div>
  <div class="star" style="top:7%;left:11%;animation-duration:2.2s"></div>
  <div class="star" style="top:14%;left:79%;animation-duration:3.4s;animation-delay:1.2s"></div>
  <div class="star" style="top:26%;left:44%;animation-duration:2.8s;animation-delay:.6s"></div>
  <div class="star" style="top:72%;left:18%;animation-duration:4.1s;animation-delay:2s"></div>
  <div class="star" style="top:81%;left:66%;animation-duration:2.5s;animation-delay:.3s"></div>
  <div class="star" style="top:54%;left:89%;animation-duration:3.9s;animation-delay:1.8s"></div>
  <div style="position:absolute;top:-80px;left:-80px;width:360px;height:360px;border-radius:50%;background:rgba(99,102,241,.12);filter:blur(70px);pointer-events:none"></div>
  <div style="position:absolute;bottom:-60px;right:-60px;width:360px;height:360px;border-radius:50%;background:rgba(59,130,246,.12);filter:blur(70px);pointer-events:none"></div>

  <div class="auth-card">
    <div class="auth-box">

      <!-- LEFT -->
      <div class="auth-left a-left">
        <div class="logo-ring-wrap a-logo float glow">
          <div class="ring" style="inset:-10px"></div>
          <div class="ring2" style="inset:-22px"></div>
          <div class="logo-box">
            <img src="https://nbscgco.vercel.app/logo.png" alt="GCO" onerror="this.style.display='none'">
          </div>
        </div>
        <div class="auth-brand a-title">
          <h2>Guidance Counseling<br>Inventory System</h2>
          <div class="sub">
            <img src="https://nbscgco.vercel.app/nbsc.png" alt="NBSC" onerror="this.style.display='none'">
            <span>Northern Bukidnon State College</span>
          </div>
        </div>
        <div class="auth-feats a-feat">
          <?php foreach([
            ['📋','Manage student records securely'],
            ['🧠','Mental health assessments'],
            ['📊','Analytics & reporting tools'],
          ] as $f): ?>
          <div class="auth-feat"><div class="icon"><?=$f[0]?></div><span><?=$f[1]?></span></div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- RIGHT -->
      <div class="auth-right a-right">
        <div class="auth-form-wrap">

          <div class="auth-heading a-0">
            <h1>Welcome back 👋</h1>
            <p>Sign in with your email or Student ID</p>
          </div>

          <?php if ($flash): ?>
          <div class="alert <?=$flash['type']==='error'?'error':'success'?> a-0">
            <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            <span><?=sanitize($flash['message'])?></span>
          </div>
          <?php endif; ?>

          <!-- Single unified form -->
          <form action="login_process.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?=generateCSRFToken()?>">

            <div class="fg a-1">
              <label class="lbl">Email or Student ID</label>
              <div class="iw">
                <div class="ico">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <input type="text" name="identifier" class="inp" placeholder="Email address or Student ID" required autofocus autocomplete="username">
              </div>
            </div>

            <div class="fg a-2">
              <label class="lbl">Password</label>
              <div class="iw">
                <div class="ico">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <input type="password" name="password" id="pw" class="inp" placeholder="Enter your password" required autocomplete="current-password">
                <button type="button" class="eye" onclick="togglePw('pw')">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
              </div>
            </div>

            <div class="a-3" style="margin-top:6px">
              <button type="submit" class="btn-primary">
                <svg style="width:17px;height:17px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                Sign In
              </button>
            </div>
          </form>

          <div class="a-4" style="margin-top:20px;text-align:center">
            <p style="font-size:.85rem;color:var(--gray-500)">
              Don't have an account? <a href="student_register.php" style="color:var(--primary);font-weight:600">Register here</a>
            </p>
          </div>

          <div style="margin-top:20px;padding-top:18px;border-top:1px solid var(--gray-100);text-align:center">
            <a href="../index.php" style="font-size:.82rem;color:var(--gray-400);display:inline-flex;align-items:center;gap:5px;transition:color .15s" onmouseover="this.style.color='var(--gray-700)'" onmouseout="this.style.color='var(--gray-400)'">
              <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
              Back to Home
            </a>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<script>
function togglePw(id) {
    const i = document.getElementById(id);
    i.type = i.type === 'password' ? 'text' : 'password';
}
</script>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
