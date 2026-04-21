<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireAuth();
$pageTitle = 'Mental Health - NBSC GCO';
require_once __DIR__.'/../includes/header.php';

try {
    $db = getDB();
    $assessments = $db->query("
        SELECT mha.*, s.student_id, s.first_name, s.last_name, s.photo
        FROM mental_health_assessments mha
        INNER JOIN students s ON mha.student_id = s.id
        ORDER BY mha.created_at DESC
    ")->fetchAll();
} catch (PDOException $e) { $assessments = []; }
$flash = getFlash();
?>
<div class="dash">
  <?php require_once __DIR__.'/../includes/admin_sidebar.php'; ?>
  <div class="main-area">
    <header class="topbar">
      <div class="topbar-left"><h2>Mental Health</h2><p>BSRS-5 Assessment Results</p></div>
    </header>
    <main class="page-body">
      <?php if ($flash): ?>
      <div class="alert <?= $flash['type']==='error'?'error':'success' ?>" style="margin-bottom:20px">
        <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        <span><?= sanitize($flash['message']) ?></span>
      </div>
      <?php endif; ?>

      <?php if (empty($assessments)): ?>
      <div class="card">
        <div class="empty-state">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <h4>No assessments yet</h4>
          <p>Students haven't submitted any mental health assessments</p>
        </div>
      </div>
      <?php else: ?>
      <div class="card">
        <div class="card-header">
          <h3>
            <div class="ch-icon" style="background:#ffe4e6;color:#9f1239">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            BSRS-5 Assessments (<?= count($assessments) ?>)
          </h3>
        </div>
        <div class="tbl-wrap">
          <table class="tbl">
            <thead>
              <tr><th>Student</th><th>Score</th><th>Risk Level</th><th>Counseling</th><th>Date</th></tr>
            </thead>
            <tbody>
              <?php foreach ($assessments as $a):
                $score = $a['total_score'] ?? 0;
                $risk  = $score <= 5 ? ['Doing Well','green'] : ($score <= 9 ? ['Need Support','amber'] : ['Immediate Support','rose']);
              ?>
              <tr>
                <td>
                  <div style="display:flex;align-items:center;gap:10px">
                    <?php if (!empty($a['photo']) && file_exists(UPLOAD_PATH.$a['photo'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($a['photo']) ?>" style="width:36px;height:36px;border-radius:50%;object-fit:cover">
                    <?php else: ?>
                    <div style="width:36px;height:36px;border-radius:50%;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700"><?= strtoupper(substr($a['first_name'],0,1).substr($a['last_name'],0,1)) ?></div>
                    <?php endif; ?>
                    <div>
                      <div style="font-weight:600;color:var(--gray-900)"><?= htmlspecialchars($a['last_name'].', '.$a['first_name']) ?></div>
                      <div style="font-size:.75rem;color:var(--gray-500)"><?= htmlspecialchars($a['student_id']) ?></div>
                    </div>
                  </div>
                </td>
                <td><strong style="font-size:1.1rem"><?= $score ?></strong><span style="color:var(--gray-400)">/20</span></td>
                <td><span class="badge <?= $risk[1] ?>"><?= $risk[0] ?></span></td>
                <td>
                  <?php if (!empty($a['requires_counseling'])): ?>
                  <span class="badge rose">Required</span>
                  <?php else: ?>
                  <span style="color:var(--gray-400);font-size:.82rem">Not required</span>
                  <?php endif; ?>
                </td>
                <td style="color:var(--gray-500);font-size:.82rem"><?= date('M d, Y', strtotime($a['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php endif; ?>
    </main>
  </div>
</div>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
