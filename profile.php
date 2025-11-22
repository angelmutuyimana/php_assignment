<?php
// profile.php - User can update name, email, and optionally password
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/data.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');

    if ($name === '' || $email === '') {
        $message = 'Name and Email are required.';
    } elseif ($password !== '' && $password !== $confirm) {
        $message = 'Passwords do not match.';
    } else {
        $res = updateUserProfile($user['id'], $name, $email, $password !== '' ? $password : null);
        if ($res['success']) {
            // Refresh session user info (id, name, email, is_admin)
            $fresh = getUserById($user['id']);
            $_SESSION['user'] = $fresh;
            $user = $fresh;
            $message = 'Profile updated successfully';
        } else {
            $message = $res['message'];
        }
    }
}
?>
<main class="container auth">
    <h2>Your Profile</h2>
    <?php if ($message): ?><div class="alert"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <form method="post" class="card form-card">
        <label>Name
            <input type="text" name="name" required value="<?php echo htmlspecialchars($user['name']); ?>" />
        </label>
        <label>Email
            <input type="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>" />
        </label>
        <label>New Password (optional)
            <input type="password" name="password" />
        </label>
        <label>Confirm New Password
            <input type="password" name="confirm_password" />
        </label>
        <button class="btn" type="submit">Save Changes</button>
    </form>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>