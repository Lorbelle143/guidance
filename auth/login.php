<?php include '../config/database.php'; ?>

<!DOCTYPE html>
<html>
<head>
<title>Guidance Office Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
<div class="card p-4 shadow" style="width:400px;">
<h4 class="text-center mb-3">Guidance Office Login</h4>

<form action="login_process.php" method="POST">
<input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
<input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
<button class="btn btn-primary w-100">Login</button>
</form>

</div>
</div>
</body>
</html>