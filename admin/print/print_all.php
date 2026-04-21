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

    // WHODAS
    $wa = $db->prepare("SELECT * FROM whodas_assessments WHERE student_id=? ORDER BY created_at DESC LIMIT 1");
    $wa->execute([$id]);
    $whodas = $wa->fetch() ?: [];

    // PID-5
    $pa = $db->prepare("SELECT * FROM pid5_assessments WHERE student_id=? ORDER BY created_at DESC LIMIT 1");
    $pa->execute([$id]);
    $pid5 = $pa->fetch() ?: [];

    // Mental Health (BSRS-5)
    $ma = $db->prepare("SELECT * FROM mental_health_assessments WHERE student_id=? ORDER BY created_at DESC LIMIT 1");
    $ma->execute([$id]);
    $mh = $ma->fetch() ?: [];

} catch (PDOException $e) { die('Database error: '.$e->getMessage()); }

$v   = fn($k) => htmlspecialchars($s[$k] ?? '');
$chk = fn($k) => !empty($s[$k]);
$age = $s['birth_date'] ? (int)date_diff(date_create($s['birth_date']), date_create('today'))->y : '';
$printTitle = 'All Forms — '.$s['last_name'].', '.$s['first_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= $printTitle ?></title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Times New Roman',Times,serif;font-size:10pt;color:#000;background:#fff}
@page{size:A4;margin:12mm 14mm}
@media print{
  .no-print{display:none!important}
  .page-break{page-break-before:always}
  body{margin:0}
}
.print-btn{position:fixed;top:12px;right:12px;z-index:999;display:flex;gap:8px}
.print-btn button{padding:8px 18px;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer}
.btn-p{background:#2563eb;color:#fff}
.btn-c{background:#6b7280;color:#fff}
table.form-tbl{width:100%;border-collapse:collapse;font-size:9pt}
table.form-tbl td,table.form-tbl th{border:1px solid #000;padding:2px 4px;vertical-align:top}
table.form-tbl th{background:#000;color:#fff;font-size:8pt;font-weight:700;text-align:left}
.field-label{font-size:7.5pt;font-weight:700;color:#333;display:block;margin-bottom:1px}
.field-val{font-size:9.5pt;border-bottom:1px solid #555;min-height:14px;padding-bottom:1px}
.field-val.bold{font-weight:700}
.section-hd{background:#000;color:#fff;font-weight:700;font-size:9pt;padding:3px 6px;margin:6px 0 0}
.chk{width:11px;height:11px;border:1px solid #000;display:inline-flex;align-items:center;justify-content:center;font-size:8pt;flex-shrink:0}
.chk.checked::after{content:'✓';font-weight:700}
.form-title{text-align:center;font-size:12pt;font-weight:700;text-decoration:underline;margin:8px 0 6px;text-transform:uppercase;letter-spacing:.5px}
.nbsc-hd{display:flex;align-items:center;justify-content:space-between;border-bottom:2.5px solid #000;padding-bottom:6px;margin-bottom:6px}
.page-footer{margin-top:10px;border-top:1px solid #ccc;padding-top:4px;display:flex;align-items:center;justify-content:space-between}
.section-wrap{margin-bottom:0}
</style>
</head>
<body>

<div class="print-btn no-print">
  <button class="btn-p" onclick="window.print()">🖨 Print All</button>
  <button class="btn-c" onclick="window.close()">✕ Close</button>
</div>


<!-- ═══════════════════════════════════════════════════ -->
<!-- PAGE 1: INDIVIDUAL INVENTORY FORM                  -->
<!-- ═══════════════════════════════════════════════════ -->
<div class="section-wrap">

<!-- NBSC Header -->
<div class="nbsc-hd">
  <img src="https://nbscgco.vercel.app/logo.png" style="width:62px;height:62px;object-fit:contain" onerror="this.style.display='none'">
  <div style="text-align:center;flex:1">
    <div style="font-size:8pt">Republic of the Philippines</div>
    <div style="font-size:15pt;font-weight:700;line-height:1.1">NORTHERN BUKIDNON STATE COLLEGE</div>
    <div style="font-size:8.5pt">Manolo Fortich, 8703 Bukidnon</div>
    <div style="font-size:7.5pt;font-style:italic;color:#b8860b">Creando Futuro, Transformationis Vitae, Ductae a Deo</div>
  </div>
  <div style="border:1px solid #000;font-size:7pt;min-width:130px">
    <div style="background:#000;color:#fff;padding:1px 4px;font-weight:700;text-align:center">Document Code Number</div>
    <div style="font-weight:700;font-size:9pt;text-align:center;padding:2px">FM-NBSC-GCO-002</div>
    <table style="width:100%;border-collapse:collapse;font-size:7pt">
      <tr>
        <td style="border:1px solid #000;padding:1px 3px;text-align:center">Issue Status</td>
        <td style="border:1px solid #000;padding:1px 3px;text-align:center">Rev No.</td>
        <td style="border:1px solid #000;padding:1px 3px;text-align:center">Effective Date</td>
        <td style="border:1px solid #000;padding:1px 3px;text-align:center">Page No.</td>
      </tr>
      <tr>
        <td style="border:1px solid #000;padding:1px 3px;text-align:center">01</td>
        <td style="border:1px solid #000;padding:1px 3px;text-align:center">00</td>
        <td style="border:1px solid #000;padding:1px 3px;text-align:center">12.15.2023</td>
        <td style="border:1px solid #000;padding:1px 3px;text-align:center">1 of 1</td>
      </tr>
    </table>
  </div>
</div>

<div class="form-title">Individual Inventory Form</div>

<!-- Row 1: Name, Program, Birth Date, Photo -->
<table class="form-tbl" style="margin-bottom:0">
  <tr>
    <td style="width:22%"><span class="field-label">Last Name</span><div class="field-val bold"><?= $v('last_name') ?></div></td>
    <td style="width:22%"><span class="field-label">First Name</span><div class="field-val bold"><?= $v('first_name') ?></div></td>
    <td style="width:6%"><span class="field-label">M.I.</span><div class="field-val bold"><?= $s['middle_name'] ? strtoupper(substr($s['middle_name'],0,1)).'.' : '' ?></div></td>
    <td style="width:28%"><span class="field-label">Program &amp; Year</span><div class="field-val"><?= $v('program_year') ?></div></td>
    <td style="width:14%"><span class="field-label">Birth Date</span><div class="field-val"><?= $s['birth_date'] ? date('Y-m-d',strtotime($s['birth_date'])) : '' ?></div></td>
    <td rowspan="2" style="width:8%;text-align:center;vertical-align:middle">
      <?php if (!empty($s['photo']) && file_exists(UPLOAD_PATH.$s['photo'])): ?>
      <img src="<?= BASE_PATH.'/uploads/'.htmlspecialchars($s['photo']) ?>" style="width:70px;height:80px;object-fit:cover;border:1px solid #000">
      <?php else: ?>
      <div style="width:70px;height:80px;border:1px solid #000;display:flex;align-items:center;justify-content:center;font-size:7pt;color:#999">Photo</div>
      <?php endif; ?>
    </td>
  </tr>
  <tr>
    <td><span class="field-label">ID No.</span><div class="field-val bold"><?= $v('student_id') ?></div></td>
    <td><span class="field-label">Gender</span><div class="field-val"><?= $v('gender') ?></div></td>
    <td><span class="field-label">Ethnicity</span><div class="field-val"><?= $v('ethnicity') ?></div></td>
    <td><span class="field-label">Religion</span><div class="field-val"><?= $v('religion') ?></div></td>
    <td><span class="field-label">Civil Status</span><div class="field-val"><?= $v('civil_status') ?></div></td>
  </tr>
</table>

<!-- Contact Information -->
<div class="section-hd">Contact Information</div>
<table class="form-tbl">
  <tr>
    <td style="width:30%"><span class="field-label">Mobile Phone Number/s</span><div class="field-val"><?= $v('mobile_number') ?></div></td>
    <td style="width:35%"><span class="field-label">Personal e-mail address</span><div class="field-val"><?= $v('personal_email') ?: $v('email') ?></div></td>
    <td><span class="field-label">Institutional e-mail address</span><div class="field-val"><?= $v('institutional_email') ?></div></td>
  </tr>
  <tr>
    <td colspan="3"><span class="field-label">Permanent Address:</span><div class="field-val"><?= $v('permanent_address') ?></div></td>
  </tr>
  <tr>
    <td colspan="3">
      <span class="field-label">This is my current address:
        <span class="chk <?= empty($s['current_address']) || $s['current_address']===$s['permanent_address']?'checked':'' ?>"></span> Yes &nbsp;
        <span class="chk <?= !empty($s['current_address']) && $s['current_address']!==$s['permanent_address']?'checked':'' ?>"></span> No &nbsp;
        Present Address: <span style="border-bottom:1px solid #000;display:inline-block;min-width:200px"><?= $v('current_address') ?></span>
      </span>
    </td>
  </tr>
</table>

<!-- Family Background -->
<div class="section-hd">Family Background</div>
<table class="form-tbl">
  <tr>
    <th style="width:22%">Profile</th>
    <th style="width:39%">Mother</th>
    <th style="width:39%">Father</th>
  </tr>
  <?php
  $rows = [
    ['Name',                  'mother_name',       'father_name'],
    ['Age and Birthday',      'mother_birthday',   'father_birthday'],
    ['Ethnicity',             'mother_ethnicity',  'father_ethnicity'],
    ['Religion',              'mother_religion',   'father_religion'],
    ['Educational Attainment','mother_education',  'father_education'],
    ['Occupation',            'mother_occupation', 'father_occupation'],
    ['Company',               'mother_company',    'father_company'],
    ['Monthly Income',        'mother_income',     'father_income'],
    ['Contact Number',        'mother_contact',    'father_contact'],
  ];
  foreach ($rows as [$lbl,$mk,$fk]):
  ?>
  <tr>
    <td style="font-weight:700;font-size:8.5pt"><?= $lbl ?></td>
    <td><?= $v($mk) ?></td>
    <td><?= $v($fk) ?></td>
  </tr>
  <?php endforeach; ?>
  <tr>
    <td colspan="3">
      <strong>Status of Parent/s:</strong>
      <?php foreach(['Married','Living Together','Divorced/Annulled','Single Parent','Separated','Widowed/Widower'] as $ps): ?>
      <span class="chk <?= ($s['parent_status']??'')===$ps?'checked':'' ?>"></span> <?= $ps ?> &nbsp;
      <?php endforeach; ?>
    </td>
  </tr>
  <tr>
    <td><strong>Number of Siblings:</strong> <?= $v('num_siblings') ?></td>
    <td colspan="2"><strong>Name of Guardian/s:</strong> <?= $v('guardian_name') ?> &nbsp;&nbsp; <strong>Contact:</strong> <?= $v('guardian_contact') ?></td>
  </tr>
</table>

<!-- Interests -->
<div class="section-hd">Interest &amp; Recreational Activities</div>
<table class="form-tbl">
  <tr>
    <td style="width:50%"><strong>Hobbies:</strong> <?= $v('hobbies') ?></td>
    <td style="width:25%"><strong>Talents:</strong> <?= $v('talents') ?></td>
    <td style="width:25%"><strong>Sports:</strong> <?= $v('sports') ?></td>
  </tr>
  <tr>
    <td><strong>Socio-civic:</strong> <?= $v('socio_civic') ?></td>
    <td colspan="2"><strong>School Org.:</strong> <?= $v('school_org') ?></td>
  </tr>
</table>

<!-- Health -->
<div class="section-hd">Health</div>
<table class="form-tbl">
  <tr>
    <td>
      • Have you ever been hospitalized? <span class="chk <?= $chk('hospitalized')?'checked':'' ?>"></span> Yes (state when/reason: <?= $v('hospitalized_details') ?>) <span class="chk <?= !$chk('hospitalized')?'checked':'' ?>"></span> No<br>
      • Have you ever had an operation? <span class="chk <?= $chk('had_operation')?'checked':'' ?>"></span> Yes (state when/reason: <?= $v('operation_details') ?>) <span class="chk <?= !$chk('had_operation')?'checked':'' ?>"></span> No<br>
      • Do you currently suffer from any illness/condition? <span class="chk <?= $chk('has_illness')?'checked':'' ?>"></span> Yes (state illness: <?= $v('illness_details') ?>) <span class="chk <?= !$chk('has_illness')?'checked':'' ?>"></span> No
    </td>
  </tr>
  <tr>
    <td>
      <strong>Common illness in the family:</strong> <?= $v('common_illness') ?> &nbsp;&nbsp;
      <strong>When did you last see a doctor?</strong> <?= $v('last_doctor_visit') ?> &nbsp;&nbsp;
      <strong>Reason for the visit:</strong> <?= $v('doctor_visit_reason') ?>
    </td>
  </tr>
</table>

<!-- Life Circumstances -->
<div class="section-hd">Life Circumstances</div>
<table class="form-tbl">
  <tr>
    <td>
      <div style="font-size:8.5pt;margin-bottom:3px">Check any of the <strong>PROBLEMS</strong> below that currently concerns you:</div>
      <?php
      $concerns = [
        'concern_fear'=>'Fear','concern_communication'=>'Communication','concern_shyness'=>'Shyness',
        'concern_loneliness'=>'Loneliness','concern_stress'=>'Stress','concern_anger'=>'Anger',
        'concern_self_confidence'=>'Self-confidence','concern_academic'=>'Academic Performance',
        'concern_career'=>'Career','concern_financial'=>'Financial',
      ];
      foreach ($concerns as $k => $lbl):
      ?>
      <span class="chk <?= $chk($k)?'checked':'' ?>"></span> <?= $lbl ?> &nbsp;
      <?php endforeach; ?>
      <br><strong>Others:</strong> <?= $v('concern_others') ?>
    </td>
  </tr>
  <tr>
    <td>
      <strong>Counselor's Remarks:</strong>
      <div style="border-bottom:1px solid #000;min-height:18px;margin-top:4px"></div>
    </td>
  </tr>
</table>

<!-- Signatures -->
<div style="display:flex;justify-content:space-between;margin-top:20px;gap:20px">
  <div style="flex:1;text-align:center">
    <?php if (!empty($s['photo']) && file_exists(UPLOAD_PATH.$s['photo'])): ?>
    <img src="<?= BASE_PATH.'/uploads/'.htmlspecialchars($s['photo']) ?>" style="width:60px;height:30px;object-fit:cover;opacity:.4;margin-bottom:2px">
    <?php endif; ?>
    <div style="border-top:1px solid #000;padding-top:2px">
      <div style="font-weight:700"><?= strtoupper($v('first_name').' '.strtoupper(substr($v('middle_name'),0,1)).'. '.$v('last_name')) ?></div>
      <div style="font-size:8pt">Student's signature over printed name</div>
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

<!-- Footer -->
<div class="page-footer">
  <img src="https://nbscgco.vercel.app/logo.png" alt="NBSC" onerror="this.style.display='none'" style="height:28px">
  <div style="font-size:7.5pt;color:#333;display:flex;align-items:center;gap:8px">
    <span>📘 NorthernBukidnonStateCollegeOfficial</span>
    <span>|</span>
    <span>🌐 www.nbsc.edu.ph</span>
  </div>
  <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/99/Bagong_Pilipinas_logo.svg/120px-Bagong_Pilipinas_logo.svg.png" alt="Bagong Pilipinas" style="height:28px" onerror="this.style.display='none'">
</div>

</div><!-- end page 1 -->


<!-- ═══════════════════════════════════════════════════ -->
<!-- PAGE 2: WHODAS 2.0                                 -->
<!-- ═══════════════════════════════════════════════════ -->
<div class="section-wrap page-break">

<!-- NBSC Header -->
<div class="nbsc-hd">
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

<?php
$whodas_domains = [
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
$wcols = ['None (0)','Mild (1)','Moderate (2)','Severe (3)','Extreme/ Cannot (4)'];
?>

<table class="form-tbl" style="font-size:8pt">
  <tr style="background:#000;color:#fff">
    <td style="width:52%;padding:3px 5px;font-weight:700">In the past 30 days, how much difficulty did you have in:</td>
    <?php foreach ($wcols as $c): ?>
    <td style="text-align:center;padding:3px 2px;font-weight:700;font-size:7.5pt"><?= $c ?></td>
    <?php endforeach; ?>
    <td style="text-align:center;padding:3px 2px;font-weight:700">Score</td>
  </tr>
  <?php $wIdx = 0; foreach ($whodas_domains as $dName => $ditems): ?>
  <tr>
    <td colspan="7" style="background:#e8e8e8;font-weight:700;padding:3px 5px;font-size:8.5pt"><?= $dName ?></td>
  </tr>
  <?php foreach ($ditems as $ditem):
    $wkey = 'whodas_'.($wIdx+1);
    $wval = $whodas[$wkey] ?? null;
    $wIdx++;
  ?>
  <tr>
    <td style="padding:2px 5px"><?= $ditem ?></td>
    <?php for ($c=0; $c<=4; $c++): ?>
    <td style="text-align:center;padding:2px"><?= ($wval !== null && (int)$wval === $c) ? '✓' : '' ?></td>
    <?php endfor; ?>
    <td style="text-align:center;font-weight:700"><?= $wval !== null ? $wval : '' ?></td>
  </tr>
  <?php endforeach; endforeach; ?>
</table>

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
    <td>H3. Not counting the days totally unable, for how many days did you cut back or reduce your usual activities?</td>
    <td style="text-align:right;font-weight:700"><?= htmlspecialchars($whodas['h3_days'] ?? '') ?> days</td>
  </tr>
  <tr>
    <td colspan="2" style="text-align:center;font-style:italic;font-size:8.5pt">This completes the questionnaire. Thank you for your participation.</td>
  </tr>
</table>

<div class="page-footer">
  <img src="https://nbscgco.vercel.app/logo.png" alt="NBSC" onerror="this.style.display='none'" style="height:28px">
  <div style="font-size:7.5pt;color:#333;display:flex;align-items:center;gap:8px">
    <span>📘 NorthernBukidnonStateCollegeOfficial</span><span>|</span><span>🌐 www.nbsc.edu.ph</span>
  </div>
  <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/99/Bagong_Pilipinas_logo.svg/120px-Bagong_Pilipinas_logo.svg.png" alt="Bagong Pilipinas" style="height:28px" onerror="this.style.display='none'">
</div>

</div><!-- end page 2 -->


<!-- ═══════════════════════════════════════════════════ -->
<!-- PAGE 3: PID-5 BF                                   -->
<!-- ═══════════════════════════════════════════════════ -->
<div class="section-wrap page-break">

<div class="nbsc-hd">
  <img src="https://nbscgco.vercel.app/logo.png" style="width:62px;height:62px;object-fit:contain" onerror="this.style.display='none'">
  <div style="text-align:center;flex:1">
    <div style="font-size:8pt">Republic of the Philippines</div>
    <div style="font-size:15pt;font-weight:700;line-height:1.1">NORTHERN BUKIDNON STATE COLLEGE</div>
    <div style="font-size:8.5pt">Manolo Fortich, 8703 Bukidnon</div>
    <div style="font-size:7.5pt;font-style:italic;color:#b8860b">Creando Futuro, Transformationis Vitae, Ductae a Deo</div>
  </div>
</div>

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

<?php
$pid5_statements = [
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
$pcols = ['Very False or Often False (0)','Sometimes or Somewhat False (1)','Sometimes or Somewhat True (2)','Very True or Often True (3)'];

$ptotal = 0; $panswered = 0;
for ($i=1; $i<=25; $i++) {
    $pval = $pid5['item_'.$i] ?? null;
    if ($pval !== null) { $ptotal += (int)$pval; $panswered++; }
}
$pavg = $panswered > 0 ? round($ptotal/$panswered, 2) : 'N/A';
?>

<table class="form-tbl" style="font-size:8pt">
  <tr style="background:#000;color:#fff">
    <td style="width:5%;text-align:center;padding:3px">#</td>
    <td style="width:42%;padding:3px 5px">Statement</td>
    <?php foreach ($pcols as $c): ?>
    <td style="text-align:center;padding:3px 2px;font-size:7pt"><?= $c ?></td>
    <?php endforeach; ?>
    <td style="text-align:center;padding:3px 2px">Score</td>
  </tr>
  <?php foreach ($pid5_statements as $i => $stmt):
    $num = $i + 1;
    $pval = $pid5['item_'.$num] ?? null;
  ?>
  <tr style="<?= $i%2===0?'background:#f9f9f9':'' ?>">
    <td style="text-align:center;font-weight:700"><?= $num ?></td>
    <td style="padding:2px 5px"><?= htmlspecialchars($stmt) ?></td>
    <?php for ($c=0; $c<=3; $c++): ?>
    <td style="text-align:center"><?= ($pval !== null && (int)$pval === $c) ? '✓' : '' ?></td>
    <?php endfor; ?>
    <td style="text-align:center;font-weight:700"><?= $pval !== null ? $pval : '' ?></td>
  </tr>
  <?php endforeach; ?>
  <tr>
    <td colspan="6" style="text-align:right;font-weight:700;padding:3px 5px">Total / Partial Raw Score:</td>
    <td style="text-align:center;font-weight:700"><?= $panswered > 0 ? $ptotal : '' ?></td>
  </tr>
  <tr>
    <td colspan="6" style="text-align:right;font-size:8pt;padding:2px 5px">Prorated Total Score (if 1–6 items left unanswered):</td>
    <td style="text-align:center;font-size:8pt"><?= $panswered < 25 && $panswered > 0 ? round($ptotal * (25/$panswered)) : 'N/A' ?></td>
  </tr>
  <tr>
    <td colspan="6" style="text-align:right;font-size:8pt;padding:2px 5px">Average Total Score:</td>
    <td style="text-align:center;font-size:8pt"><?= $pavg ?></td>
  </tr>
</table>

<div class="page-footer">
  <img src="https://nbscgco.vercel.app/logo.png" alt="NBSC" onerror="this.style.display='none'" style="height:28px">
  <div style="font-size:7.5pt;color:#333;display:flex;align-items:center;gap:8px">
    <span>📘 NorthernBukidnonStateCollegeOfficial</span><span>|</span><span>🌐 www.nbsc.edu.ph</span>
  </div>
  <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/99/Bagong_Pilipinas_logo.svg/120px-Bagong_Pilipinas_logo.svg.png" alt="Bagong Pilipinas" style="height:28px" onerror="this.style.display='none'">
</div>

</div><!-- end page 3 -->


<!-- ═══════════════════════════════════════════════════ -->
<!-- PAGE 4: BSRS-5 MENTAL HEALTH                       -->
<!-- ═══════════════════════════════════════════════════ -->
<div class="section-wrap page-break">

<div class="nbsc-hd">
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

<?php
$bsrs_items = [
  'Feeling tense or wound up',
  'Feeling unhappy',
  'Feeling irritable or bad-tempered',
  'Feeling anxious or worried',
  'Feeling that life is not worth living',
];
$bsrs_cols = ['Not at all (0)', 'A little (1)', 'Quite a bit (2)', 'Very much (3)', 'Extremely (4)'];
$mh_score = (int)($mh['total_score'] ?? 0);
$mh_riskLabel = $mh_score <= 5 ? 'Doing Well' : ($mh_score <= 9 ? 'Need Support' : 'Immediate Support');
$mh_riskColor = $mh_score <= 5 ? '#059669' : ($mh_score <= 9 ? '#d97706' : '#dc2626');
?>

<table class="form-tbl" style="font-size:8.5pt">
  <tr style="background:#000;color:#fff">
    <td style="width:5%;text-align:center;padding:3px">#</td>
    <td style="width:40%;padding:3px 5px;font-weight:700">Item</td>
    <?php foreach ($bsrs_cols as $c): ?>
    <td style="text-align:center;padding:3px 2px;font-size:7.5pt;font-weight:700"><?= $c ?></td>
    <?php endforeach; ?>
    <td style="text-align:center;padding:3px 2px;font-weight:700">Score</td>
  </tr>
  <?php foreach ($bsrs_items as $i => $item):
    $num = $i + 1;
    $bval = $mh['item_'.$num] ?? null;
  ?>
  <tr style="<?= $i%2===0?'background:#f9f9f9':'' ?>">
    <td style="text-align:center;font-weight:700"><?= $num ?></td>
    <td style="padding:4px 5px"><?= htmlspecialchars($item) ?></td>
    <?php for ($c=0; $c<=4; $c++): ?>
    <td style="text-align:center;padding:4px 2px"><?= ($bval !== null && (int)$bval === $c) ? '✓' : '' ?></td>
    <?php endfor; ?>
    <td style="text-align:center;font-weight:700"><?= $bval !== null ? $bval : '' ?></td>
  </tr>
  <?php endforeach; ?>
  <tr style="background:#e8e8e8">
    <td colspan="6" style="text-align:right;font-weight:700;padding:4px 5px">Total Score:</td>
    <td style="text-align:center;font-weight:700;font-size:11pt"><?= !empty($mh) ? $mh_score : '' ?></td>
  </tr>
</table>

<table class="form-tbl" style="margin-top:8px;font-size:8.5pt">
  <tr style="background:#e8e8e8"><td colspan="3" style="font-weight:700;padding:3px 5px">Scoring Guide</td></tr>
  <tr>
    <td style="width:33%;padding:4px 6px"><strong>0–5:</strong> Doing Well — No significant distress</td>
    <td style="width:33%;padding:4px 6px"><strong>6–9:</strong> Need Support — Mild to moderate distress</td>
    <td style="width:34%;padding:4px 6px"><strong>10–20:</strong> Immediate Support — Significant distress</td>
  </tr>
</table>

<?php if (!empty($mh)): ?>
<div style="margin-top:10px;border:2px solid <?= $mh_riskColor ?>;border-radius:4px;padding:8px 12px;display:flex;align-items:center;gap:12px">
  <div style="font-size:22pt;font-weight:700;color:<?= $mh_riskColor ?>"><?= $mh_score ?></div>
  <div>
    <div style="font-weight:700;font-size:10pt;color:<?= $mh_riskColor ?>"><?= $mh_riskLabel ?></div>
    <div style="font-size:8.5pt;color:#555">
      <?= !empty($mh['requires_counseling']) ? '⚠ Counseling is recommended for this student.' : 'No immediate counseling required.' ?>
    </div>
  </div>
</div>
<?php endif; ?>

<table class="form-tbl" style="margin-top:10px">
  <tr>
    <td style="padding:6px">
      <strong>Counselor's Remarks:</strong>
      <div style="border-bottom:1px solid #000;min-height:18px;margin-top:6px"></div>
      <div style="border-bottom:1px solid #000;min-height:18px;margin-top:8px"></div>
    </td>
  </tr>
</table>

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

<div class="page-footer">
  <img src="https://nbscgco.vercel.app/logo.png" alt="NBSC" onerror="this.style.display='none'" style="height:28px">
  <div style="font-size:7.5pt;color:#333;display:flex;align-items:center;gap:8px">
    <span>📘 NorthernBukidnonStateCollegeOfficial</span><span>|</span><span>🌐 www.nbsc.edu.ph</span>
  </div>
  <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/99/Bagong_Pilipinas_logo.svg/120px-Bagong_Pilipinas_logo.svg.png" alt="Bagong Pilipinas" style="height:28px" onerror="this.style.display='none'">
</div>

</div><!-- end page 4 -->


<!-- ═══════════════════════════════════════════════════ -->
<!-- PAGE 5: INFORMED CONSENT FORM                      -->
<!-- ═══════════════════════════════════════════════════ -->
<div class="section-wrap page-break">

<div class="nbsc-hd">
  <img src="https://nbscgco.vercel.app/logo.png" style="width:62px;height:62px;object-fit:contain" onerror="this.style.display='none'">
  <div style="text-align:center;flex:1">
    <div style="font-size:8pt">Republic of the Philippines</div>
    <div style="font-size:15pt;font-weight:700;line-height:1.1">NORTHERN BUKIDNON STATE COLLEGE</div>
    <div style="font-size:8.5pt">Manolo Fortich, 8703 Bukidnon</div>
    <div style="font-size:7.5pt;font-style:italic;color:#b8860b">Creando Futuro, Transformationis Vitae, Ductae a Deo</div>
  </div>
  <div style="border:1px solid #000;font-size:7pt;min-width:130px">
    <div style="background:#000;color:#fff;padding:1px 4px;font-weight:700;text-align:center">Document Code Number</div>
    <div style="font-weight:700;font-size:9pt;text-align:center;padding:2px">FM-NBSC-GCO-004</div>
    <table style="width:100%;border-collapse:collapse;font-size:7pt">
      <tr>
        <td style="border:1px solid #000;padding:1px 3px;text-align:center">Issue Status</td>
        <td style="border:1px solid #000;padding:1px 3px;text-align:center">Rev No.</td>
        <td style="border:1px solid #000;padding:1px 3px;text-align:center">Effective Date</td>
        <td style="border:1px solid #000;padding:1px 3px;text-align:center">Page No.</td>
      </tr>
      <tr>
        <td style="border:1px solid #000;padding:1px 3px;text-align:center">01</td>
        <td style="border:1px solid #000;padding:1px 3px;text-align:center">00</td>
        <td style="border:1px solid #000;padding:1px 3px;text-align:center">12.15.2023</td>
        <td style="border:1px solid #000;padding:1px 3px;text-align:center">1 of 1</td>
      </tr>
    </table>
  </div>
</div>

<div class="form-title">Informed Consent Form</div>

<div style="font-size:9.5pt;line-height:1.6;text-align:justify">
  <p style="font-weight:700;margin-bottom:4px">GUIDANCE AND COUNSELING</p>
  <p style="margin-bottom:8px">Guidance and Counseling is a systematic process aimed at fostering a deeper understanding of yourself, tackling your concerns, and cultivating effective strategies pertaining to your academics, behavior, personal development, and interpersonal relationships. This intricate process entails a collaborative relationship between you and a counseling professional which is driven by a committed responsibility to achieving your goals.</p>
  <p style="margin-bottom:8px">Central to this process is the disclosure of your personal information to the guidance counselor, wherein moments of anxiety or perplexity may arise. While the outcome of counseling often leans towards positive results, the degree of contentment remains varying among individuals. The outcome of counseling objectives largely relies on the active involvement of the student seeking guidance. Throughout this journey, the counselor remains a committed source of support. The termination of counseling procedures occurs upon goal attainment, referral to specialized professionals, or the client's expressed intent to conclude the sessions.</p>
  <p style="margin-bottom:8px">Absolute confidentiality characterizes all dealings within the procedures of Guidance and Counseling Services. This confidentiality extends to the scheduling of appointments, session content, counseling progress, standardized test results, and individual records, with no integration into academic, disciplinary, administrative, or career placement documentation. Individuals reserve the right to request, in writing, the release of specific counseling information to designated individuals.</p>
  <p style="font-weight:700;margin-bottom:4px">EXCEPTIONS TO CONFIDENTIALITY</p>
  <p style="margin-bottom:6px">As counseling relies on a foundation of trust between the counselor and the client, the counselor is bound to maintain the confidentiality of shared information, with exceptions based on ethical obligations that may necessitate disclosure.</p>
  <ul style="margin-left:18px;margin-bottom:8px">
    <li style="margin-bottom:4px">The guidance and counseling team operates collaboratively, allowing your counselor to seek input from other counseling professionals and related experts for the purpose of delivering optimal care. These consultations strictly serve professional and educational objectives.</li>
    <li style="margin-bottom:4px">In instances where there is clear and immediate risk of harm or abuse to oneself or others, the guidance counselor is legally mandated to report such information to the relevant authorities responsible for ensuring safety.</li>
    <li style="margin-bottom:4px">A court-issued directive, authorized by a judge, could compel the Guidance and Counseling Services staff to divulge information contained within your records.</li>
  </ul>
  <p style="margin-bottom:16px">Having duly reviewed and comprehended the information pertaining to the nature and advantages of guidance and counseling, as well as the parameters of confidentiality, I hereby give my consent by signing this document.</p>
</div>

<div style="display:flex;justify-content:space-between;gap:30px;margin-top:10px">
  <div style="flex:1;text-align:center">
    <?php if (!empty($s['photo']) && file_exists(UPLOAD_PATH.$s['photo'])): ?>
    <img src="<?= BASE_PATH.'/uploads/'.htmlspecialchars($s['photo']) ?>" style="width:60px;height:28px;object-fit:cover;opacity:.35;margin-bottom:2px">
    <?php endif; ?>
    <div style="border-top:1px solid #000;padding-top:2px">
      <div style="font-weight:700;font-size:9.5pt"><?= strtoupper($v('first_name').' '.strtoupper(substr($v('middle_name'),0,1)).'. '.$v('last_name')) ?></div>
      <div style="font-size:8pt">Name and Signature of Student</div>
    </div>
  </div>
  <div style="flex:1;text-align:center">
    <div style="min-height:30px"></div>
    <div style="border-top:1px solid #000;padding-top:2px">
      <div style="font-weight:700;font-size:9.5pt"><?= strtoupper($v('father_name') ?: $v('mother_name') ?: 'PARENT/GUARDIAN') ?></div>
      <div style="font-size:8pt">Name and Signature of Parents/Guardians</div>
    </div>
    <div style="margin-top:10px;text-align:right;font-size:9pt">
      Date: <span style="border-bottom:1px solid #000;display:inline-block;min-width:100px"><?= date('Y-m-d') ?></span>
    </div>
  </div>
</div>

<div style="text-align:center;margin-top:20px">
  <div style="min-height:30px"></div>
  <div style="border-top:1px solid #000;padding-top:2px;display:inline-block;min-width:260px">
    <div style="font-weight:700">JO AUGUSTINE G. CORPUZ, RGC</div>
    <div style="font-size:8pt">Name and Signature of Guidance Officer</div>
  </div>
</div>

<div class="page-footer">
  <img src="https://nbscgco.vercel.app/logo.png" alt="NBSC" onerror="this.style.display='none'" style="height:28px">
  <div style="font-size:7.5pt;color:#333;display:flex;align-items:center;gap:8px">
    <span>📘 NorthernBukidnonStateCollegeOfficial</span><span>|</span><span>🌐 www.nbsc.edu.ph</span>
  </div>
  <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/99/Bagong_Pilipinas_logo.svg/120px-Bagong_Pilipinas_logo.svg.png" alt="Bagong Pilipinas" style="height:28px" onerror="this.style.display='none'">
</div>

</div><!-- end page 5 -->

</body>
</html>
