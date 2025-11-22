<?php
// admin_dashboard.php - Overview for admin users: stats & recent purchases
// Access control: only role=admin
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/data.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$conn = get_db();
// Count books
$booksCount = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM books"))[0];
// Count users
$usersCount = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
// Count purchases
$purchasesCount = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM purchases"))[0];
// Recent purchases (limit 5)
$recentSql = "SELECT p.id, p.purchase_date, u.email, b.title FROM purchases p JOIN users u ON p.user_id=u.id JOIN books b ON p.book_id=b.id ORDER BY p.purchase_date DESC LIMIT 5";
$recentRes = mysqli_query($conn, $recentSql);
$recent = [];
if ($recentRes) {
    while ($row = mysqli_fetch_assoc($recentRes)) { $recent[] = $row; }
    mysqli_free_result($recentRes);
}
?>
<main class="container">
    <h2>Admin Dashboard</h2>
    <section class="grid" style="grid-template-columns: repeat(auto-fill,minmax(200px,1fr));">
        <div class="card"><h3>Books</h3><p><?php echo $booksCount; ?> total</p></div>
        <div class="card"><h3>Users</h3><p><?php echo $usersCount; ?> total</p></div>
        <div class="card"><h3>Purchases</h3><p><?php echo $purchasesCount; ?> total</p></div>
    </section>

    <section class="card" style="margin-top:1.5rem;">
        <h3>Recent Purchases</h3>
        <?php if(empty($recent)): ?>
            <p>No purchases yet.</p>
        <?php else: ?>
            <table style="width:100%; border-collapse:collapse; font-size:0.8rem;">
                <thead>
                    <tr style="text-align:left; border-bottom:1px solid #ddd;">
                        <th>ID</th><th>Date</th><th>User</th><th>Book</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($recent as $r): ?>
                    <tr style="border-bottom:1px solid #eee;">
                        <td><?php echo $r['id']; ?></td>
                        <td><?php echo htmlspecialchars($r['purchase_date']); ?></td>
                        <td><?php echo htmlspecialchars($r['email']); ?></td>
                        <td><?php echo htmlspecialchars($r['title']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <section style="margin-top:1.5rem;">
        <a class="btn" href="admin.php">Manage Books</a>
        <a class="btn-outline" href="admin_users.php">Manage Users</a>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>