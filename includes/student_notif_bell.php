<?php
/**
 * Student notification bell — include in student topbar
 * Shows notifications sent by admin
 */
require_once __DIR__.'/notifications.php';
$sid = (int)($_SESSION['student_id'] ?? 0);
try {
    $db = getDB();
    $unread = getStudentUnreadCount($db, $sid);
    $notifs = $db->prepare("
        SELECT * FROM notifications
        WHERE type = 'admin_to_student'
          AND (student_id = ? OR student_id IS NULL)
        ORDER BY created_at DESC
        LIMIT 15
    ");
    $notifs->execute([$sid]);
    $notifs = $notifs->fetchAll();
} catch (Exception $e) {
    $unread = 0; $notifs = [];
}
?>
<div style="position:relative" id="studentNotifWrap">
  <button onclick="toggleStudentNotif()" style="position:relative;width:40px;height:40px;border-radius:50%;background:var(--gray-100);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--gray-600);transition:all .15s" onmouseover="this.style.background='var(--gray-200)'" onmouseout="this.style.background='var(--gray-100)'" title="Notifications">
    <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
    <?php if ($unread > 0): ?>
    <span style="position:absolute;top:6px;right:6px;width:9px;height:9px;background:#ef4444;border-radius:50%;border:2px solid #fff"></span>
    <?php endif; ?>
  </button>

  <div id="studentNotifPanel" style="display:none;position:absolute;top:calc(100% + 10px);right:0;width:320px;background:#fff;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,.12);border:1px solid var(--gray-200);z-index:200;overflow:hidden">
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
      <button onclick="markStudentNotifsRead()" style="font-size:.75rem;color:var(--primary);background:none;border:none;cursor:pointer;font-weight:600;padding:4px 8px;border-radius:6px">Mark all read</button>
      <?php endif; ?>
    </div>

    <div style="max-height:360px;overflow-y:auto">
      <?php if (empty($notifs)): ?>
      <div style="padding:40px 20px;text-align:center;color:var(--gray-400)">
        <svg style="width:36px;height:36px;margin:0 auto 10px;display:block;opacity:.4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <div style="font-size:.85rem">No notifications yet</div>
      </div>
      <?php else: foreach ($notifs as $n):
        $isUnread = !$n['is_read'];
      ?>
      <div style="padding:13px 18px;border-bottom:1px solid var(--gray-100);<?= $isUnread ? 'background:#f0f7ff' : '' ?>">
        <div style="display:flex;align-items:flex-start;gap:10px">
          <div style="width:34px;height:34px;border-radius:50%;background:#dbeafe;color:#1d4ed8;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1rem">🔔</div>
          <div style="flex:1;min-width:0">
            <div style="font-size:.85rem;font-weight:600;color:var(--gray-900);margin-bottom:3px"><?= htmlspecialchars($n['subject']) ?></div>
            <div style="font-size:.8rem;color:var(--gray-600);line-height:1.5;margin-bottom:4px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical"><?= htmlspecialchars($n['message']) ?></div>
            <div style="font-size:.72rem;color:var(--gray-400)"><?= date('M d, Y h:i A', strtotime($n['created_at'])) ?></div>
          </div>
          <?php if ($isUnread): ?>
          <div style="width:8px;height:8px;background:#3b82f6;border-radius:50%;flex-shrink:0;margin-top:6px"></div>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; endif; ?>
    </div>

    <div style="padding:10px 18px;border-top:1px solid var(--gray-100);text-align:center">
      <span style="font-size:.78rem;color:var(--gray-400)">From NBSC Guidance &amp; Counseling Office</span>
    </div>
  </div>
</div>

<script>
function toggleStudentNotif() {
    const p = document.getElementById('studentNotifPanel');
    p.style.display = p.style.display === 'none' ? 'block' : 'none';
    if (p.style.display === 'block') markStudentNotifsRead();
}
function markStudentNotifsRead() {
    fetch('../process/mark_student_notifs_read.php', {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}})
        .then(() => {
            document.querySelectorAll('#studentNotifWrap [style*="background:#f0f7ff"]').forEach(el => el.style.background = 'transparent');
            document.querySelectorAll('#studentNotifWrap [style*="background:#3b82f6"]').forEach(el => el.remove());
            const dot = document.querySelector('#studentNotifWrap button span');
            if (dot) dot.remove();
        }).catch(() => {});
}
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('studentNotifWrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('studentNotifPanel').style.display = 'none';
    }
});
</script>
