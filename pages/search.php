<?php
session_start();
require_once "../includes/db_connect.php";

$query = $_GET['query'] ?? '';
$time_filter = $_GET['time_filter'] ?? '';
$results = [];

$baseSql = "SELECT event_id, event_name, start_time, price, location, event_img, eStatus 
            FROM events 
            WHERE eStatus = 'Chưa diễn ra'";

$params = [];
if (!empty(trim($query))) {
    $baseSql .= " AND event_name LIKE ?";
    $params[] = "%" . $query . "%";
}

if ($time_filter === 'week') {
    $baseSql .= " AND WEEK(start_time) = WEEK(CURDATE()) AND YEAR(start_time) = YEAR(CURDATE())";
} elseif ($time_filter === 'month') {
    $baseSql .= " AND MONTH(start_time) = MONTH(CURDATE()) AND YEAR(start_time) = YEAR(CURDATE())";
}

$location = $event['location'] ?? '';
$parts = explode(',', $location);
$shortLocation = $location; 
if (count($parts) >= 2) {
    $shortLocation = trim($parts[count($parts) - 2]) . ', ' . trim($parts[count($parts) - 1]);
}

$baseSql .= " ORDER BY start_time  ASC";

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
    <link rel="stylesheet" href="../assets/css/event_type.css">
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
                    <?php
                        $location = $event['location'];
                        $parts = explode(',', $location);
                        $parts = array_map('trim', $parts);

                        if (count($parts) >= 2) {
                            $location_display = implode(', ', array_slice($parts, -2));
                        } else {
                            $location_display = $location;
                        }

                        $startTime = strtotime($event['start_time']);
                        $month = date("m", $startTime);
                        $day = date("d", $startTime);
                        $year = date("Y", $startTime);
                    ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow-sm" style="transition: 0.3s">
                            <a href="detail.php?event_id=<?= urlencode($event['event_id']) ?>" class="text-decoration-none text-dark">
                                <img src="<?= htmlspecialchars($event['event_img']) ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?= htmlspecialchars($event['event_name']) ?>">
                                <div class="card-body">
                                    <div class="date-tag">Tháng <?php echo $month; ?><br><strong><?php echo $day; ?></strong></div>
                                    <p class="card-title fw-bold"><?= htmlspecialchars($event['event_name']) ?></p>
                                    <p class="card-text" style="font-size: 14px; color: #666;"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($location_display) ?></p>
                                    <p class="price"><?= number_format($event['price']) ?>+</p>
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
    <script src="../assets/js/script.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const loginModal = new bootstrap.Modal(document.getElementById("loginModal"), { backdrop: "static" });

            document.querySelectorAll(".openLogin").forEach(btn => {
                btn.addEventListener("click", function (e) {
                    e.preventDefault();
                    loginModal.show();
                });
            });

            const myTicketBtn = document.getElementById("myTicketsBtn");
            if (myTicketBtn) {
                myTicketBtn.addEventListener("click", function (e) {
                    if (!isLoggedIn) {
                        e.preventDefault();
                        loginModal.show(); 
                    } else {
                        window.location.href = "../pages/my_tickets.php"; 
                    }
                });
            }
        });
    </script>
</body>


</html>