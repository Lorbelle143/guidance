<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireAuth();
$pageTitle = 'Audit Log - NBSC GCO';
require_once __DIR__.'/../includes/header.php';

try {
    $db = getDB();
    $logs = $db->query("SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 100")->fetchAll();
} catch (PDOException $e) { $logs = []; }
?>
<div class="dash">
  <?php require_once __DIR__.'/../includes/admin_sidebar.php'; ?>
  <div class="main-area">
    <header class="topbar">
      <div class="topbar-left"><h2>Audit Log</h2><p>System activity history</p></div>
    </header>
    <main class="page-body">
      <div class="card">
        <div class="card-header">
          <h3><div class="ch-icon" style="background:#f3f4f6;color:#374151"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>Activity Log</h3>
        </div>
        <?php if (empty($logs)): ?>
        <div class="empty-state">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          <h4>No activity logs yet</h4>
          <p>System activities will appear here</p>
        </div>
        <?php else: ?>
        <div class="tbl-wrap">
          <table class="tbl">
            <thead><tr><th>Action</th><th>Description</th><th>IP Address</th><th>Date</th></tr></thead>
            <tbody>
              <?php foreach ($logs as $log): ?>
              <tr>
                <td><span class="badge blue"><?= htmlspecialchars($log['action']) ?></span></td>
                <td><?= htmlspecialchars($log['description'] ?? '—') ?></td>
                <td style="color:var(--gray-500);font-size:.82rem"><?= htmlspecialchars($log['ip_address'] ?? '—') ?></td>
                <td style="color:var(--gray-500);font-size:.82rem"><?= date('M d, Y h:i A', strtotime($log['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
    </main>
  </div>
</div>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
