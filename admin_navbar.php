<?php

// SECURE CHECK
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("ACCESS DENIED — Admins Only");
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
  <div class="container-fluid">

    <!-- BRAND -->
    <a class="navbar-brand fw-bold" href="dashboard.php">
      InventoryAdmin
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="adminNavbar">

      <!-- LEFT MENU -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">

        <li class="nav-item">
          <a class="nav-link" href="dashboard.php">Dashboard</a>
        </li>

        <!-- PRODUCTS DROPDOWN -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
             Products
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="add_product.php">Add Product</a></li>
            <li><a class="dropdown-item" href="view_products.php">View Products</a></li>
          </ul>
        </li>

        <!-- SUPPLIERS -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
             Suppliers
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="add_supplier.php">Add Supplier</a></li>
            <li><a class="dropdown-item" href="supplier_list.php">Supplier List</a></li>
          </ul>
        </li>

        <!-- REQUESTS DROPDOWN (NEW) -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
             Requests
          </a>
          <ul class="dropdown-menu">

            <!-- Stock Requests -->
            <li><a class="dropdown-item" href="admin_stock_requests.php">
              Stock Requests (In/Out)
            </a></li>

            <!-- Missing Item Requests -->
            <li><a class="dropdown-item" href="admin_missing_requests.php">
              Missing Item Requests
            </a></li>

          </ul>
        </li>

        <!-- PURCHASE ORDERS UPDATED -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
             Purchase Orders
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="create_po.php">Create PO</a></li>
            <li><a class="dropdown-item" href="admin_po_list.php">PO List</a></li>
          </ul>
        </li>

        <!-- STOCK OPERATIONS -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
             Stock
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="stock_in.php">Stock In</a></li>
            <li><a class="dropdown-item" href="stock_out.php">Stock Out</a></li>
            <li><a class="dropdown-item" href="transactions.php">All Transactions</a></li>
          </ul>
        </li>

        <!-- USERS MANAGEMENT -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Users</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item"  href="users.php">View User</a></li>
            <li><a class="dropdown-item" href="create_user.php">Create User</a></li>
          </ul>
        </li>

      </ul>

      <!-- RIGHT SIDE -->
      <ul class="navbar-nav ms-auto">

        <li class="nav-item">
          <a class="nav-link text-warning fw-bold">
            <?= htmlspecialchars($_SESSION['user']) ?> (Admin)
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-danger" href="logout.php">
            Logout
          </a>
        </li>

      </ul>

    </div>
  </div>
</nav>
