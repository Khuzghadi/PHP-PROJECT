<?php
session_start();
include "config.php";
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff'){
    header("Location: login.php"); exit;
}

$uid = (int)$_SESSION['user_id'];
$message = "";

// fetch products for dropdown
$products = $conn->query("SELECT product_id, name, quantity FROM products ORDER BY name ASC");

if(isset($_POST['submit'])){
    $product_id = (int)$_POST['product_id'];
    $qty = (int)$_POST['quantity'];

    if($product_id <= 0 || $qty <= 0){
        $message = "<div class='alert alert-danger'>Please select a product and enter a valid quantity.</div>";
    } else {
        // insert request
        $stmt = $conn->prepare("INSERT INTO stock_requests (user_id, product_id, quantity, request_type) VALUES (?, ?, ?, 'out')");
        $stmt->bind_param("iii",$uid,$product_id,$qty);
        $stmt->execute();
        $stmt->close();

        // log activity
        $act = $conn->real_escape_string("Requested stock out: ProductID $product_id (+$qty) by user $uid");
        $conn->query("INSERT INTO activity_log (user_id, action) VALUES ($uid, '$act')");

        header("Location: user_dashboard.php?msg=" . urlencode("Stock request submitted"));
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Request Stock</title></head>
<body class="bg-light">
<?php include 'user_sidebar.php'; ?>

<h4>Request Stock</h4>

<?= $message ?>

<form method="POST" class="card p-3 shadow-sm" style="max-width:600px">
  <div class="mb-3">
    <label class="form-label">Product</label>
    <select name="product_id" class="form-control" required>
      <option value="">-- Choose Product --</option>
      <?php while($p = $products->fetch_assoc()): ?>
        <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['name']) ?> (Current: <?= $p['quantity'] ?>)</option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Quantity</label>
    <input type="number" name="quantity" min="1" class="form-control" required>
  </div>

  <button name="submit" class="btn btn-primary">Submit Request</button>
</form>

  </div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
