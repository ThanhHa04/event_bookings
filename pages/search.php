<?php
require_once "../includes/db_connect.php";

$query = $_GET['query'] ?? '';
$time_filter = $_GET['time_filter'] ?? '';
$results = [];

$baseSql = "SELECT id, name, start_at, location, image, eStatus 
            FROM events 
            WHERE eStatus = 'Chưa diễn ra'";

$params = [];
if (!empty(trim($query))) {
    $baseSql .= " AND name LIKE ?";
    $params[] = "%" . $query . "%";
}

if ($time_filter === 'week') {
    $baseSql .= " AND WEEK(start_at) = WEEK(CURDATE()) AND YEAR(start_at) = YEAR(CURDATE())";
} elseif ($time_filter === 'month') {
    $baseSql .= " AND MONTH(start_at) = MONTH(CURDATE()) AND YEAR(start_at) = YEAR(CURDATE())";
}

$baseSql .= " ORDER BY start_at  ASC";

$stmt = $pdo->prepare($baseSql);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
</head>
<body>
    <?php include "../includes/header.php"; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="m-0">
                <?php if (!empty($query)): ?>
                    Kết quả cho từ khóa: <strong><?= htmlspecialchars($query) ?></strong>
                <?php else: ?>
                    Danh sách sự kiện sắp tới
                <?php endif; ?>
            </h3>

            <!-- FILTER -->
            <form method="GET" class="d-flex align-items-center gap-2">
                <input type="hidden" name="query" value="<?= htmlspecialchars($query) ?>">
                <select name="time_filter" class="form-select w-auto" onchange="this.form.submit()">
                    <option value="">-- Tất cả thời gian --</option>
                    <option value="week" <?= $time_filter === 'week' ? 'selected' : '' ?>>Tuần này</option>
                    <option value="month" <?= $time_filter === 'month' ? 'selected' : '' ?>>Tháng này</option>
                </select>
            </form>
        </div>

        <hr>

        <?php if ($results): ?>
            <div class="row">
                <?php foreach ($results as $event): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow-sm" style="transition: 0.3s">
                            <a href="detail.php?event_id=<?= urlencode($event['id']) ?>" class="text-decoration-none text-dark">
                                <img src="<?= htmlspecialchars($event['image']) ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?= htmlspecialchars($event['name']) ?>">
                                <div class="card-body">
                                    <p class="card-title fw-bold"><?= htmlspecialchars($event['name']) ?></p>
                                    <p class="card-text text-muted" style="font-size: 14px"><?= htmlspecialchars($event['start_at']) ?></p>
                                    <p class="card-text" style="font-size: 14px; color: #666;">Địa điểm: <?= htmlspecialchars($event['location']) ?></p>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <h5>Không tìm thấy sự kiện nào phù hợp.</h5>
        <?php endif; ?>
    </div>

    <?php include "../includes/footer.php"; ?>
</body>


</html>