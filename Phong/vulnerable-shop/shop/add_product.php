<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('error', 'You must be an admin to add products.');
    redirect(BASE_URL . 'auth/login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = (float)$_POST['price'];
    $user_id = $_SESSION['user_id'];

    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = UPLOAD_PATH . 'products/';
        $image_name = basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = $image_name;
        } else {
            setFlashMessage('error', 'Failed to upload image.');
        }
    }

    if (!empty($name) && $price > 0) {
        $sql = "INSERT INTO products (name, description, price, image, created_by) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$name, $description, $price, $image, $user_id])) {
            setFlashMessage('success', 'Product added successfully.');
            redirect(BASE_URL . 'shop/products.php');
        } else {
            setFlashMessage('error', 'Failed to add product.');
        }
    } else {
        setFlashMessage('error', 'Invalid product name or price.');
    }
}
?>
<!DOCTYPE html>
<html lang="en">        
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/add_product.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<main>
    <h2>Add New Product</h2>
    <form method="POST" enctype="multipart/form-data">
        <div>
            <label for="name">Product Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div>
            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea>
        </div>
        <div>
            <label for="price">Price ($):</label>
            <input type="number" id="price" name="price" step="0.01" required>
        </div>
        <div>
            <label for="image">Product Image:</label>
            <input type="file" id="image" name="image">
        </div>
        <button type="submit" onclick="return confirm('Are you sure you want to add this product?');">Add Product</button>
    </form>
    <br>
    <p><a href="<?php echo BASE_URL; ?>index.php" class="btn" >Back to Products</a></p>
</main>
</html>

<?php
require_once '../includes/footer.php';
?>