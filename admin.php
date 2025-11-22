<?php
// admin.php - Admin book management (CRUD). Restricted to role=admin.
// Session started in header. We check role for access control.
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/data.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$actionMessage = '';

// Handle add/edit/delete based on POST submissions.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'] ?? '';
    if ($mode === 'add') {
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $price = trim($_POST['price'] ?? '0');
        $category = trim($_POST['category'] ?? '');
        $featured = isset($_POST['featured']);
        $description = trim($_POST['description'] ?? '');
        if ($title && $author) {
            $id = addBook($title, $author, $price, $category, $featured, $description);
            $actionMessage = 'Book added with ID ' . $id;
        } else {
            $actionMessage = 'Title and author required.';
        }
    } elseif ($mode === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $price = trim($_POST['price'] ?? '0');
        $category = trim($_POST['category'] ?? '');
        $featured = isset($_POST['featured']);
        $description = trim($_POST['description'] ?? '');
        if ($id && $title && $author) {
            if (updateBook($id, $title, $author, $price, $category, $featured, $description)) {
                $actionMessage = 'Book updated.';
            } else {
                $actionMessage = 'Update failed.';
            }
        } else {
            $actionMessage = 'Missing fields for edit.';
        }
    } elseif ($mode === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id && deleteBook($id)) {
            $actionMessage = 'Book deleted.';
        } else {
            $actionMessage = 'Delete failed.';
        }
    }
}

$books = readBooks();
?>
<main class="container">
    <h2>Admin - Manage Books</h2>
    <?php if ($actionMessage): ?><div class="alert"><?php echo htmlspecialchars($actionMessage); ?></div><?php endif; ?>

    <section class="card">
        <h3>Add New Book</h3>
        <form method="post" class="admin-form">
            <input type="hidden" name="mode" value="add" />
            <label>Title <input type="text" name="title" required /></label>
            <label>Author <input type="text" name="author" required /></label>
            <label>Price <input type="number" step="0.01" name="price" required /></label>
            <label>Category <input type="text" name="category" required /></label>
            <label>Featured <input type="checkbox" name="featured" /></label>
            <label>Description <textarea name="description" rows="2"></textarea></label>
            <button class="btn" type="submit">Add Book</button>
        </form>
    </section>

    <section>
        <h3>Existing Books</h3>
        <div class="grid">
            <?php foreach ($books as $b): ?>
            <div class="card small">
                <h4>#<?php echo $b['id']; ?> <?php echo htmlspecialchars($b['title']); ?></h4>
                <p><?php echo htmlspecialchars($b['author']); ?></p>
                <form method="post" class="inline-form">
                    <input type="hidden" name="mode" value="delete" />
                    <input type="hidden" name="id" value="<?php echo $b['id']; ?>" />
                    <button class="btn-outline" type="submit">Delete</button>
                </form>
                <details>
                    <summary>Edit</summary>
                    <form method="post" class="admin-form">
                        <input type="hidden" name="mode" value="edit" />
                        <input type="hidden" name="id" value="<?php echo $b['id']; ?>" />
                        <label>Title <input type="text" name="title" required value="<?php echo htmlspecialchars($b['title']); ?>" /></label>
                        <label>Author <input type="text" name="author" required value="<?php echo htmlspecialchars($b['author']); ?>" /></label>
                        <label>Price <input type="number" step="0.01" name="price" required value="<?php echo htmlspecialchars($b['price']); ?>" /></label>
                        <label>Category <input type="text" name="category" required value="<?php echo htmlspecialchars($b['category']); ?>" /></label>
                        <label>Featured <input type="checkbox" name="featured" <?php if($b['featured']) echo 'checked'; ?> /></label>
                        <label>Description <textarea name="description" rows="2"><?php echo htmlspecialchars($b['description']); ?></textarea></label>
                        <button class="btn" type="submit">Save</button>
                    </form>
                </details>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>