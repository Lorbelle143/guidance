<?php 
include '../config/database.php';
if(!isset($_SESSION['user'])) header("Location: ../auth/login.php");
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-primary">
<div class="container-fluid">
<span class="navbar-brand">Guidance Inventory System</span>
<a href="../auth/logout.php" class="btn btn-light">Logout</a>
</div>
</nav>

<div class="container mt-4">
<a href="add_student.php" class="btn btn-success">Add Student</a>
<a href="view_students.php" class="btn btn-dark">View Students</a>
<a href="export_pdf.php" class="btn btn-warning">Export to Excel</a>
</div>

</body>
</html>