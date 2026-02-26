<?php
require_once 'config/database.php';
requireLogin();

// Get statistics
$stats = [];
$queries = [
    'total_rooms' => "SELECT COUNT(*) as count FROM rooms",
    'available_rooms' => "SELECT COUNT(*) as count FROM rooms WHERE status = 'Available'",
    'occupied_rooms' => "SELECT COUNT(*) as count FROM rooms WHERE status = 'Occupied'",
    'total_tenants' => "SELECT COUNT(*) as count FROM tenants",
    'active_bookings' => "SELECT COUNT(*) as count FROM bookings WHERE status IN ('Confirmed', 'Checked In')",
    'pending_payments' => "SELECT COUNT(*) as count FROM payments WHERE status = 'Pending'",
    'open_complaints' => "SELECT COUNT(*) as count FROM complaints WHERE status IN ('Open', 'In Progress')",
    'monthly_revenue' => "SELECT SUM(amount) as total FROM payments WHERE status = 'Paid' AND MONTH(payment_date) = MONTH(CURRENT_DATE())"
];

foreach ($queries as $key => $query) {
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $stats[$key] = $row['count'] ?? $row['total'] ?? 0;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst($page) ?> - Rental Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --sidebar-width: 260px;
        }
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1000;
        }
        .sidebar-brand {
            padding: 20px;
            font-size: 20px;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-menu {
            padding: 20px 0;
        }
        .sidebar-menu a {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left: 4px solid white;
        }
        .sidebar-menu a i {
            margin-right: 10px;
            width: 20px;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
        }
        .topbar {
            background: white;
            padding: 15px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
            border: none;
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 15px;
        }
        .stat-title {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #212529;
        }
        .bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .bg-gradient-success { background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%); color: white; }
        .bg-gradient-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
        .bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
        .bg-gradient-danger { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; }
        .content-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            .sidebar.active {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-building me-2"></i> Rental System
        </div>
        <div class="sidebar-menu">
            <a href="?page=dashboard" class="<?= $page == 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="?page=rooms" class="<?= $page == 'rooms' ? 'active' : '' ?>">
                <i class="fas fa-door-open"></i> Kamar
            </a>
            <a href="?page=tenants" class="<?= $page == 'tenants' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Penyewa
            </a>
            <a href="?page=bookings" class="<?= $page == 'bookings' ? 'active' : '' ?>">
                <i class="fas fa-calendar-check"></i> Booking
            </a>
            <!-- Kalender Booking -->
            <a href="modules/bookings/calendar.php" target="_blank" style="padding-left: 45px; font-size: 13px; opacity: 0.85;">
    <i class="fas fa-calendar-alt me-2"></i>Kalender Booking
</a>
            <a href="?page=payments" class="<?= $page == 'payments' ? 'active' : '' ?>">
                <i class="fas fa-money-bill-wave"></i> Pembayaran
            </a>
            <a href="?page=complaints" class="<?= $page == 'complaints' ? 'active' : '' ?>">
                <i class="fas fa-exclamation-triangle"></i> Komplain
            </a>
            <a href="?page=users" class="<?= $page == 'users' ? 'active' : '' ?>">
                <i class="fas fa-user-cog"></i> Pengguna
            </a>
            <a href="logout.php" style="margin-top: 50px; border-top: 1px solid rgba(255,255,255,0.1);">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
<!-- Topbar -->
<div class="topbar">
    <button class="btn btn-sm btn-outline-secondary d-md-none" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    <h4 class="mb-0"><?= ucfirst($page) ?></h4>
    
    <div class="d-flex align-items-center">
        <div class="me-3">
            <i class="fas fa-user-circle fa-2x"></i>
        </div>
        <div>
            <?php 
            // Ambil data dari session dengan nilai default jika kosong
            $fullName = $_SESSION['full_name'] ?? 'Pengguna'; 
            $role = $_SESSION['role'] ?? 'guest';
                    $displayRole = ($role == 'admin') ? 'Admin' : ucfirst($role);

            ?>
            <div class="fw-bold"><?= htmlspecialchars($fullName) ?></div>
            <small class="text-muted"><?= ucfirst($role) ?></small>
        </div>
    </div>
</div>

        <!-- Alert -->
        <?php $alert = getAlert(); if ($alert): ?>
            <div class="alert alert-<?= $alert['type'] ?> alert-dismissible fade show">
                <?= $alert['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Page Content -->
        <?php
        if ($page == 'dashboard') {
            include 'modules/dashboard.php';
        } else {
            $module_file = "modules/{$page}/index.php";
            if (file_exists($module_file)) {
                include $module_file;
            } else {
                echo '<div class="content-card"><h4>Module not found</h4></div>';
            }
        }
        ?>
    </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }
    </script>
</body>
</html>