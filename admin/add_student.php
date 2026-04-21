<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireAdmin();
$pageTitle = 'Add Student - NBSC GCO';
require_once __DIR__.'/../includes/header.php';
$flash = getFlash();
?>
<div class="dash">
  <?php require_once __DIR__.'/../includes/admin_sidebar.php'; ?>
  <div class="main-area">
    <header class="topbar">
      <div class="topbar-left"><h2>Add New Student</h2><p>Create a new student account</p></div>
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
              <div class="ch-icon" style="background:#dbeafe;color:#1d4ed8">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
              </div>
              Student Information
            </h3>
          </div>
          <div class="card-body">
            <form action="../process/save_student.php" method="POST" enctype="multipart/form-data" id="addForm">
              <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

              <div class="form-grid g2" style="margin-bottom:16px">
                <div class="fg2"><label>Student ID <span style="color:var(--rose)">*</span></label><input type="text" name="student_id" class="inp-plain" placeholder="e.g. 2024-001" required pattern="[A-Za-z0-9\-]+"></div>
                <div class="fg2"><label>Email</label><input type="email" name="email" class="inp-plain" placeholder="student@example.com"></div>
              </div>
              <div class="form-grid g3" style="margin-bottom:16px">
                <div class="fg2"><label>Last Name <span style="color:var(--rose)">*</span></label><input type="text" name="last_name" class="inp-plain" placeholder="Last name" required></div>
                <div class="fg2"><label>First Name <span style="color:var(--rose)">*</span></label><input type="text" name="first_name" class="inp-plain" placeholder="First name" required></div>
                <div class="fg2"><label>Middle Name</label><input type="text" name="middle_name" class="inp-plain" placeholder="Optional"></div>
              </div>
              <div class="fg2" style="margin-bottom:16px">
                <label>Default Password <span style="color:var(--rose)">*</span></label>
                <input type="password" name="password" class="inp-plain" placeholder="Student will use this to login" required minlength="6">
                <p style="font-size:.75rem;color:var(--gray-400);margin-top:4px">Minimum 6 characters</p>
              </div>
              <div class="fg2" style="margin-bottom:20px">
                <label>Photo <span style="color:var(--rose)">*</span></label>
                <input type="file" name="photo" id="photo" accept="image/*" required style="width:100%;padding:9px;border:1.5px solid var(--gray-200);border-radius:var(--radius);font-size:.875rem;background:var(--gray-50)">
                <p style="font-size:.75rem;color:var(--gray-400);margin-top:4px">JPG, PNG, GIF — max 5MB</p>
              </div>
              <div id="imgPrev" style="display:none;margin-bottom:20px">
                <img id="prev" src="" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--primary-light)">
              </div>
              <div style="display:flex;gap:12px">
                <button type="submit" class="btn-main">
                  <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                  Save Student
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
  const r=new FileReader();r.onload=e=>{document.getElementById('prev').src=e.target.result;document.getElementById('imgPrev').style.display='block'};r.readAsDataURL(f);
});
</script>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
