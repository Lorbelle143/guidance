<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
require_once __DIR__.'/../includes/notifications.php';
requireAuth();
$pageTitle = 'Send Notification — NBSC GCO';
require_once __DIR__.'/../includes/header.php';

try {
    $db = getDB();
    $students = $db->query("SELECT id, student_id, first_name, last_name, email FROM students WHERE is_active=1 ORDER BY last_name, first_name")->fetchAll();
    // Recent sent notifications
    $recent = $db->query("
        SELECT n.*, s.first_name, s.last_name, s.student_id as sid
        FROM notifications n
        LEFT JOIN students s ON n.student_id = s.id
        WHERE n.type = 'admin_to_student'
        ORDER BY n.created_at DESC LIMIT 20
    ")->fetchAll();
} catch (PDOException $e) {
    $students = []; $recent = [];
}
$flash = getFlash();
?>
<div class="dash">
  <?php require_once __DIR__.'/../includes/admin_sidebar.php'; ?>
  <div class="main-area">
    <header class="topbar">
      <div class="topbar-left">
        <h2>Send Notification</h2>
        <p>Notify students via bell &amp; email</p>
      </div>
      <?php require_once __DIR__.'/../includes/admin_notif_bell.php'; ?>
    </header>

    <main class="page-body">
      <?php if ($flash): ?>
      <div class="alert <?= $flash['type']==='error'?'error':'success' ?>" style="margin-bottom:20px">
        <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        <span><?= sanitize($flash['message']) ?></span>
      </div>
      <?php endif; ?>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

        <!-- COMPOSE FORM -->
        <div class="card">
          <div class="card-header">
            <h3>
              <div class="ch-icon" style="background:#dbeafe;color:#1d4ed8">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
              </div>
              Compose Notification
            </h3>
          </div>
          <div class="card-body" style="padding:24px">
            <form action="../process/send_notification_process.php" method="POST">
              <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

              <!-- Recipient -->
              <div style="margin-bottom:18px">
                <label style="display:block;font-size:.875rem;font-weight:600;color:var(--gray-700);margin-bottom:8px">Send To</label>
                <div style="display:flex;gap:10px;margin-bottom:10px">
                  <label style="display:flex;align-items:center;gap:7px;padding:10px 16px;border:1.5px solid var(--gray-200);border-radius:9px;cursor:pointer;flex:1;transition:all .15s" id="lbl-all">
                    <input type="radio" name="recipient" value="all" checked onchange="toggleRecipient(this)">
                    <span style="font-size:.875rem;font-weight:500">📢 All Students</span>
                  </label>
                  <label style="display:flex;align-items:center;gap:7px;padding:10px 16px;border:1.5px solid var(--gray-200);border-radius:9px;cursor:pointer;flex:1;transition:all .15s" id="lbl-specific">
                    <input type="radio" name="recipient" value="specific" onchange="toggleRecipient(this)">
                    <span style="font-size:.875rem;font-weight:500">👤 Specific Student</span>
                  </label>
                </div>
                <div id="student-select" style="display:none">
                  <select name="student_id" class="inp" style="width:100%">
                    <option value="">— Select student —</option>
                    <?php foreach ($students as $st): ?>
                    <option value="<?= $st['id'] ?>"><?= htmlspecialchars($st['last_name'].', '.$st['first_name'].' ('.$st['student_id'].')') ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <!-- Subject -->
              <div style="margin-bottom:16px">
                <label style="display:block;font-size:.875rem;font-weight:600;color:var(--gray-700);margin-bottom:6px">Subject *</label>
                <div class="iw">
                  <div class="ico"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg></div>
                  <input type="text" name="subject" class="inp" placeholder="Notification subject..." required maxlength="255">
                </div>
              </div>

              <!-- Message -->
              <div style="margin-bottom:18px">
                <label style="display:block;font-size:.875rem;font-weight:600;color:var(--gray-700);margin-bottom:6px">Message *</label>
                <textarea name="message" rows="6" required placeholder="Type your message here..." style="width:100%;padding:10px 14px;border:1.5px solid var(--gray-200);border-radius:9px;font-size:.875rem;color:var(--gray-800);background:var(--gray-50);outline:none;resize:vertical;font-family:inherit;transition:border-color .2s" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--gray-200)'"></textarea>
              </div>

              <!-- Send via -->
              <div style="margin-bottom:20px;padding:14px;background:var(--gray-50);border-radius:9px;border:1px solid var(--gray-200)">
                <div style="font-size:.8rem;font-weight:600;color:var(--gray-600);margin-bottom:10px">Send via:</div>
                <div style="display:flex;gap:16px">
                  <label style="display:flex;align-items:center;gap:7px;font-size:.875rem;cursor:pointer">
                    <input type="checkbox" name="send_bell" value="1" checked style="width:15px;height:15px;accent-color:var(--primary)">
                    🔔 Notification Bell
                  </label>
                  <label style="display:flex;align-items:center;gap:7px;font-size:.875rem;cursor:pointer">
                    <input type="checkbox" name="send_email" value="1" checked style="width:15px;height:15px;accent-color:var(--primary)">
                    📧 Email
                  </label>
                </div>
              </div>

              <button type="submit" class="btn-main" style="width:100%;justify-content:center">
                <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Send Notification
              </button>
            </form>
          </div>
        </div>

        <!-- RECENT SENT -->
        <div class="card">
          <div class="card-header">
            <h3>
              <div class="ch-icon" style="background:#d1fae5;color:#059669">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              Recently Sent
            </h3>
          </div>
          <div style="max-height:520px;overflow-y:auto">
            <?php if (empty($recent)): ?>
            <div class="empty-state" style="padding:50px 20px">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
              <p>No notifications sent yet</p>
            </div>
            <?php else: foreach ($recent as $n): ?>
            <div style="padding:14px 20px;border-bottom:1px solid var(--gray-100)">
              <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px">
                <div style="flex:1;min-width:0">
                  <div style="font-size:.875rem;font-weight:600;color:var(--gray-900);margin-bottom:3px"><?= htmlspecialchars($n['subject']) ?></div>
                  <div style="font-size:.78rem;color:var(--gray-500);margin-bottom:6px;display:flex;align-items:center;gap:8px">
                    <span class="badge <?= $n['student_id'] ? 'blue' : 'green' ?>">
                      <?= $n['student_id'] ? htmlspecialchars($n['last_name'].', '.$n['first_name']) : '📢 All Students' ?>
                    </span>
                    <span><?= date('M d, Y h:i A', strtotime($n['created_at'])) ?></span>
                  </div>
                  <div style="font-size:.8rem;color:var(--gray-600);line-height:1.5;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical"><?= htmlspecialchars($n['message']) ?></div>
                </div>
              </div>
            </div>
            <?php endforeach; endif; ?>
          </div>
        </div>

      </div>
    </main>
  </div>
</div>

<script>
function toggleRecipient(radio) {
    const sel = document.getElementById('student-select');
    const lblAll = document.getElementById('lbl-all');
    const lblSpec = document.getElementById('lbl-specific');
    if (radio.value === 'specific') {
        sel.style.display = 'block';
        lblSpec.style.borderColor = 'var(--primary)';
        lblSpec.style.background = 'var(--primary-light)';
        lblAll.style.borderColor = 'var(--gray-200)';
        lblAll.style.background = 'transparent';
    } else {
        sel.style.display = 'none';
        lblAll.style.borderColor = 'var(--primary)';
        lblAll.style.background = 'var(--primary-light)';
        lblSpec.style.borderColor = 'var(--gray-200)';
        lblSpec.style.background = 'transparent';
    }
}
// Init highlight
document.getElementById('lbl-all').style.borderColor = 'var(--primary)';
document.getElementById('lbl-all').style.background = 'var(--primary-light)';
</script>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
