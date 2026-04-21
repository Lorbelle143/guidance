br<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireStudent();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('../student/inventory_form.php');
if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    setFlash('error', 'Invalid request.');
    redirect('../student/inventory_form.php');
}

$fields = [
    'last_name', 'first_name', 'middle_name', 'program_year', 'gender',
    'ethnicity', 'religion', 'civil_status', 'mobile_number', 'personal_email',
    'institutional_email', 'permanent_address', 'current_address',
    'mother_name', 'mother_birthday', 'mother_ethnicity', 'mother_religion',
    'mother_education', 'mother_occupation', 'mother_company', 'mother_income', 'mother_contact',
    'father_name', 'father_birthday', 'father_ethnicity', 'father_religion',
    'father_education', 'father_occupation', 'father_company', 'father_income', 'father_contact',
    'parent_status', 'num_siblings', 'guardian_name', 'guardian_address', 'guardian_contact',
    'hobbies', 'talents', 'sports', 'socio_civic', 'school_org',
    'hospitalized', 'hospitalized_details', 'had_operation', 'operation_details',
    'has_illness', 'illness_details', 'common_illness', 'last_doctor_visit', 'doctor_visit_reason',
    'concern_fear', 'concern_communication', 'concern_shyness', 'concern_loneliness',
    'concern_stress', 'concern_anger', 'concern_self_confidence', 'concern_academic',
    'concern_career', 'concern_financial', 'concern_others',
];

$data = [];
foreach ($fields as $field) {
    $data[$field] = sanitize($_POST[$field] ?? '');
}

// Checkboxes default to 0
$checkboxes = ['hospitalized','had_operation','has_illness','concern_fear','concern_communication',
    'concern_shyness','concern_loneliness','concern_stress','concern_anger',
    'concern_self_confidence','concern_academic','concern_career','concern_financial'];
foreach ($checkboxes as $cb) {
    $data[$cb] = isset($_POST[$cb]) ? 1 : 0;
}

// Handle birth_date
$data['birth_date'] = !empty($_POST['birth_date']) ? sanitize($_POST['birth_date']) : null;
$data['mother_birthday'] = !empty($_POST['mother_birthday']) ? sanitize($_POST['mother_birthday']) : null;
$data['father_birthday'] = !empty($_POST['father_birthday']) ? sanitize($_POST['father_birthday']) : null;
$data['num_siblings'] = (int)($_POST['num_siblings'] ?? 0);

try {
    $db = getDB();

    // Handle photo upload
    $photoFilename = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = uploadFile($_FILES['photo']);
        if ($uploadResult['success']) {
            // Delete old photo
            $oldStmt = $db->prepare("SELECT photo FROM students WHERE id = ?");
            $oldStmt->execute([$_SESSION['student_id']]);
            $old = $oldStmt->fetch();
            if (!empty($old['photo']) && file_exists(UPLOAD_PATH . $old['photo'])) {
                unlink(UPLOAD_PATH . $old['photo']);
            }
            $photoFilename = $uploadResult['filename'];
        }
    }

    // Build SET clause
    $setClauses = [];
    $params = [];
    foreach ($data as $key => $value) {
        $setClauses[] = "$key = ?";
        $params[] = $value;
    }

    if ($photoFilename) {
        $setClauses[] = "photo = ?";
        $params[] = $photoFilename;
    }

    $setClauses[] = "updated_at = NOW()";
    $params[] = $_SESSION['student_id'];

    $sql = "UPDATE students SET " . implode(', ', $setClauses) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    // ── Notify admin of inventory form submission ─────────────────────────────
    require_once __DIR__.'/../includes/notifications.php';
    $stInfo = $db->prepare("SELECT first_name, last_name, student_id FROM students WHERE id=?");
    $stInfo->execute([$_SESSION['student_id']]);
    $stRow = $stInfo->fetch();
    createNotification(
        $db,
        'student_activity',
        $_SESSION['student_id'],
        'Inventory Form Submitted',
        ($stRow['first_name'].' '.$stRow['last_name'].' ('.$stRow['student_id'].') submitted/updated their Individual Inventory Form.'),
        null,
        '../admin/view_students.php'
    );

    setFlash('success', 'Inventory form saved successfully!');
    redirect('../student/inventory_form.php');

} catch (PDOException $e) {
    error_log("Save inventory error: " . $e->getMessage());
    setFlash('error', 'Error saving form: ' . $e->getMessage());
    redirect('../student/inventory_form.php');
}
