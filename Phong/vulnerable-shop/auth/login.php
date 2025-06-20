<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

if (isLoggedIn()) {
    redirect(BASE_URL . 'index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // MD5 hash for vulnerability example

    // Vulnerable SQL query (SQL Injection)
    $sql = "SELECT * FROM users WHERE username = '" . $username . "' AND password = '" . $password . "'";
    $result = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['username'] = $result['username'];
        $_SESSION['role'] = $result['role'];
        redirect(BASE_URL . 'index.php');
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">        
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<main class="login-container">
    <h2 class="login-title">Login</h2>

    <?php if (!empty($error)): ?>
        <p class="alert alert-danger" ><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="button_blue">Login</button>
    </form>

    <p class="register-link">Don't have an account?
        <a href="<?php echo BASE_URL; ?>auth/register.php">Register</a>
    </p>
</main>
</html>

<?php
require_once '../includes/footer.php';
?>