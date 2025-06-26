<?php
session_start();


$MAX_ATTEMPTS = 5; 
$LOCKOUT_DURATION = 60; 

// Nếu đang bị khóa đăng nhập (tồn tại lockout_time và chưa hết thời gian)
if (isset($_SESSION['lockout_time']) && time() < $_SESSION['lockout_time']) {
   $remaining = $_SESSION['lockout_time'] - time(); // tính thời gian còn lại
   $em = "Cưng định Brute Force hả? Đợi $remaining giây rồi hack típ nha.";
   header("Location: ../login.php?error=$em");
   exit;
}

//Kiểm tra nếu người dùng đã gửi username và password
if (isset($_POST['uname']) && isset($_POST['pass'])) {

   include "../db_conn.php";

   $uname = $_POST['uname'];
   $pass = $_POST['pass'];
   $data = "uname=" . $uname;

   //Kiểm tra đầu vào có trống không
   if (empty($uname)) {
      $em = "User name is required";
      header("Location: ../login.php?error=$em&$data");
      exit;
   } else if (empty($pass)) {
      $em = "Password is required";
      header("Location: ../login.php?error=$em&$data");
      exit;
   } else {
      //Truy vấn user theo username
      $sql = "SELECT * FROM users WHERE username = ?";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$uname]);

      //Nếu tìm thấy user
      if ($stmt->rowCount() == 1) {
         $user = $stmt->fetch();

         $username = $user['username'];
         $password = $user['password'];
         $fname = $user['fname'];
         $id = $user['id'];

         if (password_verify($pass, $password)) {
         //if (md5($pass) == $password){

            unset($_SESSION['login_attempts']);
            unset($_SESSION['lockout_time']);

            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;

            header("Location: ../blog.php");
            exit;
         }
      }

      // Tăng số lần sai
      if (!isset($_SESSION['login_attempts'])) {
         $_SESSION['login_attempts'] = 1;
      } else {
         $_SESSION['login_attempts']++;
      }

      // Nếu quá số lần cho phép → khóa tạm thời
      if ($_SESSION['login_attempts'] >= $MAX_ATTEMPTS) {
         $_SESSION['lockout_time'] = time() + $LOCKOUT_DURATION;
         $em = "Bạn đã nhập sai quá $MAX_ATTEMPTS lần. Vui lòng thử lại sau $LOCKOUT_DURATION giây.";
      } else {
         $remaining = $MAX_ATTEMPTS - $_SESSION['login_attempts'];
         $em = "Sai tên đăng nhập hoặc mật khẩu. Còn $remaining lần thử.";
      }

      header("Location: ../login.php?error=$em&$data");
      exit;
   }
} else {
   header("Location: ../login.php?error=Thiếu dữ liệu");
   exit;
}
?>
