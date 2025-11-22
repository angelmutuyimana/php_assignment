<?php
// header.php - common page header
// START SESSION ON EVERY PAGE THAT INCLUDES THIS
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Allows us to use $_SESSION for login tracking
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AngelBookStore</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
<?php include __DIR__ . '/nav.php'; ?>