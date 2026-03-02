<?php
session_start();
if (isset($_SESSION['user_id'])) {
	header("Location: dashboard.php");
	exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Student Record System</title>
	<style>
		body { font-family: Arial, Helvetica, sans-serif; background: #f5f7fa; color:#333; }
		.wrap { max-width:900px; margin:6vh auto; background:white; padding:32px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.08); text-align:center;}
		h1 { color:#667eea; margin-bottom:8px; }
		p { color:#666; margin-bottom:20px; }
		.btn{ display:inline-block; padding:12px 20px; margin:8px; border-radius:6px; text-decoration:none; color:white; background:linear-gradient(135deg,#667eea,#764ba2); font-weight:600;}
		.btn.secondary{ background:#6c757d; }
		.note{ margin-top:18px; color:#888; font-size:14px; }
	</style>
</head>
<body>
	<div class="wrap">
		<h1>Student Record System</h1>
		<p>Secure login and student management — add, edit, and delete student records.</p>
		<div>
			<a href="login.php" class="btn">Login</a>
			<a href="dashboard.php" class="btn secondary">Dashboard</a>
		</div>
		<div class="note">Demo credentials: <strong>user1</strong> / <strong>password123</strong></div>
	</div>
</body>
</html>
