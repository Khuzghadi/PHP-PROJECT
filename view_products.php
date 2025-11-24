<?php
session_start();
include "config.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$products = $conn->query("SELECT * FROM products ORDER BY product_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<?php include "admin_navbar.php"; ?>

<div class="container mt-4">

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h4 class="mb-0">All Products</h4>
        </div>

        <div class="card-body">
        
            <table class="table table-bordered table-striped">
<tr class="table-dark">
    <th>ID</th>
    <th>Name</th>
    <th>SKU</th>
    <th>Category</th>
    <th>Quantity</th>
    <th>Rate (Price)</th>
    <th>Price Type</th>
    <th>Actions</th>
</tr>

<?php while ($p = $products->fetch_assoc()): ?>
<tr>
    <td><?= $p['product_id'] ?></td>
    <td><?= htmlspecialchars($p['name']) ?></td>
    <td><?= htmlspecialchars($p['sku']) ?></td>
    <td><?= htmlspecialchars($p['category']) ?></td>
    <td><?= $p['quantity'] ?></td>
    <td>₹<?= number_format($p['price'], 2) ?></td>
    <td><?= htmlspecialchars($p['price_type']) ?></td>

    <td>
        <a href="edit_product.php?id=<?= $p['product_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
        <a href="delete_product.php?id=<?= $p['product_id'] ?>" class="btn btn-danger btn-sm"
           onclick="return confirm('Delete product?')">Delete</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

        </div>
    </div>
                            <?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_GET['msg']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
</div>

</body>
</html>
