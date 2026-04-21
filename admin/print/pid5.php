<?php
require_once __DIR__.'/../../config/database.php';
require_once __DIR__.'/../../includes/session.php';
require_once __DIR__.'/../../includes/functions.php';
requireAuth();

$id = (int)($_GET['id'] ?? 0);
if (!$id) die('Invalid student ID.');
try {
    $db = getDB();
    $st = $db->prepare("SELECT * FROM students WHERE id=?");
    $st->execute([$id]);
    $s  = $st->fetch();
    if (!$s) die('Student not found.');
    $pa = $db->prepare("SELECT * FROM pid5_assessments WHERE student_id=? ORDER BY created_at DESC LIMIT 1");
    $pa->execute([$id]);
    $pid5 = $pa->fetch() ?: [];
} catch (PDOException $e) { $pid5 = []; }

$v   = fn($k) => htmlspecialchars($s[$k] ?? '');
$age = $s['birth_date'] ? (int)date_diff(date_create($s['birth_date']), date_create('today'))->y : '';
$printTitle = 'PID-5 BF — '.$s['last_name'].', '.$s['first_name'];
require_once __DIR__.'/header.php';

$statements = [
  'People would describe me as reckless.',
  'I feel like I act totally on impulse.',
  "Even though I know better, I can't stop making rash decisions.",
  'I often feel like nothing I do really matters.',
  'Others see me as irresponsible.',
  "I'm not good at planning ahead.",
  "My thoughts often don't make sense to others.",
  'I worry about almost everything.',
  'I get emotional easily, often for very little reason.',
  'I fear being alone in life more than anything else.',
  "I get stuck on one way of doing things, even when it's clear it won't work.",
  "I have seen things that weren't really there.",
  'I steer clear of romantic relationships.',
  "I'm not interested in making friends.",
  'I get irritated easily by all sorts of things.',
  "I don't like to get too close to people.",
  "It's no big deal if I hurt other peoples' feelings.",
  'I rarely get enthusiastic about anything.',
  'I crave attention.',
  'I often have to deal with people who are less important than me.',
  'I often have thoughts that make sense to me but that other people say are strange.',
  'I use people to get what I want.',
  'I often "zone out" and then suddenly come to and realize that a lot of time has passed.',
  'Things around me often feel unreal, or more real than usual.',
  'It is easy for me to take advantage of others.',
];
$cols = ['Very False or Often False (0)','Sometimes or Somewhat False (1)','Sometimes or Somewhat True (2)','Very True or Often True (3)'];

$total = 0; $answered = 0;
for ($i=1; $i<=25; $i++) {
    $val = $pid5['item_'.$i] ?? null;
    if ($val !== null) { $total += (int)$val; $answered++; }
}
$avg = $answered > 0 ? round($total/$answered, 2) : 'N/A';
?>

<!-- NBSC Header -->
<div style="display:flex;align-items:center;justify-content:space-between;border-bottom:2.5px solid #000;padding-bottom:6px;margin-bottom:6px">
  <img src="https://nbscgco.vercel.app/logo.png" style="width:62px;height:62px;object-fit:contain" onerror="this.style.display='none'">
  <div style="text-align:center;flex:1">
    <div style="font-size:8pt">Republic of the Philippines</div>
    <div style="font-size:15pt;font-weight:700;line-height:1.1">NORTHERN BUKIDNON STATE COLLEGE</div>
    <div style="font-size:8.5pt">Manolo Fortich, 8703 Bukidnon</div>
    <div style="font-size:7.5pt;font-style:italic;color:#b8860b">Creando Futuro, Transformationis Vitae, Ductae a Deo</div>
  </div>
</div>

<!-- Student Info -->
<table class="form-tbl" style="margin-bottom:6px">
  <tr>
    <td style="width:20%"><span class="field-label">Last Name</span><div class="field-val bold"><?= $v('last_name') ?></div></td>
    <td style="width:20%"><span class="field-label">First Name</span><div class="field-val bold"><?= $v('first_name') ?></div></td>
    <td style="width:8%"><span class="field-label">M.I.</span><div class="field-val"><?= $s['middle_name'] ? strtoupper(substr($s['middle_name'],0,1)).'.' : '' ?></div></td>
    <td style="width:8%"><span class="field-label">Age</span><div class="field-val"><?= $age ?></div></td>
    <td style="width:8%"><span class="field-label">Sex</span><div class="field-val"><?= $v('gender') ?></div></td>
  </tr>
  <tr>
    <td colspan="3"><span class="field-label">Program &amp; Year</span><div class="field-val"><?= $v('program_year') ?></div></td>
    <td colspan="2"><span class="field-label">Date</span><div class="field-val"><?= !empty($pid5['created_at']) ? date('Y-m-d',strtotime($pid5['created_at'])) : date('Y-m-d') ?></div></td>
  </tr>
</table>

<div style="text-align:center;margin-bottom:6px">
  <div style="font-size:11pt;font-weight:700">The Personality Inventory for DSM-5 — Brief Form (PID-5-BF) — Adult</div>
</div>

<div style="font-size:8.5pt;margin-bottom:6px;border:1px solid #000;padding:4px">
  <strong>Instructions:</strong> This is a list of things different people might say about themselves. There are no right or wrong answers. Describe yourself as honestly as possible, selecting the response that best describes you.
</div>

<!-- PID-5 Table -->
<table class="form-tbl" style="font-size:8pt">
  <tr style="background:#000;color:#fff">
    <td style="width:5%;text-align:center;padding:3px">#</td>
    <td style="width:42%;padding:3px 5px">Statement Indicator</td>
    <?php foreach ($cols as $c): ?>
    <td style="text-align:center;padding:3px 2px;font-size:7pt"><?= $c ?></td>
    <?php endforeach; ?>
    <td style="text-align:center;padding:3px 2px">Score</td>
  </tr>
  <?php foreach ($statements as $i => $stmt):
    $num = $i + 1;
    $val = $pid5['item_'.$num] ?? null;
  ?>
  <tr style="<?= $i%2===0?'background:#f9f9f9':'' ?>">
    <td style="text-align:center;font-weight:700"><?= $num ?></td>
    <td style="padding:2px 5px"><?= htmlspecialchars($stmt) ?></td>
    <?php for ($c=0; $c<=3; $c++): ?>
    <td style="text-align:center"><?= ($val !== null && (int)$val === $c) ? '✓' : '' ?></td>
    <?php endfor; ?>
    <td style="text-align:center;font-weight:700"><?= $val !== null ? $val : '' ?></td>
  </tr>
  <?php endforeach; ?>
  <tr>
    <td colspan="6" style="text-align:right;font-weight:700;padding:3px 5px">Total / Partial Raw Score:</td>
    <td style="text-align:center;font-weight:700"><?= $answered > 0 ? $total : '' ?></td>
  </tr>
  <tr>
    <td colspan="6" style="text-align:right;font-size:8pt;padding:2px 5px">Prorated Total Score (if 1–6 items left unanswered):</td>
    <td style="text-align:center;font-size:8pt"><?= $answered < 25 && $answered > 0 ? round($total * (25/$answered)) : 'N/A' ?></td>
  </tr>
  <tr>
    <td colspan="6" style="text-align:right;font-size:8pt;padding:2px 5px">Average Total Score:</td>
    <td style="text-align:center;font-size:8pt"><?= $avg ?></td>
  </tr>
</table>

<?php require_once __DIR__.'/footer.php'; ?>
