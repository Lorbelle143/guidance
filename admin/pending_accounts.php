<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireAuth();
$pageTitle = 'Pending Accounts - NBSC GCO';
require_once __DIR__.'/../includes/header.php';

// Handle approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    $id     = (int)$_POST['id'];
    $action = $_POST['action'];
    try {
        $db = getDB();
        if ($action === 'approve') {
            $db->prepare("UPDATE students SET is_active=1 WHERE id=?")->execute([$id]);
            setFlash('success', 'Account approved successfully.');
        } elseif ($action === 'reject') {
            $db->prepare("DELETE FROM students WHERE id=? AND is_active=0")->execute([$id]);
            setFlash('success', 'Account rejected and removed.');
        }
    } catch (PDOException $e) { setFlash('error', 'Error processing request.'); }
    redirect('pending_accounts.php');
}

try {
    $db = getDB();
    $pending = $db->query("SELECT * FROM students WHERE is_active=0 ORDER BY created_at DESC")->fetchAll();
} catch (PDOException $e) { $pending = []; }
$flash = getFlash();
?>
<div class="dash">
  <?php require_once __DIR__.'/../includes/admin_sidebar.php'; ?>
  <div class="main-area">
    <header class="topbar">
      <div class="topbar-left"><h2>Pending Accounts</h2><p><?= count($pending) ?> accounts awaiting approval</p></div>
    </header>
    <main class="page-body">
      <?php if ($flash): ?>
      <div class="alert <?= $flash['type']==='error'?'error':'success' ?>" style="margin-bottom:20px">
        <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        <span><?= sanitize($flash['message']) ?></span>
      </div>
      <?php endif; ?>

      <div class="card">
        <div class="card-header">
          <h3>
            <div class="ch-icon" style="background:#fef3c7;color:#d97706">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            Pending Student Accounts
          </h3>
        </div>
        <?php if (empty($pending)): ?>
        <div class="empty-state">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <h4>No pending accounts</h4>
          <p>All student accounts have been reviewed</p>
        </div>
        <?php else: ?>
        <div class="tbl-wrap">
          <table class="tbl">
            <thead><tr><th>Photo</th><th>Student ID</th><th>Name</th><th>Email</th><th>Registered</th><th>Actions</th></tr></thead>
            <tbody>
              <?php foreach ($pending as $s): ?>
              <tr>
                <td>
                  <?php if (!empty($s['photo']) && file_exists(UPLOAD_PATH.$s['photo'])): ?>
                  <img src="../uploads/<?= htmlspecialchars($s['photo']) ?>" style="width:40px;height:40px;border-radius:50%;object-fit:cover">
                  <?php else: ?>
                  <div style="width:40px;height:40px;border-radius:50%;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700"><?= strtoupper(substr($s['first_name'],0,1).substr($s['last_name'],0,1)) ?></div>
                  <?php endif; ?>
                </td>
                <td style="font-weight:600"><?= htmlspecialchars($s['student_id']) ?></td>
                <td><?= htmlspecialchars($s['last_name'].', '.$s['first_name']) ?></td>
                <td style="color:var(--gray-500)"><?= htmlspecialchars($s['email'] ?? '—') ?></td>
                <td style="color:var(--gray-500);font-size:.82rem"><?= date('M d, Y', strtotime($s['created_at'])) ?></td>
                <td>
                  <div style="display:flex;gap:6px">
                    <form method="POST" style="display:inline">
                      <input type="hidden" name="id" value="<?= $s['id'] ?>">
                      <input type="hidden" name="action" value="approve">
                      <button type="submit" class="btn-sm green">
                        <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Approve
                      </button>
                    </form>
                    <form method="POST" style="display:inline" onsubmit="return confirm('Reject and delete this account?')">
                      <input type="hidden" name="id" value="<?= $s['id'] ?>">
                      <input type="hidden" name="action" value="reject">
                      <button type="submit" class="btn-sm red">
                        <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reject
                      </button>
                    </form>
                  </div>
                </td>
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
