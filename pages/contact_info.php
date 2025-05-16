<?php
session_start();
require_once "../config.php";
require_once "../includes/db_connect.php";

// Kiểm tra nếu không có sự kiện ID, quay về trang chủ
if (!isset($_GET['event_id'])) {
    header("Location: home.php");
    exit();
}

$event_id = $_GET['event_id'];

// Lấy thông tin sự kiện
$stmt = $pdo->prepare("SELECT name, price FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    die("Lỗi: Sự kiện không tồn tại.");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhập Thông Tin Liên Hệ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Nhập Thông Tin Liên Hệ</h2>
    <p><strong>Sự kiện:</strong> <?= htmlspecialchars($event["name"]) ?></p>
    <p><strong>Giá vé:</strong> <?= number_format($event["price"], 0, ",", ".") ?> VND</p>
    
    <form action="payment_confirm.php" method="POST">
        <input type="hidden" name="event_id" value="<?= $event_id ?>">
        
        <div class="mb-3">
            <label class="form-label">Họ và tên</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Số lượng vé</label>
            <input type="number" name="quantity" class="form-control" min="1" value="1" required>
        </div>
        <button type="submit" class="btn btn-primary">Tiếp tục</button>
    </form>
</div>
</body>
</html>
