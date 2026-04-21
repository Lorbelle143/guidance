<?php
/**
 * Admin notification bell — include in admin topbar
 * Shows student activity notifications (submissions, uploads, registrations)
 */
require_once __DIR__.'/notifications.php';
try {
    $db = getDB();
    $unread = getAdminUnreadCount($db);
    $notifs = $db->query("
        SELECT n.*, s.first_name, s.last_name, s.student_id as sid, s.photo
        FROM notifications n
        LEFT JOIN students s ON n.student_id = s.id
        WHERE n.type = 'student_activity'
        ORDER BY n.created_at DESC
        LIMIT 15
    ")->fetchAll();
} catch (Exception $e) {
    $unread = 0; $notifs = [];
}
?>
<div style="position:relative" id="adminNotifWrap">
  <button onclick="toggleAdminNotif()" style="position:relative;width:40px;height:40px;border-radius:50%;background:var(--gray-100);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--gray-600);transition:all .15s" onmouseover="this.style.background='var(--gray-200)'" onmouseout="this.style.background='var(--gray-100)'" title="Notifications">
    <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
    <?php if ($unread > 0): ?>
    <span style="position:absolute;top:6px;right:6px;width:9px;height:9px;background:#ef4444;border-radius:50%;border:2px solid #fff"></span>
    <?php endif; ?>
  </button>

  <div id="adminNotifPanel" style="display:none;position:absolute;top:calc(100% + 10px);right:0;width:340px;background:#fff;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,.12);border:1px solid var(--gray-200);z-index:200;overflow:hidden">
    <!-- Header -->
    <div style="padding:14px 18px;border-bottom:1px solid var(--gray-100);display:flex;align-items:center;justify-content:space-between">
      <div>
        <div style="font-size:.9rem;font-weight:700;color:var(--gray-900)">Notifications</div>
        <?php if ($unread > 0): ?>
        <div style="font-size:.72rem;color:#ef4444;font-weight:600"><?= $unread ?> unread</div>
        <?php else: ?>
        <div style="font-size:.72rem;color:var(--gray-400)">All caught up</div>
        <?php endif; ?>
      </div>
      <?php if ($unread > 0): ?>
      <button onclick="markAdminNotifsRead()" style="font-size:.75rem;color:var(--primary);background:none;border:none;cursor:pointer;font-weight:600;padding:4px 8px;border-radius:6px" onmouseover="this.style.background='var(--primary-light)'" onmouseout="this.style.background='none'">Mark all read</button>
      <?php endif; ?>
    </div>

    <!-- List -->
    <div style="max-height:360px;overflow-y:auto">
      <?php if (empty($notifs)): ?>
      <div style="padding:40px 20px;text-align:center;color:var(--gray-400)">
        <svg style="width:36px;height:36px;margin:0 auto 10px;display:block;opacity:.4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <div style="font-size:.85rem">No activity yet</div>
      </div>
      <?php else: foreach ($notifs as $n):
        $ini = strtoupper(substr($n['first_name']??'?',0,1).substr($n['last_name']??'',0,1));
        $isUnread = !$n['admin_read'];
      ?>
      <div style="display:flex;gap:12px;padding:13px 18px;border-bottom:1px solid var(--gray-100);<?= $isUnread ? 'background:#f0f7ff' : '' ?>;transition:background .1s" onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='<?= $isUnread ? '#f0f7ff' : 'transparent' ?>'">
        <?php if (!empty($n['photo']) && file_exists(UPLOAD_PATH.$n['photo'])): ?>
        <img src="../uploads/<?= htmlspecialchars($n['photo']) ?>" style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;border:2px solid var(--gray-200)">
        <?php else: ?>
        <div style="width:36px;height:36px;border-radius:50%;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;flex-shrink:0"><?= $ini ?></div>
        <?php endif; ?>
        <div style="flex:1;min-width:0">
          <div style="font-size:.82rem;font-weight:600;color:var(--gray-900);margin-bottom:2px"><?= htmlspecialchars($n['subject']) ?></div>
          <div style="font-size:.78rem;color:var(--gray-500);margin-bottom:3px"><?= htmlspecialchars(($n['first_name']??'').' '.($n['last_name']??'')) ?> &bull; <?= htmlspecialchars($n['sid']??'') ?></div>
          <div style="font-size:.75rem;color:var(--gray-400)"><?= date('M d, h:i A', strtotime($n['created_at'])) ?></div>
        </div>
        <?php if ($isUnread): ?>
        <div style="width:8px;height:8px;background:#3b82f6;border-radius:50%;flex-shrink:0;margin-top:6px"></div>
        <?php endif; ?>
      </div>
      <?php endforeach; endif; ?>
    </div>

    <!-- Footer -->
    <div style="padding:10px 18px;border-top:1px solid var(--gray-100);text-align:center">
      <a href="view_students.php" style="font-size:.8rem;color:var(--primary);font-weight:600;text-decoration:none">View all students →</a>
    </div>
  </div>
</div>

<script>
function toggleAdminNotif() {
    const p = document.getElementById('adminNotifPanel');
    p.style.display = p.style.display === 'none' ? 'block' : 'none';
    if (p.style.display === 'block') markAdminNotifsRead();
}
function markAdminNotifsRead() {
    fetch('../process/mark_admin_notifs_read.php', {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}})
        .then(() => {
            // Remove red dot and unread count
            document.querySelectorAll('#adminNotifWrap [style*="background:#f0f7ff"]').forEach(el => el.style.background = 'transparent');
            document.querySelectorAll('#adminNotifWrap [style*="background:#3b82f6"]').forEach(el => el.remove());
            const dot = document.querySelector('#adminNotifWrap button span');
            if (dot) dot.remove();
        }).catch(() => {});
}
document.addEventListener('click', function(e) {
    if (!document.getElementById('adminNotifWrap').contains(e.target)) {
        document.getElementById('adminNotifPanel').style.display = 'none';
    }
});
</script>
