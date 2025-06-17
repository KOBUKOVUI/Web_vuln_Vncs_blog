<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('error', 'You must be an admin to access this page.');
    redirect(BASE_URL . 'auth/login.php');
}

// Handle delete product
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    $sql = "SELECT image FROM products WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Delete product image if exists
        if ($product['image'] && file_exists(UPLOAD_PATH . 'products/' . $product['image'])) {
            unlink(UPLOAD_PATH . 'products/' . $product['image']);
        }
        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$product_id])) {
            setFlashMessage('success', 'Product deleted successfully.');
        } else {
            setFlashMessage('error', 'Failed to delete product.');
        }
    } else {
        setFlashMessage('error', 'Product not found.');
    }
    redirect(BASE_URL . 'admin/manage_products.php');
}

// Handle edit product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $product_id = (int)$_POST['product_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']) ?: null;
    $price = (float)$_POST['price'];

    // Check if product exists
    if (!productExists($pdo, $product_id)) {
        setFlashMessage('error', 'Product not found.');
        redirect(BASE_URL . 'admin/manage_products.php');
    }

    if (empty($name) || $price <= 0) {
        setFlashMessage('error', 'Invalid product name or price.');
    } else {
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $target_dir = UPLOAD_PATH . 'products/';
            $image_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $target_dir . $image_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image = $image_name;
                // Delete old image if exists
                $sql = "SELECT image FROM products WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$product_id]);
                $old_image = $stmt->fetchColumn();
                if ($old_image && file_exists($target_dir . $old_image)) {
                    unlink($target_dir . $old_image);
                }
            } else {
                setFlashMessage('error', 'Failed to upload image.');
                redirect(BASE_URL . 'admin/manage_products.php');
            }
        }

        $sql = "UPDATE products SET name = ?, description = ?, price = ?" . ($image ? ", image = ?" : "") . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $params = [$name, $description, $price];
        if ($image) {
            $params[] = $image;
        }
        $params[] = $product_id;
        if ($stmt->execute($params)) {
            setFlashMessage('success', 'Product updated successfully.');
        } else {
            setFlashMessage('error', 'Failed to update product.');
        }
    }
    redirect(BASE_URL . 'admin/manage_products.php');
}

// Fetch all products
$sql = "SELECT id, name, description, price, image, created_at FROM products ORDER BY created_at DESC";
$products = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <h2>Manage Products</h2>
    <p><a href="<?php echo BASE_URL; ?>shop/add_product.php" class="btn">Add New Product</a></p>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Image</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo $product['id']; ?></td>
                <td><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>$<?php echo number_format($product['price'], 2); ?></td>
                <td>
                    <?php if ($product['image']): ?>
                        <img src="<?php echo BASE_URL . 'uploads/products/' . htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" style="max-width: 50px;">
                    <?php endif; ?>
                </td>
                <td><?php echo $product['created_at']; ?></td>
                <td>
                    <button class="btn" onclick="editProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars(addslashes($product['name']), ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars(addslashes($product['description'] ?: ''), ENT_QUOTES, 'UTF-8'); ?>')">Edit</button>
                    <button class="btn btn-danger" onclick="if(confirm('Are you sure you want to delete this product?')){window.location.href='<?php echo BASE_URL; ?>admin/manage_products.php?action=delete&id=<?php echo $product['id']; ?>';}">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal" style="display: none;">
        <h3>Edit Product</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" id="edit_product_id">
            <div>
                <label for="edit_name">Name:</label>
                <input type="text" id="edit_name" name="name" required>
            </div>
            <div>
                <label for="edit_description">Description:</label>
                <textarea id="edit_description" name="description"></textarea>
            </div>
            <div>
                <label for="edit_price">Price ($):</label>
                <input type="number" id="edit_price" name="price" step="0.01" required>
            </div>
            <div>
                <label for="edit_image">New Image (optional):</label>
                <input type="file" id="edit_image" name="image">
            </div>
            <button type="submit" name="edit_product" class="btn">Save Changes</button>
            <button type="button" class="btn" onclick="closeModal()">Cancel</button>
        </form>
    </div>

    <script>
        function editProduct(id, name, price, description) {
            document.getElementById('edit_product_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_price').value = parseFloat(price).toFixed(2);
            document.getElementById('edit_description').value = description;
            document.getElementById('editProductModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editProductModal').style.display = 'none';
        }
    </script>
</main>

<?php
require_once '../includes/footer.php';
?>