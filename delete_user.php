<?php
session_start();
include "config.php";

// SECURITY
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("ACCESS DENIED");
}

if (!isset($_GET['id'])) {
    header("Location: users.php?msg=Invalid user ID");
    exit;
}

$id = (int) $_GET['id'];

// 1) DELETE ACTIVITY LOGS
$conn->query("DELETE FROM activity_log WHERE user_id = $id");

// 2) DELETE STOCK REQUESTS
$conn->query("DELETE FROM stock_requests WHERE user_id = $id");

// 3) DELETE MISSING ITEM REQUESTS
$conn->query("DELETE FROM missing_stock_requests WHERE user_id = $id");

// 4) DELETE PURCHASE ORDERS made by user (optional)
// If your PO table has user_id, uncomment below:
// $conn->query("DELETE FROM purchase_orders WHERE created_by = $id");

// 5) FINALLY DELETE THE USER
$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: users.php?msg=User deleted successfully");
    exit;
} else {
    $stmt->close();
    header("Location: users.php?msg=Failed to delete user");
    exit;
}
