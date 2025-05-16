<?php
session_start();
require_once "../includes/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    die("Bạn cần đăng nhập để xem vé.");
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM purchased_tickets WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$tableMap = [
    'music' => 'music_events',
    'visit' => 'visit_events',
    'special' => 'special_events',
    'featured' => 'featured_events',
    'events' => 'events'
];

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Vé đã mua</title>
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
            width: 120px;
            height: 120px;
            object-fit: cover;
            margin-right: 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<?php include "../includes/header.php"; ?>
<div class="container mt-4">
    <h2 class="mb-4">Vé đã mua</h2>
    <?php
    if (empty($tickets)) {
        echo "<p>Bạn chưa mua vé nào.</p>";
    }
    foreach ($tickets as $ticket) {
        $type = $ticket["event_type"];
        $eventTable = $tableMap[$type] ?? "events";
        $eventStmt = $pdo->prepare("SELECT * FROM $eventTable WHERE id = ?");
        $eventStmt->execute([$ticket["event_id"]]);
        $event = $eventStmt->fetch(PDO::FETCH_ASSOC);
        if (!$event) continue;

        $img = htmlspecialchars($event["image"]);
        $img = (str_starts_with($img, "http") ? $img : "../assets/images/" . $img);
    ?>
    <div class="ticket-card">
        <img src="<?= $img ?>" alt="Ảnh sự kiện">
        <div>
            <h5><?= htmlspecialchars($event["name"]) ?></h5>
            <p>Ngày tổ chức: <?= htmlspecialchars($event["date"]) ?></p>
            <p>Số lượng: <?= $ticket["quantity"] ?></p>
            <p>Người mua: <?= htmlspecialchars($ticket["full_name"]) ?></p>
            <p>Email: <?= htmlspecialchars($ticket["email"]) ?> | SĐT: <?= htmlspecialchars($ticket["phone"]) ?></p>
        </div>
    </div>
    <?php } ?>
</div>
</body>
<?php include "../includes/footer.php"; ?>
</html>
