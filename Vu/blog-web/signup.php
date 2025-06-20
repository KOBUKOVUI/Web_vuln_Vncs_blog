<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Sign Up</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<style>
		body {
			background-color: #f3f4f6;
		}

		.signup-box {
			max-width: 450px;
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
	</style>
</head>

<body>

	<div class="d-flex justify-content-center align-items-center vh-100">
		<form class="signup-box" action="php/signup.php" method="post">
			<h2 class="text-center form-title mb-3">✍️ Create Account</h2>

			<!-- Thông báo lỗi -->
			<?php if (isset($_GET['error'])) { ?>
				<div class="alert alert-danger">
					<?= htmlspecialchars($_GET['error']) ?>
				</div>
			<?php } ?>

			<!-- Thông báo thành công -->
			<?php if (isset($_GET['success'])) { ?>
				<div class="alert alert-success">
					<?= htmlspecialchars($_GET['success']) ?>
				</div>
			<?php } ?>

			<!-- Họ tên -->
			<div class="mb-3">
				<label class="form-label">👤 Full Name</label>
				<input type="text"
					class="form-control"
					name="fname"
					placeholder="Your full name"
					value="<?= isset($_GET['fname']) ? htmlspecialchars($_GET['fname']) : '' ?>"
					required>
			</div>

			<!-- Tên đăng nhập -->
			<div class="mb-3">
				<label class="form-label">📛 Username</label>
				<input type="text"
					class="form-control"
					name="uname"
					placeholder="Choose a username"
					value="<?= isset($_GET['uname']) ? htmlspecialchars($_GET['uname']) : '' ?>"
					required>
			</div>

			<!-- Mật khẩu -->
			<div class="mb-3">
				<label class="form-label">🔒 Password</label>
				<input type="password"
					class="form-control"
					name="pass"
					placeholder="Enter a strong password"
					required>
			</div>

			<!-- Nút tạo tài khoản -->
			<div class="d-grid mb-3">
				<button type="submit" class="btn btn-primary">Sign Up</button>
			</div>

			<!-- Link trở lại trang login -->
			<div class="text-center">
				<a href="login.php" class="text-decoration-none">← Already have an account? Login</a>
			</div>
		</form>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>