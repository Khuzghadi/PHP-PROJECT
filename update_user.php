<?php
session_start();
include "config.php";
include "auth_check.php"; 
// ONLY ADMIN CAN ACCESS
if (!isset($_SESSION['user_id'])) { header("Location: loginForm.html?msg=Please login");
exit; }
// SECURITY
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("ACCESS DENIED");
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die("Invalid user ID.");

// Handle form submit
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $zone = trim($_POST['zone_name'] ?? '');

    if ($username === '' || $role === '') {
        $message = "<div class='alert alert-danger'>Username and role are required.</div>";
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, role = ?, zone_name = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $username, $role, $zone, $id);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>User updated successfully.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to update user. Try again.</div>";
        }
        $stmt->close();
        // reload updated data below
    }
}

// Fetch user data (fresh)
$stmt = $conn->prepare("SELECT user_id, username, role, zone_name FROM users WHERE user_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    $stmt->close();
    die("User not found.");
}
$user = $res->fetch_assoc();
$stmt->close();

// include navbar
include "admin_navbar.php";
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Update User - Admin</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* small custom adjustments to match other pages */
    body { background: #f8f9fa; }
    .card-custom { max-width: 720px; margin: 30px auto; border-radius: 10px; box-shadow: 0 6px 18px rgba(0,0,0,0.05); }
    .card-header-custom { background: linear-gradient(90deg,#0d6efd,#6610f2); color: #fff; border-top-left-radius: 10px; border-top-right-radius: 10px; }
    .form-actions { display:flex; gap:12px; }
    @media (max-width:575px){ .form-actions { flex-direction: column; } }
  </style>
</head>
<body>

<div class="container">
  <div class="card card-custom">
    <div class="card-header card-header-custom p-3">
      <h4 class="mb-0">Update User</h4>
      <div class="text-muted small">Edit user details and zone assignment</div>
    </div>

    <div class="card-body p-4">
      <?= $message ?>

      <form method="POST" class="row g-3">

        <div class="col-12">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Role</label>
          <select name="role" class="form-select" required onchange="toggleZoneField()">
            <option value="admin" <?= $user['role']=='admin' ? 'selected' : '' ?>>Admin</option>
            <option value="staff" <?= $user['role']=='staff' ? 'selected' : '' ?>>Staff</option>
          </select>
        </div>

        <div class="col-md-6" id="zoneWrapper">
          <label class="form-label">Zone Name</label>
          <input type="text" name="zone_name" class="form-control" placeholder="e.g., Zone 1" value="<?= htmlspecialchars($user['zone_name']) ?>">
          <div class="form-text">Zone required for staff. Leave empty for admin.</div>
        </div>

        <div class="col-12 form-actions mt-2">
          <button type="submit" class="btn btn-success">Save Changes</button>
          <a href="users.php" class="btn btn-outline-secondary">Back to Users</a>
          <a href="change_password.php?id=<?= $user['user_id'] ?>" class="btn btn-info text-white">Change Password</a>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
  // show/hide zone field depending on role
  function toggleZoneField(){
    const role = document.querySelector('select[name="role"]').value;
    const zoneWrapper = document.getElementById('zoneWrapper');
    if(role === 'staff'){
      zoneWrapper.style.display = 'block';
    } else {
      zoneWrapper.style.display = 'none';
      zoneWrapper.querySelector('input').value = '';
    }
  }
  // initial toggle based on current role
  document.addEventListener('DOMContentLoaded', toggleZoneField);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
