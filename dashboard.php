<?php
session_start();
include "config.php";

// SECURITY CHECK (FIXED)
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    die("ACCESS DENIED");
}

/* -----------------------------
   SAFE COUNT QUERIES (ADMIN CARDS)
------------------------------ */

// COUNT TOTAL PRODUCTS
$stmt = $conn->prepare("SELECT COUNT(*) FROM products");
$stmt->execute();
$stmt->bind_result($total_products);
$stmt->fetch();
$stmt->close();

// COUNT TOTAL SUPPLIERS
$stmt = $conn->prepare("SELECT COUNT(*) FROM suppliers");
$stmt->execute();
$stmt->bind_result($total_suppliers);
$stmt->fetch();
$stmt->close();

// COUNT PENDING REQUESTS (all types)
$stmt = $conn->prepare("
    SELECT 
        (SELECT COUNT(*) FROM stock_requests WHERE status='Pending')
      + (SELECT COUNT(*) FROM missing_stock_requests WHERE status='Pending')
    AS pending_total
");
$stmt->execute();
$stmt->bind_result($pending_requests);
$stmt->fetch();
$stmt->close();

// LOW STOCK ITEMS (< 10)
$low_stock = $conn->query("SELECT name, quantity FROM products WHERE quantity < 10");

// RECENT TRANSACTIONS
$recent_trans = $conn->query("
    SELECT t.*, p.name AS product_name
    FROM transactions t
    JOIN products p ON t.product_id = p.product_id
    ORDER BY t.trans_id DESC 
    LIMIT 8
");

include 'admin_navbar.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

    <h2 class="mb-4">Admin Dashboard</h2>

    <!-- TOP STAT CARDS -->
    <div class="row">

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h5>Total Products</h5>
                <h2><?= $total_products ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h5>Total Suppliers</h5>
                <h2><?= $total_suppliers ?></h2>
            </div>
        </div>

        <!-- NEW REQUESTS CARD -->
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h5>Pending Requests</h5>
                <h2><?= $pending_requests ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h5>User</h5>
                <h4><?= htmlspecialchars($_SESSION['user']) ?> (Admin)</h4>
            </div>
        </div>
    </div>

    <!-- LOW STOCK ALERTS -->
    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-danger text-white">
            <strong>⚠ Low Stock Alerts (Below 10)</strong>
        </div>
        <div class="card-body">

            <?php if($low_stock->num_rows == 0): ?>
                <p class="text-muted">No low stock items.</p>
            <?php else: ?>
                <table class="table table-bordered">
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                    </tr>

                    <?php while($row = $low_stock->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td class="text-danger fw-bold"><?= $row['quantity'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            <?php endif; ?>

        </div>
    </div>

    <!-- RECENT TRANSACTIONS -->
    <div class="card mt-4 shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
            <strong>Recent Stock Transactions</strong>
        </div>
        <div class="card-body">

            <table class="table table-striped">
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Qty</th>
                    <th>Date</th>
                </tr>

                <?php while($t = $recent_trans->fetch_assoc()): ?>
                <tr>
                    <td><?= $t['trans_id'] ?></td>
                    <td><?= htmlspecialchars($t['product_name']) ?></td>
                    <td>
                        <?php if($t['type'] == 'in'): ?>
                            <span class="badge bg-success">Stock In</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Stock Out</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $t['quantity'] ?></td>
                    <td><?= $t['created_at'] ?></td>
                </tr>
                <?php endwhile; ?>
            </table>

        </div>
    </div>

</div>

</body>
</html>
