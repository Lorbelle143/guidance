<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireAuth();
$pageTitle = 'Follow-up Tracking - NBSC GCO';
require_once __DIR__.'/../includes/header.php';
?>
<div class="dash">
  <?php require_once __DIR__.'/../includes/admin_sidebar.php'; ?>
  <div class="main-area">
    <header class="topbar">
      <div class="topbar-left"><h2>Follow-up Tracking</h2><p>Track student counseling follow-ups</p></div>
    </header>
    <main class="page-body">
      <div class="card"><div class="empty-state" style="padding:80px 20px">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        <h4>Follow-up Tracking</h4><p>This feature is under development</p>
      </div></div>
    </main>
  </div>
</div>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
