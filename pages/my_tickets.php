<?php
session_start();
require_once "../includes/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    die("Bạn cần đăng nhập để xem vé.");
}

$user_id = $_SESSION['user_id'];
$status = $_GET['status'] ?? 'all';

// Build câu query theo status
if ($status == 'paid') {
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE user_id = ? AND pStatus = 'paid' ORDER BY payment_at DESC");
} elseif ($status == 'pending') {
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE user_id = ? AND pStatus = 'pending' ORDER BY payment_at DESC");
} elseif ($status == 'cancelled') {
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE user_id = ? AND pStatus = 'cancel' ORDER BY payment_at DESC");
} else {
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE user_id = ? ORDER BY payment_at DESC");
}

$stmt = $pdo->prepare("
    SELECT 
        p.payment_id, p.user_id, p.payment_at, p.method, p.amount, p.fullname, p.email, p.phone, p.pStatus as payment_status,
        t.event_id, t.quantity, ts.seat_id, s.seat_number
    FROM payments p
    LEFT JOIN tickets t ON p.payment_id = t.payment_id
    LEFT JOIN ticket_seats ts ON t.ticket_id = ts.ticket_id
    LEFT JOIN seats s ON ts.seat_id = s.seat_id
    WHERE p.user_id = ?
    " . ($status !== 'all' ? " AND p.pStatus = ?" : "") . "
    ORDER BY p.payment_at DESC
");

$params = [$user_id];
if ($status !== 'all') {
    $params[] = $status === 'cancelled' ? 'cancel' : $status;
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
    <style>

        .ticket-card {
            display: flex;
            align-items: center;
            border: 1px solid #ccc;
            margin-bottom: 16px;
            padding: 12px;
            border-radius: 10px;
        }
        .ticket-card img {
            width: 40%;
            height: 100%;
            object-fit: cover;
            margin-right: 20px;
            border-radius: 8px;
        }
        .ticket-card {
            display: flex;
            align-items: center;
            border: 1px solid #ccc;
            margin-bottom: 16px;
            padding: 12px;
            border-radius: 10px;
        }

        .ticket-card img {
            width: 40%;
            height: auto;
            object-fit: cover;
            margin-right: 20px;
            border-radius: 8px;
        }

        .card-content {
            display: flex;
            justify-content: flex-start; /* không cần space-between */
            align-items: flex-start;
            gap: 20px;
            width: 100%;
        }

        .card-left {
            flex: 1;
        }

        .card-right {
            margin-left: auto;
            text-align: left; 
            min-width: auto;
        }
        
        .float-end{
            font-size:20px;
        }

    </style>
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
            <a class="nav-link <?= $status == 'paid' ? 'active' : '' ?>" href="?status=paid">Thành công</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $status == 'pending' ? 'active' : '' ?>" href="?status=pending">Đang xử lý</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $status == 'cancel' ? 'active' : '' ?>" href="?status=cancelled">Đã hủy</a>
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
                <p>Số lượng: <?= $ticket["quantity"] ?></p>
                <p>Ghế: <?= htmlspecialchars($ticket['seat_number']) ?></p>
                <p>Người mua: <?= htmlspecialchars($ticket["fullname"]) ?></p>
                <p>Email: <?= htmlspecialchars($ticket["email"]) ?> | SĐT: <?= htmlspecialchars($ticket["phone"]) ?></p>
            </div>
            <div class="card-right">
                <p>
                    <span class="float-end">
                        <?php if ($ticket['payment_status'] == 'paid'): ?>
                            <span class="badge bg-success">Thành công</span>
                        <?php elseif ($ticket['payment_status'] == 'pending'): ?>
                            <span class="badge bg-warning text-dark">Đang xử lý</span>
                        <?php elseif ($ticket['payment_status'] == 'cancel'): ?>
                            <span class="badge bg-danger">Đã hủy</span>
                        <?php else: ?>
                            <span class="badge bg-secondary"><?= htmlspecialchars($ticket['payment_status']) ?></span>
                        <?php endif; ?>
                    </span>
                </p>

            </div>
        </div>
    </div>

    <?php } ?>
</div>
<?php include "../includes/footer.php"; ?>
</body>
</html>
