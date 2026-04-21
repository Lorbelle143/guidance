<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireStudent();

$pageTitle = 'Individual Inventory Form - NBSC GCO';
require_once __DIR__ . '/../includes/header.php';

// Load existing data
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$_SESSION['student_id']]);
    $student = $stmt->fetch();
} catch (PDOException $e) {
    $student = [];
}

$v = function($key) use ($student) {
    return htmlspecialchars($student[$key] ?? '');
};
$checked = function($key) use ($student) {
    return !empty($student[$key]) ? 'checked' : '';
};
?>

<div class="dashboard-wrapper">
    <?php require_once __DIR__ . '/../includes/sidebar_student.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <div class="page-title">
                <h4>Individual Inventory Form</h4>
                <p>Northern Bukidnon State College — Guidance and Counseling Office</p>
            </div>
        </div>

        <div class="page-body">
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert-custom <?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>">
                <i class="bi bi-<?php echo $flash['type'] === 'error' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                <span><?php echo sanitize($flash['message']); ?></span>
            </div>
            <?php endif; ?>

            <form action="../process/save_inventory.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                <!-- ===== HEADER INFO ===== -->
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header-custom" style="background: #1a56db; color: white; border-radius: 14px 14px 0 0;">
                        <h5 style="color: white;">
                            <i class="bi bi-building"></i>
                            NORTHERN BUKIDNON STATE COLLEGE
                        </h5>
                        <span style="font-size: 0.85rem; opacity: 0.9;">GUIDANCE AND COUNSELING OFFICE</span>
                    </div>
                    <div style="text-align: center; padding: 8px; background: #f0f4ff; border-bottom: 1px solid var(--gray-200);">
                        <strong style="font-size: 1rem; letter-spacing: 1px;">Individual Inventory Form</strong>
                    </div>

                    <div class="card-body-custom">
                        <div style="display: grid; grid-template-columns: 1fr 1fr 0.5fr 1fr 0.5fr; gap: 12px; align-items: end;">
                            <div>
                                <label class="form-label-custom">Last Name <span style="color:red">*</span></label>
                                <input type="text" name="last_name" class="form-control-custom" value="<?php echo $v('last_name'); ?>" required>
                            </div>
                            <div>
                                <label class="form-label-custom">First Name <span style="color:red">*</span></label>
                                <input type="text" name="first_name" class="form-control-custom" value="<?php echo $v('first_name'); ?>" required>
                            </div>
                            <div>
                                <label class="form-label-custom">M.I.</label>
                                <input type="text" name="middle_name" class="form-control-custom" value="<?php echo $v('middle_name'); ?>" maxlength="5">
                            </div>
                            <div>
                                <label class="form-label-custom">Program & Year</label>
                                <input type="text" name="program_year" class="form-control-custom" value="<?php echo $v('program_year'); ?>" placeholder="e.g. BSIT 1st Year">
                            </div>
                            <div>
                                <label class="form-label-custom">Birth Date</label>
                                <input type="date" name="birth_date" class="form-control-custom" value="<?php echo $v('birth_date'); ?>">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr; gap: 12px; margin-top: 12px; align-items: end;">
                            <div>
                                <label class="form-label-custom">ID No. <span style="color:red">*</span></label>
                                <input type="text" name="student_id_display" class="form-control-custom" value="<?php echo $v('student_id'); ?>" readonly style="background:#f3f4f6;">
                            </div>
                            <div>
                                <label class="form-label-custom">Gender</label>
                                <select name="gender" class="form-control-custom">
                                    <option value="">Select...</option>
                                    <option value="Male" <?php echo ($student['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo ($student['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other" <?php echo ($student['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label-custom">Ethnicity</label>
                                <input type="text" name="ethnicity" class="form-control-custom" value="<?php echo $v('ethnicity'); ?>">
                            </div>
                            <div>
                                <label class="form-label-custom">Religion</label>
                                <input type="text" name="religion" class="form-control-custom" value="<?php echo $v('religion'); ?>">
                            </div>
                            <div>
                                <label class="form-label-custom">Civil Status</label>
                                <select name="civil_status" class="form-control-custom">
                                    <option value="">Select...</option>
                                    <?php foreach (['Single','Married','Widowed','Separated','Divorced'] as $cs): ?>
                                    <option value="<?php echo $cs; ?>" <?php echo ($student['civil_status'] ?? '') === $cs ? 'selected' : ''; ?>><?php echo $cs; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== CONTACT INFO ===== -->
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header-custom">
                        <h5><i class="bi bi-telephone"></i> Contact Information</h5>
                    </div>
                    <div class="card-body-custom">
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                            <div>
                                <label class="form-label-custom">Mobile Phone Number/s</label>
                                <input type="text" name="mobile_number" class="form-control-custom" value="<?php echo $v('mobile_number'); ?>" placeholder="09XX XXX XXXX">
                            </div>
                            <div>
                                <label class="form-label-custom">Personal E-mail Address</label>
                                <input type="email" name="personal_email" class="form-control-custom" value="<?php echo $v('personal_email'); ?>">
                            </div>
                            <div>
                                <label class="form-label-custom">Institutional E-mail Address</label>
                                <input type="email" name="institutional_email" class="form-control-custom" value="<?php echo $v('institutional_email'); ?>" placeholder="@nbsc.edu.ph">
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 12px;">
                            <div>
                                <label class="form-label-custom">Permanent Address</label>
                                <input type="text" name="permanent_address" class="form-control-custom" value="<?php echo $v('permanent_address'); ?>">
                            </div>
                            <div>
                                <label class="form-label-custom">Current Address</label>
                                <input type="text" name="current_address" class="form-control-custom" value="<?php echo $v('current_address'); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== FAMILY BACKGROUND ===== -->
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header-custom">
                        <h5><i class="bi bi-people"></i> Family Background</h5>
                    </div>
                    <div class="card-body-custom">
                        <div style="overflow-x: auto;">
                            <table style="width:100%; border-collapse:collapse; font-size:0.875rem;">
                                <thead>
                                    <tr style="background:var(--gray-50);">
                                        <th style="padding:10px; border:1px solid var(--gray-200); text-align:left; width:160px;">Profile</th>
                                        <th style="padding:10px; border:1px solid var(--gray-200); text-align:center;">Mother</th>
                                        <th style="padding:10px; border:1px solid var(--gray-200); text-align:center;">Father</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $rows = [
                                        ['Name', 'mother_name', 'father_name', 'text'],
                                        ['Age and Birthday', 'mother_birthday', 'father_birthday', 'date'],
                                        ['Ethnicity', 'mother_ethnicity', 'father_ethnicity', 'text'],
                                        ['Religion', 'mother_religion', 'father_religion', 'text'],
                                        ['Educational Attainment', 'mother_education', 'father_education', 'text'],
                                        ['Occupation', 'mother_occupation', 'father_occupation', 'text'],
                                        ['Company', 'mother_company', 'father_company', 'text'],
                                        ['Monthly Income', 'mother_income', 'father_income', 'text'],
                                        ['Contact Number', 'mother_contact', 'father_contact', 'text'],
                                    ];
                                    foreach ($rows as $row):
                                    ?>
                                    <tr>
                                        <td style="padding:8px 10px; border:1px solid var(--gray-200); font-weight:500; color:var(--gray-600);"><?php echo $row[0]; ?></td>
                                        <td style="padding:6px 10px; border:1px solid var(--gray-200);">
                                            <input type="<?php echo $row[3]; ?>" name="<?php echo $row[1]; ?>" class="form-control-custom" value="<?php echo $v($row[1]); ?>" style="margin:0;">
                                        </td>
                                        <td style="padding:6px 10px; border:1px solid var(--gray-200);">
                                            <input type="<?php echo $row[3]; ?>" name="<?php echo $row[2]; ?>" class="form-control-custom" value="<?php echo $v($row[2]); ?>" style="margin:0;">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-top: 16px;">
                            <div>
                                <label class="form-label-custom">Status of Parent/s</label>
                                <select name="parent_status" class="form-control-custom">
                                    <option value="">Select...</option>
                                    <?php foreach (['Married','Living Together','Divorced/Annulled','Single Parent','Separated','Widowed/Widower'] as $ps): ?>
                                    <option value="<?php echo $ps; ?>" <?php echo ($student['parent_status'] ?? '') === $ps ? 'selected' : ''; ?>><?php echo $ps; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="form-label-custom">Number of Siblings</label>
                                <input type="number" name="num_siblings" class="form-control-custom" value="<?php echo $v('num_siblings'); ?>" min="0">
                            </div>
                            <div>
                                <label class="form-label-custom">Name of Guardian/s</label>
                                <input type="text" name="guardian_name" class="form-control-custom" value="<?php echo $v('guardian_name'); ?>">
                            </div>
                            <div>
                                <label class="form-label-custom">Address of Guardian/s</label>
                                <input type="text" name="guardian_address" class="form-control-custom" value="<?php echo $v('guardian_address'); ?>">
                            </div>
                            <div>
                                <label class="form-label-custom">Contact Number of Guardian/s</label>
                                <input type="text" name="guardian_contact" class="form-control-custom" value="<?php echo $v('guardian_contact'); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== INTERESTS ===== -->
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header-custom">
                        <h5><i class="bi bi-star"></i> Interest & Recreational Activities</h5>
                    </div>
                    <div class="card-body-custom">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div>
                                <label class="form-label-custom">Hobbies</label>
                                <input type="text" name="hobbies" class="form-control-custom" value="<?php echo $v('hobbies'); ?>">
                            </div>
                            <div>
                                <label class="form-label-custom">Talents</label>
                                <input type="text" name="talents" class="form-control-custom" value="<?php echo $v('talents'); ?>">
                            </div>
                            <div>
                                <label class="form-label-custom">Sports</label>
                                <input type="text" name="sports" class="form-control-custom" value="<?php echo $v('sports'); ?>">
                            </div>
                            <div>
                                <label class="form-label-custom">Socio-civic</label>
                                <input type="text" name="socio_civic" class="form-control-custom" value="<?php echo $v('socio_civic'); ?>">
                            </div>
                            <div>
                                <label class="form-label-custom">School Org.</label>
                                <input type="text" name="school_org" class="form-control-custom" value="<?php echo $v('school_org'); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== HEALTH ===== -->
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header-custom">
                        <h5><i class="bi bi-heart-pulse"></i> Health</h5>
                    </div>
                    <div class="card-body-custom">
                        <div style="display: flex; flex-direction: column; gap: 14px;">
                            <div style="display: grid; grid-template-columns: auto 1fr; gap: 12px; align-items: center;">
                                <label style="font-size:0.875rem; color:var(--gray-700);">Have you ever been hospitalized?</label>
                                <div style="display:flex; gap:16px; align-items:center;">
                                    <label style="display:flex; gap:6px; align-items:center; font-size:0.875rem; cursor:pointer;">
                                        <input type="radio" name="hospitalized" value="1" <?php echo !empty($student['hospitalized']) ? 'checked' : ''; ?>> Yes
                                    </label>
                                    <label style="display:flex; gap:6px; align-items:center; font-size:0.875rem; cursor:pointer;">
                                        <input type="radio" name="hospitalized" value="0" <?php echo empty($student['hospitalized']) ? 'checked' : ''; ?>> No
                                    </label>
                                    <input type="text" name="hospitalized_details" class="form-control-custom" 
                                           value="<?php echo $v('hospitalized_details'); ?>" placeholder="If yes, state when/reason" style="flex:1;">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: auto 1fr; gap: 12px; align-items: center;">
                                <label style="font-size:0.875rem; color:var(--gray-700);">Have you ever had an operation?</label>
                                <div style="display:flex; gap:16px; align-items:center;">
                                    <label style="display:flex; gap:6px; align-items:center; font-size:0.875rem; cursor:pointer;">
                                        <input type="radio" name="had_operation" value="1" <?php echo !empty($student['had_operation']) ? 'checked' : ''; ?>> Yes
                                    </label>
                                    <label style="display:flex; gap:6px; align-items:center; font-size:0.875rem; cursor:pointer;">
                                        <input type="radio" name="had_operation" value="0" <?php echo empty($student['had_operation']) ? 'checked' : ''; ?>> No
                                    </label>
                                    <input type="text" name="operation_details" class="form-control-custom" 
                                           value="<?php echo $v('operation_details'); ?>" placeholder="If yes, state when/reason" style="flex:1;">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: auto 1fr; gap: 12px; align-items: center;">
                                <label style="font-size:0.875rem; color:var(--gray-700);">Do you currently suffer from any illness/condition?</label>
                                <div style="display:flex; gap:16px; align-items:center;">
                                    <label style="display:flex; gap:6px; align-items:center; font-size:0.875rem; cursor:pointer;">
                                        <input type="radio" name="has_illness" value="1" <?php echo !empty($student['has_illness']) ? 'checked' : ''; ?>> Yes
                                    </label>
                                    <label style="display:flex; gap:6px; align-items:center; font-size:0.875rem; cursor:pointer;">
                                        <input type="radio" name="has_illness" value="0" <?php echo empty($student['has_illness']) ? 'checked' : ''; ?>> No
                                    </label>
                                    <input type="text" name="illness_details" class="form-control-custom" 
                                           value="<?php echo $v('illness_details'); ?>" placeholder="If yes, state illness" style="flex:1;">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                <div>
                                    <label class="form-label-custom">Common illness in the family</label>
                                    <input type="text" name="common_illness" class="form-control-custom" value="<?php echo $v('common_illness'); ?>">
                                </div>
                                <div>
                                    <label class="form-label-custom">When did you last see a doctor?</label>
                                    <input type="text" name="last_doctor_visit" class="form-control-custom" value="<?php echo $v('last_doctor_visit'); ?>">
                                </div>
                                <div style="grid-column: span 2;">
                                    <label class="form-label-custom">Reason for the visit</label>
                                    <input type="text" name="doctor_visit_reason" class="form-control-custom" value="<?php echo $v('doctor_visit_reason'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== LIFE CIRCUMSTANCES ===== -->
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header-custom">
                        <h5><i class="bi bi-check2-square"></i> Life Circumstances</h5>
                    </div>
                    <div class="card-body-custom">
                        <p style="font-size:0.875rem; color:var(--gray-600); margin-bottom:14px;">
                            Check any of the <strong>PROBLEMS</strong> below that currently concerns you:
                        </p>
                        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px;">
                            <?php
                            $concerns = [
                                'concern_fear' => 'Fear',
                                'concern_communication' => 'Communication',
                                'concern_shyness' => 'Shyness',
                                'concern_loneliness' => 'Loneliness',
                                'concern_stress' => 'Stress',
                                'concern_anger' => 'Anger',
                                'concern_self_confidence' => 'Self-confidence',
                                'concern_academic' => 'Academic Performance',
                                'concern_career' => 'Career',
                                'concern_financial' => 'Financial',
                            ];
                            foreach ($concerns as $key => $label):
                            ?>
                            <label style="display:flex; gap:8px; align-items:center; font-size:0.875rem; cursor:pointer; padding:8px; border:1px solid var(--gray-200); border-radius:8px; transition:all 0.15s;"
                                   onmouseover="this.style.background='var(--primary-light)'" onmouseout="this.style.background='white'">
                                <input type="checkbox" name="<?php echo $key; ?>" value="1" <?php echo $checked($key); ?> style="width:16px; height:16px; accent-color:var(--primary);">
                                <?php echo $label; ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <div style="margin-top: 12px;">
                            <label class="form-label-custom">Others (please specify)</label>
                            <input type="text" name="concern_others" class="form-control-custom" value="<?php echo $v('concern_others'); ?>">
                        </div>
                    </div>
                </div>

                <!-- ===== PHOTO ===== -->
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header-custom">
                        <h5><i class="bi bi-camera"></i> Photo</h5>
                    </div>
                    <div class="card-body-custom" style="display:flex; gap:24px; align-items:center;">
                        <div>
                            <?php if (!empty($student['photo']) && file_exists(UPLOAD_PATH . $student['photo'])): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($student['photo']); ?>" id="photoPreview"
                                 style="width:100px; height:120px; object-fit:cover; border:2px solid var(--gray-200); border-radius:8px;">
                            <?php else: ?>
                            <div id="photoPreview" style="width:100px; height:120px; background:var(--gray-100); border:2px dashed var(--gray-300); border-radius:8px; display:flex; align-items:center; justify-content:center; color:var(--gray-400);">
                                <i class="bi bi-person" style="font-size:2.5rem;"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="form-label-custom">Upload/Change Photo</label>
                            <input type="file" name="photo" id="photoInput" accept="image/*"
                                   style="display:block; padding:8px; border:1.5px solid var(--gray-200); border-radius:9px; font-size:0.875rem; background:var(--gray-50);">
                            <small style="color:var(--gray-400); font-size:0.78rem;">JPG, PNG — max 5MB. Leave blank to keep current photo.</small>
                        </div>
                    </div>
                </div>

                <!-- ===== SUBMIT ===== -->
                <div style="display:flex; gap:12px; justify-content:flex-end; padding-bottom:20px;">
                    <a href="student_dashboard.php" class="btn-main outline">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                    <button type="submit" class="btn-main primary">
                        <i class="bi bi-save"></i> Save Inventory Form
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('photoInput').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('photoPreview');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.id = 'photoPreview';
                img.style = 'width:100px; height:120px; object-fit:cover; border:2px solid var(--gray-200); border-radius:8px;';
                preview.replaceWith(img);
            }
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
