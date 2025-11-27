<?php
if(!isset($_SESSION)) session_start();
if(!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'staff'){
    die("ACCESS DENIED - Staff Only");
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
/* ---- Sidebar Styling ---- */
.sidebar {
    width: 250px;
    height: 100vh;
    background: #ffffff;
    border-right: 1px solid #e5e5e5;
    padding: 25px 18px;
    position: fixed;
    top: 0;
    left: 0;
}

.sidebar .brand-title {
    font-size: 20px;
    font-weight: 700;
    color: #0d6efd;
    margin-bottom: 2px;
}

.sidebar small {
    font-size: 13px;
}

.menu-title {
    color: #6c757d;
    font-weight: 600;
    font-size: 12px;
    margin-top: 25px;
    margin-bottom: 5px;
    letter-spacing: 1px;
}

.sidebar a.menu-link {
    display: block;
    padding: 10px 12px;
    border-radius: 8px;
    text-decoration: none;
    color: #333;
    font-size: 15px;
    margin-bottom: 4px;
    transition: all 0.2s ease;
}

.sidebar a.menu-link:hover {
    background: #f2f6ff;
    color: #0d6efd;
}

.sidebar a.menu-active {
    background: #e7f0ff;
    color: #0d6efd !important;
    font-weight: 600;
}

/* Icon styling */
.menu-link i {
    width: 22px;
    text-align: center;
    margin-right: 6px;
}

/* Push content right */
.page-wrapper {
    margin-left: 260px;
    padding: 25px;
}
</style>

<!-- FontAwesome Icons -->
<script src="https://kit.fontawesome.com/a2e0e6ad65.js" crossorigin="anonymous"></script>

<div class="sidebar">
    <div class="mb-4">
    <div class="brand-title">Inventory Staff</div>
    <small class="text-muted d-block"><?= htmlspecialchars($_SESSION['user']) ?></small>
    <small class="text-primary fw-semibold">Zone: <?= htmlspecialchars($_SESSION['zone_name'] ?? 'N/A') ?></small>
</div>


    <div class="menu-title">MAIN MENU</div>
    <a href="user_dashboard.php" 
       class="menu-link <?php if(basename($_SERVER['PHP_SELF'])=='user_dashboard.php') echo 'menu-active'; ?>">
       <i class="fas fa-home"></i> Dashboard
    </a>

    <div class="menu-title">STOCK OPERATIONS</div>
    <a href="request_stock.php"
       class="menu-link <?php if(basename($_SERVER['PHP_SELF'])=='request_stock.php') echo 'menu-active'; ?>">
       <i class="fas fa-plus-circle"></i> Request Stock
    </a>

    <a href="return_stock.php"
       class="menu-link <?php if(basename($_SERVER['PHP_SELF'])=='return_stock.php') echo 'menu-active'; ?>">
       <i class="fas fa-undo-alt"></i> Return Stock
    </a>

    <div class="menu-title">INVENTORY REQUESTS</div>
    <a href="request_new_item.php"
       class="menu-link <?php if(basename($_SERVER['PHP_SELF'])=='request_new_item.php') echo 'menu-active'; ?>">
       <i class="fas fa-lightbulb"></i> Request New Item
    </a>

    <div class="menu-title">MY ACTIVITY</div>
    <a href="my_requests.php"
       class="menu-link <?php if(basename($_SERVER['PHP_SELF'])=='my_requests.php') echo 'menu-active'; ?>">
       <i class="fas fa-list"></i> My Requests
    </a>
<div class="menu-title">STOCK HISTORY</div>
<a href="my_stock_history.php"
   class="menu-link <?php if(basename($_SERVER['PHP_SELF'])=='my_stock_history.php') echo 'menu-active'; ?>">
   <i class="fas fa-history"></i> My Stock History
</a>

    <div class="menu-title">ACCOUNT</div>
    <a href="logout.php" class="menu-link text-danger">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>

</div>

<!-- Content Wrapper starts in page -->
<div class="page-wrapper">
