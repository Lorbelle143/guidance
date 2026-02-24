<?php
include '../config/database.php';

$photo = $_FILES['photo']['name'];
$temp = $_FILES['photo']['tmp_name'];
move_uploaded_file($temp,"../uploads/".$photo);

mysqli_query($conn,"INSERT INTO students 
(student_id,last_name,first_name,email,photo)
VALUES 
('$_POST[student_id]','$_POST[last_name]','$_POST[first_name]','$_POST[email]','$photo')");

header("Location: ../admin/view_students.php");
?>