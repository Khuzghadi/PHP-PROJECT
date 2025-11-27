<?php
session_start();
include "config.php";
include "auth_check.php";

// ADMIN ONLY
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include "admin_navbar.php";

$message = "";

// FORM HANDLER
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name    = trim($_POST['name']);
    $phone   = trim($_POST['phone']);
    $email   = trim($_POST['email']);
    $address = trim($_POST['address']);

    if ($name == "" || $phone == "" || $email == "" || $address == "") {
        $message = "<div class='alert alert-danger'>All fields are required.</div>";
    } else {

        $stmt = $conn->prepare("
            INSERT INTO suppliers (name, phone, email, Address)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssss", $name, $phone, $email, $address);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Supplier added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to add supplier.</div>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Supplier</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f7f7f7; }
.card-custom {
    max-width: 650px;
    margin: 40px auto;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.card-header-custom {
    background: linear-gradient(90deg,#0d6efd,#6610f2);
    color:#fff;
    padding:18px;
    border-radius:12px 12px 0 0;
}
</style>
</head>

<body>

<div class="container">

    <div class="card card-custom">
        <div class="card-header-custom">
            <h4 class="mb-0">Add Supplier</h4>
            <small>Add new supplier details</small>
        </div>

        <div class="card-body p-4">

            <?= $message ?>

            <form method="POST" class="row g-3">

                <div class="col-md-12">
                    <label class="form-label">Supplier Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" required></textarea>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-success w-100">Add Supplier</button>
                    <a href="supplier_list.php" class="btn btn-outline-secondary w-50">Back</a>
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>
