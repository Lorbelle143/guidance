<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireAdmin();
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { setFlash('error','Invalid student.'); redirect('view_students.php'); }
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM students WHERE id=?");
    $stmt->execute([$id]);
    $student = $stmt->fetch();
    if (!$student) { setFlash('error','Student not found.'); redirect('view_students.php'); }
} catch (PDOException $e) { setFlash('error','Error loading student.'); redirect('view_students.php'); }
$pageTitle = 'Edit Student - NBSC GCO';
require_once __DIR__.'/../includes/header.php';
$flash = getFlash();
?>
<div class="dash">
  <?php require_once __DIR__.'/../includes/admin_sidebar.php'; ?>
  <div class="main-area">
    <header class="topbar">
      <div class="topbar-left"><h2>Edit Student</h2><p><?= htmlspecialchars($student['last_name'].', '.$student['first_name']) ?></p></div>
      <div class="topbar-right">
        <a href="view_students.php" class="btn-sm gray">
          <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
          Back
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

      <div style="max-width:720px">
        <div class="card">
          <div class="card-header">
            <h3>
              <div class="ch-icon" style="background:#fef3c7;color:#d97706">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
              </div>
              Edit Student Information
            </h3>
          </div>
          <div class="card-body">
            <!-- Current photo -->
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;padding:16px;background:var(--gray-50);border-radius:var(--radius);border:1px solid var(--gray-200)">
              <?php if (!empty($student['photo']) && file_exists(UPLOAD_PATH.$student['photo'])): ?>
              <img src="../uploads/<?= htmlspecialchars($student['photo']) ?>" id="prev" style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--primary-light)">
              <?php else: ?>
              <div id="prev" style="width:72px;height:72px;border-radius:50%;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700"><?= strtoupper(substr($student['first_name'],0,1).substr($student['last_name'],0,1)) ?></div>
              <?php endif; ?>
              <div>
                <p style="font-size:.875rem;font-weight:600;color:var(--gray-800)"><?= htmlspecialchars($student['first_name'].' '.$student['last_name']) ?></p>
                <p style="font-size:.8rem;color:var(--gray-500)"><?= htmlspecialchars($student['student_id']) ?></p>
              </div>
            </div>

            <form action="../process/update_student.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
              <input type="hidden" name="id" value="<?= $student['id'] ?>">
              <input type="hidden" name="current_photo" value="<?= htmlspecialchars($student['photo']) ?>">

              <div class="form-grid g2" style="margin-bottom:16px">
                <div class="fg2"><label>Student ID <span style="color:var(--rose)">*</span></label><input type="text" name="student_id" class="inp-plain" value="<?= htmlspecialchars($student['student_id']) ?>" required></div>
                <div class="fg2"><label>Email</label><input type="email" name="email" class="inp-plain" value="<?= htmlspecialchars($student['email']??'') ?>"></div>
              </div>
              <div class="form-grid g3" style="margin-bottom:16px">
                <div class="fg2"><label>Last Name <span style="color:var(--rose)">*</span></label><input type="text" name="last_name" class="inp-plain" value="<?= htmlspecialchars($student['last_name']) ?>" required></div>
                <div class="fg2"><label>First Name <span style="color:var(--rose)">*</span></label><input type="text" name="first_name" class="inp-plain" value="<?= htmlspecialchars($student['first_name']) ?>" required></div>
                <div class="fg2"><label>Middle Name</label><input type="text" name="middle_name" class="inp-plain" value="<?= htmlspecialchars($student['middle_name']??'') ?>"></div>
              </div>
              <div class="fg2" style="margin-bottom:20px">
                <label>Update Photo <span style="font-size:.75rem;color:var(--gray-400)">(leave blank to keep current)</span></label>
                <input type="file" name="photo" id="photo" accept="image/*" style="width:100%;padding:9px;border:1.5px solid var(--gray-200);border-radius:var(--radius);font-size:.875rem;background:var(--gray-50)">
              </div>
              <div style="display:flex;gap:12px">
                <button type="submit" class="btn-main">
                  <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                  Update Student
                </button>
                <a href="view_students.php" class="btn-sm gray" style="padding:10px 18px">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<script>
document.getElementById('photo').addEventListener('change',function(){
  const f=this.files[0];if(!f)return;
  const r=new FileReader();r.onload=e=>{
    const p=document.getElementById('prev');
    if(p.tagName==='IMG'){p.src=e.target.result;}
    else{const img=document.createElement('img');img.id='prev';img.src=e.target.result;img.style='width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--primary-light)';p.replaceWith(img);}
  };r.readAsDataURL(f);
});
</script>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
