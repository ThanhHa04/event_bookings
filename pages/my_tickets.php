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
    $stmt = $pdo->prepare("SELECT * FROM purchased_tickets WHERE user_id = ? AND payment_status = 'paid' ORDER BY created_at DESC");
} elseif ($status == 'pending') {
    $stmt = $pdo->prepare("SELECT * FROM purchased_tickets WHERE user_id = ? AND payment_status = 'pending' ORDER BY created_at DESC");
} elseif ($status == 'cancelled') {
    $stmt = $pdo->prepare("SELECT * FROM purchased_tickets WHERE user_id = ? AND payment_status = 'cancelled' ORDER BY created_at DESC");
} else {
    $stmt = $pdo->prepare("SELECT * FROM purchased_tickets WHERE user_id = ? ORDER BY created_at DESC");
}

$stmt->execute([$user_id]);
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
            <a class="nav-link <?= $status == 'cancelled' ? 'active' : '' ?>" href="?status=cancelled">Đã hủy</a>
        </li>
    </ul>

    <?php
    if (empty($tickets)) {
        echo "<p>Bạn chưa có vé nào trong mục này.</p>";
    }
    foreach ($tickets as $ticket) {
        $stmtEvent = $pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmtEvent->execute([$ticket["event_id"]]);
        $event = $stmtEvent->fetch(PDO::FETCH_ASSOC);
        if (!$event) continue;

        $img = htmlspecialchars($event["image"]);
        $img = (str_starts_with($img, "http") ? $img : "../assets/images/" . $img);
    ?>
    <div class="ticket-card">
        <img src="<?= $img ?>" alt="Ảnh sự kiện">
        <div class="card-content">
            <div class="card-left">
                <h5><?= htmlspecialchars($event["name"]) ?> (<?= htmlspecialchars($event["event_type"]) ?>)</h5>
                <p>Ngày tổ chức: <?= htmlspecialchars($event["start_at"]) ?></p>
                <p>Số lượng: <?= $ticket["quantity"] ?></p>
                <p>Ghế: <?= htmlspecialchars($ticket['seat_number']) ?></p>
                <p>Người mua: <?= htmlspecialchars($ticket["full_name"]) ?></p>
                <p>Email: <?= htmlspecialchars($ticket["email"]) ?> | SĐT: <?= htmlspecialchars($ticket["phone"]) ?></p>
            </div>
            <div class="card-right">
                <p>Trạng thái thanh toán:
                    <span class="float-end">
                        <?php if ($ticket['payment_status'] == 'paid'): ?>
                            <span class="badge bg-success">Thành công</span>
                        <?php elseif ($ticket['payment_status'] == 'pending'): ?>
                            <span class="badge bg-warning text-dark">Đang xử lý</span>
                        <?php elseif ($ticket['payment_status'] == 'cancelled'): ?>
                            <span class="badge bg-danger">Đã hủy</span>
                        <?php else: ?>
                            <span class="badge bg-secondary"><?= htmlspecialchars($ticket['payment_status']) ?></span>
                        <?php endif; ?>
                    </span>
                </p>

                <p>Trạng thái xác nhận:
                    <span class="float-end">
                        <?php if ($ticket['is_confirmed'] == 1): ?>
                            <span class="text-success fw-bold">Đã xác nhận ✅</span>
                        <?php else: ?>
                            <span class="text-warning fw-bold">Chưa xác nhận ⏳</span>
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
