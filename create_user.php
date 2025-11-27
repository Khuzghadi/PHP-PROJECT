<?php
session_start();
include "config.php";
include "auth_check.php"; 
// ONLY ADMIN CAN ACCESS
if (!isset($_SESSION['user_id'])) { header("Location: loginForm.html?msg=Please login");
exit; }
// Only admin allowed
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("ACCESS DENIED");
}

include "admin_navbar.php";

$message = "";

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username  = trim($_POST['username']);
    $password  = trim($_POST['password']);
    $role      = trim($_POST['role']);
    $zone_name = trim($_POST['zone_name']);

    // Validation
    if ($username == "" || $password == "" || $role == "") {
        $message = "<div class='alert alert-danger'>All fields are required.</div>";
    } else if ($role == "staff" && $zone_name == "") {
        $message = "<div class='alert alert-danger'>Zone Name is required for staff.</div>";
    } else {

        // CHECK DUPLICATE USERNAME
        $check = $conn->prepare("SELECT * FROM users WHERE username=?");
        $check->bind_param("s", $username);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "<div class='alert alert-danger'>Username already exists.</div>";
        } else {

            // Insert User
            $stmt = $conn->prepare("
                INSERT INTO users (username, password, role, zone_name)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("ssss",
                $username,
                $password,
                $role,
                $zone_name
            );

            if ($stmt->execute()) {
                $message = "<div class='alert alert-success'>User created successfully!</div>";
            } else {
                $message = "<div class='alert alert-danger'>Failed to create user.</div>";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

    <div class="card shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Create New User</h4>
        </div>

        <div class="card-body">

            <?= $message ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="text" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" id="roleSelect" class="form-control" required onchange="toggleZone()">
                        <option value="">-- Select Role --</option>
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <!-- ZONE NAME FIELD -->
                <div class="mb-3" id="zoneField" style="display:none;">
                    <label class="form-label">Zone Name</label>
                    <input type="text" name="zone_name" class="form-control" placeholder="e.g., Zone 1, East Wing">
                </div>

                <button class="btn btn-success w-100">Create User</button>

            </form>

        </div>
    </div>

</div>

<script>
function toggleZone() {
    let role = document.getElementById("roleSelect").value;
    let zoneField = document.getElementById("zoneField");

    if (role === "staff") {
        zoneField.style.display = "block";
    } else {
        zoneField.style.display = "none";
    }
}
</script>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
</body>
</html>
