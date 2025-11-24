<?php
// create_po_for_missing.php
session_start();
include "config.php";
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    die("ACCESS DENIED");
}

if(!isset($_GET['id'])) die("Missing request id");
$mid = (int)$_GET['id'];

// fetch missing request
$stmt = $conn->prepare("SELECT * FROM missing_stock_requests WHERE id = ?");
$stmt->bind_param("i", $mid);
$stmt->execute();
$mr = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$mr) die("Request not found.");

// fetch suppliers for dropdown
$suppliers = $conn->query("SELECT supplier_id, name FROM suppliers ORDER BY name ASC");

$message = "";
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supplier_id'], $_POST['quantity'])) {
    $supplier_id = (int)$_POST['supplier_id'];
    $qty = (int)$_POST['quantity'];
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0.0;

    if ($supplier_id <= 0 || $qty <= 0) {
        $message = "<div class='alert alert-danger'>Please select supplier and enter valid quantity.</div>";
    } else {
        // transaction: create new product -> create PO -> update missing request
        $conn->begin_transaction();
        try {
            // create product (Uncategorized)
            $pname = $mr['item_name'];
            $cat = 'Uncategorized';
            $stmt = $conn->prepare("INSERT INTO products (name, sku, category, price, quantity) VALUES (?, ?, ?, ?, 0)");
            // generate a simple SKU: MISSING-<id>
            $sku = "MISSING-".$mid;
            $stmt->bind_param("sssd", $pname, $sku, $cat, $price);
            $stmt->execute();
            $product_id = $stmt->insert_id;
            $stmt->close();

            // create purchase order
            $stmt = $conn->prepare("INSERT INTO purchase_orders (supplier_id, product_id, quantity, status) VALUES (?, ?, ?, 'Pending')");
            $stmt->bind_param("iii", $supplier_id, $product_id, $qty);
            $stmt->execute();
            $po_id = $stmt->insert_id;
            $stmt->close();

            // mark missing request approved
            $stmt = $conn->prepare("UPDATE missing_stock_requests SET status='Approved' WHERE id = ?");
            $stmt->bind_param("i",$mid);
            $stmt->execute();
            $stmt->close();

            // activity log
            $act = $conn->real_escape_string("Approved missing request $mid -> product $product_id created and PO $po_id created (supplier $supplier_id, qty $qty)");
            $stmt = $conn->prepare("INSERT INTO activity_log (user_id, action) VALUES (?, ?)");
            $stmt->bind_param("is", $_SESSION['user_id'], $act);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            header("Location: admin_missing_requests.php?msg=" . urlencode("PO $po_id created; product $product_id added"));
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $message = "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}
?>

<!doctype html>
<html>
<head>
  <title>Create PO from Missing Request</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'admin_navbar.php'; ?>

<div class="container mt-4">
  <h4>Create PO for: <?= htmlspecialchars($mr['item_name']) ?></h4>
  <?php if($message) echo $message; ?>

  <form method="POST" class="card p-3" style="max-width:700px">
    <div class="mb-3">
      <label class="form-label">Supplier</label>
      <select name="supplier_id" class="form-control" required>
        <option value="">-- Select Supplier --</option>
        <?php while($s = $suppliers->fetch_assoc()): ?>
          <option value="<?= $s['supplier_id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Quantity to Order</label>
      <input type="number" name="quantity" min="1" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Estimated Unit Price (optional)</label>
      <input type="number" step="0.01" name="price" class="form-control">
    </div>

    <button class="btn btn-success">Create PO & Approve Request</button>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
