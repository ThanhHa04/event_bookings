<?php
require_once '../includes/db_connect.php';

// Lấy tất cả event_id có trong bảng events
$stmt = $pdo->query("SELECT id FROM events");
$event_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Cấu hình 10x5
$seat_rows = range('A', 'E'); // 5 hàng: A-E
$seats_per_row = 10;          // 10 cột: 1-10

foreach ($event_ids as $event_id) {
    echo "<strong>Đang kiểm tra event_id = $event_id...</strong><br>";

    // Lấy giá từ bảng events
    $stmtEvent = $pdo->prepare("SELECT price FROM events WHERE id = ?");
    $stmtEvent->execute([$event_id]);
    $event_price = $stmtEvent->fetchColumn();

    if ($event_price === false) {
        echo "❌ Không tìm thấy giá cho event_id = $event_id. Bỏ qua.<br><br>";
        continue;
    }

    echo "✅ Giá ghế = {$event_price} VND.<br>";

    // Kiểm tra xem có ghế nào đã được đặt không
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM seats WHERE event_id = ? AND is_booked = 1");
    $stmtCheck->execute([$event_id]);
    $booked_count = $stmtCheck->fetchColumn();

    if ($booked_count > 0) {
        echo "❌ Bỏ qua event_id = $event_id vì đã có $booked_count ghế được đặt.<br><br>";
        continue; // bỏ qua sự kiện này, không xóa/tạo ghế mới
    }

    // Nếu không có ghế đã đặt → tiếp tục xóa + tạo ghế mới
    echo "✅ Không có ghế đã đặt. Xóa và tạo ghế mới cho event_id = $event_id...<br>";

    // Xóa ghế cũ
    $stmtDelete = $pdo->prepare("DELETE FROM seats WHERE event_id = ?");
    $stmtDelete->execute([$event_id]);

    // Tạo ghế mới
    foreach ($seat_rows as $row) {
        // Quy định VIP: hàng A, B → VIP; còn lại Regular
        $seat_type = ($row == 'A' || $row == 'B') ? 'vip' : 'regular';

        for ($j = 1; $j <= $seats_per_row; $j++) {
            $seat_number = $row . $j;
            $stmtSeat = $pdo->prepare("INSERT INTO seats (event_id, seat_number, is_booked, price, seat_type, created_at) VALUES (?, ?, 0, ?, ?, NOW())");
            $stmtSeat->execute([$event_id, $seat_number, $event_price, $seat_type]);
        }
    }

    echo "✓ Xong event_id = $event_id<br><br>";
}

echo "<strong style='color: green;'>Hoàn tất tạo ghế 10x5 (VIP + Regular) cho tất cả các event chưa có ghế đặt.</strong>";
?>
