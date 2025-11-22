<?php
// data.php - Database-backed helper functions (mysqli, procedural)
// This replaces the JSON approach with MySQL storage for books, users, purchases.
// Comments explain connection, query, and data retrieval at each step.

require_once __DIR__ . '/db.php';

// ----------------------
// Book helpers
// ----------------------
function readBooks() {
    // 1) Get connection
    $conn = get_db();
    // 2) Prepare and execute SQL
    $sql = "SELECT id, title, author, price, category, image, featured, description FROM books ORDER BY id";
    $res = mysqli_query($conn, $sql);
    if (!$res) {
        die('Query error: ' . mysqli_error($conn));
    }
    // 3) Fetch all rows as associative array
    $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
    mysqli_free_result($res);
    return $rows;
}

function getBookById($id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "SELECT id, title, author, price, category, image, featured, description FROM books WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_free_result($res);
    mysqli_stmt_close($stmt);
    return $row ? $row : null;
}

function addBook($title, $author, $price, $category, $featured, $description, $imagePath = null) {
    // Allows providing a custom image path (uploaded file). Falls back to placeholder.
    $conn = get_db();
    $image = $imagePath ? $imagePath : 'assets/images/placeholder.jpg';
    $stmt = mysqli_prepare($conn, "INSERT INTO books (title, author, price, category, image, featured, description) VALUES (?,?,?,?,?,?,?)");
    $feat = $featured ? 1 : 0;
    $price = (float)$price;
    mysqli_stmt_bind_param($stmt, 'ssdssis', $title, $author, $price, $category, $image, $feat, $description);
    mysqli_stmt_execute($stmt);
    $newId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    return $newId;
}

function updateBook($id, $title, $author, $price, $category, $featured, $description) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "UPDATE books SET title=?, author=?, price=?, category=?, featured=?, description=? WHERE id=?");
    $feat = $featured ? 1 : 0;
    $price = (float)$price;
    mysqli_stmt_bind_param($stmt, 'ssdsisi', $title, $author, $price, $category, $feat, $description, $id);
    $ok = mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $ok && $affected >= 0; // true even if values unchanged
}

function deleteBook($id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "DELETE FROM books WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $affected > 0;
}

// ----------------------
// User helpers
// ----------------------
function findUserByEmail($email) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "SELECT id, name, email, password, role FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_free_result($res);
    mysqli_stmt_close($stmt);
    return $row ? $row : null;
}

function registerUser($name, $email, $password, $role = 'buyer') {
    // Ensure role is valid (admin or buyer)
    $role = ($role === 'admin') ? 'admin' : 'buyer';
    if (findUserByEmail($email)) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    $conn = get_db();
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password, role) VALUES (?,?,?,?)");
    mysqli_stmt_bind_param($stmt, 'ssss', $name, $email, $hashed, $role);
    $ok = mysqli_stmt_execute($stmt);
    if (!$ok) {
        $msg = 'Signup failed: ' . mysqli_error($conn);
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => $msg];
    }
    mysqli_stmt_close($stmt);
    return ['success' => true, 'message' => 'Signup successful'];
}

function authenticateUser($email, $password) {
    $row = findUserByEmail($email);
    if ($row && password_verify($password, $row['password'])) {
        return [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'email' => $row['email'],
            'role' => $row['role']
        ];
    }
    return null;
}

// ----------------------
// Purchases helpers
// ----------------------
function getPurchasedBookIds($userId) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "SELECT book_id FROM purchases WHERE user_id = ? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $ids = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $ids[] = (int)$row['book_id'];
    }
    mysqli_free_result($res);
    mysqli_stmt_close($stmt);
    return $ids;
}

function purchaseBook($userId, $bookId) {
    $conn = get_db();
    // Use INSERT IGNORE to avoid duplicate entries if unique constraint exists.
    $stmt = mysqli_prepare($conn, "INSERT IGNORE INTO purchases (user_id, book_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, 'ii', $userId, $bookId);
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $affected > 0;
}

// ----------------------
// Additional User CRUD (admin and profile updates)
// ----------------------
function listUsers() {
    $conn = get_db();
    $sql = "SELECT id, name, email, role, created_at FROM users ORDER BY id";
    $res = mysqli_query($conn, $sql);
    if (!$res) { die('Query error: ' . mysqli_error($conn)); }
    $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
    mysqli_free_result($res);
    return $rows;
}

function getUserById($id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "SELECT id, name, email, role FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_free_result($res);
    mysqli_stmt_close($stmt);
    return $row ? $row : null;
}

function updateUserProfile($id, $name, $email, $newPassword = null) {
    $conn = get_db();
    // Check email uniqueness for another user
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? AND id <> ?");
    mysqli_stmt_bind_param($stmt, 'si', $email, $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $exists = mysqli_fetch_assoc($res);
    mysqli_free_result($res);
    mysqli_stmt_close($stmt);
    if ($exists) {
        return ['success' => false, 'message' => 'Email already in use by another account'];
    }

    if ($newPassword && trim($newPassword) !== '') {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "UPDATE users SET name=?, email=?, password=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'sssi', $name, $email, $hashed, $id);
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE users SET name=?, email=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'ssi', $name, $email, $id);
    }
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    if (!$ok) {
        return ['success' => false, 'message' => 'Update failed'];
    }
    return ['success' => true, 'message' => 'Profile updated'];
}

function deleteUser($id) {
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $affected > 0;
}

function updateUserRole($id, $role) {
    $role = ($role === 'admin') ? 'admin' : 'buyer';
    $conn = get_db();
    $stmt = mysqli_prepare($conn, "UPDATE users SET role = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'si', $role, $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

?>

?>