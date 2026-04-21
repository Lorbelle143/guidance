<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireAuth();
$pageTitle = 'Password Requests - NBSC GCO';
require_once __DIR__.'/../includes/header.php';
?>
<div class="dash">
  <?php require_once __DIR__.'/../includes/admin_sidebar.php'; ?>
  <div class="main-area">
    <header class="topbar">
      <div class="topbar-left"><h2>Password Requests</h2><p>Student password reset requests</p></div>
    </header>
    <main class="page-body">
      <div class="card"><div class="empty-state" style="padding:80px 20px">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
        <h4>Password Requests</h4><p>No pending password reset requests</p>
      </div></div>
    </main>
  </div>
</div>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
