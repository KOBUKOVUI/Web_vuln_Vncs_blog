<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('error', 'You must be an admin to access this page.');
    redirect(BASE_URL . 'auth/login.php');
}

// Handle delete comment
if (isset($_GET['action']) && $_GET['action'] === 'delete' &&   isset($_GET['id'])) {
    $comment_id = (int)$_GET['id'];
    $sql = "DELETE FROM comments WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$comment_id])) {
        setFlashMessage('success', 'Comment deleted successfully.');
    } else {
        setFlashMessage('error', 'Failed to delete comment.');
    }
    redirect(BASE_URL . 'admin/manage_comments.php');
}   
// Handle edit comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_comment'])) {
    $comment_id = (int)$_POST['comment_id'];
    $content = $_POST['content'];

    if (empty($content)) {
        setFlashMessage('error', 'Comment content cannot be empty.');
    } else {
        $sql = "UPDATE comments SET content = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$content, $comment_id])) {
            setFlashMessage('success', 'Comment updated successfully.');
        } else {
            setFlashMessage('error', 'Failed to update comment.');
        }
    }
    redirect(BASE_URL . 'admin/manage_comments.php');
}

// Fetch all comments
$sql = "
SELECT 
    comments.id AS comment_id,
    products.name AS product_name,
    users.username,
    comments.content,
    comments.created_at
FROM comments
JOIN users ON comments.user_id = users.id
JOIN products ON comments.product_id = products.id
ORDER BY comments.id ASC
";
$comments = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>  
<!DOCTYPE html>
<html lang="en">        
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/edit_comment.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
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
    <h2>Manage Comments</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Username</th>
                    <th>Comment</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($comment['comment_id']); ?></td>
                        <td><?php echo htmlspecialchars($comment['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($comment['username']); ?></td>
                        <td><?php echo htmlspecialchars($comment['content']); ?></td>
                        <td><?php echo htmlspecialchars($comment['created_at']); ?></td>
                        <td>
                            <button onclick="editComment(<?php echo $comment['comment_id']; ?>, '<?php echo htmlspecialchars($comment['content'] ?: '', ENT_QUOTES, 'UTF-8'); ?>')" class="btn btn-primary">Edit</button>
                            <a href="<?php echo BASE_URL; ?>admin/manage_comments.php?action=delete&id=<?php echo $comment['comment_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this comment?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <!-- Edit Comment Modal -->
     <div id="editCommentModal" style="display: none; position: fixed; top: 20%; left: 50%; transform: translateX(-50%); background: white; padding: 20px; border: 1px solid #ccc;">
        <h3>Edit Comment</h3>
        <form method="POST" action="">
            <input type="hidden" name="comment_id" id="edit_comment_id">
            <div>
                <label for="edit_content">Content:</label>
                <input type="text" id="edit_content" name="content" required>
            </div>
            <button type="submit" name="edit_comment" class="btn" >Save Changes</button>
            <button type="button" class="btn" onclick="closeModal()">Cancel</button>
        </form>
    </div>
    <script>
        function editComment(id, content) {
            document.getElementById('edit_comment_id').value = id;
            document.getElementById('edit_content').value = content;
            document.getElementById('editCommentModal').style.display = 'block';
        }
        function closeModal() {
            document.getElementById('editCommentModal').style.display = 'none';
        }
    </script>
    
</html>

<?php
require_once '../includes/footer.php';
?>