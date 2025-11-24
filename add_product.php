<?php
session_start();
include "config.php";

// ONLY ADMIN CAN ACCESS
if (!isset($_SESSION['user_id']) && $_SESSION['role'] != 'admin') {
    die("ACCESS DENIED");
}

$message = "";

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $sku = $_POST['sku'];
    $cat = $_POST['category'];
    $price = $_POST['price'];
    $qty = $_POST['quantity'];

    if ($name == "" || $sku == "" || $cat == "" || $price <= 0 || $qty < 0) {
        $message = "<div class='alert alert-danger'>All fields are required.</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO products(name, sku, category, price, quantity, price_type)
                        VALUES(?,?,?,?,?,?)");
                        $stmt->bind_param("sssdis", $name, $sku, $cat, $price, $qty, $price_type);

        $conn->query($sql);

        // ACTIVITY LOG
        $conn->query("
            INSERT INTO activity_log(user_id, action)
            VALUES(".$_SESSION['user_id'].", 'Added product: $name')
        ");

        $message = "<div class='alert alert-success'>Product added successfully.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<?php include "admin_navbar.php"; ?>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Add New Product</h4>
        </div>

        <div class="card-body">
            <?= $message ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Product Name:</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">SKU:</label>
                    <input type="text" name="sku" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category:</label>
                    <input type="text" name="category" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price (₹):</label>
                    <input type="number" name="price" class="form-control" min="1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Initial Quantity:</label>
                    <input type="number" name="quantity" class="form-control" min="0" required>
                </div>
                <div class="mb-3">
    <label class="form-label">Price Type (per unit)</label>
    <input type="text" name="price_type" class="form-control" placeholder="e.g., piece, kg, box" required>
</div>

                <button name="submit" class="btn btn-primary w-100">Add Product</button>

            </form>
        </div>
    </div>
</div>

</body>
</html>
