<?php
session_start();
include "config.php";
include "auth_check.php"; 
// ONLY ADMIN CAN ACCESS
if (!isset($_SESSION['user_id'])) { header("Location: login.php?msg=Please login");
exit; }
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff'){
    header("Location: login.php"); exit;
}

// counts for this user
$uid = (int)$_SESSION['user_id'];

// totals (user-specific)
$total_requests = $conn->query("SELECT COUNT(*) AS c FROM stock_requests WHERE user_id=$uid")->fetch_assoc()['c'];
$pending_requests = $conn->query("SELECT COUNT(*) AS c FROM stock_requests WHERE user_id=$uid AND status='Pending'")->fetch_assoc()['c'];
$approved_requests = $conn->query("SELECT COUNT(*) AS c FROM stock_requests WHERE user_id=$uid AND status='Approved'")->fetch_assoc()['c'];
$rejected_requests = $conn->query("SELECT COUNT(*) AS c FROM stock_requests WHERE user_id=$uid AND status='Rejected'")->fetch_assoc()['c'];

// quick recent requests
$recent = $conn->query("SELECT sr.*, p.name FROM stock_requests sr LEFT JOIN products p ON p.product_id=sr.product_id WHERE sr.user_id=$uid ORDER BY sr.created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
  <title>User Dashboard</title>
</head>
<body class="bg-light">

<?php include 'user_sidebar.php'; ?>

<h4>Dashboard</h4>

<?php
// show optional message
if(isset($_GET['msg'])): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($_GET['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card shadow-sm p-3">
      <small class="text-muted">Total Requests</small>
      <h3 class="mt-2"><?= $total_requests ?></h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card shadow-sm p-3">
      <small class="text-muted">Pending</small>
      <h3 class="mt-2"><?= $pending_requests ?></h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card shadow-sm p-3">
      <small class="text-muted">Approved</small>
      <h3 class="mt-2"><?= $approved_requests ?></h3>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card shadow-sm p-3">
      <small class="text-muted">Rejected</small>
      <h3 class="mt-2"><?= $rejected_requests ?></h3>
    </div>
  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-header">
    <strong>Quick Actions</strong>
  </div>
  <div class="card-body">
    <a href="request_stock.php" class="btn btn-primary me-2">Request Stock</a>
    <a href="return_stock.php" class="btn btn-warning me-2">Return Stock</a>
    <a href="request_new_item.php" class="btn btn-outline-secondary">Request New Item</a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header">
    <strong>Recent Requests</strong>
  </div>
  <div class="card-body">
    <?php if($recent->num_rows == 0): ?>
      <div class="text-muted">No requests yet.</div>
    <?php else: ?>
      <table class="table">
        <thead class="table-light">
          <tr><th>ID</th><th>Product</th><th>Type</th><th>Qty</th><th>Status</th><th>Date</th></tr>
        </thead>
        <tbody>
          <?php while($r = $recent->fetch_assoc()): ?>
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

<!-- close content wrapper and include bootstrap js -->
  </div> <!-- close flex-grow-1 -->
</div> <!-- close d-flex -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
