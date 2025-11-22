<?php
// signup.php - User registration page
// Explains each step with comments for beginners.
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/data.php';

$message = '';
$errors = [];
// If form submitted we process input
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Get form fields safely
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $role = trim($_POST['role'] ?? 'buyer'); // role selected by user

    // 2. Validate requirements
    if ($name === '') { $errors['name'] = 'Full name is required.'; }

    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    }

    if ($password === '') {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    }

    if ($confirm === '') {
        $errors['confirm_password'] = 'Please confirm your password.';
    } elseif ($password !== $confirm) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    if ($role !== 'admin' && $role !== 'buyer') { $role = 'buyer'; }

    if (empty($errors)) {
        // 3. Call helper to register WITH role
        $result = registerUser($name, $email, $password, $role);
        if ($result['success']) {
            // 4. Do NOT auto-login; redirect to login page with success flag
            header('Location: login.php?registered=1');
            exit;
        } else {
            // Show backend message (e.g., email already registered)
            $message = $result['message'];
        }
    }
}
?>
<style>
/* Page-scoped override: make only the Sign Up button green */
.fb-btn-primary { background:#42b72a !important; }
.fb-btn-primary:hover { background:#36a420 !important; }
/* small field error styling */
.field-error { color:#d32f2f; font-size:0.75rem; margin-top:0.3rem; }
.alert-errors { background:#fdecea; color:#611a15; border:1px solid #f5c6cb; padding:0.6rem 0.8rem; border-radius:6px; margin-bottom:0.9rem; font-size:0.85rem; }
</style>
<div class="auth-wrapper">
    <div class="fb-card">
    <div class="fb-logo">AngelBookStore</div>
        <h2>Create Account</h2>
        <p class="sub">Join and start exploring books.</p>
        <?php if (!empty($errors)): ?>
            <div class="alert-errors">
                <strong>Please fix the following:</strong>
                <ul style="margin:0.4rem 0 0 1rem;">
                    <?php foreach($errors as $e): ?>
                        <li><?php echo htmlspecialchars($e); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if ($message): ?><div class="alert" style="font-size:0.85rem;">&lt;?php echo htmlspecialchars($message); ?&gt;</div><?php endif; ?>
        <form method="post">
            <div class="fb-input-group">
                <label for="name">Full Name</label>
                <input id="name" type="text" name="name" placeholder="John Doe" required value="<?php echo isset($name)?htmlspecialchars($name):''; ?>" />
                <?php if(isset($errors['name'])): ?><div class="field-error"><?php echo htmlspecialchars($errors['name']); ?></div><?php endif; ?>
            </div>
            <div class="fb-input-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" placeholder="user@example.com" required value="<?php echo isset($email)?htmlspecialchars($email):''; ?>" />
                <?php if(isset($errors['email'])): ?><div class="field-error"><?php echo htmlspecialchars($errors['email']); ?></div><?php endif; ?>
            </div>
            <div class="fb-input-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="Choose a secure password" minlength="6" required />
                <?php if(isset($errors['password'])): ?><div class="field-error"><?php echo htmlspecialchars($errors['password']); ?></div><?php endif; ?>
            </div>
            <div class="fb-input-group">
                <label for="confirm_password">Confirm Password</label>
                <input id="confirm_password" type="password" name="confirm_password" placeholder="Repeat password" minlength="6" required />
                <?php if(isset($errors['confirm_password'])): ?><div class="field-error"><?php echo htmlspecialchars($errors['confirm_password']); ?></div><?php endif; ?>
            </div>
            <div class="fb-input-group">
                <label>Role</label>
                <div class="fb-role-group">
                    <label><input type="radio" name="role" value="buyer" <?php if(!isset($role) || $role==='buyer') echo 'checked'; ?> /> Buyer</label>
                    <label><input type="radio" name="role" value="admin" <?php if(isset($role) && $role==='admin') echo 'checked'; ?> /> Admin</label>
                </div>
                <div class="fb-small-note">Admins manage books & users. Buyers purchase books.</div>
            </div>
            <button class="fb-btn-primary" type="submit">Sign Up</button>
            <div class="fb-link-row" style="margin-top:0.9rem;">Already have an account? <a href="login.php">Log In</a></div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>