<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireStudent();
$pageTitle = 'Upload Documents - NBSC Student Portal';
require_once __DIR__.'/../includes/header.php';
try {
    $db = getDB();
    $st = $db->prepare("SELECT * FROM students WHERE id=?"); $st->execute([$_SESSION['student_id']]); $student = $st->fetch();
    $_SESSION['student_photo'] = $student['photo'] ?? '';
    $ds = $db->prepare("SELECT * FROM student_documents WHERE student_id=? ORDER BY uploaded_at DESC"); $ds->execute([$_SESSION['student_id']]); $docs = $ds->fetchAll();
} catch (PDOException $e) { $docs = []; $student = []; }
$dtypes  = ['inventory_form'=>'Inventory Form','whodas'=>'WHODAS 2.0','pid5'=>'PID-5','consent_form'=>'Consent Form','other'=>'Other'];
$dbadges = ['inventory_form'=>'blue','whodas'=>'green','pid5'=>'purple','consent_form'=>'amber','other'=>'gray'];
$flash   = getFlash();
$ini     = strtoupper(substr($student['first_name']??'S',0,1).substr($student['last_name']??'',0,1));
?>
<div class="dash">
  <?php require_once __DIR__.'/../includes/student_sidebar.php'; ?>
  <div class="main-area">
    <header class="topbar">
      <div class="topbar-left"><h2>Upload Documents</h2><p>Scan and upload your guidance office forms</p></div>
      <div class="topbar-right">
        <div class="topbar-avatar" onclick="location.href='profile.php'" title="Edit Profile">
          <?php if (!empty($student['photo']) && file_exists(UPLOAD_PATH.$student['photo'])): ?>
          <img src="../uploads/<?= htmlspecialchars($student['photo']) ?>" alt="">
          <?php else: ?><?= $ini ?><?php endif; ?>
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

      <div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start">
        <!-- Upload Form -->
        <div class="card">
          <div class="card-header">
            <h3>
              <div class="ch-icon" style="background:#d1fae5;color:#059669">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
              </div>
              Upload New Document
            </h3>
          </div>
          <div class="card-body">
            <form action="../process/upload_document_process.php" method="POST" enctype="multipart/form-data" id="uploadForm">
              <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
              <div class="fg2">
                <label>Document Type <span style="color:var(--rose)">*</span></label>
                <select name="document_type" class="inp-plain" required>
                  <option value="">Select document type...</option>
                  <?php foreach ($dtypes as $k => $v): ?>
                  <option value="<?= $k ?>"><?= $v ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="fg2">
                <label>File <span style="color:var(--rose)">*</span></label>
                <input type="file" name="document" id="docFile" accept="image/*,.pdf" required style="width:100%;padding:9px;border:1.5px solid var(--gray-200);border-radius:var(--radius);font-size:.875rem;background:var(--gray-50)">
                <p style="font-size:.75rem;color:var(--gray-400);margin-top:4px">JPG, PNG, PDF — max 10MB</p>
              </div>
              <div id="imgPrev" style="display:none;margin-bottom:16px">
                <img id="prev" src="" style="max-width:100%;max-height:300px;border-radius:var(--radius);border:1px solid var(--gray-200)">
              </div>
              <div class="fg2">
                <label>Notes (Optional)</label>
                <textarea name="notes" class="inp-plain" rows="3" placeholder="Add any notes about this document..."></textarea>
              </div>
              <button type="submit" class="btn-main emerald">
                <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                Upload Document
              </button>
            </form>
          </div>
        </div>

        <!-- Instructions -->
        <div class="card">
          <div class="card-header"><h3><div class="ch-icon" style="background:#e0e7ff;color:#4f46e5"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>How to Upload</h3></div>
          <div class="card-body">
            <ol style="padding-left:18px;display:flex;flex-direction:column;gap:10px;font-size:.875rem;color:var(--gray-600)">
              <li>Take a clear photo of your document</li>
              <li>Select the document type</li>
              <li>Choose your file</li>
              <li>Add notes if needed</li>
              <li>Click Upload Document</li>
            </ol>
            <div class="info-box" style="margin-top:16px">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
              <p>Make sure the document is clear and readable before uploading.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Uploaded Documents -->
      <div class="card" style="margin-top:20px">
        <div class="card-header">
          <h3><div class="ch-icon" style="background:#e0e7ff;color:#4f46e5"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>My Uploaded Documents</h3>
          <span class="badge blue"><?= count($docs) ?> files</span>
        </div>
        <?php if (empty($docs)): ?>
        <div class="empty-state"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><h4>No documents yet</h4><p>Upload your first document above</p></div>
        <?php else: ?>
        <div class="tbl-wrap">
          <table class="tbl">
            <thead><tr><th>Type</th><th>File Name</th><th>Upload Date</th><th>Size</th><th>Actions</th></tr></thead>
            <tbody>
              <?php foreach ($docs as $d): ?>
              <tr>
                <td><span class="badge <?= $dbadges[$d['document_type']]??'gray' ?>"><?= $dtypes[$d['document_type']]??'Document' ?></span></td>
                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($d['document_name']) ?></td>
                <td style="color:var(--gray-500);font-size:.82rem"><?= date('M d, Y h:i A',strtotime($d['uploaded_at'])) ?></td>
                <td style="color:var(--gray-500);font-size:.82rem"><?= number_format($d['file_size']/1024,1) ?> KB</td>
                <td><div style="display:flex;gap:6px">
                  <a href="../uploads/documents/<?= htmlspecialchars($d['file_path']) ?>" target="_blank" class="btn-sm blue"><svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>View</a>
                  <a href="../process/delete_document.php?id=<?= $d['id'] ?>" class="btn-sm red" onclick="return confirm('Delete?')"><svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Delete</a>
                </div></td>
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
<script>
document.getElementById('docFile').addEventListener('change',function(){
  const f=this.files[0];if(!f)return;
  if(f.type.startsWith('image/')){
    const r=new FileReader();r.onload=e=>{document.getElementById('prev').src=e.target.result;document.getElementById('imgPrev').style.display='block'};r.readAsDataURL(f);
  } else { document.getElementById('imgPrev').style.display='none'; }
});
</script>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
