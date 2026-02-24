<?php
include '../config/database.php';

$username = $_POST['username'];
$password = md5($_POST['password']);

$query = mysqli_query($conn,"SELECT * FROM users WHERE username='$username' AND password='$password'");

if(mysqli_num_rows($query) > 0){
    $_SESSION['user'] = $username;
    header("Location: ../admin/dashboard.php");
}else{
    echo "Invalid Login";
}
?>