<?php include '../config/database.php'; ?>

<!DOCTYPE html>
<html>
<head>
<title>Add Student</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
<div class="card shadow">
<div class="card-header bg-primary text-white">Individual Inventory Form</div>
<div class="card-body">

<form action="../process/save_student.php" method="POST" enctype="multipart/form-data">

<input type="text" name="student_id" class="form-control mb-3" placeholder="Student ID" required>
<input type="text" name="last_name" class="form-control mb-3" placeholder="Last Name" required>
<input type="text" name="first_name" class="form-control mb-3" placeholder="First Name" required>
<input type="email" name="email" class="form-control mb-3" placeholder="Email">

<label>Upload Photo</label>
<input type="file" name="photo" class="form-control mb-3" required>

<button class="btn btn-primary">Save</button>

</form>

</div>
</div>
</div>
</body>
</html>