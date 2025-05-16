<?php
session_start();
require_once "../config.php";
require_once "../includes/db_connect.php";

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION["user_id"])) {
    header("Location: ../index.php");
    exit();
}

// Lấy dữ liệu từ URL
$event_id = $_GET['event_id'] ?? '';
$quantity = $_GET['quantity'] ?? '1';
$total_price = $_GET['total_price'] ?? '0';

// Kiểm tra giá trị hợp lệ
if (!is_numeric($quantity) || $quantity <= 0) {
    $quantity = '1';
}
if (!is_numeric($total_price) || $total_price < 0) {
    $total_price = '0';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhập Thông Tin Khách Hàng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container-form {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-submit {
            width: 100%;
            background-color: #28a745;
            color: white;
            padding: 12px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-submit:hover {
            background-color: #218838;
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<?php include "../includes/header.php"; ?>

<div class="container">
    <div class="container-form">
        <h3 class="text-center mb-4">Nhập Thông Tin Khách Hàng</h3>
        <form action="../process/payment_process.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Họ và Tên:</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Số điện thoại:</label>
                <input type="text" name="phone" class="form-control" required>
            </div>
            <!-- Truyền dữ liệu cần thiết -->
            <input type="hidden" name="event_id" value="<?= htmlspecialchars($event_id) ?>">
            <input type="hidden" name="quantity" value="<?= htmlspecialchars($quantity) ?>">
            <input type="hidden" name="total_price" value="<?= htmlspecialchars($total_price) ?>">

            <button type="submit" class="btn btn-submit">Tiếp tục</button>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>

</body>
</html>
