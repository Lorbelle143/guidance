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
} catch (PDOException $e) { die('Database error.'); }

$v = fn($k) => htmlspecialchars($s[$k] ?? '');
$printTitle = 'Informed Consent Form — '.$s['last_name'].', '.$s['first_name'];
require_once __DIR__.'/header.php';
?>

<!-- NBSC Header -->
<div style="display:flex;align-items:center;justify-content:space-between;border-bottom:2.5px solid #000;padding-bottom:6px;margin-bottom:8px">
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

<!-- Signatures -->
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

<?php require_once __DIR__.'/footer.php'; ?>
