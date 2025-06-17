<?php
require_once 'config/config.php';
require_once 'includes/header.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
?>

<main>
    <h1>Welcome to Vulnerable Shop</h1>
    <p>This is a demonstration website designed for security testing and educational purposes only.</p>
    <p>Explore the shop, create an account, or test various features to learn about common web vulnerabilities.</p>
    <a href="<?php echo BASE_URL; ?>shop/products.php" class="btn">Browse Products</a>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="<?php echo BASE_URL; ?>auth/register.php" class="btn">Register Now</a>
    <?php endif; ?>
</main>

<?php
require_once 'includes/footer.php';
?>