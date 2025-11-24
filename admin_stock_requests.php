<?php
// admin_stock_requests.php
session_start();
include "config.php";
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    die("ACCESS DENIED");
}

// Approve or reject action handling (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['req_id'])) {
    $action = $_POST['action'];            // 'approve' or 'reject'
    $req_id = (int)$_POST['req_id'];

    if ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE stock_requests SET status = 'Rejected' WHERE req_id = ?");
        $stmt->bind_param("i", $req_id);
        $stmt->execute();
        $stmt->close();

        $stmt2 = $conn->prepare("INSERT INTO activity_log (user_id, action) VALUES (?, ?)");
        $msg = "Rejected stock request ID $req_id";
        $stmt2->bind_param("is", $_SESSION['user_id'], $msg);
        $stmt2->execute();
        $stmt2->close();

        header("Location: admin_stock_requests.php?msg=" . urlencode("Request rejected"));
        exit;
    }

    if ($action === 'approve') {
        // load request
        $stmt = $conn->prepare("SELECT user_id, product_id, quantity, request_type, status FROM stock_requests WHERE req_id = ? FOR UPDATE");
        $stmt->bind_param("i", $req_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 0) {
            $stmt->close();
            die("Request not found.");
        }
        $r = $res->fetch_assoc();
        $stmt->close();

        if ($r['status'] !== 'Pending') {
            die("Request is not pending.");
        }

        $product_id = (int)$r['product_id'];
        $qty = (int)$r['quantity'];
        $type = $r['request_type']; // 'out' or 'return'

        // begin transaction
        $conn->begin_transaction();

        try {
            // fetch current product quantity FOR UPDATE
            $stmt = $conn->prepare("SELECT quantity FROM products WHERE product_id = ? FOR UPDATE");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows === 0) throw new Exception("Product not found.");
            $prod = $res->fetch_assoc();
            $stmt->close();

            $current = (int)$prod['quantity'];

            if ($type === 'out') {
                if ($qty > $current) throw new Exception("Insufficient stock to approve the request (Current: $current).");
                $new_qty = $current - $qty;
            } else { // return
                $new_qty = $current + $qty;
            }

            // update products quantity
            $stmt = $conn->prepare("UPDATE products SET quantity = ? WHERE product_id = ?");
            $stmt->bind_param("ii", $new_qty, $product_id);
            $stmt->execute();
            $stmt->close();

            // update stock_requests to Approved
            $stmt = $conn->prepare("UPDATE stock_requests SET status='Approved' WHERE req_id = ?");
            $stmt->bind_param("i", $req_id);
            $stmt->execute();
            $stmt->close();

            // insert into transactions
            $stmt = $conn->prepare("INSERT INTO transactions (product_id, type, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $product_id, $type, $qty);
            $stmt->execute();
            $stmt->close();

            // activity log
            $act = $conn->real_escape_string("Approved request $req_id (type: $type, qty: $qty) - product $product_id updated to $new_qty");
            $stmt = $conn->prepare("INSERT INTO activity_log (user_id, action) VALUES (?, ?)");
            $stmt->bind_param("is", $_SESSION['user_id'], $act);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            header("Location: admin_stock_requests.php?msg=" . urlencode("Request approved and inventory updated"));
            exit;

        } catch (Exception $e) {
            $conn->rollback();
            $err = $e->getMessage();
            die("Error approving request: " . htmlspecialchars($err));
        }
    }
}

// Fetch pending requests
$sql = "SELECT sr.req_id, sr.user_id, u.username, sr.product_id, p.name AS product_name, sr.quantity, sr.request_type, sr.status, sr.created_at
        FROM stock_requests sr
        JOIN users u ON u.user_id = sr.user_id
        LEFT JOIN products p ON p.product_id = sr.product_id
        ORDER BY sr.created_at DESC";
$res = $conn->query($sql);
?>

<!doctype html>
<html>
<head>
  <title>Admin — Stock Requests</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'admin_navbar.php'; ?>

<div class="container mt-4">
  <?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
  <?php endif; ?>

  <h4>Stock Requests (Approve / Reject)</h4>

  <table class="table table-striped">
    <thead class="table-dark">
      <tr><th>ID</th><th>User</th><th>Product</th><th>Type</th><th>Qty</th><th>Status</th><th>Date</th><th>Action</th></tr>
    </thead>
    <tbody>
    <?php while($r = $res->fetch_assoc()): ?>
      <tr>
        <td><?= $r['req_id'] ?></td>
        <td><?= htmlspecialchars($r['username']) ?></td>
        <td><?= htmlspecialchars($r['product_name'] ?? '—') ?></td>
        <td><?= ucfirst($r['request_type']) ?></td>
        <td><?= $r['quantity'] ?></td>
        <td>
          <?php if($r['status']=='Pending') echo '<span class="badge bg-warning text-dark">Pending</span>';
                elseif($r['status']=='Approved') echo '<span class="badge bg-success">Approved</span>';
                else echo '<span class="badge bg-danger">Rejected</span>'; ?>
        </td>
        <td><?= $r['created_at'] ?></td>
        <td>
          <?php if($r['status']=='Pending'): ?>
            <form method="POST" style="display:inline">
              <input type="hidden" name="req_id" value="<?= $r['req_id'] ?>">
              <button name="action" value="approve" class="btn btn-sm btn-success" onclick="return confirm('Approve this request?')">Approve</button>
            </form>

            <form method="POST" style="display:inline">
              <input type="hidden" name="req_id" value="<?= $r['req_id'] ?>">
              <button name="action" value="reject" class="btn btn-sm btn-danger" onclick="return confirm('Reject this request?')">Reject</button>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
