<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('error', 'You must be an admin to access this page.');
    redirect(BASE_URL . 'auth/login.php');
}

// Handle delete user
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    if ($user_id !== $_SESSION['user_id']) { // Prevent self-deletion
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$user_id])) {
            setFlashMessage('success', 'User deleted successfully.');
        } else {
            setFlashMessage('error', 'Failed to delete user.');
        }
    } else {
        setFlashMessage('error', 'You cannot delete yourself.');
    }
    redirect(BASE_URL . 'admin/manage_users.php');
}

// Handle edit user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $user_id = (int)$_POST['user_id'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $balance = (float)$_POST['balance'];
    $role = $_POST['role'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlashMessage('error', 'Invalid email format.');
    } else {
        $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            setFlashMessage('error', 'Email is already in use.');
        } else {
            $sql = "UPDATE users SET email = ?, full_name = ?, balance = ?, role = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$email, $full_name, $balance, $role, $user_id])) {
                setFlashMessage('success', 'User updated successfully.');
            } else {
                setFlashMessage('error', 'Failed to update user.');
            }
        }
    }
    redirect(BASE_URL . 'admin/manage_users.php');
}

// Fetch all users
$sql = "SELECT id, username, email, full_name, balance, role, created_at FROM users ORDER BY created_at DESC";
$users = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <h2>Manage Users</h2>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Full Name</th>
            <th>Balance</th>
            <th>Role</th>
            <th>Joined</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($user['full_name'] ?: 'Not set', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo number_format($user['balance'], 2); ?></td>
                <td><?php echo htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo $user['created_at']; ?></td>
                <td>
                    <button onclick="editUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($user['full_name'] ?: '', ENT_QUOTES, 'UTF-8'); ?>','<?php echo htmlspecialchars($user['balance'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo $user['role']; ?>')">Edit</button>
                    <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                        <button class="btn btn-danger" onclick="if(confirm('Are you sure you want to delete this user?')){window.location.href='<?php echo BASE_URL; ?>admin/manage_users.php?action=delete&id=<?php echo $user['id']; ?>';}">Delete</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Edit User Modal -->
    <div id="editUserModal" style="display: none; position: fixed; top: 20%; left: 50%; transform: translateX(-50%); background: white; padding: 20px; border: 1px solid #ccc;">
        <h3>Edit User</h3>
        <form method="POST" action="">
            <input type="hidden" name="user_id" id="edit_user_id">
            <div>
                <label for="edit_email">Email:</label>
                <input type="email" id="edit_email" name="email" required>
            </div>
            <div>
                <label for="edit_full_name">Full Name:</label>
                <input type="text" id="edit_full_name" name="full_name">
            </div>
            <div>
                <label for="edit_balance">Balance:</label>
                <input type="number" id="edit_balance" name="balance">
            </div>
            <div>
                <label for="edit_role">Role:</label>
                <select id="edit_role" name="role">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" name="edit_user">Save Changes</button>
            <button type="button" onclick="closeModal()">Cancel</button>
        </form>
    </div>

    <script>
        function editUser(id, email, full_name, balance, role) {
            document.getElementById('edit_user_id').value = id;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_full_name').value = full_name;
            document.getElementById('edit_balance').value = parseFloat(balance).toFixed(2);;
            document.getElementById('edit_role').value = role;
            document.getElementById('editUserModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editUserModal').style.display = 'none';
        }
    </script>
</main>

<?php
require_once '../includes/footer.php';
?>