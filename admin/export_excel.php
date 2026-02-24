<?php
include '../config/database.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=students.xls");

echo "Student ID\tName\tEmail\n";

$result = mysqli_query($conn,"SELECT * FROM students");
while($row=mysqli_fetch_assoc($result)){
echo "{$row['student_id']}\t{$row['last_name']} {$row['first_name']}\t{$row['email']}\n";
}
?>