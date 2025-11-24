<?php
// user_sidebar.php
// This file expects session has been started and user authenticated.
// Minimal guard if included directly:
if(!isset($_SESSION)) session_start();
if(!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'staff'){
    die("ACCESS DENIED - Staff Only");
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="d-flex">
  <!-- Sidebar -->
  <nav class="flex-column bg-white border-end vh-100 p-3" style="width:260px">
    <div class="mb-4">
      <a href="user_dashboard.php" class="text-decoration-none">
        <h5 class="fw-bold">Inventory — Staff</h5>
        <small class="text-muted"><?= htmlspecialchars($_SESSION['user']) ?></small>
      </a>
    </div>

    <div class="mb-3 text-muted small">MAIN MENU</div>
    <a href="user_dashboard.php" class="d-block py-2 px-2 rounded <?php if(basename($_SERVER['PHP_SELF'])=='user_dashboard.php') echo 'bg-light'; ?>">Dashboard</a>

    <div class="mt-4 mb-2 text-muted small">STOCK OPERATIONS</div>
    <a href="request_stock.php" class="d-block py-2 px-2 rounded <?php if(basename($_SERVER['PHP_SELF'])=='request_stock.php') echo 'bg-light'; ?>">Request Stock</a>
    <a href="return_stock.php" class="d-block py-2 px-2 rounded <?php if(basename($_SERVER['PHP_SELF'])=='return_stock.php') echo 'bg-light'; ?>">Return Stock</a>

    <div class="mt-4 mb-2 text-muted small">INVENTORY REQUESTS</div>
    <a href="request_new_item.php" class="d-block py-2 px-2 rounded <?php if(basename($_SERVER['PHP_SELF'])=='request_new_item.php') echo 'bg-light'; ?>">Request New Item</a>

    <div class="mt-4 mb-2 text-muted small">MY ACTIVITY</div>
    <a href="my_requests.php" class="d-block py-2 px-2 rounded <?php if(basename($_SERVER['PHP_SELF'])=='my_requests.php') echo 'bg-light'; ?>">My Requests</a>

    <div class="mt-4 mb-2 text-muted small">ACCOUNT</div>
    <a href="logout.php" class="d-block py-2 px-2 rounded text-danger">Logout</a>
  </nav>

  <!-- Content wrapper will be opened in each page -->
  <div class="flex-grow-1 p-4">
