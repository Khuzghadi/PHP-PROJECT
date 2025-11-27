<?php
session_start();
include "config.php";
include "auth_check.php";

if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include "admin_navbar.php";

// SEARCH
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

$query = "SELECT * FROM suppliers";
if ($search != "") {
    $query .= " WHERE name LIKE '%$search%' 
                OR phone LIKE '%$search%' 
                OR email LIKE '%$search%' 
                OR Address LIKE '%$search%'";
}

$suppliers = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Supplier List</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f7f7f7; }
.top-bar { display:flex; justify-content:space-between; align-items:center; }
</style>
</head>

<body>

<div class="container mt-4">

    <div class="top-bar mb-3">
        <h3 class="fw-bold">Suppliers</h3>
        <a href="add_supplier.php" class="btn btn-primary">+ Add Supplier</a>
    </div>

    <!-- Search -->
    <form class="mb-3">
        <div class="input-group">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search suppliers">
            <button class="btn btn-outline-primary">Search</button>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body">
            
            <?php if ($suppliers->num_rows == 0): ?>
                <p class="text-muted">No suppliers found.</p>
            <?php else: ?>

                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th style="width:150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php while($s = $suppliers->fetch_assoc()): ?>
                        <tr>
                            <td><?= $s['supplier_id'] ?></td>
                            <td><?= htmlspecialchars($s['name']) ?></td>
                            <td><?= htmlspecialchars($s['phone']) ?></td>
                            <td><?= htmlspecialchars($s['email']) ?></td>
                            <td><?= htmlspecialchars($s['Address']) ?></td>

                            <td>
                                <a href="edit_supplier.php?id=<?= $s['supplier_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="delete_supplier.php?id=<?= $s['supplier_id'] ?>"
                                   onclick="return confirm('Delete this supplier?')"
                                   class="btn btn-sm btn-danger">
                                   Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                    </tbody>
                </table>

            <?php endif; ?>

        </div>
    </div>

</div>

</body>
</html>
