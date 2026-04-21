<?php
require_once __DIR__.'/../../config/database.php';
require_once __DIR__.'/../../includes/session.php';
require_once __DIR__.'/../../includes/functions.php';
requireAuth();

$id = (int)($_GET['id'] ?? 0);
if (!$id) die('Invalid student ID.');

try {
    $db  = getDB();
    $st  = $db->prepare("SELECT * FROM students WHERE id=?");
    $st->execute([$id]);
    $s   = $st->fetch();
    if (!$s) die('Student not found.');
} catch (PDOException $e) { die('Database error.'); }

$v   = fn($k) => htmlspecialchars($s[$k] ?? '');
$chk = fn($k) => !empty($s[$k]);
$printTitle = 'Individual Inventory Form — '.$s['last_name'].', '.$s['first_name'];
require_once __DIR__.'/header.php';
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
    <td colspan="1"><span class="field-label">Ethnicity</span><div class="field-val"><?= $v('ethnicity') ?></div></td>
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

<?php require_once __DIR__.'/footer.php'; ?>
