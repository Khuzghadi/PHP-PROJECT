<?php
session_start();
include "config.php";

// ALLOW ONLY ADMIN & STAFF
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$message = "";

// WHEN FORM SUBMITTED
if (isset($_POST['submit'])) {
    $product_id = $_POST['product_id'];
    $qty = $_POST['quantity'];

    if ($qty <= 0) {
        $message = "<div class='alert alert-danger'>Quantity must be greater than 0.</div>";
    } else {
        // UPDATE PRODUCT STOCK
        $conn->query("UPDATE products SET quantity = quantity + $qty WHERE product_id = $product_id");

        // INSERT TRANSACTION LOG
        $conn->query("
            INSERT INTO transactions(product_id, type, quantity) 
            VALUES($product_id, 'in', $qty)
        ");

        // OPTIONAL: INSERT ACTIVITY LOG
        $conn->query("
            INSERT INTO activity_log(user_id, action) 
            VALUES(".$_SESSION['user_id'].", 'Stock In: +$qty for Product ID $product_id')
        ");

        $message = "<div class='alert alert-success'>Stock added successfully!</div>";
    }
}

// GET LIST OF PRODUCTS
$products = $conn->query("SELECT * FROM products ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Stock In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<?php include "admin_navbar.php"; ?>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Stock In (Add Stock)</h4>
        </div>
        <div class="card-body">

            <?= $message ?>

            <form method="POST">

                <!-- PRODUCT SELECTION -->
                <div class="mb-3">
                    <label class="form-label">Select Product:</label>
                    <select name="product_id" class="form-control" required>
                        <option value="">-- Choose Product --</option>
                        <?php while ($p = $products->fetch_assoc()): ?>
                            <option value="<?= $p['product_id'] ?>">
    <?= $p['name'] ?>
    — Qty: <?= $p['quantity'] ?>
    — ₹<?= $p['price'] ?>/<?= $p['price_type'] ?>
</option>

                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- QUANTITY INPUT -->
                <div class="mb-3">
                    <label class="form-label">Quantity to Add:</label>
                    <input type="number" name="quantity" class="form-control" min="1" required>
                </div>

                <!-- SUBMIT BUTTON -->
                <button name="submit" class="btn btn-success w-100">Add Stock</button>

            </form>
        </div>
    </div>
</div>

</body>
</html>
