<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('error', 'You must be logged in as an admin to access this page.');
    redirect(BASE_URL . 'auth/login.php');
}
?>

<main>
    <h2>Admin Dashboard</h2>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>!</p>
    <p>This is the admin panel for managing the Vulnerable Shop.</p>
    <ul class="admin-links">
        <a href="<?php echo BASE_URL; ?>admin/manage_users.php" class="btn">Manage Users</a>
        <br>
        <br>
        <a href="<?php echo BASE_URL; ?>admin/manage_products.php" class="btn">Manage Products</a>
    </ul>
</main>

<?php
require_once '../includes/footer.php';
?>