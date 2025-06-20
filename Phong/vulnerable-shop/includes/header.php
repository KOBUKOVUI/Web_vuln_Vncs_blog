<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vulnerable Shop</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <header class="site-header">
        <div class="logo">
            <a href="<?php echo BASE_URL; ?>">Vulnerable Shop</a>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="<?php echo BASE_URL; ?>">Home</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="<?php echo BASE_URL; ?>user/profile.php">Profile</a></li>
                    <li><a href="<?php echo BASE_URL; ?>shop/cart.php" >Your Cart</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="<?php echo BASE_URL; ?>admin/index.php" >Admin Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo BASE_URL; ?>auth/logout.php" >Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo BASE_URL; ?>auth/login.php">Login</a></li>
                    <li><a href="<?php echo BASE_URL; ?>auth/register.php" >Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <?php if ($flash = getFlashMessage('success')): ?>
            <p class="flash-message flash-success"><?php echo htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if ($flash = getFlashMessage('error')): ?>
            <p class="flash-message flash-error"><?php echo htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

