<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireAuth();
$pageTitle = 'Analytics - NBSC GCO';
require_once __DIR__.'/../includes/header.php';

try {
    $db = getDB();
    $totalStudents  = $db->query("SELECT COUNT(*) FROM students WHERE is_active=1")->fetchColumn();
    $totalDocs      = $db->query("SELECT COUNT(*) FROM student_documents")->fetchColumn();
    $totalAssess    = $db->query("SELECT COUNT(*) FROM mental_health_assessments")->fetchColumn() ?? 0;
    $highRisk       = $db->query("SELECT COUNT(*) FROM mental_health_assessments WHERE risk_level='immediate-support'")->fetchColumn() ?? 0;
    $byDocType      = $db->query("SELECT document_type, COUNT(*) as c FROM student_documents GROUP BY document_type ORDER BY c DESC")->fetchAll();
    $byMonth        = $db->query("SELECT DATE_FORMAT(created_at,'%b %Y') as month, COUNT(*) as c FROM students WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY month ORDER BY created_at")->fetchAll();
} catch (PDOException $e) {
    $totalStudents = $totalDocs = $totalAssess = $highRisk = 0;
    $byDocType = $byMonth = [];
}
$dtypes = ['inventory_form'=>'Inventory Form','whodas'=>'WHODAS 2.0','pid5'=>'PID-5','consent_form'=>'Consent Form','other'=>'Other'];
$dbadges = ['inventory_form'=>'blue','whodas'=>'green','pid5'=>'purple','consent_form'=>'amber','other'=>'gray'];
?>
<div class="dash">
  <?php require_once __DIR__.'/../includes/admin_sidebar.php'; ?>
  <div class="main-area">
    <header class="topbar">
      <div class="topbar-left"><h2>Analytics</h2><p>System overview and statistics</p></div>
    </header>
    <main class="page-body">

      <!-- Stats -->
      <div class="stats-grid" style="margin-bottom:24px">
        <div class="stat-card">
          <div class="stat-icon blue"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
          <div><div class="stat-val"><?= number_format($totalStudents) ?></div><div class="stat-lbl">Active Students</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
          <div><div class="stat-val"><?= number_format($totalDocs) ?></div><div class="stat-lbl">Documents Uploaded</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon rose"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
          <div><div class="stat-val"><?= number_format($totalAssess) ?></div><div class="stat-lbl">MH Assessments</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon amber"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
          <div><div class="stat-val"><?= number_format($highRisk) ?></div><div class="stat-lbl">High Risk Cases</div></div>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
        <!-- Documents by Type -->
        <div class="card">
          <div class="card-header">
            <h3><div class="ch-icon" style="background:#e0e7ff;color:#4f46e5"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>Documents by Type</h3>
          </div>
          <div class="card-body">
            <?php if (empty($byDocType)): ?>
            <p style="color:var(--gray-400);text-align:center;padding:20px">No documents yet</p>
            <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:12px">
              <?php foreach ($byDocType as $row):
                $pct = $totalDocs > 0 ? round(($row['c']/$totalDocs)*100) : 0;
                $badge = $dbadges[$row['document_type']] ?? 'gray';
              ?>
              <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                  <span class="badge <?= $badge ?>"><?= $dtypes[$row['document_type']] ?? $row['document_type'] ?></span>
                  <span style="font-size:.82rem;font-weight:600;color:var(--gray-700)"><?= $row['c'] ?> (<?= $pct ?>%)</span>
                </div>
                <div style="height:6px;background:var(--gray-100);border-radius:3px;overflow:hidden">
                  <div style="height:100%;width:<?= $pct ?>%;background:var(--primary);border-radius:3px;transition:width .5s"></div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Student Registrations by Month -->
        <div class="card">
          <div class="card-header">
            <h3><div class="ch-icon" style="background:#d1fae5;color:#059669"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg></div>Registrations (Last 6 Months)</h3>
          </div>
          <div class="card-body">
            <?php if (empty($byMonth)): ?>
            <p style="color:var(--gray-400);text-align:center;padding:20px">No data yet</p>
            <?php else:
              $maxVal = max(array_column($byMonth, 'c'));
            ?>
            <div style="display:flex;flex-direction:column;gap:10px">
              <?php foreach ($byMonth as $row):
                $pct = $maxVal > 0 ? round(($row['c']/$maxVal)*100) : 0;
              ?>
              <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                  <span style="font-size:.82rem;color:var(--gray-600)"><?= $row['month'] ?></span>
                  <span style="font-size:.82rem;font-weight:600;color:var(--gray-800)"><?= $row['c'] ?> students</span>
                </div>
                <div style="height:6px;background:var(--gray-100);border-radius:3px;overflow:hidden">
                  <div style="height:100%;width:<?= $pct ?>%;background:var(--emerald);border-radius:3px"></div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
