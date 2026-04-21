<?php
/**
 * ReadEase - Admin Account Setup Script
 * Run this ONCE after importing database.sql to create the 5 admin accounts.
 * Then DELETE this file for security.
 *
 * Visit: http://localhost/reading-tool/setup_admins.php
 */

require_once __DIR__ . '/includes/db.php';

// ============================================================
// DEFINE YOUR 5 ADMIN ACCOUNTS HERE
// Change names, emails, and passwords as needed
// ============================================================
$admins = [
    [
        'name'     => 'Lorbelle Ganzan',
        'email'    => 'lorbelleganzan@gmail.com',
        'password' => 'admin123',
    ],
    [
        'name'     => 'Admin Group 2',
        'email'    => 'admin.group2@gmail.com',
        'password' => 'Admin@Group2',
    ],
    [
        'name'     => 'Admin Group 3',
        'email'    => 'admin.group3@gmail.com',
        'password' => 'Admin@Group3',
    ],
    [
        'name'     => 'Admin Group 4',
        'email'    => 'admin.group4@gmail.com',
        'password' => 'Admin@Group4',
    ],
    [
        'name'     => 'Admin Group 5',
        'email'    => 'admin.group5@gmail.com',
        'password' => 'Admin@Group5',
    ],
];

// ============================================================
// PROCESS
// ============================================================
$results = [];

foreach ($admins as $admin) {
    // Check if email already exists
    $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $check->execute([$admin['email']]);

    if ($check->fetch()) {
        // Update existing account to admin role and new password
        $hash = password_hash($admin['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('UPDATE users SET name = ?, password = ?, role = "admin" WHERE email = ?');
        $stmt->execute([$admin['name'], $hash, $admin['email']]);
        $results[] = ['status' => 'updated', 'email' => $admin['email'], 'name' => $admin['name']];
    } else {
        // Insert new admin account
        $hash = password_hash($admin['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, "admin")');
        $stmt->execute([$admin['name'], $hash, $admin['email']]);
        $results[] = ['status' => 'created', 'email' => $admin['email'], 'name' => $admin['name']];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Setup - ReadEase</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: system-ui, sans-serif; background: #f1f5f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 2rem; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,.1); padding: 2rem; max-width: 600px; width: 100%; }
    h1 { font-size: 1.5rem; color: #1e293b; margin-bottom: .5rem; }
    p.sub { color: #64748b; margin-bottom: 1.5rem; font-size: .9rem; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
    th { background: #f8fafc; text-align: left; padding: .6rem .8rem; font-size: .8rem; color: #64748b; text-transform: uppercase; letter-spacing: .05em; border-bottom: 1px solid #e2e8f0; }
    td { padding: .7rem .8rem; border-bottom: 1px solid #f1f5f9; font-size: .9rem; color: #334155; }
    .badge { display: inline-block; padding: .2rem .6rem; border-radius: 999px; font-size: .75rem; font-weight: 600; }
    .badge-created { background: #d1fae5; color: #065f46; }
    .badge-updated { background: #dbeafe; color: #1e40af; }
    .warning { background: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px; padding: 1rem; font-size: .875rem; color: #92400e; }
    .warning strong { display: block; margin-bottom: .3rem; }
    .btn { display: inline-block; margin-top: 1rem; padding: .6rem 1.2rem; background: #4f46e5; color: #fff; border-radius: 8px; text-decoration: none; font-size: .9rem; }
  </style>
</head>
<body>
  <div class="card">
    <h1>✅ Admin Accounts Setup Complete</h1>
    <p class="sub">The following admin accounts have been created or updated.</p>

    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($results as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['name']) ?></td>
          <td><?= htmlspecialchars($r['email']) ?></td>
          <td><span class="badge badge-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="warning">
      <strong>⚠️ Security Warning</strong>
      Delete this file immediately after setup to prevent unauthorized access.<br>
      File to delete: <code>reading-tool/setup_admins.php</code>
    </div>

    <a href="login.php" class="btn">Go to Login →</a>
  </div>
</body>
</html>
