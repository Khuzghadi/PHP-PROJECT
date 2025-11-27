<?php
// admin_missing_requests.php
session_start();
include "config.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    die("ACCESS DENIED");
}

// Handle rejection POST
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['reject_id'])){
    $id = (int)$_POST['reject_id'];

    $stmt = $conn->prepare("UPDATE missing_stock_requests SET status = 'Rejected' WHERE id = ?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $stmt->close();

    $act = $conn->real_escape_string("Rejected missing-item request ID $id");
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, action) VALUES (?, ?)");
    $stmt->bind_param("is", $_SESSION['user_id'], $act);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_missing_requests.php?msg=" . urlencode("Missing item request rejected"));
    exit;
}

// Fetch missing requests
$sql = "SELECT m.*, u.username FROM missing_stock_requests m JOIN users u ON u.user_id = m.user_id ORDER BY m.created_at DESC";
$res = $conn->query($sql);
?>

<!doctype html>
<html>
<head>
  <title>Admin — Missing Item Requests</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'admin_navbar.php'; ?>

<div class="container mt-4">
  <?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
  <?php endif; ?>

  <h4>Missing Item Requests</h4>

  <table class="table table-bordered">
    <thead class="table-dark"><tr><th>ID</th><th>User</th><th>Item</th><th>Description</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
    <tbody>
      <?php while($m = $res->fetch_assoc()): ?>
      <tr>
        <td><?= $m['id'] ?></td>
        <td><?= htmlspecialchars($m['username']) ?></td>
        <td><?= htmlspecialchars($m['item_name']) ?></td>
        <td><?= nl2br(htmlspecialchars($m['description'])) ?></td>
        <td>
          <?php if($m['status']=='Pending') echo '<span class="badge bg-warning text-dark">Pending</span>';
                elseif($m['status']=='Approved') echo '<span class="badge bg-success">Approved</span>';
                else echo '<span class="badge bg-danger">Rejected</span>'; ?>
        </td>
        <td><?= $m['created_at'] ?></td>
        <td>
          <?php if($m['status']=='Pending'): ?>
            <!-- Approve -> go to create PO page -->
            <a href="create_po_for_missing.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-success">Approve & Create PO</a>

            <form method="POST" style="display:inline">
              <input type="hidden" name="reject_id" value="<?= $m['id'] ?>">
              <button class="btn btn-sm btn-danger" onclick="return confirm('Reject this request?')">Reject</button>
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
