<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

if (isLoggedIn()) {
    redirect(BASE_URL . 'index.php');
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $full_name = $_POST['full_name'];

    // Check if username or email already exists
    $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        $error = 'Username or email already exists';
    } else {
        // Insert new user
        $sql = "INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, 'user')";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$username, $email, $password, $full_name])) {
            $success = 'Registration successful! <a href="' . BASE_URL . 'auth/login.php">Login now</a>';
        } else {
            $error = 'Registration failed';
        }
    }
}
?>

<main>
    <h2>Register</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="<?php echo BASE_URL; ?>auth/login.php">Login</a></p>
</main>

<?php
require_once '../includes/footer.php';
?>