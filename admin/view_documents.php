<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireAuth();
$pageTitle = 'Documents - NBSC GCO';
require_once __DIR__.'/../includes/header.php';

$search  = sanitize($_GET['search'] ?? '');
$docType = sanitize($_GET['doc_type'] ?? '');

try {
    $db = getDB();
    $where = []; $params = [];
    if ($search) { $where[] = "(s.student_id LIKE ? OR s.last_name LIKE ? OR s.first_name LIKE ?)"; $p = "%$search%"; $params = array_merge($params,[$p,$p,$p]); }
    if ($docType) { $where[] = "sd.document_type = ?"; $params[] = $docType; }
    $wClause = $where ? 'WHERE '.implode(' AND ',$where) : '';
    $stmt = $db->prepare("SELECT sd.*,s.student_id as sid,s.first_name,s.last_name,s.photo FROM student_documents sd INNER JOIN students s ON sd.student_id=s.id $wClause ORDER BY s.last_name,s.first_name,sd.uploaded_at DESC");
    $stmt->execute($params);
    $docs = $stmt->fetchAll();
    $counts = $db->query("SELECT document_type,COUNT(*) as c FROM student_documents GROUP BY document_type")->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) { $docs = []; $counts = []; }

$dtypes  = ['inventory_form'=>'Inventory Form','whodas'=>'WHODAS 2.0','pid5'=>'PID-5','consent_form'=>'Consent Form','other'=>'Other'];
$dbadges = ['inventory_form'=>'blue','whodas'=>'green','pid5'=>'purple','consent_form'=>'amber','other'=>'gray'];
$flash   = getFlash();
?>
<div class="dash">
  <?php require_once __DIR__.'/../includes/admin_sidebar.php'; ?>
  <div class="main-area">
    <header class="topbar">
      <div class="topbar-left"><h2>Student Documents</h2><p>All uploaded documents sorted by student name</p></div>
    </header>
    <main class="page-body">
      <?php if ($flash): ?>
      <div class="alert <?= $flash['type']==='error'?'error':'success' ?>" style="margin-bottom:20px">
        <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        <span><?= sanitize($flash['message']) ?></span>
      </div>
      <?php endif; ?>

      <!-- Stats -->
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">
        <?php foreach ([['inventory_form','Inventory Forms','blue'],['whodas','WHODAS 2.0','green'],['pid5','PID-5','purple'],['consent_form','Consent Forms','amber']] as [$k,$lbl,$col]): ?>
        <div class="stat-card">
          <div class="stat-icon <?= $col ?>">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          </div>
          <div><div class="stat-val"><?= $counts[$k] ?? 0 ?></div><div class="stat-lbl"><?= $lbl ?></div></div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Search & Filter -->
      <div class="card" style="margin-bottom:20px">
        <div class="card-body" style="padding:16px 20px">
          <form method="GET" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
            <div style="position:relative;flex:1;min-width:200px">
              <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:var(--gray-400)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by student name or ID..." class="inp-plain" style="padding-left:38px">
            </div>
            <select name="doc_type" class="inp-plain" style="width:200px">
              <option value="">All Document Types</option>
              <?php foreach ($dtypes as $k => $v): ?>
              <option value="<?= $k ?>" <?= $docType===$k?'selected':'' ?>><?= $v ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-main">Filter</button>
            <?php if ($search || $docType): ?><a href="view_documents.php" class="btn-sm gray">Clear</a><?php endif; ?>
          </form>
        </div>
      </div>

      <!-- Table -->
      <div class="card">
        <div class="card-header">
          <h3>
            <div class="ch-icon" style="background:#e0e7ff;color:#4f46e5">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            Documents (<?= count($docs) ?>)
          </h3>
        </div>
        <?php if (empty($docs)): ?>
        <div class="empty-state">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          <h4>No documents found</h4>
          <p>Students haven't uploaded any documents yet</p>
        </div>
        <?php else: ?>
        <div class="tbl-wrap">
          <table class="tbl">
            <thead><tr><th>Student</th><th>Student ID</th><th>Document Type</th><th>File Name</th><th>Upload Date</th><th>Size</th><th>Actions</th></tr></thead>
            <tbody>
              <?php $prev = ''; foreach ($docs as $d):
                $isNew = $prev !== $d['sid']; $prev = $d['sid'];
              ?>
              <tr <?= $isNew ? 'style="background:#f8faff"' : '' ?>>
                <td>
                  <div style="display:flex;align-items:center;gap:10px">
                    <?php if (!empty($d['photo']) && file_exists(UPLOAD_PATH.$d['photo'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($d['photo']) ?>" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid var(--gray-200)">
                    <?php else: ?>
                    <div style="width:36px;height:36px;border-radius:50%;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700"><?= strtoupper(substr($d['first_name'],0,1).substr($d['last_name'],0,1)) ?></div>
                    <?php endif; ?>
                    <span style="font-weight:600;color:var(--gray-900)"><?= htmlspecialchars($d['last_name'].', '.$d['first_name']) ?></span>
                  </div>
                </td>
                <td style="color:var(--gray-500);font-size:.82rem"><?= htmlspecialchars($d['sid']) ?></td>
                <td><span class="badge <?= $dbadges[$d['document_type']] ?? 'gray' ?>"><?= $dtypes[$d['document_type']] ?? 'Document' ?></span></td>
                <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:.82rem"><?= htmlspecialchars($d['document_name']) ?></td>
                <td style="color:var(--gray-500);font-size:.82rem"><?= date('M d, Y h:i A', strtotime($d['uploaded_at'])) ?></td>
                <td style="color:var(--gray-500);font-size:.82rem"><?= number_format($d['file_size']/1024,1) ?> KB</td>
                <td>
                  <div style="display:flex;gap:6px">
                    <a href="../uploads/documents/<?= htmlspecialchars($d['file_path']) ?>" target="_blank" class="btn-sm blue">
                      <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                      View
                    </a>
                    <a href="../process/admin_delete_document.php?id=<?= $d['id'] ?>" class="btn-sm red" onclick="return confirm('Delete this document?')">
                      <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                      Delete
                    </a>
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
