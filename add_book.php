<?php
// add_book.php - Dedicated form for adding a new book with image upload.
// Access restricted to admin role.
// Beginner friendly: procedural PHP + comments.
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/data.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$message = '';
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Collect and trim form inputs
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $featured = isset($_POST['featured']);
    $description = trim($_POST['description'] ?? '');

    // 2. Validate required fields
    if ($title === '') $errors[] = 'Title is required';
    if ($author === '') $errors[] = 'Author is required';
    if ($price === '' || !is_numeric($price)) $errors[] = 'Valid price is required';
    if ($category === '') $errors[] = 'Category is required';
    if ($description === '') $errors[] = 'Description is required';

    // 3. Handle file upload (optional but recommended)
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['image']['tmp_name'];
            $originalName = basename($_FILES['image']['name']);
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (!in_array($ext, $allowed)) {
                $errors[] = 'Invalid image type. Allowed: jpg,jpeg,png,gif,webp';
            } else {
                // Generate safe unique filename
                $safeBase = preg_replace('/[^a-zA-Z0-9_-]/','_', pathinfo($originalName, PATHINFO_FILENAME));
                $newName = $safeBase . '_' . time() . '.' . $ext;
                $destDir = __DIR__ . '/assets/images/';
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0775, true);
                }
                $destPath = $destDir . $newName;
                if (move_uploaded_file($tmp, $destPath)) {
                    // Use relative path stored in DB
                    $imagePath = 'assets/images/' . $newName;
                } else {
                    $errors[] = 'Failed to move uploaded image.';
                }
            }
        } else {
            $errors[] = 'Image upload error code: ' . $_FILES['image']['error'];
        }
    }

    // 4. If no errors, insert into DB
    if (empty($errors)) {
        $newId = addBook($title, $author, $price, $category, $featured, $description, $imagePath);
        if ($newId) {
            $message = 'Book added successfully (ID ' . $newId . ')';
            // Clear form values after success
            $title = $author = $price = $category = $description = '';
            $featured = false;
        } else {
            $errors[] = 'Insert failed.';
        }
    }
}
?>
<main class="container">
    <h2>Add New Book</h2>
    <p><a class="btn-outline" href="admin.php">← Back to Book List</a></p>
    <?php if ($message): ?><div class="alert" style="background:#d4f8d4; color:#084c0c;"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <?php if ($errors): ?>
        <div class="alert" style="background:#ffe0e0; color:#820000;">
            <strong>Errors:</strong>
            <ul style="margin:0.4rem 0 0; padding-left:1.1rem;">
                <?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data" class="card book-form">
        <div class="form-grid">
            <label>Title
                <input type="text" name="title" placeholder="e.g. Clean Code" required value="<?php echo htmlspecialchars($title ?? ''); ?>" />
            </label>
            <label>Author
                <input type="text" name="author" placeholder="e.g. Robert C. Martin" required value="<?php echo htmlspecialchars($author ?? ''); ?>" />
            </label>
            <label>Price
                <input type="number" step="0.01" name="price" placeholder="e.g. 29.99" required value="<?php echo htmlspecialchars($price ?? ''); ?>" />
            </label>
            <label>Category
                <select name="category" required>
                    <option value="" disabled <?php if(empty($category)) echo 'selected'; ?>>Select category</option>
                    <?php $cats = ['Programming','Fiction','Self-Help','Science','History','Business'];
                    foreach($cats as $c): ?>
                        <option value="<?php echo $c; ?>" <?php if(($category ?? '') === $c) echo 'selected'; ?>><?php echo $c; ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="featured-label">
                <input type="checkbox" name="featured" <?php if(!empty($featured)) echo 'checked'; ?> /> Featured
            </label>
            <label class="image-upload">Book Image
                <input type="file" name="image" accept="image/*" />
            </label>
        </div>
        <label>Description
            <textarea name="description" rows="5" placeholder="Brief summary or marketing description" required><?php echo htmlspecialchars($description ?? ''); ?></textarea>
        </label>
        <button class="btn" type="submit">Add Book</button>
    </form>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>