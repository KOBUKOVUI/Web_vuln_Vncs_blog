<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('error', 'You must be logged in as an admin to access this page.');
    redirect(BASE_URL . 'auth/login.php');
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
<body>
    <header class="site-header">
    <div class="logo">
        <a href="<?php echo BASE_URL; ?>admin/index.php">Admin Dashboard</a>
    </div>
    <nav class="main-nav">
            <ul>
                <li><a href="<?php echo BASE_URL; ?>">Home</a></li>
                <li><a href="<?php echo BASE_URL; ?>admin/manage_users.php">Manage Users</a></li>
                <li><a href="<?php echo BASE_URL; ?>admin/manage_products.php">Manage Products</a></li>
                <li><a href="<?php echo BASE_URL; ?>admin/manage_comments.php">Manage Comments</a></li>
                <li><a href="<?php echo BASE_URL; ?>auth/logout.php" >Logout</a></li>
            </ul>
    </nav>
    </header>
    <main>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>!</p>
        <p>This is the admin panel for managing the Vulnerable Shop.</p>          
</body>
</html>
<?php
require_once '../includes/footer.php';
?>