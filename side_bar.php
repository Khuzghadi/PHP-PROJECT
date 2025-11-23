<?php
// sidebar.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
        width: 100%;
        height: 100%;
        overflow-x: hidden;
        margin: 0;
        padding: 0;
    }
    #sidebar {
      min-height: 100vh;
      transition: all 0.3s;
    }
    .sidebar-collapsed {
      width: 70px !important;
    }
    .sidebar-collapsed .nav-link span {
      display: none;
    }
    #sidebar {
        background-color: #bde1d7;
    }
    .icon-color {
        color: #198754 !important;
    }
    .sidebar-collapsed {
        width: 70px !important;
    }
    .sidebar-collapsed .nav-link span {
        display: none;
    }

  </style>
</head>
<body>
<div class="d-flex">
  <!-- Sidebar -->
  <div id="sidebar" style="background-color: #e4f2b4;">

    <ul class="nav flex-column">
      <li class="nav-item"><a href="dashboard.php" target="f2" class="nav-link text-white"><i class="bi bi-speedometer2 icon-color"></i> <span>Dashboard</span></a></li>
      <li class="nav-item">
        <a class="nav-link text-white" target="f2" data-bs-toggle="collapse" href="#stockMenu" role="button"><i class="bi bi-box-seam icon-color"></i> <span>Stock Management</span></a>
        <div class="collapse" id="stockMenu">
          <ul class="nav flex-column ms-3">
            <li><a href="#" target="f2" class="nav-link text-white">Add Product</a></li>
            <li><a href="#" target="f2" class="nav-link text-white">Add Purchase Invoice</a></li>
            <li><a href="display_stock.php" target="f2" class="nav-link text-white">View Stock</a></li>
          </ul>
        </div>
      </li>
      <li class="nav-item"><a href="#" target="f2" class="nav-link text-white"><i class="bi bi-people icon-color"></i> <span>User Management</span></a></li>
      <li class="nav-item">
        <a class="nav-link text-white" data-bs-toggle="collapse" href="#requestMenu" role="button"><i class="bi bi-inbox icon-color"></i> <span>Requests & Allocation</span></a>
        <div class="collapse" id="requestMenu">
          <ul class="nav flex-column ms-3">
            <li><a href="#" target="f2" class="nav-link text-white">View Requests</a></li>
            <li><a href="#" target="f2" class="nav-link text-white">Pending Allocations</a></li>
          </ul>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white" data-bs-toggle="collapse" href="#reportMenu" role="button"><i class="bi bi-bar-chart icon-color"></i> <span>Reporting</span></a>
        <div class="collapse" id="reportMenu">
          <ul class="nav flex-column ms-3">
            <li><a href="#" target="f2" class="nav-link text-white">Consumption</a></li>
            <li><a href="#" target="f2" class="nav-link text-white">Returns & Losses</a></li>
            <li><a href="#" target="f2" class="nav-link text-white">Broken Items</a></li>
          </ul>
        </div>
      </li>
      <li class="nav-item"><a href="#" target="f2" class="nav-link text-white"><i class="bi bi-chat-dots icon-color"></i> <span>Communication</span></a></li>
      <li class="nav-item mt-3"><a href="login.php" target="f2" class="nav-link text-danger"><i class="bi bi-box-arrow-right icon-color"></i> <span>Logout</span></a></li>
    </ul>
  </div>
</div>

<script>
  function adjustSidebar() {
    var width = window.innerWidth;
    var sidebar = document.getElementById('sidebar');
    if (width <= 70) {
      sidebar.classList.add('sidebar-collapsed');
    } else {
      sidebar.classList.remove('sidebar-collapsed');
    }
  }

  window.addEventListener('resize', adjustSidebar);
  window.addEventListener('load', adjustSidebar);
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>
</html>
