<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

if (!isLoggedIn()) {
    setFlashMessage('error', 'You must be logged in to view profiles.');
    redirect(BASE_URL . 'auth/login.php');
}

// Vulnerable: No access control, allows viewing any user's profile via ?id=
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['user_id'];
$sql = "SELECT id, username, email, full_name, avatar, role, balance, created_at FROM users WHERE id = $user_id";
$result = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    setFlashMessage('error', 'User not found.');
    redirect(BASE_URL . 'index.php');
}
?>

<!DOCTYPE html>
<html lang="en">        
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/profile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<main>
    <h2>User Profile</h2>
    <div class="profile-info">
        <p><strong>Username:</strong> <?php echo htmlspecialchars($result['username'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($result['email'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($result['full_name'] ?: 'Not set', ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Role:</strong> <?php echo htmlspecialchars($result['role'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Balance:</strong> $<?php echo number_format($result['balance'], 2); ?></p>
        <p><strong>Joined:</strong> <?php echo $result['created_at']; ?></p>
        <?php if ($result['avatar']): ?>
            <p><strong>Avatar:</strong><br>
                <img src="<?php echo BASE_URL . 'uploads/avatars/' . htmlspecialchars($result['avatar'], ENT_QUOTES, 'UTF-8'); ?>" alt="Avatar" style="max-width: 150px;">
            </p>
        <?php endif; ?>
        <?php if ($user_id == $_SESSION['user_id']): ?>
            <p>
                <a href="<?php echo BASE_URL; ?>user/edit_profile.php" class="btn btn-primary">Edit Profile</a>
                <a href="<?php echo BASE_URL; ?>user/upload_avatar.php" class="btn btn-primary">Upload Avatar</a>
            </p>
        <?php endif; ?>
    </div>
</main>
</html>

<?php
require_once '../includes/footer.php';
?>