<?php
$pg = basename($_SERVER['PHP_SELF']);
$ini = strtoupper(substr($_SESSION['full_name']??'S',0,1));
$photo = $_SESSION['student_photo'] ?? '';
?>
<aside class="sidebar student-sidebar">
  <div class="sb-logo">
    <img src="https://nbscgco.vercel.app/logo.png" alt="GCO" onerror="this.style.display='none'">
    <div class="brand"><strong>Student Portal</strong><span>NBSC Guidance Office</span></div>
  </div>
  <nav class="sb-nav">
    <a href="student_dashboard.php" class="sb-link <?= $pg==='student_dashboard.php'?'on':'' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
      Dashboard
    </a>
    <a href="inventory_form.php" class="sb-link <?= $pg==='inventory_form.php'?'on':'' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      Inventory Form
    </a>
    <a href="mental_health.php" class="sb-link <?= $pg==='mental_health.php'?'on':'' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      Mental Health
    </a>
    <a href="profile.php" class="sb-link <?= $pg==='profile.php'?'on':'' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
      Edit Profile
    </a>
    <a href="../auth/logout.php" class="sb-link danger" style="margin-top:auto">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      Sign Out
    </a>
  </nav>
  <div class="sb-footer">
    <div class="sb-user">
      <div class="av">
        <?php if (!empty($photo) && file_exists(UPLOAD_PATH.$photo)): ?>
          <img src="../uploads/<?= htmlspecialchars($photo) ?>" alt="">
        <?php else: ?><?= $ini ?><?php endif; ?>
      </div>
      <div class="info">
        <div class="name"><?= htmlspecialchars($_SESSION['full_name'] ?? 'Student') ?></div>
        <div class="role"><?= htmlspecialchars($_SESSION['student_number'] ?? '') ?></div>
      </div>
      <a href="../auth/logout.php" class="logout" title="Sign Out">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      </a>
    </div>
  </div>
</aside>
