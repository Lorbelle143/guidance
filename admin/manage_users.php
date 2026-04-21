<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireAuth();
$pageTitle = 'User Management — NBSC GCO';
require_once __DIR__.'/../includes/header.php';

$search  = sanitize($_GET['search'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;
$offset  = ($page - 1) * $perPage;

try {
    $db = getDB();
    $where  = $search ? "WHERE student_id LIKE ? OR last_name LIKE ? OR first_name LIKE ? OR email LIKE ?" : '';
    $params = $search ? ["%$search%","%$search%","%$search%","%$search%"] : [];

    $total = $db->prepare("SELECT COUNT(*) FROM students $where");
    $total->execute($params);
    $totalRows  = (int)$total->fetchColumn();
    $totalPages = (int)ceil($totalRows / $perPage);

    $stmt = $db->prepare("SELECT * FROM students $where ORDER BY last_name, first_name LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);
    $students = $stmt->fetchAll();
} catch (PDOException $e) {
    $students = []; $totalRows = 0; $totalPages = 0;
}

$flash = getFlash();
?>
<style>
/* ── User card grid ── */
.uc-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
}
@media(max-width:1100px){ .uc-grid{ grid-template-columns: repeat(2,1fr); } }
@media(max-width:640px){  .uc-grid{ grid-template-columns: 1fr; } }

.uc-card {
  background: #fff;
  border: 1px solid var(--gray-200);
  border-radius: var(--radius-md);
  padding: 22px 20px 18px;
  box-shadow: var(--shadow-sm);
  transition: box-shadow .2s, transform .2s;
  display: flex;
  flex-direction: column;
  gap: 0;
}
.uc-card:hover {
  box-shadow: var(--shadow-md);
  transform: translateY(-2px);
}

/* Avatar */
.uc-avatar {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background: linear-gradient(135deg, #f97316, #ea580c);
  color: #fff;
  font-size: 1.25rem;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  overflow: hidden;
  border: 3px solid #fff;
  box-shadow: 0 2px 8px rgba(249,115,22,.3);
}
.uc-avatar img { width:100%; height:100%; object-fit:cover; }

/* Header row */
.uc-head {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  margin-bottom: 14px;
}
.uc-info { flex: 1; min-width: 0; }
.uc-name {
  font-size: .95rem;
  font-weight: 800;
  color: var(--gray-900);
  line-height: 1.25;
  text-transform: uppercase;
  letter-spacing: .01em;
  margin-bottom: 3px;
}
.uc-sid {
  font-size: .75rem;
  color: var(--gray-400);
  font-weight: 500;
  font-family: monospace;
}

/* Meta rows */
.uc-meta {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 16px;
}
.uc-meta-row {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: .82rem;
  color: var(--gray-600);
}
.uc-meta-row svg {
  width: 14px;
  height: 14px;
  color: var(--gray-400);
  flex-shrink: 0;
}

/* Action buttons */
.uc-actions {
  display: flex;
  gap: 8px;
  margin-top: auto;
}
.uc-btn {
  flex: 1;
  padding: 9px 0;
  border: none;
  border-radius: 8px;
  font-size: .82rem;
  font-weight: 700;
  cursor: pointer;
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  transition: all .15s;
  font-family: 'Inter', sans-serif;
}
.uc-btn.edit {
  background: #2563eb;
  color: #fff;
}
.uc-btn.edit:hover { background: #1d4ed8; }
.uc-btn.del {
  background: #dc2626;
  color: #fff;
}
.uc-btn.del:hover { background: #b91c1c; }

/* Header banner */
.um-banner {
  background: linear-gradient(135deg, #fff5f0, #fff);
  border: 1px solid #fed7aa;
  border-radius: var(--radius-md);
  padding: 20px 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
}
.um-banner-left h2 {
  font-size: 1.3rem;
  font-weight: 800;
  color: var(--gray-900);
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 4px;
}
.um-banner-left p { font-size: .85rem; color: var(--gray-500); }
.um-create-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 11px 22px;
  background: linear-gradient(135deg, #f97316, #ea580c);
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: .9rem;
  font-weight: 700;
  cursor: pointer;
  text-decoration: none;
  transition: all .2s;
  box-shadow: 0 4px 14px rgba(249,115,22,.35);
  font-family: 'Inter', sans-serif;
}
.um-create-btn:hover {
  background: linear-gradient(135deg, #ea580c, #c2410c);
  box-shadow: 0 6px 20px rgba(249,115,22,.5);
  transform: translateY(-1px);
}
</style>

<div class="dash">
  <?php require_once __DIR__.'/../includes/admin_sidebar.php'; ?>
  <div class="main-area">
    <header class="topbar">
      <div class="topbar-left">
        <h2>User Management</h2>
        <p><?= number_format($totalRows) ?> student account<?= $totalRows !== 1 ? 's' : '' ?></p>
      </div>
      <div class="topbar-right">
        <?php require_once __DIR__.'/../includes/admin_notif_bell.php'; ?>
        <a href="profile.php" class="topbar-avatar" title="Profile">
          <?= strtoupper(substr($_SESSION['full_name'] ?? 'A', 0, 1)) ?>
        </a>
      </div>
    </header>

    <main class="page-body">
      <?php if ($flash): ?>
      <div class="alert <?= $flash['type']==='error'?'error':'success' ?>" style="margin-bottom:20px">
        <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        <span><?= sanitize($flash['message']) ?></span>
      </div>
      <?php endif; ?>

      <!-- Banner -->
      <div class="um-banner">
        <div class="um-banner-left">
          <h2>🔒 User Account Management</h2>
          <p>Manage student login accounts, passwords, and permissions</p>
        </div>
        <a href="add_student.php" class="um-create-btn">
          <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
          Create User
        </a>
      </div>

      <!-- Search -->
      <div class="card" style="margin-bottom:20px">
        <div class="card-body" style="padding:14px 20px">
          <form method="GET" style="display:flex;gap:10px;align-items:center">
            <div style="position:relative;flex:1;max-width:380px">
              <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--gray-400)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name, ID or email..." class="inp-plain" style="padding-left:36px">
            </div>
            <button type="submit" class="btn-main">Search</button>
            <?php if ($search): ?>
            <a href="manage_users.php" class="btn-sm gray">Clear</a>
            <?php endif; ?>
          </form>
        </div>
      </div>

      <!-- Cards grid -->
      <?php if (empty($students)): ?>
      <div class="card">
        <div class="empty-state">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          <h4>No users found</h4>
          <p><?= $search ? 'Try a different search term' : 'No student accounts yet' ?></p>
          <a href="add_student.php" class="btn-main">Add Student</a>
        </div>
      </div>
      <?php else: ?>
      <div class="uc-grid">
        <?php foreach ($students as $s):
          $ini = strtoupper(substr($s['first_name'],0,1));
          $fullName = strtoupper($s['last_name'].', '.$s['first_name'].(!empty($s['middle_name']) ? ' '.strtoupper(substr($s['middle_name'],0,1)).'.' : ''));
        ?>
        <div class="uc-card">
          <!-- Head -->
          <div class="uc-head">
            <div class="uc-avatar">
              <?php if (!empty($s['photo']) && file_exists(UPLOAD_PATH.$s['photo'])): ?>
              <img src="../uploads/<?= htmlspecialchars($s['photo']) ?>" alt="">
              <?php else: ?>
              <?= $ini ?>
              <?php endif; ?>
            </div>
            <div class="uc-info">
              <div class="uc-name"><?= htmlspecialchars($fullName) ?></div>
              <div class="uc-sid"><?= htmlspecialchars($s['student_id']) ?></div>
            </div>
          </div>

          <!-- Meta -->
          <div class="uc-meta">
            <div class="uc-meta-row">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($s['email'] ?: '—') ?></span>
            </div>
            <div class="uc-meta-row">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <span>Joined <?= date('M j, Y', strtotime($s['created_at'])) ?></span>
            </div>
          </div>

          <!-- Actions -->
          <div class="uc-actions">
            <a href="edit_student.php?id=<?= $s['id'] ?>" class="uc-btn edit">
              <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
              ✏️ Edit
            </a>
            <a href="delete_student.php?id=<?= $s['id'] ?>" class="uc-btn del"
               onclick="return confirm('Delete <?= htmlspecialchars(addslashes($s['first_name'].' '.$s['last_name'])) ?>? This cannot be undone.')">
              <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              Delete
            </a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Pagination -->
      <?php if ($totalPages > 1): ?>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-top:24px;flex-wrap:wrap;gap:12px">
        <span style="font-size:.82rem;color:var(--gray-500)">
          Showing <?= $offset+1 ?>–<?= min($offset+$perPage,$totalRows) ?> of <?= number_format($totalRows) ?> users
        </span>
        <div style="display:flex;gap:6px">
          <?php if ($page > 1): ?>
          <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>" class="btn-sm gray">← Prev</a>
          <?php endif; ?>
          <?php for ($i = max(1,$page-2); $i <= min($totalPages,$page+2); $i++): ?>
          <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="btn-sm <?= $i===$page?'blue':'gray' ?>"><?= $i ?></a>
          <?php endfor; ?>
          <?php if ($page < $totalPages): ?>
          <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>" class="btn-sm gray">Next →</a>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>
      <?php endif; ?>

    </main>
  </div>
</div>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
