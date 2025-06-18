<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <!-- Bootstrap & Icons -->
    <link rel="stylesheet" href="css/dashborad.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
        <div class="logo">
            <i class="bi bi-shield-lock"></i> ADMIN PANEL
        </div>
        <nav class="nav flex-column mt-3 px-2">
            <a class="nav-link active" href="#"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a class="nav-link" href="users.php"><i class="bi bi-person"></i> Quản lý tài khoản</a>
            <a class="nav-link" href="events.php"><i class="bi bi-calendar-event"></i> Quản lý sự kiện</a>
            <a class="nav-link" href="orders.php"><i class="bi bi-ticket-perforated"></i> Quản lý đơn hàng</a>
            <a class="nav-link" href="revenue.php"><i class="bi bi-bar-chart-line"></i> Doanh thu</a>
            <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="mb-4"><i class="bi bi-speedometer2"></i> Bảng điều khiển</h1>

        <!-- Thống kê nhanh -->
        <div class="row">
            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 card-icon"><i class="bi bi-people"></i></div>
                        <div>
                            <h5 class="card-title mb-1">Người dùng</h5>
                            <p class="card-text text-muted">123 tài khoản</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 card-icon"><i class="bi bi-calendar-event"></i></div>
                        <div>
                            <h5 class="card-title mb-1">Sự kiện</h5>
                            <p class="card-text text-muted">14 đang diễn ra</p>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 card-icon"><i class="bi bi-ticket-perforated"></i></div>
                        <div>
                            <h5 class="card-title mb-1">Vé đã bán</h5>
                            <p class="card-text text-muted">530 vé</p>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 card-icon"><i class="bi bi-currency-dollar"></i></div>
                        <div>
                            <h5 class="card-title mb-1">Doanh thu</h5>
                            <p class="card-text text-muted">120 triệu VND</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
