<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

// Restrict access to logged-in users
if (!isLoggedIn()) {
    setFlashMessage('error', 'You must be logged in to view the cart.');
    redirect(BASE_URL . 'auth/login.php');
}

$user_id = $_SESSION['user_id'];

// Handle add to cart
if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    if (productExists($pdo, $product_id)) {
        $sql = "SELECT price, name FROM products WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            // Check if product already in transactions
            $sql = "SELECT id, amount, quantity FROM transactions WHERE user_id = ? AND product_id = ? AND type = 'purchase'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id, $product_id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // Update quantity and amount
                $new_quantity = $existing['quantity'] + 1;
                $new_amount = $product['price'] * $new_quantity;
                $sql = "UPDATE transactions SET quantity = ?, amount = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$new_quantity, $new_amount, $existing['id']]);
            } else {
                // Insert new transaction
                $sql = "INSERT INTO transactions (user_id, product_id, amount, quantity, type) VALUES (?, ?, ?, 1, 'purchase')";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$user_id, $product_id, $product['price']]);
            }
            setFlashMessage('success', 'Product added to cart.');
        } else {
            setFlashMessage('error', 'Product not found.');
        }
    } else {
        setFlashMessage('error', 'Invalid product.');
    }
    redirect(BASE_URL . 'shop/cart.php');
}

// Handle remove from cart
if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    $sql = "DELETE FROM transactions WHERE user_id = ? AND product_id = ? AND type = 'purchase'";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$user_id, $product_id])) {
        setFlashMessage('success', 'Product removed from cart.');
    } else {
        setFlashMessage('error', 'Failed to remove product.');
    }
    redirect(BASE_URL . 'shop/cart.php');
}

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    // Fetch cart items to calculate total
    $sql = "SELECT t.product_id, t.amount, t.quantity, p.price FROM transactions t JOIN products p ON t.product_id = p.id WHERE t.user_id = ? AND t.type = 'purchase'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cart_items)) {
        setFlashMessage('error', 'Your cart is empty.');
        redirect(BASE_URL . 'shop/cart.php');
    }

    $total = 0;
    foreach ($cart_items as $item) {
        if (productExists($pdo, $item['product_id'])) {
            $total += $item['amount'];
        }
    }

    // Check user balance
    $sql = "SELECT balance FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        setFlashMessage('error', 'User not found.');
        redirect(BASE_URL . 'shop/cart.php');
    }

    if ($user['balance'] < $total) {
        setFlashMessage('error', 'Insufficient balance.');
        redirect(BASE_URL . 'shop/cart.php');
    }

    try {
        $pdo->beginTransaction();
        // Update user balance
        $sql = "UPDATE users SET balance = balance - ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$total, $user_id]);
        // Clear transactions
        $sql = "DELETE FROM transactions WHERE user_id = ? AND type = 'purchase'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $pdo->commit();
        setFlashMessage('success', 'Purchase completed successfully.');
    } catch (Exception $e) {
        $pdo->rollBack();
        setFlashMessage('error', 'Failed to process transaction.');
    }
    redirect(BASE_URL . 'shop/cart.php');
}

// Fetch cart items from transactions
$cart_items = [];
$total = 0;
$sql = "SELECT t.product_id, t.amount, t.quantity, p.name, p.image, p.price FROM transactions t JOIN products p ON t.product_id = p.id WHERE t.user_id = ? AND t.type = 'purchase'";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($transactions as $item) {
    if (productExists($pdo, $item['product_id'])) {
        $cart_items[$item['product_id']] = [
            'id' => $item['product_id'],
            'name' => $item['name'],
            'image' => $item['image'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'subtotal' => $item['amount']
        ];
        $total += $item['amount'];
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
<main class="container">
    <h2 class="mb-4">Shopping Cart</h2>
    <?php if (empty($cart_items)): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php if ($item['image']): ?>
                                    <img src="<?php echo BASE_URL . 'uploads/products/' . htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid" style="max-width: 50px;">
                                <?php endif; ?>
                            </td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>shop/cart.php?action=remove&id=<?php echo $item['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to remove this product?');">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total:</strong></td>
                        <td>$<?php echo number_format($total, 2); ?></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="checkout" value="1">
            <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to complete the purchase?');">Checkout</button>
        </form>
    <?php endif; ?>
    <p class="mt-3"><a href="<?php echo BASE_URL; ?>" class="btn btn-primary">Continue Shopping</a></p>
</main>
</html>

<?php
require_once '../includes/footer.php';
?>