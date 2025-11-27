<?php
session_start();
include "config.php";
include "auth_check.php"; 
// ONLY ADMIN CAN ACCESS
if (!isset($_SESSION['user_id'])) { header("Location: loginForm.html?msg=Please login");
exit; }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("ACCESS DENIED");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user_id = $_POST['user_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $type = $_POST['request_type'];

    $stmt = $conn->prepare("
        INSERT INTO stock_requests (user_id, product_id, quantity, request_type, status)
        VALUES (?, ?, ?, ?, 'Pending')
    ");
    $stmt->bind_param("iiis", $user_id, $product_id, $quantity, $type);
    $stmt->execute();
    $stmt->close();

    header("Location: users.php?msg=" . urlencode("Request raised for user"));
    exit;
}
?>
