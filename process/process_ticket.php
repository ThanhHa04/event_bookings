<?php
session_start();
require_once "../config.php";
require_once "../includes/db_connect.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "Bạn cần đăng nhập để mua vé."]);
    exit;
}

$user_id        = $_SESSION["user_id"];
$type           = $_POST["type"] ?? '';
$event_id       = intval($_POST["event_id"] ?? 0);
$quantity       = intval($_POST["quantity"] ?? 1);
$full_name      = trim($_POST["full_name"] ?? '');
$email          = trim($_POST["email"] ?? '');
$phone          = trim($_POST["phone"] ?? '');
$payment_method = trim($_POST["payment_method"] ?? '');

if ($event_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Sự kiện không hợp lệ."]);
    exit;
}

// Truy vấn từ bảng events duy nhất
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo json_encode(["status" => "error", "message" => "Sự kiện không tồn tại hoặc đã bị xoá."]);
    exit;
}

$total_price = $event["price"] * $quantity;

try {
    $insert = $pdo->prepare("
        INSERT INTO purchased_tickets 
        (user_id, event_id, quantity, full_name, email, phone, payment_method, event_type, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $insert->execute([
        $user_id, $event_id, $quantity,
        $full_name, $email, $phone,
        $payment_method, $event["event_type"]
    ]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Lỗi khi lưu dữ liệu: " . $e->getMessage()]);
    exit;
}

echo json_encode([
    "status"  => "success",
    "message" => "🎉 Bạn đã đặt vé thành công!"
]);
