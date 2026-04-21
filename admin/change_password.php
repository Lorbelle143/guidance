<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireAuth();
$pageTitle = 'Change Password - NBSC GCO';
require_once __DIR__.'/../includes/header.php';
$flash = getFlash();
?>
<div class="dash">
  <?php require_once __DIR__.'/../includes/admin_sidebar.php'; ?>
  <div class="main-area">
    <header class="topbar">
      <div class="topbar-left"><h2>Change Password</h2><p>Update your admin account password</p></div>
      <div class="topbar-right">
        <a href="admin_dashboard.php" class="btn-sm gray">
          <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
          Back
        </a>
        <div class="topbar-avatar" style="background:linear-gradient(135deg,#f97316,#dc2626);cursor:default">
          <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
      </div>
    </header>
    <main class="page-body">
      <?php if ($flash): ?>
      <div class="alert <?= $flash['type']==='error'?'error':'success' ?>" style="margin-bottom:20px">
        <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        <span><?= sanitize($flash['message']) ?></span>
      </div>
      <?php endif; ?>

      <div style="max-width:480px">
        <!-- Account Info -->
        <div class="card" style="margin-bottom:20px">
          <div class="card-body" style="display:flex;align-items:center;gap:16px">
            <div style="width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#f97316,#dc2626);display:flex;align-items:center;justify-content:center;flex-shrink:0">
              <svg style="width:24px;height:24px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
              <div style="font-size:.95rem;font-weight:700;color:var(--gray-900)"><?= htmlspecialchars($_SESSION['full_name'] ?? 'Administrator') ?></div>
              <div style="font-size:.82rem;color:var(--gray-500)"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></div>
              <span class="badge rose" style="margin-top:4px">Admin</span>
            </div>
          </div>
        </div>

        <!-- Change Password Form -->
        <div class="card">
          <div class="card-header">
            <h3>
              <div class="ch-icon" style="background:#fef3c7;color:#d97706">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
              </div>
              Change Password
            </h3>
          </div>
          <div class="card-body">
            <form action="../process/admin_change_password.php" method="POST">
              <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
              <div class="fg2">
                <label>Current Password <span style="color:var(--rose)">*</span></label>
                <input type="password" name="current_password" class="inp-plain" placeholder="Enter current password" required>
              </div>
              <div class="fg2">
                <label>New Password <span style="color:var(--rose)">*</span></label>
                <input type="password" name="new_password" id="np" class="inp-plain" placeholder="Minimum 6 characters" required minlength="6">
              </div>
              <div class="fg2" style="margin-bottom:24px">
                <label>Confirm New Password <span style="color:var(--rose)">*</span></label>
                <input type="password" name="confirm_password" id="cp" class="inp-plain" placeholder="Repeat new password" required minlength="6">
              </div>
              <div class="info-box" style="margin-bottom:20px">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p>After changing your password, you will need to log in again with the new password.</p>
              </div>
              <button type="submit" class="btn-main" style="background:var(--amber)">
                <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Update Password
              </button>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const np = document.getElementById('np').value;
    const cp = document.getElementById('cp').value;
    if (np !== cp) { e.preventDefault(); alert('Passwords do not match!'); }
});
</script>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
