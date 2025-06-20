<?php
session_start();
require_once "../includes/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$eventId = $_GET['event_id'] ?? '';

if ($eventId) {
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM seats WHERE event_id = ? AND sStatus != 'Còn trống'");
    $stmtCheck->execute([$eventId]);
    $hasBooking = $stmtCheck->fetchColumn() > 0;

    if ($hasBooking) {
        $_SESSION['error'] = "Không thể xóa sự kiện vì đã có người đặt chỗ.";
    } else {
        $stmtEvent = $pdo->prepare("DELETE FROM events WHERE event_id = ?");
        $stmtEvent->execute([$eventId]);
    }
}

header("Location: events.php");
exit();
