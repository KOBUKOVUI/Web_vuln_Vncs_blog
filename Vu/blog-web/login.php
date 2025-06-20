<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .form-box {
      width: 100%;
      max-width: 420px;
      padding: 2rem;
      border-radius: 16px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      background-color: #fff;
    }
    body {
      background: #f2f4f7;
    }
  </style>
</head>
<body>
  <div class="d-flex justify-content-center align-items-center vh-100">
    <form class="form-box" action="php/login.php" method="post">
      <h2 class="text-center mb-4 text-primary">🔐 Login</h2>

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
               placeholder="Enter your username"
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
               placeholder="Enter your password"
               required>
      </div>

      <!-- Nút login -->
      <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary">Login</button>
      </div>

      <!-- Liên kết điều hướng -->
      <div class="text-center">
        <a href="signup.php" class="text-decoration-none me-3">👥 Sign Up</a>
        <a href="admin-login.php" class="text-decoration-none me-3">🛠 Admin Login</a>
        <a href="blog.php" class="text-decoration-none">📰 Blog</a>
      </div>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
