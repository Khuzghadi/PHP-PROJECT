<?php
session_start();
include "config.php";

// ONLY LOGGED-IN USERS
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// FETCH ALL TRANSACTIONS WITH PRODUCT NAMES
$transactions = $conn->query("
    SELECT t.*, p.name 
    FROM transactions t
    JOIN products p ON p.product_id = t.product_id
    ORDER BY t.trans_id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Stock Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Small enhancements */
        .search-box {
            max-width: 300px;
            float: right;
        }
    </style>
</head>

<body class="bg-light">

<?php include "admin_navbar.php"; ?>

<div class="container mt-4">

    <div class="card shadow-sm">

        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">All Stock Transactions</h4>

            <!-- SEARCH BOX (client side filter) -->
            <input type="text" id="search" class="form-control search-box" placeholder="Search product...">
        </div>

        <div class="card-body">

            <table class="table table-bordered table-striped" id="transactionsTable">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($t = $transactions->fetch_assoc()): ?>
                    <tr>
                        <td><?= $t['trans_id'] ?></td>
                        <td><?= $t['name'] ?></td>

                        <td>
                            <?php if ($t['type'] == "in"): ?>
                                <span class="badge bg-success">Stock In</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Stock Out</span>
                            <?php endif; ?>
                        </td>

                        <td><?= $t['quantity'] ?></td>
                        <td><?= $t['created_at'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        </div>
    </div>

</div>

<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- SIMPLE SEARCH FILTER -->
<script>
document.getElementById("search").addEventListener("keyup", function () {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll("#transactionsTable tbody tr");

    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(value) ? "" : "none";
    });
});
</script>

</body>
</html>
