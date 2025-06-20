<?php
session_start();
require_once "../includes/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = $_POST['ticket_id'] ?? '';
    $seat_id = $_POST['seat_id'] ?? '';
    $new_status = $_POST['new_status'] ?? '';
    $order_id = $_GET['order_id'] ?? '';

    $allowed = ['Thành công', 'Đã hủy', 'Đã sử dụng'];
    if (!in_array($new_status, $allowed)) {
        die("Trạng thái không hợp lệ.");
    }

    $stmt = $pdo->prepare("UPDATE tickets SET tStatus = ? WHERE ticket_id = ?");
    $stmt->execute([$new_status, $ticket_id]);

    if ($seat_id) {
        $newSeatStatus = ($new_status === 'Đã hủy') ? 'Còn trống' : (($new_status === 'Thành công') ? 'Đã đặt' : null);

        if ($newSeatStatus) {
            $updateSeat = $pdo->prepare("UPDATE seats SET sStatus = ? WHERE seat_id = ?");
            $updateSeat->execute([$newSeatStatus, $seat_id]);
        }
    }

    header("Location: orders.php?order_id=" . urlencode($order_id));
    exit();
}
?>
