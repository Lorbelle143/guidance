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
    // Try to get WHODAS assessment data
    $wa = $db->prepare("SELECT * FROM whodas_assessments WHERE student_id=? ORDER BY created_at DESC LIMIT 1");
    $wa->execute([$id]);
    $whodas = $wa->fetch() ?: [];
} catch (PDOException $e) { $whodas = []; }

$v = fn($k) => htmlspecialchars($s[$k] ?? '');
$age = $s['birth_date'] ? (int)date_diff(date_create($s['birth_date']), date_create('today'))->y : '';
$printTitle = 'WHODAS 2.0 — '.$s['last_name'].', '.$s['first_name'];
require_once __DIR__.'/header.php';

// WHODAS items grouped by domain
$domains = [
  'D1 — Understanding and communicating' => [
    'D1.1 Concentrating on doing something for ten minutes?',
    'D1.2 Remembering to do important things?',
    'D1.3 Analysing and finding solutions to problems in day-to-day life?',
    'D1.4 Learning a new task, e.g. learning how to get to a new place?',
    'D1.5 Generally understanding what people say?',
    'D1.6 Starting and maintaining a conversation?',
  ],
  'D2 — Getting around' => [
    'D2.1 Standing for long periods such as 30 minutes?',
    'D2.2 Standing up from sitting down?',
    'D2.3 Moving around inside your home?',
    'D2.4 Getting out of your home?',
    'D2.5 Walking a long distance such as a kilometre?',
  ],
  'D3 — Self-care' => [
    'D3.1 Washing your whole body?',
    'D3.2 Getting dressed?',
    'D3.3 Eating?',
    'D3.4 Staying by yourself for a few days?',
  ],
  'D4 — Getting along with people' => [
    'D4.1 Dealing with people you do not know?',
    'D4.2 Maintaining a friendship?',
    'D4.3 Getting along with people who are close to you?',
    'D4.4 Making new friends?',
    'D4.5 Sexual activities?',
  ],
  'D5 — Life activities (household)' => [
    'D5.1 Taking care of your household responsibilities?',
    'D5.2 Doing most important household tasks well?',
    'D5.3 Getting all the household work done that you needed to do?',
    'D5.4 Getting your household work done as quickly as needed?',
  ],
  'D5 — Life activities (work/school)' => [
    'D5.5 Your day-to-day work/school?',
    'D5.6 Doing your most important work/school tasks well?',
    'D5.7 Getting all the work done that you need to do?',
    'D5.8 Getting your work done as quickly as needed?',
  ],
  'D6 — Participation in society' => [
    'D6.1 Joining in community activities in the same way as anyone else can?',
    'D6.2 Problems because of barriers or hindrances in the world around you?',
    'D6.3 Living with dignity because of attitudes and actions of others?',
    'D6.4 How much time did you spend on your health condition or its consequences?',
    'D6.5 How much have you been emotionally affected by your health condition?',
    'D6.6 How much has your health been a drain on the financial resources of you or your family?',
    'D6.7 How much of a problem did your family have because of your health problems?',
    'D6.8 Problems doing things by yourself for relaxation or pleasure?',
  ],
];
$cols = ['None (0)','Mild (1)','Moderate (2)','Severe (3)','Extreme/ Cannot (4)'];
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
  <div style="font-size:11pt;font-weight:700">World Health Organization Disability Assessment Schedule 2.0 (WHODAS 2.0)</div>
  <div style="font-size:8.5pt">36-item version, self-administered</div>
</div>

<!-- Student Info Row -->
<table class="form-tbl" style="margin-bottom:6px">
  <tr>
    <td style="width:25%"><span class="field-label">Last Name</span><div class="field-val bold"><?= $v('last_name') ?></div></td>
    <td style="width:25%"><span class="field-label">First Name</span><div class="field-val bold"><?= $v('first_name') ?></div></td>
    <td style="width:8%"><span class="field-label">M.I.</span><div class="field-val"><?= $s['middle_name'] ? strtoupper(substr($s['middle_name'],0,1)).'.' : '' ?></div></td>
    <td style="width:8%"><span class="field-label">Age</span><div class="field-val"><?= $age ?></div></td>
    <td><span class="field-label">Program &amp; Year</span><div class="field-val"><?= $v('program_year') ?></div></td>
  </tr>
  <tr>
    <td><span class="field-label">Sex</span><div class="field-val"><?= $v('gender') ?></div></td>
    <td colspan="4"><span class="field-label">Instructions:</span> <span style="font-size:8.5pt">Think back over the <strong>past 30 days</strong>. Rate how much difficulty you had in each area due to a health condition. Select only one response per item.</span></td>
  </tr>
</table>

<!-- WHODAS Table -->
<table class="form-tbl" style="font-size:8pt">
  <tr style="background:#000;color:#fff">
    <td style="width:52%;padding:3px 5px;font-weight:700">In the past 30 days, how much difficulty did you have in:</td>
    <?php foreach ($cols as $c): ?>
    <td style="text-align:center;padding:3px 2px;font-weight:700;font-size:7.5pt"><?= $c ?></td>
    <?php endforeach; ?>
    <td style="text-align:center;padding:3px 2px;font-weight:700">Score</td>
  </tr>
  <?php $itemIdx = 0; foreach ($domains as $domainName => $items): ?>
  <tr>
    <td colspan="7" style="background:#e8e8e8;font-weight:700;padding:3px 5px;font-size:8.5pt"><?= $domainName ?></td>
  </tr>
  <?php foreach ($items as $item):
    $key = 'whodas_'.($itemIdx+1);
    $val = $whodas[$key] ?? null;
    $itemIdx++;
  ?>
  <tr>
    <td style="padding:2px 5px"><?= $item ?></td>
    <?php for ($c=0; $c<=4; $c++): ?>
    <td style="text-align:center;padding:2px"><?= ($val !== null && (int)$val === $c) ? '✓' : '' ?></td>
    <?php endfor; ?>
    <td style="text-align:center;font-weight:700"><?= $val !== null ? $val : '' ?></td>
  </tr>
  <?php endforeach; endforeach; ?>
</table>

<!-- Summary Questions -->
<table class="form-tbl" style="margin-top:4px;font-size:9pt">
  <tr style="background:#e8e8e8"><td colspan="2" style="font-weight:700;padding:3px 5px">Summary Questions (past 30 days)</td></tr>
  <tr>
    <td>H1. Overall, how many days were these difficulties present?</td>
    <td style="text-align:right;font-weight:700;min-width:80px"><?= htmlspecialchars($whodas['h1_days'] ?? '') ?> days</td>
  </tr>
  <tr>
    <td>H2. For how many days were you totally unable to carry out your usual activities or work?</td>
    <td style="text-align:right;font-weight:700"><?= htmlspecialchars($whodas['h2_days'] ?? '') ?> days</td>
  </tr>
  <tr>
    <td>H3. Not counting the days totally unable, for how many days did you cut back or reduce your usual activities or work because of any health condition?</td>
    <td style="text-align:right;font-weight:700"><?= htmlspecialchars($whodas['h3_days'] ?? '') ?> days</td>
  </tr>
  <tr>
    <td colspan="2" style="text-align:center;font-style:italic;font-size:8.5pt">This completes the questionnaire. Thank you for your participation.</td>
  </tr>
</table>

<?php require_once __DIR__.'/footer.php'; ?>
