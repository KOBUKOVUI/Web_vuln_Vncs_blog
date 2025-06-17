<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

$sql = "SELECT id, name, price, image FROM products";
$products = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <h2>Products</h2>
    <div class="product-list">
        <?php if (empty($products)): ?>
            <p>No products available.</p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="product-item">
                    <?php if ($product['image']): ?>
                        <img src="<?php echo BASE_URL . 'uploads/products/' . htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" style="max-width: 100px;">
                    <?php endif; ?>
                    <h3><a href="<?php echo BASE_URL; ?>shop/product_detail.php?id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></a></h3>
                    <p>Price: $<?php echo number_format($product['price'], 2); ?></p>
                    <a href="<?php echo BASE_URL; ?>shop/cart.php?action=add&id=<?php echo $product['id']; ?>" class="btn">Add to Cart</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php
require_once '../includes/footer.php';
?>