<?php
/**
 * QR Code Scanner Page (Admin Only)
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$pageTitle = 'Scan Student QR Code - Guidance Office System';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-qr-code-scan"></i> Scan Student QR Code</h5>
                </div>
                <div class="card-body">
                    <div id="reader" style="width: 100%;"></div>
                    
                    <div id="result" class="mt-4" style="display: none;">
                        <div class="alert alert-success">
                            <h5><i class="bi bi-check-circle"></i> Student Found!</h5>
                            <div id="studentInfo"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
function onScanSuccess(decodedText, decodedResult) {
    try {
        const data = JSON.parse(decodedText);
        
        // Display student information
        document.getElementById('studentInfo').innerHTML = `
            <table class="table table-borderless mb-0">
                <tr><td><strong>Student ID:</strong></td><td>${data.student_id}</td></tr>
                <tr><td><strong>Name:</strong></td><td>${data.name}</td></tr>
                <tr><td><strong>Email:</strong></td><td>${data.email || 'N/A'}</td></tr>
            </table>
            <a href="view_students.php?search=${data.student_id}" class="btn btn-primary mt-2">
                <i class="bi bi-eye"></i> View Full Profile
            </a>
        `;
        
        document.getElementById('result').style.display = 'block';
        
        // Stop scanning
        html5QrcodeScanner.clear();
    } catch (e) {
        alert('Invalid QR Code format');
    }
}

function onScanFailure(error) {
    // Handle scan failure silently
}

let html5QrcodeScanner = new Html5QrcodeScanner(
    "reader",
    { fps: 10, qrbox: {width: 250, height: 250} },
    false
);
html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
