<?php
// admin_users.php - Admin can view all users, change roles, and delete users
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/data.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$selfId = $_SESSION['user']['id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'] ?? '';
    if ($mode === 'toggle_role') {
        $id = (int)($_POST['id'] ?? 0);
        $to = $_POST['to'] ?? 'buyer';
        if ($id && $id !== $selfId) {
            if (updateUserRole($id, $to)) {
                $message = 'User role updated';
            } else {
                $message = 'Failed to update role';
            }
        } else {
            $message = 'Cannot change your own role here.';
        }
    } elseif ($mode === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id && $id !== $selfId) {
            if (deleteUser($id)) {
                $message = 'User deleted';
            } else {
                $message = 'Delete failed';
            }
        } else {
            $message = 'Cannot delete your own account via admin.';
        }
    }
}

$users = listUsers();
?>
<main class="container">
    <h2>Admin - Users</h2>
    <?php if ($message): ?><div class="alert"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <div class="grid">
        <?php foreach ($users as $u): ?>
            <div class="card small">
                <h4>#<?php echo $u['id']; ?> <?php echo htmlspecialchars($u['name']); ?></h4>
                <p><?php echo htmlspecialchars($u['email']); ?></p>
                <p>Role: <?php echo htmlspecialchars($u['role']); ?></p>
                <div>
                    <?php if ($u['id'] !== $selfId): ?>
                    <form method="post" class="inline-form" style="margin-right:6px;">
                        <input type="hidden" name="mode" value="toggle_role" />
                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>" />
                        <input type="hidden" name="to" value="<?php echo $u['role'] === 'admin' ? 'buyer' : 'admin'; ?>" />
                        <button class="btn-outline" type="submit"><?php echo $u['role'] === 'admin' ? 'Set Buyer' : 'Set Admin'; ?></button>
                    </form>
                    <form method="post" class="inline-form">
                        <input type="hidden" name="mode" value="delete" />
                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>" />
                        <button class="btn-outline" type="submit">Delete</button>
                    </form>
                    <?php else: ?>
                        <span class="info">(You)</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>