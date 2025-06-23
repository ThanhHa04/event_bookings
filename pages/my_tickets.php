<?php
session_start();
require_once "../includes/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    die("Bạn cần đăng nhập để xem vé.");
}

$user_id = $_SESSION['user_id'];
$status = $_GET['status'] ?? 'all';
$estatus = $_GET['estatus'] ?? 'all';
$tstatus = $_GET['tstatus'] ?? 'all';

$statusMap = [
    'upcoming' => 'Chưa diễn ra',
    'active' => 'Đang diễn ra',
    'ended' => 'Đã kết thúc',
    'cancelled' => 'Đã hủy',
];

// Xây dựng câu truy vấn động
$query = "
    SELECT 
        p.payment_id, p.user_id, p.payment_at, p.method, p.amount, p.fullname, p.email, p.phone,
        o.event_id, o.quantity, t.seat_id, s.seat_number,
        e.event_name, e.start_time, e.event_img, e.eStatus, t.tStatus
    FROM payments p
    LEFT JOIN orders o ON p.payment_id = o.payment_id
    LEFT JOIN tickets t ON o.order_id = t.order_id
    LEFT JOIN seats s ON t.seat_id = s.seat_id
    LEFT JOIN events e ON o.event_id = e.event_id
    WHERE p.user_id = ?
";

// Thêm điều kiện nếu có
$params = [$user_id];

if ($status !== 'all') {
    $query .= " AND e.eStatus = ?";
    $params[] = $statusMap[$status] ?? '';
}

if ($tstatus !== 'all') {
    $query .= " AND t.tStatus = ?";
    $params[] = $tstatus;
}

// Thêm sắp xếp
$query .= ($status === 'all' ? " ORDER BY e.start_time ASC" : " ORDER BY p.payment_at DESC");

// Chuẩn bị và thực thi
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Vé đã mua</title>
    <link rel="icon" href="../assets/images/icove.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../assets/css/ticket.css">
</head>
<body>
<?php include "../includes/header.php"; ?>
<div class="container mt-4">
    <h2 class="mb-4">Vé đã mua</h2>

    <!-- Tabs trạng thái -->
    <ul class="nav nav-tabs mb-4">
          <li class="nav-item">
            <a class="nav-link <?= $tstatus == 'all' ? 'active' : '' ?>" href="?tstatus=all&estatus=<?= $estatus ?>">Tất cả</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tstatus == 'Thành công' ? 'active' : '' ?>" href="?tstatus=Thành%20công&estatus=<?= $estatus ?>">Thành công</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tstatus == 'Đã hủy' ? 'active' : '' ?>" href="?tstatus=Đã%20hủy&estatus=<?= $estatus ?>">Đã hủy</a>
        </li>
    </ul>

    <?php
    if (empty($orders)) {
        echo "<p>Bạn chưa có vé nào trong mục này.</p>";
    }
    foreach ($orders as $ticket) {
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
                <h5><strong><?= htmlspecialchars($event["event_name"]) ?></strong></h5><br>
                <p>Ngày tổ chức: <?= htmlspecialchars($event["start_time"]) ?></p>
                <p>Email: <?= htmlspecialchars($ticket["email"]) ?> | SĐT: <?= htmlspecialchars($ticket["phone"]) ?></p>
                <p>Ghế: <?= htmlspecialchars($ticket['seat_number']) ?></p>
                <p>Người mua: <?= htmlspecialchars($ticket["fullname"]) ?></p>
                <p>Trạng thái sự kiện: <?= htmlspecialchars($ticket["eStatus"]) ?></p>
            </div>
            <div class="card-right">
                <div class="card-right">    
                    <span class="float-end">
                        <?php
                            $tStatus = $ticket['tStatus'];
                            if ($tStatus === 'Thành công') {
                                echo '<span class="badge bg-success">Thành công</span>';
                            } elseif ($tStatus === 'Đã hủy') {
                                echo '<span class="badge bg-danger">Đã hủy</span>';
                            } else {
                                echo '<span class="badge bg-warning text-dark">'.htmlspecialchars($tStatus).'</span>';
                            }
                        ?>
                    </span>
            </div>
            </div>
        </div>
    </div>

    <?php } ?>
</div>
<?php include "../includes/footer.php"; ?>
</body>
</html>
                            