<?php
// admin_po_list.php
session_start();
include "config.php";
include "auth_check.php"; 
// ONLY ADMIN CAN ACCESS
if (!isset($_SESSION['user_id'])) { header("Location: loginForm.html?msg=Please login");
exit; }
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') die("ACCESS DENIED");

// handle receive action
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['receive_po_id'])){
    $po_id = (int)$_POST['receive_po_id'];

    // begin transaction
    $conn->begin_transaction();
    try {
        // fetch po
        $stmt = $conn->prepare("SELECT supplier_id, product_id, quantity, status FROM purchase_orders WHERE po_id = ? FOR UPDATE");
        $stmt->bind_param("i",$po_id);
        $stmt->execute();
        $po = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if(!$po) throw new Exception("PO not found");
        if($po['status'] === 'Received') throw new Exception("PO already received");

        $product_id = (int)$po['product_id'];
        $qty = (int)$po['quantity'];

        // fetch product current qty FOR UPDATE
        $stmt = $conn->prepare("SELECT quantity FROM products WHERE product_id = ? FOR UPDATE");
        $stmt->bind_param("i",$product_id);
        $stmt->execute();
        $prod = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if(!$prod) throw new Exception("Product not found for PO");

        $new_qty = (int)$prod['quantity'] + $qty;

        // update product qty
        $stmt = $conn->prepare("UPDATE products SET quantity = ? WHERE product_id = ?");
        $stmt->bind_param("ii",$new_qty,$product_id);
        $stmt->execute();
        $stmt->close();

        // update PO status to Received
        $stmt = $conn->prepare("UPDATE purchase_orders SET status='Received' WHERE po_id = ?");
        $stmt->bind_param("i",$po_id);
        $stmt->execute();
        $stmt->close();

        // insert transaction
        $type = 'in';
        $stmt = $conn->prepare("INSERT INTO transactions (product_id, type, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $product_id, $type, $qty);
        $stmt->execute();
        $stmt->close();

        // activity log
        $act = $conn->real_escape_string("PO $po_id received. Product $product_id increased by $qty to $new_qty");
        $stmt = $conn->prepare("INSERT INTO activity_log (user_id, action) VALUES (?, ?)");
        $stmt->bind_param("is", $_SESSION['user_id'], $act);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        header("Location: admin_po_list.php?msg=" . urlencode("PO marked as received and stock updated"));
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        die("Error receiving PO: " . htmlspecialchars($e->getMessage()));
    }
}

// fetch PO list
$sql = "SELECT po.*, s.name AS supplier_name, p.name AS product_name
        FROM purchase_orders po
        LEFT JOIN suppliers s ON s.supplier_id = po.supplier_id
        LEFT JOIN products p ON p.product_id = po.product_id
        ORDER BY po.created_at DESC";
$res = $conn->query($sql);
?>

<!doctype html>
<html>
<head>
  <title>Admin — Purchase Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'admin_navbar.php'; ?>

<div class="container mt-4">
  <?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
  <?php endif; ?>

  <h4>Purchase Orders</h4>
  <table class="table table-striped">
    <thead class="table-dark"><tr><th>PO ID</th><th>Supplier</th><th>Product</th><th>Qty</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
    <tbody>
      <?php while($po = $res->fetch_assoc()): ?>
      <tr>
        <td><?= $po['po_id'] ?></td>
        <td><?= htmlspecialchars($po['supplier_name'] ?? '—') ?></td>
        <td><?= htmlspecialchars($po['product_name'] ?? $po['product_id']) ?></td>
        <td><?= $po['quantity'] ?></td>
        <td><?= $po['status'] === 'Pending' ? '<span class="badge bg-warning text-dark">Pending</span>' : '<span class="badge bg-success">Received</span>' ?></td>
        <td><?= $po['created_at'] ?></td>
        <td>
          <?php if($po['status'] === 'Pending'): ?>
            <form method="POST" style="display:inline">
              <input type="hidden" name="receive_po_id" value="<?= $po['po_id'] ?>">
              <button class="btn btn-sm btn-success" onclick="return confirm('Mark PO as received?')">Mark Received</button>
            </form>
          <?php else: ?>
            <small class="text-muted">—</small>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
</body>
</html>
