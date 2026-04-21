<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
requireStudent();
$pageTitle = 'Edit Profile - NBSC Student Portal';
require_once __DIR__.'/../includes/header.php';
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM students WHERE id=?"); $stmt->execute([$_SESSION['student_id']]); $student = $stmt->fetch();
    $_SESSION['student_photo'] = $student['photo'] ?? '';
} catch (PDOException $e) { setFlash('error','Error loading profile.'); redirect('student_dashboard.php'); }
$flash = getFlash();
$ini   = strtoupper(substr($student['first_name'],0,1).substr($student['last_name'],0,1));
?>
<div class="dash">
  <?php require_once __DIR__.'/../includes/student_sidebar.php'; ?>
  <div class="main-area">
    <header class="topbar">
      <div class="topbar-left"><h2>Edit Profile</h2><p>Update your personal information</p></div>
      <div class="topbar-right">
        <div class="topbar-avatar" title="Profile">
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

      <div style="display:grid;grid-template-columns:280px 1fr;gap:20px;align-items:start">
        <!-- Profile Card -->
        <div class="card">
          <div class="card-body" style="text-align:center;padding:28px 20px">
            <?php if (!empty($student['photo']) && file_exists(UPLOAD_PATH.$student['photo'])): ?>
            <img src="../uploads/<?= htmlspecialchars($student['photo']) ?>" style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:4px solid var(--primary-light);margin-bottom:14px">
            <?php else: ?>
            <div style="width:100px;height:100px;border-radius:50%;background:var(--primary-light);color:var(--primary);display:inline-flex;align-items:center;justify-content:center;font-size:2rem;font-weight:800;margin-bottom:14px"><?= $ini ?></div>
            <?php endif; ?>
            <h4 style="font-size:1rem;font-weight:700;color:var(--gray-900);margin-bottom:4px"><?= htmlspecialchars($student['first_name'].' '.$student['last_name']) ?></h4>
            <p style="font-size:.82rem;color:var(--gray-500);margin-bottom:10px"><?= htmlspecialchars($student['student_id']) ?></p>
            <span class="badge <?= $student['is_active']?'green':'gray' ?>"><?= $student['is_active']?'Active':'Inactive' ?></span>
            <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--gray-100);text-align:left">
              <div style="display:flex;flex-direction:column;gap:10px;font-size:.82rem">
                <div style="display:flex;justify-content:space-between"><span style="color:var(--gray-500)">Email</span><span style="color:var(--gray-800);font-weight:500"><?= htmlspecialchars($student['email']??'N/A') ?></span></div>
                <div style="display:flex;justify-content:space-between"><span style="color:var(--gray-500)">Last Login</span><span style="color:var(--gray-800);font-weight:500"><?= $student['last_login']?date('M d, Y',strtotime($student['last_login'])):'First login' ?></span></div>
                <div style="display:flex;justify-content:space-between"><span style="color:var(--gray-500)">Registered</span><span style="color:var(--gray-800);font-weight:500"><?= date('M d, Y',strtotime($student['created_at'])) ?></span></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Edit Form -->
        <div style="display:flex;flex-direction:column;gap:20px">
          <!-- Profile Info -->
          <div class="card">
            <div class="card-header"><h3><div class="ch-icon" style="background:#dbeafe;color:#1d4ed8"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>Personal Information</h3></div>
            <div class="card-body">
              <form action="../process/update_student.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="hidden" name="id" value="<?= $student['id'] ?>">
                <input type="hidden" name="current_photo" value="<?= htmlspecialchars($student['photo']??'') ?>">
                <div class="form-grid g2" style="margin-bottom:16px">
                  <div class="fg2"><label>Last Name</label><input type="text" name="last_name" class="inp-plain" value="<?= htmlspecialchars($student['last_name']) ?>" required></div>
                  <div class="fg2"><label>First Name</label><input type="text" name="first_name" class="inp-plain" value="<?= htmlspecialchars($student['first_name']) ?>" required></div>
                </div>
                <div class="form-grid g2" style="margin-bottom:16px">
                  <div class="fg2"><label>Middle Name</label><input type="text" name="middle_name" class="inp-plain" value="<?= htmlspecialchars($student['middle_name']??'') ?>"></div>
                  <div class="fg2"><label>Email</label><input type="email" name="email" class="inp-plain" value="<?= htmlspecialchars($student['email']??'') ?>"></div>
                </div>
                <div class="fg2" style="margin-bottom:20px">
                  <label>Update Photo <span style="font-size:.75rem;color:var(--gray-400)">(leave blank to keep current)</span></label>
                  <input type="file" name="photo" accept="image/*" style="width:100%;padding:9px;border:1.5px solid var(--gray-200);border-radius:var(--radius);font-size:.875rem;background:var(--gray-50)">
                </div>
                <button type="submit" class="btn-main">
                  <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                  Save Changes
                </button>
              </form>
            </div>
          </div>

          <!-- Change Password -->
          <div class="card">
            <div class="card-header"><h3><div class="ch-icon" style="background:#fef3c7;color:#d97706"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg></div>Change Password</h3></div>
            <div class="card-body">
              <form action="../process/student_change_password.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <div class="form-grid g3" style="margin-bottom:16px">
                  <div class="fg2"><label>Current Password</label><input type="password" name="current_password" class="inp-plain" required></div>
                  <div class="fg2"><label>New Password</label><input type="password" name="new_password" class="inp-plain" required minlength="6"></div>
                  <div class="fg2"><label>Confirm Password</label><input type="password" name="confirm_password" class="inp-plain" required minlength="6"></div>
                </div>
                <button type="submit" class="btn-main" style="background:var(--amber)">
                  <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                  Change Password
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
