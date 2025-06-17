<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add to cart
if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    if (productExists($pdo, $product_id)) {
        $sql = "SELECT price, name FROM products WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity']++;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'quantity' => 1,
                    'price' => $product['price'],
                    'name' => $product['name']
                ];
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
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        setFlashMessage('success', 'Product removed from cart.');
    } else {
        setFlashMessage('error', 'Product not in cart.');
    }
    redirect(BASE_URL . 'shop/cart.php');
}

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    if (!isLoggedIn()) {
        setFlashMessage('error', 'You must be logged in to checkout.');
        redirect(BASE_URL . 'auth/login.php');
    }
    if (empty($_SESSION['cart'])) {
        setFlashMessage('error', 'Your cart is empty.');
        redirect(BASE_URL . 'shop/cart.php');
    }

    $user_id = $_SESSION['user_id'];
    $total = 0;
    foreach ($_SESSION['cart'] as $product_id => $cart_item) {
        if (productExists($pdo, $product_id)) {
            $total += $cart_item['quantity'] * $cart_item['price'];
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

    // Process transaction
    try {
        $pdo->beginTransaction();
        foreach ($_SESSION['cart'] as $product_id => $cart_item) {
            if (productExists($pdo, $product_id)) {
                $sql = "INSERT INTO transactions (user_id, product_id, amount, type) VALUES (?, ?, ?, 'purchase')";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$user_id, $product_id, $cart_item['price'] * $cart_item['quantity']]);
            }
        }
        $sql = "UPDATE users SET balance = balance - ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$total, $user_id]);
        $pdo->commit();

        $_SESSION['cart'] = [];
        setFlashMessage('success', 'Purchase completed successfully.');
    } catch (Exception $e) {
        $pdo->rollBack();
        setFlashMessage('error', 'Failed to process transaction.');
    }
    redirect(BASE_URL . 'shop/cart.php');
}

// Fetch cart items
$cart_items = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $sql = "SELECT id, name, image FROM products WHERE id IN (" . implode(',', array_fill(0, count($ids), '?')) . ")";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        $cart_items[$product['id']] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'image' => $product['image'],
            'quantity' => $_SESSION['cart'][$product['id']]['quantity'],
            'price' => $_SESSION['cart'][$product['id']]['price'],
            'subtotal' => $_SESSION['cart'][$product['id']]['quantity'] * $_SESSION['cart'][$product['id']]['price']
        ];
        $total += $cart_items[$product['id']]['subtotal'];
    }
}
?>

<main>
    <h2>Shopping Cart</h2>
    <?php if (empty($cart_items)): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Product</th>
                <th>Image</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
            <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <?php if ($item['image']): ?>
                            <img src="<?php echo BASE_URL . 'uploads/products/' . htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>" style="max-width: 50px;">
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
                <td colspan="4" style="text-align: right;"><strong>Total:</strong></td>
                <td>$<?php echo number_format($total, 2); ?></td>
                <td></td>
            </tr>
        </table>
        <?php if (isLoggedIn()): ?>
            <form method="POST" action="">
                <input type="hidden" name="checkout" value="1">
                <button type="submit" class="btn">Checkout</button>
            </form>
        <?php else: ?>
            <p><a href="<?php echo BASE_URL; ?>auth/login.php">Login</a> to checkout.</p>
        <?php endif; ?>
    <?php endif; ?>
    <p><a href="<?php echo BASE_URL; ?>shop/products.php" class="btn">Continue Shopping</a></p>
</main>

<?php
require_once '../includes/footer.php';
?>