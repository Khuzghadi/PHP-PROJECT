<?php
session_start();
include "config.php";
include "auth_check.php"; 
// ONLY ADMIN CAN ACCESS
if (!isset($_SESSION['user_id'])) { header("Location: loginForm.html?msg=Please login");
exit; }
// SECURITY CHECK
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("ACCESS DENIED");
}

// Validate product id
if (!isset($_GET['id'])) {
    die("Invalid Product ID");
}

$product_id = (int)$_GET['id'];
$message = "";

/* ---------------------------------
   FETCH EXISTING PRODUCT (Secure)
---------------------------------- */
$stmt = $conn->prepare("SELECT product_id, name, sku, category, price, quantity, price_type 
                        FROM products 
                        WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();
$stmt->close();

/* ---------------------------------
   HANDLE UPDATE
---------------------------------- */
if (isset($_POST['update'])) {

    $name       = trim($_POST['name']);
    $sku        = trim($_POST['sku']);
    $category   = trim($_POST['category']);
    $price      = (float)$_POST['price'];
    $quantity   = (int)$_POST['quantity'];
    $price_type = trim($_POST['price_type']);

    if ($name == "" || $sku == "" || $category == "" || $price <= 0 || $quantity < 0) {
        $message = "<div class='alert alert-danger'>Please fill all fields correctly.</div>";
    } else {

        $stmt = $conn->prepare("
            UPDATE products 
            SET name=?, sku=?, category=?, price=?, quantity=?, price_type=?
            WHERE product_id=?
        ");
        $stmt->bind_param("sssdisi", 
            $name, $sku, $category, $price, $quantity, $price_type, $product_id
        );

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Product updated successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to update product.</div>";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<?php include "admin_navbar.php"; ?>

<div class="container mt-4">

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Edit Product</h4>
        </div>

        <div class="card-body">

            <?= $message ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" 
                           value="<?= htmlspecialchars($product['name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" class="form-control" 
                           value="<?= htmlspecialchars($product['sku']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control"
                           value="<?= htmlspecialchars($product['category']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price (Rate)</label>
                    <input type="number" step="0.01" name="price" class="form-control"
                           value="<?= $product['price'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" min="0" class="form-control"
                           value="<?= $product['quantity'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price Type</label>
                    <input type="text" name="price_type" class="form-control"
                           value="<?= htmlspecialchars($product['price_type']) ?>" placeholder="e.g., piece, box, kg">
                </div>

                <button name="update" class="btn btn-primary w-100">Update Product</button>

            </form>
        </div>
    </div>

</div>

</body>
</html>
