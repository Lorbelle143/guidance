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
    $ma = $db->prepare("SELECT * FROM mental_health_assessments WHERE student_id=? ORDER BY created_at DESC LIMIT 1");
    $ma->execute([$id]);
    $mh = $ma->fetch() ?: [];
} catch (PDOException $e) { $mh = []; }

$v   = fn($k) => htmlspecialchars($s[$k] ?? '');
$age = $s['birth_date'] ? (int)date_diff(date_create($s['birth_date']), date_create('today'))->y : '';
$printTitle = 'BSRS-5 — '.$s['last_name'].', '.$s['first_name'];
require_once __DIR__.'/header.php';

$score = (int)($mh['total_score'] ?? 0);
$riskLabel = $score <= 5 ? 'Doing Well' : ($score <= 9 ? 'Need Support' : 'Immediate Support');
$riskColor = $score <= 5 ? '#059669' : ($score <= 9 ? '#d97706' : '#dc2626');

// BSRS-5 items
$items = [
    'Feeling tense or wound up',
    'Feeling unhappy',
    'Feeling irritable or bad-tempered',
    'Feeling anxious or worried',
    'Feeling that life is not worth living',
];
$cols = ['Not at all (0)', 'A little (1)', 'Quite a bit (2)', 'Very much (3)', 'Extremely (4)'];
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

<div style="text-align:center;margin-bottom:4px">
  <div style="font-size:11pt;font-weight:700">Brief Symptom Rating Scale — 5 Items (BSRS-5)</div>
  <div style="font-size:8.5pt">Mental Health Self-Assessment</div>
</div>

<!-- Student Info Row -->
<table class="form-tbl" style="margin-bottom:6px">
  <tr>
    <td style="width:25%"><span class="field-label">Last Name</span><div class="field-val bold"><?= $v('last_name') ?></div></td>
    <td style="width:25%"><span class="field-label">First Name</span><div class="field-val bold"><?= $v('first_name') ?></div></td>
    <td style="width:8%"><span class="field-label">M.I.</span><div class="field-val"><?= $s['middle_name'] ? strtoupper(substr($s['middle_name'],0,1)).'.' : '' ?></div></td>
    <td style="width:8%"><span class="field-label">Age</span><div class="field-val"><?= $age ?></div></td>
    <td style="width:8%"><span class="field-label">Sex</span><div class="field-val"><?= $v('gender') ?></div></td>
    <td><span class="field-label">Program &amp; Year</span><div class="field-val"><?= $v('program_year') ?></div></td>
  </tr>
  <tr>
    <td colspan="3"><span class="field-label">Student ID</span><div class="field-val"><?= $v('student_id') ?></div></td>
    <td colspan="3"><span class="field-label">Date</span><div class="field-val"><?= !empty($mh['created_at']) ? date('Y-m-d', strtotime($mh['created_at'])) : date('Y-m-d') ?></div></td>
  </tr>
</table>

<div style="font-size:8.5pt;margin-bottom:6px;border:1px solid #000;padding:4px">
  <strong>Instructions:</strong> During the past week, how much were you bothered by the following problems? Please select the number that best describes how much you were bothered by each problem.
</div>

<!-- BSRS-5 Table -->
<table class="form-tbl" style="font-size:8.5pt">
  <tr style="background:#000;color:#fff">
    <td style="width:5%;text-align:center;padding:3px">#</td>
    <td style="width:40%;padding:3px 5px;font-weight:700">Item</td>
    <?php foreach ($cols as $c): ?>
    <td style="text-align:center;padding:3px 2px;font-size:7.5pt;font-weight:700"><?= $c ?></td>
    <?php endforeach; ?>
    <td style="text-align:center;padding:3px 2px;font-weight:700">Score</td>
  </tr>
  <?php foreach ($items as $i => $item):
    $num = $i + 1;
    $val = $mh['item_'.$num] ?? null;
  ?>
  <tr style="<?= $i%2===0?'background:#f9f9f9':'' ?>">
    <td style="text-align:center;font-weight:700"><?= $num ?></td>
    <td style="padding:4px 5px"><?= htmlspecialchars($item) ?></td>
    <?php for ($c=0; $c<=4; $c++): ?>
    <td style="text-align:center;padding:4px 2px"><?= ($val !== null && (int)$val === $c) ? '✓' : '' ?></td>
    <?php endfor; ?>
    <td style="text-align:center;font-weight:700"><?= $val !== null ? $val : '' ?></td>
  </tr>
  <?php endforeach; ?>
  <tr style="background:#e8e8e8">
    <td colspan="6" style="text-align:right;font-weight:700;padding:4px 5px">Total Score:</td>
    <td style="text-align:center;font-weight:700;font-size:11pt"><?= !empty($mh) ? $score : '' ?></td>
  </tr>
</table>

<!-- Scoring Guide -->
<table class="form-tbl" style="margin-top:8px;font-size:8.5pt">
  <tr style="background:#e8e8e8"><td colspan="3" style="font-weight:700;padding:3px 5px">Scoring Guide</td></tr>
  <tr>
    <td style="width:33%;padding:4px 6px"><strong>0–5:</strong> Doing Well — No significant distress</td>
    <td style="width:33%;padding:4px 6px"><strong>6–9:</strong> Need Support — Mild to moderate distress</td>
    <td style="width:34%;padding:4px 6px"><strong>10–20:</strong> Immediate Support — Significant distress</td>
  </tr>
</table>

<!-- Result Summary -->
<?php if (!empty($mh)): ?>
<div style="margin-top:10px;border:2px solid <?= $riskColor ?>;border-radius:4px;padding:8px 12px;display:flex;align-items:center;gap:12px">
  <div style="font-size:22pt;font-weight:700;color:<?= $riskColor ?>"><?= $score ?></div>
  <div>
    <div style="font-weight:700;font-size:10pt;color:<?= $riskColor ?>"><?= $riskLabel ?></div>
    <div style="font-size:8.5pt;color:#555">
      <?php if (!empty($mh['requires_counseling'])): ?>
      ⚠ Counseling is recommended for this student.
      <?php else: ?>
      No immediate counseling required.
      <?php endif; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Counselor Remarks -->
<table class="form-tbl" style="margin-top:10px">
  <tr>
    <td style="padding:6px">
      <strong>Counselor's Remarks:</strong>
      <div style="border-bottom:1px solid #000;min-height:18px;margin-top:6px"></div>
      <div style="border-bottom:1px solid #000;min-height:18px;margin-top:8px"></div>
    </td>
  </tr>
</table>

<!-- Signatures -->
<div style="display:flex;justify-content:space-between;margin-top:20px;gap:20px">
  <div style="flex:1;text-align:center">
    <div style="min-height:32px"></div>
    <div style="border-top:1px solid #000;padding-top:2px">
      <div style="font-weight:700"><?= strtoupper($v('first_name').' '.strtoupper(substr($v('middle_name'),0,1)).'. '.$v('last_name')) ?></div>
      <div style="font-size:8pt">Student's Signature over Printed Name</div>
    </div>
  </div>
  <div style="flex:1;text-align:center">
    <div style="min-height:32px"></div>
    <div style="border-top:1px solid #000;padding-top:2px">
      <div style="font-weight:700">JO AUGUSTINE G. CORPUZ, RGC</div>
      <div style="font-size:8pt">Guidance Counselor's Name and Signature</div>
    </div>
  </div>
</div>

<?php require_once __DIR__.'/footer.php'; ?>
