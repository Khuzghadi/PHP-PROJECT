<?php
session_start();
include "config.php";
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff'){
    header("Location: login.php"); exit;
}

$uid = (int)$_SESSION['user_id'];
$message = "";

if(isset($_POST['submit'])){
    $name = trim($_POST['item_name'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    if($name === ''){
        $message = "<div class='alert alert-danger'>Item name is required.</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO missing_stock_requests (user_id, item_name, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $uid, $name, $desc);
        $stmt->execute();
        $stmt->close();

        $act = $conn->real_escape_string("Requested new item: $name by user $uid");
        $conn->query("INSERT INTO activity_log (user_id, action) VALUES ($uid, '$act')");

        header("Location: user_dashboard.php?msg=" . urlencode("New item request submitted"));
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Request New Item</title></head>
<body class="bg-light">
<?php include 'user_sidebar.php'; ?>

<h4>Request New Item</h4>

<?= $message ?>

<form method="POST" class="card p-3 shadow-sm" style="max-width:700px">
  <div class="mb-3">
    <label class="form-label">Item Name</label>
    <input type="text" name="item_name" class="form-control" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Description (optional)</label>
    <textarea name="description" class="form-control" rows="4"></textarea>
  </div>

  <button name="submit" class="btn btn-secondary">Submit Request</button>
</form>

  </div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
