<?php
session_start();
include "config.php";
include "auth_check.php";

if ($_SESSION['role'] !== 'staff') {
    header("Location: login.php"); 
    exit;
}

include "user_sidebar.php";

$uid = (int)$_SESSION['user_id'];

// Fetch Approved Stock Out (User Received)
$received = $conn->query("
    SELECT sr.*, p.name 
    FROM stock_requests sr
    JOIN products p ON p.product_id = sr.product_id
    WHERE sr.user_id = $uid AND sr.status='Approved' AND sr.request_type='out'
    ORDER BY sr.created_at DESC
");

// Fetch Approved Returns (User Returned)
$returned = $conn->query("
    SELECT sr.*, p.name 
    FROM stock_requests sr
    JOIN products p ON p.product_id = sr.product_id
    WHERE sr.user_id = $uid AND sr.status='Approved' AND sr.request_type='return'
    ORDER BY sr.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>My Stock History</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.section-title {
    font-size:18px;
    font-weight:700;
}
</style>
</head>
<body>

<div class="page-wrapper">

    <h3 class="fw-bold mb-3">My Stock History</h3>

    <!-- Received Items -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <strong>Items Received From Inventory</strong>
        </div>
        <div class="card-body">

            <?php if ($received->num_rows == 0): ?>
                <p class="text-muted">No received items yet.</p>
            <?php else: ?>

                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Date Received</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($r = $received->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['name']) ?></td>
                            <td class="fw-bold text-success"><?= $r['quantity'] ?></td>
                            <td><?= $r['created_at'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>

            <?php endif; ?>

        </div>
    </div>

    <!-- Returned Items -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <strong>Items Returned to Inventory</strong>
        </div>
        <div class="card-body">

            <?php if ($returned->num_rows == 0): ?>
                <p class="text-muted">No returned items yet.</p>
            <?php else: ?>

                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Qty Returned</th>
                            <th>Return Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($r = $returned->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['name']) ?></td>
                            <td class="fw-bold text-danger"><?= $r['quantity'] ?></td>
                            <td><?= $r['created_at'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>

            <?php endif; ?>

        </div>
    </div>

</div>

</body>
</html>
