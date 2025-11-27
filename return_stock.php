<?php
session_start();
include "config.php";
include "auth_check.php";

if ($_SESSION['role'] !== 'staff') {
    die("ACCESS DENIED");
}

$uid = (int)$_SESSION['user_id'];
$message = "";

// HANDLE FORM SUBMISSION
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $product_id = (int)$_POST['product_id'];
    $qty        = (int)$_POST['quantity'];

    // 1. HOW MUCH USER RECEIVED
    $received = $conn->query("
        SELECT COALESCE(SUM(quantity),0) AS total
        FROM stock_requests
        WHERE user_id = $uid 
          AND product_id = $product_id
          AND request_type = 'out'
          AND status = 'Approved'
    ")->fetch_assoc()['total'];

    // 2. HOW MUCH USER RETURNED
    $returned = $conn->query("
        SELECT COALESCE(SUM(quantity),0) AS total
        FROM stock_requests
        WHERE user_id = $uid 
          AND product_id = $product_id
          AND request_type = 'return'
          AND status = 'Approved'
    ")->fetch_assoc()['total'];

    // CURRENT BALANCE
    $balance = $received - $returned;

    // VALIDATION
    if ($balance <= 0) {
        header("Location: return_stock.php?msg=You do not have this item anymore!");
        exit;
    }

    if ($qty > $balance) {
        header("Location: return_stock.php?msg=You cannot return more than you currently have!");
        exit;
    }

    // INSERT RETURN REQUEST
    $stmt = $conn->prepare("
        INSERT INTO stock_requests (user_id, product_id, quantity, request_type, status)
        VALUES (?, ?, ?, 'return', 'Pending')
    ");
    $stmt->bind_param("iii", $uid, $product_id, $qty);
    $stmt->execute();
    $stmt->close();

    header("Location: user_dashboard.php?msg=Return request submitted");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Return Stock</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f8f9fa; }
.card-custom { max-width: 650px; margin: 30px auto; border-radius:12px; }
.header-custom { background:#0d6efd; color:white; padding:15px 20px; border-radius:12px 12px 0 0; }
</style>
</head>

<body>

<?php include "user_sidebar.php"; ?>

<div class="page-wrapper">

    <div class="card shadow-sm card-custom">
        <div class="header-custom">
            <h4 class="mb-0">Return Stock</h4>
            <small>Return items back to inventory</small>
        </div>

        <div class="card-body p-4">

            <?php if(isset($_GET['msg'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['msg']) ?></div>
            <?php endif; ?>

            <?php
            // FETCH USER BALANCES FOR PRODUCTS
            $products = $conn->query("
                SELECT p.product_id, p.name,
                (
                    (SELECT COALESCE(SUM(quantity),0)
                     FROM stock_requests 
                     WHERE user_id=$uid AND product_id=p.product_id 
                       AND request_type='out' AND status='Approved')
                    -
                    (SELECT COALESCE(SUM(quantity),0)
                     FROM stock_requests 
                     WHERE user_id=$uid AND product_id=p.product_id 
                       AND request_type='return' AND status='Approved')
                ) AS balance
                FROM products p
            ");
            ?>

            <form method="POST" class="row g-3">

                <div class="col-md-12">
                    <label class="form-label">Select Product</label>
                    <select name="product_id" class="form-control" id="productSelect" required>
                        <option value="">-- Select Product --</option>

                        <?php while($p = $products->fetch_assoc()): ?>
                            <?php if ($p['balance'] > 0): ?>
                                <option 
                                    value="<?= $p['product_id'] ?>" 
                                    data-max="<?= $p['balance'] ?>"
                                >
                                    <?= htmlspecialchars($p['name']) ?> 
                                    (You have: <?= $p['balance'] ?>)
                                </option>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Return Quantity</label>
                    <input type="number" id="qtyInput" name="quantity" class="form-control" min="1" required>
                    <small id="maxHint" class="text-muted"></small>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-success w-100">Submit Return</button>
                    <a href="user_dashboard.php" class="btn btn-outline-secondary w-50">Cancel</a>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
document.getElementById('productSelect').addEventListener('change', function() {
    let max = this.options[this.selectedIndex].dataset.max;
    let qtyInput = document.getElementById('qtyInput');

    qtyInput.max = max;
    document.getElementById('maxHint').textContent = 
        "Maximum returnable: " + max;
});
</script>

</body>
</html>
