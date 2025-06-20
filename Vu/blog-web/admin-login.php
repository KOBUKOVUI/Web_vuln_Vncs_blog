<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Admin Login</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<style>
		body {
			background-color: #f3f4f6;
		}

		.login-box {
			max-width: 420px;
			width: 100%;
			padding: 2rem;
			background-color: #fff;
			border-radius: 16px;
			box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
		}

		.form-title {
			font-weight: bold;
			color: #0d6efd;
		}

		.sub-text {
			font-size: 0.95rem;
			color: #666;
		}
	</style>
</head>

<body>

	<div class="d-flex justify-content-center align-items-center vh-100">
		<form class="login-box" action="admin/admin-login.php" method="post">
			<h2 class="text-center form-title mb-3">🔐 Admin Login</h2>
			<p class="text-center sub-text mb-4">Only for Administrator</p>

			<!-- Hiển thị lỗi -->
			<?php if (isset($_GET['error'])) { ?>
				<div class="alert alert-danger">
					<?= htmlspecialchars($_GET['error']) ?>
				</div>
			<?php } ?>

			<!-- Tên đăng nhập -->
			<div class="mb-3">
				<label for="uname" class="form-label">👤 Username</label>
				<input type="text"
					class="form-control"
					id="uname"
					name="uname"
					placeholder="Enter admin username"
					value="<?= isset($_GET['uname']) ? htmlspecialchars($_GET['uname']) : '' ?>"
					required>
			</div>

			<!-- Mật khẩu -->
			<div class="mb-3">
				<label for="pass" class="form-label">🔒 Password</label>
				<input type="password"
					class="form-control"
					id="pass"
					name="pass"
					placeholder="Enter password"
					required>
			</div>

			<!-- Nút submit -->
			<div class="d-grid mb-3">
				<button type="submit" class="btn btn-primary">Login</button>
			</div>

			<!-- Link về login user -->
			<div class="text-center">
				<a href="login.php" class="text-decoration-none">← User Login</a>
			</div>
		</form>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>