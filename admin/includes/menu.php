<div class="sidebar d-flex flex-column">
    <div class="logo">
        <i class="bi bi-shield-lock"></i> ADMIN PANEL
    </div>
    <nav class="nav flex-column mt-3 px-2">
        <a class="nav-link <?= ($current_page == 'dashboard') ? 'active' : '' ?>" href="dashboard.php">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a class="nav-link <?= ($current_page == 'users') ? 'active' : '' ?>" href="users.php">
            <i class="bi bi-person"></i> Quản lý tài khoản
        </a>
        <a class="nav-link <?= ($current_page == 'events') ? 'active' : '' ?>" href="events.php">
            <i class="bi bi-calendar-event"></i> Quản lý sự kiện
        </a>
        <a class="nav-link <?= ($current_page == 'orders') ? 'active' : '' ?>" href="orders.php">
            <i class="bi bi-ticket-perforated"></i> Quản lý đơn hàng
        </a>
        <a class="nav-link <?= ($current_page == 'history') ? 'active' : '' ?>" href="history.php">
            <i class="bi bi-check-circle"></i> Quản lý thanh toán
        </a>
        <a class="nav-link" href="logout.php">
            <i class="bi bi-box-arrow-right"></i> Đăng xuất
        </a>
    </nav>
</div>
