<?php
session_start();
include "config.php";
include "auth_check.php"; 
// ONLY ADMIN CAN ACCESS
if (!isset($_SESSION['user_id'])) { header("Location: loginForm.html?msg=Please login");
exit; }
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff'){
    header("Location: login.php"); exit;
}
$uid = (int)$_SESSION['user_id'];

$stock_reqs = $conn->query("SELECT sr.*, p.name FROM stock_requests sr LEFT JOIN products p ON p.product_id = sr.product_id WHERE sr.user_id=$uid ORDER BY sr.created_at DESC");
$missing_reqs = $conn->query("SELECT * FROM missing_stock_requests WHERE user_id=$uid ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head><title>My Requests</title></head>
<body class="bg-light">
<?php include 'user_sidebar.php'; ?>

<h4>My Requests</h4>

<div class="card shadow-sm mb-4">
  <div class="card-header">Stock Requests</div>
  <div class="card-body">
    <?php if($stock_reqs->num_rows == 0): ?>
      <div class="text-muted">No stock/return requests yet.</div>
    <?php else: ?>
      <table class="table">
        <thead class="table-light">
          <tr><th>ID</th><th>Product</th><th>Type</th><th>Qty</th><th>Status</th><th>Date</th></tr>
        </thead>
        <tbody>
          <?php while($r = $stock_reqs->fetch_assoc()): ?>
            <tr>
              <td><?= $r['req_id'] ?></td>
              <td><?= htmlspecialchars($r['name'] ?? '—') ?></td>
              <td><?= ucfirst($r['request_type']) ?></td>
              <td><?= $r['quantity'] ?></td>
              <td>
                <?php
                  $s = $r['status'];
                  if($s=='Pending') echo '<span class="badge bg-warning text-dark">Pending</span>';
                  elseif($s=='Approved') echo '<span class="badge bg-success">Approved</span>';
                  else echo '<span class="badge bg-danger">Rejected</span>';
                ?>
              </td>
              <td><?= $r['created_at'] ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-header">Requested New Items</div>
  <div class="card-body">
    <?php if($missing_reqs->num_rows == 0): ?>
      <div class="text-muted">No new item requests yet.</div>
    <?php else: ?>
      <table class="table">
        <thead class="table-light"><tr><th>ID</th><th>Item</th><th>Description</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
          <?php while($m = $missing_reqs->fetch_assoc()): ?>
            <tr>
              <td><?= $m['id'] ?></td>
              <td><?= htmlspecialchars($m['item_name']) ?></td>
              <td><?= nl2br(htmlspecialchars($m['description'])) ?></td>
              <td>
                <?php
                  $s = $m['status'];
                  if($s=='Pending') echo '<span class="badge bg-warning text-dark">Pending</span>';
                  elseif($s=='Approved') echo '<span class="badge bg-success">Approved</span>';
                  else echo '<span class="badge bg-danger">Rejected</span>';
                ?>
              </td>
              <td><?= $m['created_at'] ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

  </div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
