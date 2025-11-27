<?php
session_start();
include "config.php";
include "auth_check.php"; 
// ONLY ADMIN CAN ACCESS
if (!isset($_SESSION['user_id'])) { header("Location: loginForm.html?msg=Please login");
exit; }
// SECURITY CHECK
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("ACCESS DENIED");
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die("Invalid user ID");

$message = "";

// Fetch username for display
$stmt = $conn->prepare("SELECT username FROM users WHERE user_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows == 0) die("User not found");
$user = $res->fetch_assoc();
$stmt->close();

// Handle password update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $newpass  = trim($_POST['password']);
    $confirm  = trim($_POST['confirm_password']);

    if ($newpass == "" || $confirm == "") {
        $message = "<div class='alert alert-danger'>All fields required.</div>";

    } elseif ($newpass !== $confirm) {
        $message = "<div class='alert alert-danger'>Passwords do not match.</div>";

    } else {

        // IMPORTANT: Force logout target user
        $stmt = $conn->prepare("UPDATE users SET password=?, force_logout=1 WHERE user_id=?");
        $stmt->bind_param("si", $newpass, $id);
        $stmt->execute();
        $stmt->close();

        // If admin changed their own password → logout immediately
        if ($id == $_SESSION['user_id']) {
            session_destroy();
            header("Location: login.php?msg=Password updated, please login again");
            exit;
        }

        $message = "<div class='alert alert-success'>Password updated & user forced to logout.</div>";
    }
}

include "admin_navbar.php";
?>

<!DOCTYPE html>
<html>
<head>
<title>Change Password</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body { background: #f8f9fa; }
    .card-custom {
        max-width: 600px;
        margin: 40px auto;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .card-header-custom {
        background: linear-gradient(90deg, #6610f2, #0d6efd);
        color: #fff;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        padding: 20px;
    }
</style>
</head>

<body>

<div class="container">

    <div class="card card-custom">

        <div class="card-header card-header-custom">
            <h4 class="mb-0">Change Password</h4>
            <small>User: <?= htmlspecialchars($user['username']) ?></small>
        </div>

        <div class="card-body p-4">

            <?= $message ?>

            <form method="POST" class="row g-3">

                <div class="col-12">
                    <label class="form-label">New Password</label>
                    <input type="text" name="password" class="form-control" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Confirm Password</label>
                    <input type="text" name="confirm_password" class="form-control" required>
                </div>

                <div class="col-12 d-flex gap-2 mt-3">
                    <button class="btn btn-success w-100">Update Password</button>
                    <a href="users.php" class="btn btn-outline-secondary w-50">Back</a>
                </div>

            </form>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
