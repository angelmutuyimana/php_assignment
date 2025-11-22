<?php
// nav.php - navigation bar
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>
<nav class="navbar">
    <div class="nav-left">
    <a class="brand" href="index.php">AngelBookStore</a>
    </div>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <?php if(!$user): ?>
            <a href="login.php">Login</a>
            <a href="signup.php">Signup</a>
        <?php else: ?>
            <?php if($user['role']==='admin'): ?>
                <a href="admin_dashboard.php">Admin Dashboard</a>
                <a href="admin.php">Manage Books</a>
                <a href="admin_users.php">Manage Users</a>
            <?php else: ?>
                <a href="dashboard.php">My Dashboard</a>
            <?php endif; ?>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        <?php endif; ?>
    </div>
</nav>