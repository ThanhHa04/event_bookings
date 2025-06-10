<?php
require_once '../includes/db_connect.php';

$event_id = $_GET['event_id'] ?? null;

if (!$event_id) {
    die("Thiếu event_id");
}

$seat_rows = ['A', 'B', 'C', 'D'];  // bạn có thể thay đổi
$seats_per_row = 10;                // ví dụ mỗi hàng có 10 ghế

// Xoá ghế cũ (nếu có, phòng tránh bị duplicate)
$stmt = $pdo->prepare("DELETE FROM seats WHERE event_id = ?");
$stmt->execute([$event_id]);

// Tạo ghế mới
for ($i = 0; $i < count($seat_rows); $i++) {
    for ($j = 1; $j <= $seats_per_row; $j++) {
        $seat_number = $seat_rows[$i] . $j;
        $stmt = $pdo->prepare("INSERT INTO seats (event_id, seat_number, is_booked, created_at) VALUES (?, ?, 0, NOW())");
        $stmt->execute([$event_id, $seat_number]);
    }
}

echo "Đã tạo ghế cho sự kiện event_id = $event_id thành công!";
?>
