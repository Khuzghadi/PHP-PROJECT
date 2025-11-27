<?php
session_start();
include "config.php";
include "auth_check.php"; 
// ONLY ADMIN CAN ACCESS
if (!isset($_SESSION['user_id'])) { header("Location: login.php?msg=Please login");
exit; }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("ACCESS DENIED");
}

include "admin_navbar.php";

// Fetch all zones for dropdown
$zoneQuery = $conn->query("SELECT DISTINCT zone_name FROM users WHERE zone_name IS NOT NULL AND zone_name != '' ORDER BY zone_name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Users Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        #searchBox { max-width: 300px; }
        .action-btns button, .action-btns a { margin-right: 4px; }
    </style>
</head>

<body class="bg-light">

<div class="container mt-4">

    <h3 class="mb-4">Users Management</h3>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <!-- SEARCH & FILTER BAR -->
    <div class="card shadow-sm mb-3 p-3">

        <div class="row g-3">

            <div class="col-md-4">
                <input type="text" id="searchBox" class="form-control" placeholder="Search username or zone...">
            </div>

            <div class="col-md-3">
                <select id="roleFilter" class="form-control">
                    <option value="">Filter by Role (All)</option>
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                </select>
            </div>

            <div class="col-md-3">
                <select id="zoneFilter" class="form-control">
                    <option value="">Filter by Zone (All)</option>
                    <?php while ($z = $zoneQuery->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($z['zone_name']) ?>">
                            <?= htmlspecialchars($z['zone_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

        </div>

    </div>

    <!-- USER TABLE -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <strong>All Users</strong>
        </div>

        <div class="card-body">

            <table class="table table-bordered table-hover" id="usersTable">
                <thead class="table-secondary">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Zone</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                $users = $conn->query("SELECT * FROM users ORDER BY user_id ASC");
                while ($u = $users->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= $u['user_id'] ?></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td><?= $u['role'] ?></td>
                        <td><?= $u['zone_name'] ?: "—" ?></td>

                        <td class="action-btns">

                            <!-- Raise Request -->
                            <button class="btn btn-primary btn-sm"
                                onclick="openRequestModal(<?= $u['user_id'] ?>, '<?= $u['username'] ?>')">
                                Request
                            </button>

                            <!-- Update User -->
                            <a href="update_user.php?id=<?= $u['user_id'] ?>" class="btn btn-warning btn-sm">
                                Update
                            </a>

                            <!-- Change Password -->
                            <a href="change_password.php?id=<?= $u['user_id'] ?>" class="btn btn-info btn-sm text-white">
                                Change Password
                            </a>

                            <!-- Delete User -->
                            <a href="delete_user.php?id=<?= $u['user_id'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this user?')">
                                Delete
                            </a>

                        </td>

                    </tr>
                <?php endwhile; ?>
                </tbody>

            </table>

        </div>
    </div>
</div>


<!-- REQUEST MODAL -->
<div class="modal fade" id="requestModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <form method="POST" action="raise_request_admin.php">

        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Raise Stock Request</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

            <input type="hidden" name="user_id" id="modal_user_id">

            <p><strong>User:</strong> <span id="modal_username"></span></p>

            <!-- PRODUCT -->
            <div class="mb-3">
                <label class="form-label">Product</label>
                <select name="product_id" class="form-control" required>
                    <option value="">-- Select Product --</option>
                    <?php
                    $p = $conn->query("SELECT product_id, name, quantity FROM products ORDER BY name ASC");
                    while ($prod = $p->fetch_assoc()):
                    ?>
                        <option value="<?= $prod['product_id'] ?>">
                            <?= htmlspecialchars($prod['name']) ?> (Qty: <?= $prod['quantity'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Quantity -->
            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" min="1" class="form-control" required>
            </div>

            <!-- Request Type -->
            <div class="mb-3">
                <label class="form-label">Request Type</label>
                <select name="request_type" class="form-control" required>
                    <option value="out">Stock Out</option>
                    <option value="return">Return</option>
                </select>
            </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-success">Submit</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>

      </form>

    </div>
  </div>
</div>


<script>
// Open Request Modal
function openRequestModal(id, name) {
    document.getElementById("modal_user_id").value = id;
    document.getElementById("modal_username").innerText = name;
    new bootstrap.Modal(document.getElementById("requestModal")).show();
}

// SEARCH + FILTER
document.addEventListener("input", filterTable);

function filterTable() {
    let search = document.getElementById("searchBox").value.toLowerCase();
    let role = document.getElementById("roleFilter").value;
    let zone = document.getElementById("zoneFilter").value;

    let rows = document.querySelectorAll("#usersTable tbody tr");

    rows.forEach(row => {
        let username = row.children[1].innerText.toLowerCase();
        let user_role = row.children[2].innerText;
        let user_zone = row.children[3].innerText.toLowerCase();

        let matchSearch = username.includes(search) || user_zone.includes(search);
        let matchRole = (role === "" || user_role === role);
        let matchZone = (zone === "" || user_zone === zone.toLowerCase());

        if (matchSearch && matchRole && matchZone) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}
</script>

<!--  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
</body>
</html>
