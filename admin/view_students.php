<?php include '../config/database.php'; ?>

<!DOCTYPE html>
<html>
<head>
<title>Students</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
<h3>Student Records</h3>
<table class="table table-bordered">
<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Photo</th>
</tr>

<?php
$result = mysqli_query($conn,"SELECT * FROM students");
while($row=mysqli_fetch_assoc($result)){
echo "<tr>
<td>{$row['student_id']}</td>
<td>{$row['last_name']}, {$row['first_name']}</td>
<td>{$row['email']}</td>
<td><img src='../uploads/{$row['photo']}' width='60'></td>
</tr>";
}
?>

</table>
</div>
</body>
</html>