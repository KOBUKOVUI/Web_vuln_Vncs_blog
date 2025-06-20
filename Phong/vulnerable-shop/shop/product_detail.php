<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

if (!isset($_GET['id']) || !($product_id = (int)$_GET['id'])) {
    setFlashMessage('error', 'Invalid product ID.');
    redirect(BASE_URL . 'shop/products.php');
}

$sql = "SELECT id, name, description, price, image FROM products WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    setFlashMessage('error', 'Product not found.');
    redirect(BASE_URL . 'shop/products.php');
}

// Vulnerable: XSS in comment handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user_id'];
    $sql = "INSERT INTO comments (product_id, user_id, content) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$product_id, $user_id, $comment]);
    setFlashMessage('success', 'Comment added successfully.');
    redirect(BASE_URL . 'shop/product_detail.php?id=' . $product_id);
}

// Fetch comments
$sql = "SELECT c.content, c.created_at, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.product_id = ? ORDER BY c.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$product_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <h2><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
    <div class="product-detail">
        <?php if ($product['image']): ?>
            <img src="<?php echo BASE_URL . 'uploads/products/' . htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" style="max-width: 200px;">
        <?php endif; ?>
        <p><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description'] ?: 'No description available.', ENT_QUOTES, 'UTF-8'); ?></p>
        <a href="<?php echo BASE_URL; ?>shop/cart.php?action=add&id=<?php echo $product['id']; ?>" class="btn">Add to Cart</a>
    </div>

    <h3>Comments</h3>
    <?php if (isLoggedIn()): ?>
        <form method="POST" action="">
            <div>
                <label for="comment">Add a Comment:</label>
                <textarea id="comment" name="comment" class="form-control" required></textarea>
            </div>
            <br>
            <button type="submit" class="btn btn-primary">Submit Comment</button>
        </form>
    <?php else: ?>
        <p><a href="<?php echo BASE_URL; ?>auth/login.php">Login</a> to add a comment.</p>
    <?php endif; ?>

    <div class="comment-list">
        <?php if (empty($comments)): ?>
            <p>No comments yet.</p>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment-item">
                    <!-- Vulnerable: No htmlspecialchars, allows XSS -->
                    <p><strong><?php echo htmlspecialchars($comment['username'], ENT_QUOTES, 'UTF-8'); ?>:</strong> <?php echo $comment['content']; ?></p>
                    <p><small>Posted on: <?php echo $comment['created_at']; ?></small></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
</html>

<?php
require_once '../includes/footer.php';
?>