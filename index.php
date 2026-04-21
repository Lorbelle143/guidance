<?php
require_once __DIR__.'/config/config.php';
require_once __DIR__.'/includes/session.php';
require_once __DIR__.'/includes/functions.php';

// Already logged in → go to dashboard
if (isAuthenticated()) {
    isStudent() ? redirect('student/student_dashboard.php') : redirect('admin/admin_dashboard.php');
}
$pageTitle = 'NBSC Guidance & Counseling Office';
require_once __DIR__.'/includes/header.php';
?>
<style>
/* ── Landing page ── */
*{box-sizing:border-box;margin:0;padding:0}
.lp-wrap{min-height:100vh;display:flex;flex-direction:column;background:#0f172a;position:relative;overflow:hidden}

/* Background */
.lp-bg{position:absolute;inset:0;background:url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcROE3tdSJOhol7z2c9L5Y6Sawh5ZmEU7GT8Dg&s') center/cover no-repeat;opacity:.18}
.lp-grad{position:absolute;inset:0;background:linear-gradient(135deg,rgba(15,23,42,.97) 0%,rgba(30,27,75,.92) 50%,rgba(15,23,42,.97) 100%)}

/* Floating orbs */
.orb{position:absolute;border-radius:50%;filter:blur(80px);pointer-events:none;animation:orbFloat 8s ease-in-out infinite}
.orb1{width:500px;height:500px;background:rgba(99,102,241,.15);top:-150px;left:-150px;animation-delay:0s}
.orb2{width:400px;height:400px;background:rgba(59,130,246,.12);bottom:-100px;right:-100px;animation-delay:3s}
.orb3{width:300px;height:300px;background:rgba(139,92,246,.1);top:40%;left:60%;animation-delay:1.5s}
@keyframes orbFloat{0%,100%{transform:translateY(0) scale(1)}50%{transform:translateY(-30px) scale(1.05)}}

/* Stars */
.star{position:absolute;width:3px;height:3px;background:#fff;border-radius:50%;animation:twinkle 3s ease-in-out infinite}
@keyframes twinkle{0%,100%{opacity:.2;transform:scale(1)}50%{opacity:1;transform:scale(1.5)}}

/* Nav */
.lp-nav{position:relative;z-index:10;display:flex;align-items:center;justify-content:space-between;padding:20px 48px;border-bottom:1px solid rgba(255,255,255,.06)}
.lp-nav .brand{display:flex;align-items:center;gap:12px}
.lp-nav .brand img{width:42px;height:42px;object-fit:contain}
.lp-nav .brand-text h3{font-size:.95rem;font-weight:700;color:#fff;line-height:1.2}
.lp-nav .brand-text p{font-size:.7rem;color:rgba(148,163,184,.8)}
.lp-nav .nav-btns{display:flex;gap:10px}
.btn-ghost{padding:9px 20px;border:1.5px solid rgba(255,255,255,.15);border-radius:9px;color:rgba(255,255,255,.85);font-size:.875rem;font-weight:500;text-decoration:none;transition:all .2s;background:transparent}
.btn-ghost:hover{border-color:rgba(255,255,255,.4);background:rgba(255,255,255,.06);color:#fff}
.btn-solid{padding:9px 22px;border:none;border-radius:9px;background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;font-size:.875rem;font-weight:600;text-decoration:none;transition:all .2s;cursor:pointer}
.btn-solid:hover{background:linear-gradient(135deg,#818cf8,#6366f1);box-shadow:0 4px 20px rgba(99,102,241,.4);transform:translateY(-1px)}

/* Hero */
.lp-hero{position:relative;z-index:10;flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:60px 24px 40px}
.hero-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(99,102,241,.15);border:1px solid rgba(99,102,241,.3);border-radius:20px;padding:6px 16px;font-size:.78rem;font-weight:600;color:#a5b4fc;margin-bottom:28px;animation:fadeUp .6s ease both}
.hero-badge span{width:6px;height:6px;background:#6366f1;border-radius:50%;animation:pulse 2s infinite}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(1.5)}}

.hero-title{font-size:clamp(2.2rem,5vw,3.8rem);font-weight:800;color:#fff;line-height:1.15;letter-spacing:-.03em;margin-bottom:20px;animation:fadeUp .6s .1s ease both}
.hero-title .accent{background:linear-gradient(135deg,#818cf8,#6366f1,#a78bfa);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.hero-sub{font-size:1.05rem;color:rgba(148,163,184,.9);max-width:560px;line-height:1.7;margin-bottom:40px;animation:fadeUp .6s .2s ease both}

.hero-btns{display:flex;gap:14px;flex-wrap:wrap;justify-content:center;animation:fadeUp .6s .3s ease both}
.btn-hero-primary{padding:14px 32px;background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;border:none;border-radius:12px;font-size:1rem;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .2s;cursor:pointer}
.btn-hero-primary:hover{background:linear-gradient(135deg,#818cf8,#6366f1);box-shadow:0 8px 30px rgba(99,102,241,.45);transform:translateY(-2px)}
.btn-hero-outline{padding:14px 32px;background:transparent;color:#fff;border:1.5px solid rgba(255,255,255,.2);border-radius:12px;font-size:1rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .2s}
.btn-hero-outline:hover{border-color:rgba(255,255,255,.5);background:rgba(255,255,255,.06);transform:translateY(-2px)}

@keyframes fadeUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}

/* Stats row */
.hero-stats{display:flex;gap:40px;margin-top:56px;animation:fadeUp .6s .4s ease both;flex-wrap:wrap;justify-content:center}
.stat-item{text-align:center}
.stat-item .num{font-size:1.8rem;font-weight:800;color:#fff;line-height:1}
.stat-item .lbl{font-size:.75rem;color:rgba(148,163,184,.7);margin-top:4px}

/* Divider */
.stat-div{width:1px;background:rgba(255,255,255,.1);align-self:stretch}

/* Features section */
.lp-features{position:relative;z-index:10;padding:60px 48px;border-top:1px solid rgba(255,255,255,.06)}
.feat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;max-width:1100px;margin:0 auto}
.feat-card{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:24px;transition:all .2s}
.feat-card:hover{background:rgba(255,255,255,.07);border-color:rgba(99,102,241,.3);transform:translateY(-3px)}
.feat-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;margin-bottom:14px}
.feat-card h4{font-size:.95rem;font-weight:700;color:#fff;margin-bottom:6px}
.feat-card p{font-size:.82rem;color:rgba(148,163,184,.8);line-height:1.6}

/* Footer */
.lp-footer{position:relative;z-index:10;padding:20px 48px;border-top:1px solid rgba(255,255,255,.06);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.lp-footer p{font-size:.78rem;color:rgba(100,116,139,.8)}
.lp-footer .footer-links{display:flex;gap:20px}
.lp-footer .footer-links a{font-size:.78rem;color:rgba(100,116,139,.8);text-decoration:none;transition:color .15s}
.lp-footer .footer-links a:hover{color:rgba(148,163,184,1)}

@media(max-width:640px){
  .lp-nav{padding:16px 20px}
  .lp-features{padding:40px 20px}
  .lp-footer{padding:16px 20px;flex-direction:column;text-align:center}
  .hero-stats{gap:24px}
  .stat-div{display:none}
}
</style>

<div class="lp-wrap">
  <!-- Background layers -->
  <div class="lp-bg"></div>
  <div class="lp-grad"></div>

  <!-- Orbs -->
  <div class="orb orb1"></div>
  <div class="orb orb2"></div>
  <div class="orb orb3"></div>

  <!-- Stars -->
  <?php
  $stars = [[7,11,2.2],[14,79,3.4,1.2],[26,44,2.8,.6],[72,18,4.1,2],[81,66,2.5,.3],[54,89,3.9,1.8],[35,25,3.1,.9],[60,55,2.6,1.5],[18,70,4.3,.4],[45,88,2.9,2.2]];
  foreach($stars as $st):
  ?>
  <div class="star" style="top:<?=$st[0]?>%;left:<?=$st[1]?>%;animation-duration:<?=$st[2]?>s<?=isset($st[3])?';animation-delay:'.$st[3].'s':''?>"></div>
  <?php endforeach; ?>

  <!-- Nav -->
  <nav class="lp-nav">
    <div class="brand">
      <img src="https://nbscgco.vercel.app/logo.png" alt="NBSC" onerror="this.style.display='none'">
      <div class="brand-text">
        <h3>NBSC — GCO</h3>
        <p>Guidance & Counseling Office</p>
      </div>
    </div>
    <div class="nav-btns">
      <a href="auth/student_register.php" class="btn-ghost">Register</a>
      <a href="auth/login.php" class="btn-solid">Sign In</a>
    </div>
  </nav>

  <!-- Hero -->
  <section class="lp-hero">
    <div class="hero-badge">
      <span></span>
      Northern Bukidnon State College
    </div>

    <h1 class="hero-title">
      Guidance &amp; Counseling<br>
      <span class="accent">Inventory System</span>
    </h1>

    <p class="hero-sub">
      A centralized platform for managing student records, mental health assessments,
      and counseling services at NBSC.
    </p>

    <div class="hero-btns">
      <a href="auth/student_register.php" class="btn-hero-primary">
        <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
        Get Started
      </a>
      <a href="auth/login.php" class="btn-hero-outline">
        <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
        Sign In
      </a>
    </div>

    <div class="hero-stats">
      <div class="stat-item">
        <div class="num">📋</div>
        <div class="lbl">Inventory Forms</div>
      </div>
      <div class="stat-div"></div>
      <div class="stat-item">
        <div class="num">🧠</div>
        <div class="lbl">Mental Health</div>
      </div>
      <div class="stat-div"></div>
      <div class="stat-item">
        <div class="num">📊</div>
        <div class="lbl">Analytics</div>
      </div>
      <div class="stat-div"></div>
      <div class="stat-item">
        <div class="num">🔒</div>
        <div class="lbl">Secure Records</div>
      </div>
    </div>
  </section>

  <!-- Features -->
  <section class="lp-features">
    <div class="feat-grid">
      <div class="feat-card">
        <div class="feat-icon" style="background:rgba(99,102,241,.15)">📋</div>
        <h4>Student Inventory</h4>
        <p>Complete individual inventory forms with personal, family, and academic background.</p>
      </div>
      <div class="feat-card">
        <div class="feat-icon" style="background:rgba(16,185,129,.15)">🧠</div>
        <h4>Mental Health</h4>
        <p>BSRS-5, WHODAS 2.0, and PID-5 assessments to monitor student well-being.</p>
      </div>
      <div class="feat-card">
        <div class="feat-icon" style="background:rgba(245,158,11,.15)">📄</div>
        <h4>Document Management</h4>
        <p>Upload, manage, and print official GCO forms and consent documents.</p>
      </div>
      <div class="feat-card">
        <div class="feat-icon" style="background:rgba(239,68,68,.15)">📊</div>
        <h4>Analytics & Reports</h4>
        <p>Track trends, generate reports, and monitor student counseling progress.</p>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="lp-footer">
    <p>© <?=date('Y')?> Northern Bukidnon State College — Guidance &amp; Counseling Office</p>
    <div class="footer-links">
      <a href="auth/login.php">Sign In</a>
      <a href="auth/student_register.php">Register</a>
    </div>
  </footer>
</div>

<?php require_once __DIR__.'/includes/footer.php'; ?>
