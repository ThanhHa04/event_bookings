<?php
session_start();
require_once "../includes/db_connect.php";

// Lấy dữ liệu từ form
$event_id     = $_POST['event_id'];
$event_name   = $_POST['event_name'];
$start_time   = $_POST['start_time'];
$price        = $_POST['price'];
$duration     = $_POST['duration'];
$location     = $_POST['location'];
$total_seats  = (int)$_POST['total_seats'];
$event_type   = $_POST['event_type'];
$eStatus      = $_POST['eStatus'];
$old_img      = $_POST['old_event_img'];
$new_img_link = trim($_POST['event_img_link'] ?? '');
$event_img    = $old_img;

// Xử lý ảnh upload
if (isset($_FILES['event_img']) && $_FILES['event_img']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = "../assets/images/";
    $filename = uniqid("event_") . "_" . basename($_FILES["event_img"]["name"]);
    $targetFile = $uploadDir . $filename;

    if (move_uploaded_file($_FILES["event_img"]["tmp_name"], $targetFile)) {
        $event_img = $filename;
    }
} else if (!empty($new_img_link) && $new_img_link !== $old_img) {
    $event_img = $new_img_link;
}

// 1. Lấy số lượng ghế cũ
$stmt = $pdo->prepare("SELECT total_seats FROM events WHERE event_id = ?");
$stmt->execute([$event_id]);
$currentData = $stmt->fetch(PDO::FETCH_ASSOC);
$old_total_seats = (int)$currentData['total_seats'];
$seats_changed = ($old_total_seats !== $total_seats);

// 2. Nếu số ghế thay đổi, kiểm tra có ghế đã được đặt chưa
if ($seats_changed) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM seats WHERE event_id = ? AND sStatus != 'Còn trống'");
    $stmt->execute([$event_id]);
    $occupied = $stmt->fetchColumn();

    if ($occupied > 0) {
        $_SESSION['error'] = "Không thể thay đổi số lượng ghế vì có ghế đã được đặt!";
        header("Location: events.php");
        exit;
    }

    // Nếu không có ghế đã đặt thì xoá ghế cũ
    $pdo->prepare("DELETE FROM seats WHERE event_id = ?")->execute([$event_id]);

    // Tạo lại danh sách ghế mới
    $vipCount = floor($total_seats * 0.2);
    $regularCount = $total_seats - $vipCount;

    $row = 'A';
    $col = 1;

    function formatSeatNumber($r, $c) {
        return $r . $c;
    }

    function formatSeatId($i) {
        return 'S' . str_pad($i, 3, '0', STR_PAD_LEFT);
    }

    $stmt = $pdo->query("SELECT MAX(CAST(SUBSTRING(seat_id, 2) AS UNSIGNED)) AS max_id FROM seats");
    $max = $stmt->fetchColumn();
    $seatIndex = ($max ?? 0) + 1;

    $insertStmt = $pdo->prepare("
        INSERT INTO seats (seat_id, event_id, seat_type, seat_number, sStatus, seat_price)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    for ($i = 0; $i < $vipCount; $i++) {
        $seat_number = formatSeatNumber($row, $col++);
        if ($col > 10) { $col = 1; $row++; }
        $insertStmt->execute([
            formatSeatId($seatIndex++),
            $event_id,
            'vip',
            $seat_number,
            'Còn trống',
            round($price * 1.5)
        ]);
    }

    for ($i = 0; $i < $regularCount; $i++) {
        $seat_number = formatSeatNumber($row, $col++);
        if ($col > 10) { $col = 1; $row++; }
        $insertStmt->execute([
            formatSeatId($seatIndex++),
            $event_id,
            'normal',
            $seat_number,
            'Còn trống',
            $price
        ]);
    }
}

// 3. Cập nhật thông tin sự kiện (luôn thực hiện dù có thay đổi số ghế hay không)
$stmt = $pdo->prepare("
    UPDATE events SET
        event_name = :event_name,
        start_time = :start_time,
        price = :price,
        duration = :duration,
        location = :location,
        total_seats = :total_seats,
        event_type = :event_type,
        eStatus = :eStatus,
        event_img = :event_img
    WHERE event_id = :event_id
");

$success = $stmt->execute([
    'event_name'   => $event_name,
    'start_time'   => $start_time,
    'price'        => $price,
    'duration'     => $duration,
    'location'     => $location,
    'total_seats'  => $total_seats,
    'event_type'   => $event_type,
    'eStatus'      => $eStatus,
    'event_img'    => $event_img,
    'event_id'     => $event_id
]);

if ($success) {
    $_SESSION['success'] = $seats_changed
        ? "Cập nhật sự kiện và số lượng ghế thành công!"
        : "Cập nhật sự kiện thành công!";
} else {
    $_SESSION['error'] = "Có lỗi xảy ra khi cập nhật sự kiện.";
}

header("Location: events.php");
exit;
?>
