<?php
// book.php - Book details page with purchase option.
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/data.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$book = getBookById($id);
if (!$book) {
    echo '<main class="container"><p>Book not found.</p></main>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

// Handle purchase via query param buy=1 for simplicity.
$purchaseMessage = '';
if (isset($_GET['buy']) && isset($_SESSION['user'])) {
    // Store purchase in DB (user_id, book_id)
    $did = purchaseBook($_SESSION['user']['id'], $book['id']);
    if ($did) {
        $purchaseMessage = 'Purchase recorded!';
    } else {
        $purchaseMessage = 'You already purchased this book.';
    }
}

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$alreadyBought = false;
if ($user) {
    $ids = getPurchasedBookIds($user['id']);
    $alreadyBought = in_array((int)$book['id'], $ids);
}
?>
<main class="container">
    <div class="book-detail card">
        <div class="detail-img">
            <img src="<?php echo htmlspecialchars($book['image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" />
        </div>
        <div class="detail-body">
            <h2><?php echo htmlspecialchars($book['title']); ?></h2>
            <p class="author">By <?php echo htmlspecialchars($book['author']); ?></p>
            <p class="price">Price: $<?php echo number_format($book['price'], 2); ?></p>
            <p class="description"><?php echo htmlspecialchars($book['description']); ?></p>
            <?php if ($purchaseMessage): ?><div class="alert"><?php echo htmlspecialchars($purchaseMessage); ?></div><?php endif; ?>
            <?php if($user): ?>
                <?php if(!$alreadyBought): ?>
                    <a class="btn" href="book.php?id=<?php echo $book['id']; ?>&buy=1">Buy</a>
                <?php else: ?>
                    <p class="info">You own this book.</p>
                <?php endif; ?>
            <?php else: ?>
                <p class="info">Please <a href="login.php">login</a> to purchase.</p>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>