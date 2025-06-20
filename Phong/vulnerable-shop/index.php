<?php
require_once 'config/config.php';
require_once 'includes/header.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

$sql = "SELECT id, name, price, image FROM products";
$products = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
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
    <h1>Welcome to Vulnerable Shop</h1>
    <p>This is a demonstration website designed for security testing and educational purposes only.</p>
    <p>Explore the shop, create an account, or test various features to learn about common web vulnerabilities.</p>
    <h2>Our Products</h2>
    <div class="product-list">
        <?php if (empty($products)): ?>
            <p>No products available.</p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="product-item">
                    <?php if ($product['image']): ?>
                        <img src="<?php echo BASE_URL . 'uploads/products/' . htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" style="max-width: 150px;">
                    <?php endif; ?>
                    <h3><a href="<?php echo BASE_URL; ?>shop/product_detail.php?id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></a></h3>
                    <p>Price: $<?php echo number_format($product['price'], 2); ?></p>
                    <a href="<?php echo BASE_URL; ?>shop/cart.php?action=add&id=<?php echo $product['id']; ?>" class="btn btn-primary">Add to Cart</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <p><a href="<?php echo BASE_URL; ?>auth/register.php" >Register Now</a> to shopping</p>
    <?php endif; ?>
    <p>For more information, check out our <a href="<?php echo BASE_URL; ?>download.php">Downloads</a> page.</p>
</body>
</html>

<?php
require_once 'includes/footer.php';
?>