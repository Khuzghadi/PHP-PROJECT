<?php
session_start();
include "config.php";

// ALLOW ONLY LOGGED-IN USERS (ADMIN & STAFF)
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$message = "";

// WHEN FORM SUBMITTED
if (isset($_POST['submit'])) {
    $product_id = $_POST['product_id'];
    $qty = $_POST['quantity'];

    // Get current product quantity
    $current = $conn->query("SELECT quantity FROM products WHERE product_id=$product_id")
                    ->fetch_assoc()['quantity'];

    if ($qty <= 0) {
        $message = "<div class='alert alert-danger'>Quantity must be greater than 0.</div>";
    } 
    else if ($qty > $current) {
        $message = "<div class='alert alert-danger'>Not enough stock! Current stock: $current</div>";
    } 
    else {

        // UPDATE STOCK
        $conn->query("UPDATE products SET quantity = quantity - $qty WHERE product_id = $product_id");

        // INSERT TRANSACTION
        $conn->query("
            INSERT INTO transactions(product_id, type, quantity)
            VALUES($product_id, 'out', $qty)
        ");

        // OPTIONAL: Insert activity log
        $conn->query("
            INSERT INTO activity_log(user_id, action)
            VALUES(".$_SESSION['user_id'].", 'Stock Out: -$qty for Product ID $product_id')
        ");

        $message = "<div class='alert alert-success'>Stock reduced successfully!</div>";
    }
}

// GET PRODUCT LIST
$products = $conn->query("SELECT * FROM products ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Stock Out</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<?php include "admin_navbar.php"; ?>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">Stock Out (Remove Stock)</h4>
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
                                <?= $p['name'] ?> (Current: <?= $p['quantity'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- QUANTITY INPUT -->
                <div class="mb-3">
                    <label class="form-label">Quantity to Remove:</label>
                    <input type="number" name="quantity" class="form-control" min="1" required>
                </div>

                <!-- SUBMIT BUTTON -->
                <button name="submit" class="btn btn-warning w-100 text-dark fw-bold">
                    Remove Stock
                </button>

            </form>
        </div>
    </div>
</div>

</body>
</html>
