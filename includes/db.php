<?php
// includes/db.php - MySQL connection using mysqli (procedural, beginner-friendly)
// 1) Configure your credentials below if different on your machine.
//    XAMPP default: user 'root', password '' (empty), host 'localhost'.
// 2) We create (or expect) a database named 'bookstore'.

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'bookstore';

/**
 * get_db - returns a shared mysqli connection.
 * We keep a static connection so we do not reconnect on every call.
 */
function get_db() {
    static $conn = null;
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
    if ($conn === null) {
        // Create connection
        $conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
        // If DB does not exist yet, connection may fail. You can import db.sql first.
        if (!$conn) {
            die('Database connection failed: ' . mysqli_connect_error());
        }
        // Set charset to UTF-8 for safety
        mysqli_set_charset($conn, 'utf8mb4');
    }
    return $conn;
}
?>