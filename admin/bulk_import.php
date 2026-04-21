<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireAuth();
$pageTitle = 'Bulk Import - NBSC GCO';
require_once __DIR__.'/../includes/header.php';
?>
<div class="dash">
  <?php require_once __DIR__.'/../includes/admin_sidebar.php'; ?>
  <div class="main-area">
    <header class="topbar">
      <div class="topbar-left"><h2>Bulk Import</h2><p>Import multiple student records</p></div>
    </header>
    <main class="page-body">
      <div class="card"><div class="empty-state" style="padding:80px 20px">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
        <h4>Bulk Import</h4><p>This feature is under development</p>
      </div></div>
    </main>
  </div>
</div>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
