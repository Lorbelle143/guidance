<?php $pg = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar">
  <div class="sb-logo">
    <img src="https://nbscgco.vercel.app/logo.png" alt="GCO" onerror="this.style.display='none'" style="width:40px;height:40px;object-fit:contain">
    <div class="brand"><strong>Admin Panel</strong><span>NBSC Guidance Office</span></div>
  </div>

  <nav class="sb-nav">

    <!-- Home -->
    <a href="admin_dashboard.php" class="sb-link <?= $pg==='admin_dashboard.php'?'on':'' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
      Home
      <?php
      try { $db=getDB(); $c=$db->query("SELECT COUNT(*) FROM students")->fetchColumn(); if($c>0) echo '<span class="sb-badge">'.$c.'</span>'; } catch(Exception $e){}
      ?>
    </a>

    <!-- Students -->
    <a href="view_students.php" class="sb-link <?= $pg==='view_students.php'?'on':'' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      Students
      <?php
      try { $db=getDB(); $c=$db->query("SELECT COUNT(*) FROM students")->fetchColumn(); if($c>0) echo '<span class="sb-badge" style="background:#fff3e0;color:#e65100">'.$c.'</span>'; } catch(Exception $e){}
      ?>
    </a>

    <!-- Mental Health -->
    <a href="mental_health.php" class="sb-link <?= $pg==='mental_health.php'?'on':'' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      Mental Health
    </a>

    <!-- Follow-up Tracking -->
    <a href="followup_tracking.php" class="sb-link <?= $pg==='followup_tracking.php'?'on':'' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
      Follow-up Tracking
    </a>

    <!-- Session Notes -->
    <a href="session_notes.php" class="sb-link <?= $pg==='session_notes.php'?'on':'' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
      Session Notes
    </a>

    <!-- Consent Tracker -->
    <a href="consent_tracker.php" class="sb-link <?= $pg==='consent_tracker.php'?'on':'' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
      Consent Tracker
    </a>

    <!-- MH Trends -->
    <a href="mh_trends.php" class="sb-link <?= $pg==='mh_trends.php'?'on':'' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
      MH Trends
    </a>

    <!-- Analytics -->
    <a href="analytics.php" class="sb-link <?= $pg==='analytics.php'?'on':'' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
      Analytics
    </a>

    <!-- Reports & Export -->
    <a href="export_pdf.php" class="sb-link <?= $pg==='export_pdf.php'?'on':'' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      Reports &amp; Export
    </a>

    <!-- Bulk Import -->
    <a href="bulk_import.php" class="sb-link <?= $pg==='bulk_import.php'?'on':'' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
      Bulk Import
    </a>

    <!-- Password Requests -->
    <a href="password_requests.php" class="sb-link <?= $pg==='password_requests.php'?'on':'' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
      Password Requests
      <?php
      try { $db=getDB(); $c=$db->query("SELECT COUNT(*) FROM password_requests WHERE status='pending'")->fetchColumn(); if($c>0) echo '<span class="sb-badge" style="background:#ef4444;color:#fff">'.$c.'</span>'; } catch(Exception $e){}
      ?>
    </a>

    <!-- User Management -->
    <a href="manage_users.php" class="sb-link <?= $pg==='manage_users.php'?'on':'' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
      User Management
    </a>

    <!-- Pending Accounts -->
    <a href="pending_accounts.php" class="sb-link <?= $pg==='pending_accounts.php'?'on':'' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      Pending Accounts
      <?php
      try { $db=getDB(); $c=$db->query("SELECT COUNT(*) FROM students WHERE is_active=0")->fetchColumn(); if($c>0) echo '<span class="sb-badge" style="background:#fff3e0;color:#e65100">'.$c.'</span>'; } catch(Exception $e){}
      ?>
    </a>

    <!-- Send Notification -->
    <a href="send_notification.php" class="sb-link <?= $pg==='send_notification.php'?'on':'' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
      Send Notification
    </a>

    <!-- Audit Log -->
    <a href="audit_log.php" class="sb-link <?= $pg==='audit_log.php'?'on':'' ?>">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      Audit Log
    </a>

    <!-- Sign Out -->
    <a href="../auth/logout.php" class="sb-link danger" style="margin-top:auto">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      Sign Out
    </a>

  </nav>

  <div class="sb-footer">
    <div class="sb-user">
      <div class="av" style="background:linear-gradient(135deg,#f97316,#dc2626)">
        <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
      </div>
      <div class="info">
        <div class="name"><?= htmlspecialchars($_SESSION['full_name'] ?? 'Administrator') ?></div>
        <div class="role"><?= htmlspecialchars($_SESSION['username'] ?? 'admin') ?></div>
      </div>
      <a href="../auth/logout.php" class="logout" title="Sign Out">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      </a>
    </div>
  </div>
</aside>
