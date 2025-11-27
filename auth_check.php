<?php
// ALWAYS start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include "config.php";

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please login");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Check if user still exists
$sql = $conn->query("SELECT force_logout FROM users WHERE user_id = $user_id");

// If user is deleted or missing in DB -> logout immediately
if ($sql->num_rows == 0) {
    session_destroy();
    header("Location: loginForm.html?msg=Account no longer exists");
    exit;
}

$check = $sql->fetch_assoc();

// Force logout system
if ($check['force_logout'] == 1) {
    session_destroy();
    header("Location: loginForm.html?msg=Session expired, please login again");
    exit;
}
?>
