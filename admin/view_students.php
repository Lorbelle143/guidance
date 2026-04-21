<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireAuth();
$pageTitle = 'All Students - NBSC GCO';
require_once __DIR__.'/../includes/header.php';

$search  = sanitize($_GET['search'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;
$offset  = ($page - 1) * $perPage;

try {
    $db = getDB();
    $where  = $search ? "WHERE student_id LIKE ? OR last_name LIKE ? OR first_name LIKE ? OR email LIKE ?" : '';
    $params = $search ? ["%$search%","%$search%","%$search%","%$search%"] : [];
    $total  = $db->prepare("SELECT COUNT(*) FROM students $where");
    $total->execute($params);
    $totalRows = $total->fetchColumn();
    $totalPages = ceil($totalRows / $perPage);
    $stmt = $db->prepare("SELECT * FROM students $where ORDER BY last_name,first_name LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);
    $students = $stmt->fetchAll();
} catch (PDOException $e) { $students = []; $totalRows = 0; $totalPages = 0; }

$flash = getFlash();
?>
<div class="dash">
  <?php require_once __DIR__.'/../includes/admin_sidebar.php'; ?>
  <div class="main-area">
    <header class="topbar">
      <div class="topbar-left"><h2>All Students</h2><p><?= number_format($totalRows) ?> total students</p></div>
      <div class="topbar-right">
        <a href="add_student.php" class="btn-main">
          <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
          Add Student
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

      <!-- Search -->
      <div class="card" style="margin-bottom:20px">
        <div class="card-body" style="padding:16px 20px">
          <form method="GET" style="display:flex;gap:12px;align-items:center">
            <div style="position:relative;flex:1;max-width:400px">
              <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:var(--gray-400)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name, ID or email..." class="inp-plain" style="padding-left:38px">
            </div>
            <button type="submit" class="btn-main">Search</button>
            <?php if ($search): ?><a href="view_students.php" class="btn-sm gray">Clear</a><?php endif; ?>
          </form>
        </div>
      </div>

      <!-- Table -->
      <div class="card">
        <div class="card-header">
          <h3>
            <div class="ch-icon" style="background:#dbeafe;color:#1d4ed8">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            Student Records
          </h3>
          <a href="export_pdf.php<?= $search ? '?search='.urlencode($search) : '' ?>" class="btn-sm blue" target="_blank">
            <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export PDF
          </a>
        </div>
        <?php if (empty($students)): ?>
        <div class="empty-state">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          <h4>No students found</h4>
          <p><?= $search ? 'Try a different search term' : 'Add your first student to get started' ?></p>
          <a href="add_student.php" class="btn-main">Add Student</a>
        </div>
        <?php else: ?>
        <div class="tbl-wrap">
          <table class="tbl">
            <thead><tr><th>Photo</th><th>Student ID</th><th>Name</th><th>Email</th><th>Status</th><th>Date Added</th><th style="min-width:280px">Actions</th></tr></thead>
            <tbody>
              <?php foreach ($students as $s): ?>
              <tr>
                <td>
                  <?php if (!empty($s['photo']) && file_exists(UPLOAD_PATH.$s['photo'])): ?>
                  <img src="../uploads/<?= htmlspecialchars($s['photo']) ?>" style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid var(--gray-200)">
                  <?php else: ?>
                  <div style="width:40px;height:40px;border-radius:50%;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700"><?= strtoupper(substr($s['first_name'],0,1).substr($s['last_name'],0,1)) ?></div>
                  <?php endif; ?>
                </td>
                <td style="font-weight:600;color:var(--gray-900)"><?= htmlspecialchars($s['student_id']) ?></td>
                <td><?= htmlspecialchars($s['last_name'].', '.$s['first_name']) ?><?= !empty($s['middle_name']) ? ' '.substr($s['middle_name'],0,1).'.' : '' ?></td>
                <td style="color:var(--gray-500)"><?= htmlspecialchars($s['email'] ?? '—') ?></td>
                <td><span class="badge <?= $s['is_active'] ? 'green' : 'gray' ?>"><?= $s['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                <td style="color:var(--gray-500)"><?= date('M d, Y', strtotime($s['created_at'])) ?></td>
                <td>
                  <div style="display:flex;gap:5px;flex-wrap:wrap">
                    <!-- View -->
                    <a href="view_student.php?id=<?= $s['id'] ?>" class="btn-sm blue" title="View">
                      <svg style="width:12px;height:12px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                      View
                    </a>
                    <!-- Edit -->
                    <a href="edit_student.php?id=<?= $s['id'] ?>" class="btn-sm amber" title="Edit">
                      <svg style="width:12px;height:12px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                      Edit
                    </a>
                    <!-- Print dropdown -->
                    <div style="position:relative;display:inline-block" class="print-drop">
                      <button class="btn-sm green" onclick="togglePrint(this)" title="Print Forms">
                        <svg style="width:12px;height:12px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print ▾
                      </button>
                      <div class="print-menu" style="display:none;position:absolute;top:100%;left:0;z-index:50;background:#fff;border:1px solid var(--gray-200);border-radius:var(--radius);box-shadow:var(--shadow-lg);min-width:210px;overflow:hidden">
                        <!-- Print All -->
                        <a href="print/print_all.php?id=<?= $s['id'] ?>" target="_blank" style="display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:.82rem;color:#fff;background:#1d4ed8;text-decoration:none;font-weight:700;border-bottom:2px solid #1e40af" onmouseover="this.style.background='#1e40af'" onmouseout="this.style.background='#1d4ed8'">
                          <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                          🖨 Print All Forms
                        </a>
                        <a href="print/inventory_form.php?id=<?= $s['id'] ?>" target="_blank" style="display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:.82rem;color:var(--gray-700);text-decoration:none;transition:background .1s" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                          <svg style="width:14px;height:14px;color:#2563eb" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                          Individual Inventory Form
                        </a>
                        <a href="print/whodas.php?id=<?= $s['id'] ?>" target="_blank" style="display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:.82rem;color:var(--gray-700);text-decoration:none;transition:background .1s" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                          <svg style="width:14px;height:14px;color:#059669" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                          WHODAS 2.0
                        </a>
                        <a href="print/pid5.php?id=<?= $s['id'] ?>" target="_blank" style="display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:.82rem;color:var(--gray-700);text-decoration:none;transition:background .1s" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                          <svg style="width:14px;height:14px;color:#7c3aed" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                          PID-5 BF Adult
                        </a>
                        <a href="print/mental_health.php?id=<?= $s['id'] ?>" target="_blank" style="display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:.82rem;color:var(--gray-700);text-decoration:none;transition:background .1s" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                          <svg style="width:14px;height:14px;color:#dc2626" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                          BSRS-5 Mental Health
                        </a>
                        <a href="print/consent_form.php?id=<?= $s['id'] ?>" target="_blank" style="display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:.82rem;color:var(--gray-700);text-decoration:none;transition:background .1s" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                          <svg style="width:14px;height:14px;color:#d97706" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                          Informed Consent Form
                        </a>
                      </div>
                    </div>
                    <!-- Delete -->
                    <a href="delete_student.php?id=<?= $s['id'] ?>" class="btn-sm red" title="Delete" onclick="return confirm('Delete this student?')">
                      <svg style="width:12px;height:12px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                      Delete
                    </a>
                    <!-- Approve/Deactivate toggle -->
                    <?php if (!$s['is_active']): ?>
                    <a href="approve_student.php?id=<?= $s['id'] ?>" class="btn-sm green" title="Approve" onclick="return confirm('Approve this student account?')">
                      <svg style="width:12px;height:12px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                      Approve
                    </a>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php if ($totalPages > 1): ?>
        <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--gray-100)">
          <span style="font-size:.82rem;color:var(--gray-500)">Showing <?= $offset+1 ?>–<?= min($offset+$perPage,$totalRows) ?> of <?= $totalRows ?></span>
          <div style="display:flex;gap:6px">
            <?php if ($page > 1): ?><a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>" class="btn-sm gray">← Prev</a><?php endif; ?>
            <?php for ($i = max(1,$page-2); $i <= min($totalPages,$page+2); $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="btn-sm <?= $i===$page?'blue':'gray' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?><a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>" class="btn-sm gray">Next →</a><?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
      </div>
    </main>
  </div>
</div>
<script>
function togglePrint(btn) {
    const menu = btn.nextElementSibling;
    document.querySelectorAll('.print-menu').forEach(m => { if (m !== menu) m.style.display = 'none'; });
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.print-drop')) {
        document.querySelectorAll('.print-menu').forEach(m => m.style.display = 'none');
    }
});
</script>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
