<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

if (!isLoggedIn()) {
    setFlashMessage('error', 'You must be logged in to edit your profile.');
    redirect(BASE_URL . 'auth/login.php');
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT email, full_name FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlashMessage('error', 'Invalid email format.');
    } else {
        // Check if email is already used by another user
        $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            setFlashMessage('error', 'Email is already in use.');
        } else {
            // Update profile
            $sql = "UPDATE users SET email = ?, full_name = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$email, $full_name, $user_id])) {
                setFlashMessage('success', 'Profile updated successfully.');
                redirect(BASE_URL . 'user/profile.php');
            } else {
                setFlashMessage('error', 'Failed to update profile.');
            }
        }
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
<main>
    <h2>Edit Profile</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email"id="email" name="email" value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>
        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text"id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?: '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to update your profile?');">Save Changes</button>
    </form>
    <p><a href="<?php echo BASE_URL; ?>user/profile.php">Back to Profile</a></p>
</main>
</html>

<?php
require_once '../includes/footer.php';
?>