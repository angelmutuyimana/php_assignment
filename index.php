<?php
// index.php - Home Page
// Shows featured books & list of categories.
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/data.php';
$books = readBooks();
$featured = array_filter($books, function($b){ return !empty($b['featured']); });
$categories = [];
foreach ($books as $b) { $categories[$b['category']] = true; }
?>
<main class="container">
    <section class="hero">
    <h1>Welcome to AngelBookStore</h1>
        <p>Discover books across multiple categories. Simple demo app for learning PHP.</p>
    </section>

    <section class="featured">
        <h2>Featured Books</h2>
        <div class="grid">
            <?php foreach ($featured as $book): ?>
                <div class="card">
                    <div class="card-img">
                        <img src="<?php echo htmlspecialchars($book['image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" />
                    </div>
                    <div class="card-body">
                        <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p class="author">By <?php echo htmlspecialchars($book['author']); ?></p>
                        <p class="price">$<?php echo number_format($book['price'], 2); ?></p>
                        <a class="btn" href="book.php?id=<?php echo $book['id']; ?>">Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="categories">
        <h2>Categories</h2>
        <div class="chip-row">
            <?php foreach (array_keys($categories) as $cat): ?>
                <span class="chip"><?php echo htmlspecialchars($cat); ?></span>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="cta">
        <h2>Get Started</h2>
        <p><?php if(!isset($_SESSION['user'])): ?>
            <a class="btn" href="signup.php">Create an Account</a> or <a class="btn-outline" href="login.php">Login</a>
        <?php else: ?>
            <a class="btn" href="dashboard.php">Go to Dashboard</a>
        <?php endif; ?></p>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>