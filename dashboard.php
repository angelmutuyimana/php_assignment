<?php
// dashboard.php - User dashboard must be protected by login.
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/data.php';

// BASIC ACCESS CONTROL: if not logged in redirect to login.
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'buyer') {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];
$books = readBooks();
// Fetch purchased book IDs for this user from DB
$purchasedIds = getPurchasedBookIds($user['id']);

// Separate purchased and available using IDs
$purchasedBooks = array_filter($books, function($b) use ($purchasedIds){ return in_array((int)$b['id'], $purchasedIds); });
$availableBooks = array_filter($books, function($b) use ($purchasedIds){ return !in_array((int)$b['id'], $purchasedIds); });
?>
<main class="container">
    <h2>Buyer Dashboard</h2>
    <section class="user-info card">
        <h3>Your Info</h3>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    </section>

    <section>
        <h3>Purchased Books</h3>
        <?php if (empty($purchasedBooks)): ?>
            <p>You have not purchased any books yet.</p>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($purchasedBooks as $book): ?>
                    <div class="card small">
                        <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                        <p class="author">By <?php echo htmlspecialchars($book['author']); ?></p>
                        <a class="btn-outline" href="book.php?id=<?php echo $book['id']; ?>">View</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section>
        <h3>Available Books</h3>
        <div class="grid">
            <?php foreach ($availableBooks as $book): ?>
                <div class="card small">
                    <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                    <p class="author">By <?php echo htmlspecialchars($book['author']); ?></p>
                    <p class="price">$<?php echo number_format($book['price'], 2); ?></p>
                    <a class="btn" href="book.php?id=<?php echo $book['id']; ?>">Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>