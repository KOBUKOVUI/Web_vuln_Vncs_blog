<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

if (!isLoggedIn()) {
    setFlashMessage('error', 'You must be logged in to upload an avatar.');
    redirect(BASE_URL . 'auth/login.php');
}

$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];
    $filename = $file['name'];
    $target_dir = UPLOAD_PATH . 'avatars/';
    $target_file = $target_dir . basename($filename);

    // Vulnerable: No file type or MIME validation
    if ($file['error'] === UPLOAD_ERR_OK) {
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // Update avatar path in database
            $sql = "UPDATE users SET avatar = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $avatar_path = basename($filename);
            if ($stmt->execute([$avatar_path, $user_id])) {
                setFlashMessage('success', 'Avatar uploaded successfully.');
                redirect(BASE_URL . 'user/profile.php');
            } else {
                setFlashMessage('error', 'Failed to update avatar in database.');
            }
        } else {
            setFlashMessage('error', 'Failed to upload file.');
        }
    } else {
        setFlashMessage('error', 'File upload error.');
    }
}
?>

<main>
    <h2>Upload Avatar</h2>
    <form method="POST" enctype="multipart/form-data">
        <div>
            <label for="avatar">Choose Avatar:</label>
            <input type="file" id="avatar" name="avatar" required>
        </div>
        <button type="submit">Upload</button>
    </form>
    <p><a href="<?php echo BASE_URL; ?>user/profile.php">Back to Profile</a></p>
</main>

<?php
require_once '../includes/footer.php';
?>