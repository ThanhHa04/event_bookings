<?php
session_start();
require_once "../includes/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    die("Bạn cần đăng nhập để xem vé.");
}

$user_id = $_SESSION['user_id'];
$status = $_GET['status'] ?? 'all';

$stmt = $pdo->prepare("
    SELECT 
        p.payment_id, p.user_id, p.payment_at, p.method, p.amount, p.fullname, p.email, p.phone,
        t.event_id, t.quantity, ts.seat_id, s.seat_number,
        e.event_name, e.start_time, e.event_img, e.eStatus
    FROM payments p
    LEFT JOIN tickets t ON p.payment_id = t.payment_id
    LEFT JOIN ticket_seats ts ON t.ticket_id = ts.ticket_id
    LEFT JOIN seats s ON ts.seat_id = s.seat_id
    LEFT JOIN events e ON t.event_id = e.event_id
    WHERE p.user_id = ?
    " . ($status !== 'all' ? " AND e.eStatus = ?" : "") . "
    ORDER BY " . ($status === 'all' ? 'e.start_time ASC' : 'p.payment_at DESC')."
");

$statusMap = [
    'upcoming' => 'Chưa diễn ra',
    'active' => 'Đang diễn ra',
    'ended' => 'Đã kết thúc',
    'cancelled' => 'Đã hủy',
];
$params = [$user_id];
if ($status !== 'all') {
    $params[] = $statusMap[$status] ?? '';
}
$stmt->execute($params);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Vé đã mua</title>
    <link rel="icon" href="../assets/images/icove.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/ticket.css">
</head>
<body>
<?php include "../includes/header.php"; ?>
<div class="container mt-4">
    <h2 class="mb-4">Vé đã mua</h2>

    <!-- Tabs trạng thái -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?= $status == 'all' ? 'active' : '' ?>" href="?status=all">Tất cả</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $status == 'upcoming' ? 'active' : '' ?>" href="?status=upcoming">Chưa diễn ra</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $status == 'active' ? 'active' : '' ?>" href="?status=active">Đang diễn ra</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $status == 'ended' ? 'active' : '' ?>" href="?status=ended">Đã kết thúc</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $status == 'cancelled' ? 'active' : '' ?>" href="?status=cancelled">Đã hủy</a>
        </li>
    </ul>

    <?php
    if (empty($tickets)) {
        echo "<p>Bạn chưa có vé nào trong mục này.</p>";
    }
    foreach ($tickets as $ticket) {
        $stmtEvent = $pdo->prepare("SELECT * FROM events WHERE event_id = ?");
        $stmtEvent->execute([$ticket["event_id"]]);
        $event = $stmtEvent->fetch(PDO::FETCH_ASSOC);
        if (!$event) continue;

        $img = htmlspecialchars($event["event_img"]);
        $img = (str_starts_with($img, "http") ? $img : "../assets/images/" . $img);
    ?>
    <div class="ticket-card">
        <img src="<?= $img ?>" alt="Ảnh sự kiện">
        <div class="card-content">
            <div class="card-left">
                <h5><?= htmlspecialchars($event["event_name"]) ?></h5>
                <p>Ngày tổ chức: <?= htmlspecialchars($event["start_time"]) ?></p>
                <p>Ghế: <?= htmlspecialchars($ticket['seat_number']) ?></p>
                <p>Người mua: <?= htmlspecialchars($ticket["fullname"]) ?></p>
                <p>Email: <?= htmlspecialchars($ticket["email"]) ?> | SĐT: <?= htmlspecialchars($ticket["phone"]) ?></p>
            </div>
            <div class="card-right">
                <span class="float-end">
                    <?php
                        $eStatus = $ticket['eStatus'];
                        if ($eStatus == 'Chưa diễn ra') {
                            echo '<span class="badge bg-info text-dark">Chưa diễn ra</span>';
                        } elseif ($eStatus == 'Đang diễn ra') {
                            echo '<span class="badge bg-success">Đang diễn ra</span>';
                        } elseif ($eStatus == 'Đã kết thúc') {
                            echo '<span class="badge bg-secondary">Đã kết thúc</span>';
                        } elseif ($eStatus == 'Đã bị hủy') {
                            echo '<span class="badge bg-danger">Đã hủy</span>';
                        } else {
                            echo '<span class="badge bg-warning text-dark">'.htmlspecialchars($eStatus).'</span>';
                        }
                    ?>
                </span>
            </div>
        </div>
    </div>

    <?php } ?>
</div>
<?php include "../includes/footer.php"; ?>
</body>
</html>
