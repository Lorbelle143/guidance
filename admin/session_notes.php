<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireAuth();
$pageTitle = 'Session Notes - NBSC GCO';
require_once __DIR__.'/../includes/header.php';
?>
<div class="dash">
  <?php require_once __DIR__.'/../includes/admin_sidebar.php'; ?>
  <div class="main-area">
    <header class="topbar">
      <div class="topbar-left"><h2>Session Notes</h2><p>Counseling session records</p></div>
    </header>
    <main class="page-body">
      <div class="card"><div class="empty-state" style="padding:80px 20px">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        <h4>Session Notes</h4><p>This feature is under development</p>
      </div></div>
    </main>
  </div>
</div>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
