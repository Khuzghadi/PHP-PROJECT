<?php
session_start();
include "config.php";

// ONLY ADMIN
if (!isset($_SESSION['user_id']) && $_SESSION['role'] != 'admin') {
    die("ACCESS DENIED");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // FETCH PRODUCT NAME FOR LOG
    $prod = $conn->query("SELECT name FROM products WHERE product_id=$id")->fetch_assoc();
    $pname = $prod['name'];

    // DELETE PRODUCT
    $conn->query("DELETE FROM products WHERE product_id=$id");

    // LOG
    $conn->query("
        INSERT INTO activity_log(user_id, action)
        VALUES(".$_SESSION['user_id'].", 'Deleted product: $pname')
    ");

    header("Location: view_product.php?msg=deleted");
    exit;
}
?>
