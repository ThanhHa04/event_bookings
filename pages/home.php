<?php
require_once "../config.php";
require_once "../includes/db_connect.php";

$now = new DateTime();

// Lấy tất cả event để kiểm tra trạng thái
$stmtStatus = $pdo->query("SELECT id, start_at FROM events");
$allEvents = $stmtStatus->fetchAll(PDO::FETCH_ASSOC);

foreach ($allEvents as $event) {
    $start = new DateTime($event['start_at']);
    $end = clone $start;

    if ($now < $start) {
        $newStatus = "Chưa diễn ra";
    } else {
        $newStatus = "Đã diễn ra";
    }

    $stmtCheck = $pdo->prepare("SELECT eStatus FROM events WHERE id = ?");
    $stmtCheck->execute([$event['id']]);
    $currentStatus = $stmtCheck->fetchColumn();

    if ($currentStatus !== $newStatus) {
        $stmtUpdate = $pdo->prepare("UPDATE events SET eStatus = ? WHERE id = ?");
        $stmtUpdate->execute([$newStatus, $event['id']]);
    }
}
// Lấy danh sách sự kiện cho slider
$eventQuery = "SELECT * FROM events WHERE eStatus = 'Chưa diễn ra' ORDER BY RAND() LIMIT 5";
$eventResult = mysqli_query($conn, $eventQuery);
$active = 'active';

// Lấy danh sách sự kiện sắp diễn ra
$specialQuery = "SELECT * FROM events WHERE start_at >= CURDATE() AND eStatus = 'Chưa diễn ra' ORDER BY start_at ASC LIMIT 6";
$specialResult = mysqli_query($conn, $specialQuery);

// Bước 1: Lấy các event được đặt nhiều nhất (tối đa 6)
$popularQuery = " SELECT e.id, e.image, e.name FROM events e JOIN ( SELECT event_id, COUNT(*) AS total FROM purchased_tickets GROUP BY event_id ORDER BY total DESC ) AS pt ON pt.event_id = e.id WHERE e.eStatus = 'Chưa diễn ra' ORDER BY pt.total DESC LIMIT 6";
$stmt = $pdo->query($popularQuery);
$popularEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
$needed = 6 - count($popularEvents);
if ($needed > 0) {
    $excludedIds = array_column($popularEvents, 'id');
    $placeholders = rtrim(str_repeat('?,', count($excludedIds)), ',');
    $randomQuery = " SELECT id, image, name FROM events WHERE eStatus = 'Chưa diễn ra' " . (count($excludedIds) ? "AND id NOT IN ($placeholders)" : "") . "ORDER BY RAND() LIMIT $needed";
    $stmt = $pdo->prepare($randomQuery);
    $stmt->execute($excludedIds);
    $randomEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $featuredEvents = array_merge($popularEvents, $randomEvents);
} else {
    $featuredEvents = $popularEvents;
}

// Lấy sự kiện ca nhạc
$musicQuery = "SELECT * FROM events WHERE event_type = 'music' AND eStatus = 'Chưa diễn ra' ORDER BY start_at ASC LIMIT 8";
$musicResult = mysqli_query($conn, $musicQuery);
$musicEvents = mysqli_fetch_all($musicResult, MYSQLI_ASSOC); 
$eventChunks = array_chunk($musicEvents, 4);

// Lấy sự kiện tham quan
$visitQuery = "SELECT * FROM events WHERE event_type = 'visit' AND eStatus = 'Chưa diễn ra' ORDER BY start_at ASC LIMIT 8";
$visitResult = mysqli_query($conn, $visitQuery);
$visitEvents = mysqli_fetch_all($visitResult, MYSQLI_ASSOC);
$visitChunks = array_chunk($visitEvents, 4);
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - Bán vé sự kiện</title>
    <link rel="icon" href="../assets/images/icove.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>
    <?php include "../includes/header.php"; ?>
    <?php include "../includes/login_modal.php"; ?>
    <?php include "../includes/register_modal.php"; ?>

    <div class="container mt-3">
        <div id="eventSlider" class="carousel slide mx-auto" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php for ($i = 0; $i < mysqli_num_rows($eventResult); $i++): ?>
                    <button type="button" data-bs-target="#eventSlider" data-bs-slide-to="<?php echo $i; ?>" class="<?php echo $i == 0 ? 'active' : ''; ?>"></button>
                <?php endfor; ?>
            </div>

            <div class="carousel-inner">
                <?php 
                mysqli_data_seek($eventResult, 0); 
                $first = true;
                while ($row = mysqli_fetch_assoc($eventResult)): ?>
                    <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                        <a href="payment.php?event_id=<?php echo $row['id']; ?>">
                            <img src="<?php echo (str_starts_with($row['image'], 'http') ? $row['image'] : '../assets/images/' . htmlspecialchars($row['image'])); ?>" class="d-block" alt="Event Image">
                        </a>
                    </div>
                    <?php $first = false; ?>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Slider sự kiện đặc biệt -->
    <div class="container mt-5">
        <div id="specialEventSlider" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php 
                $specialEvents = mysqli_fetch_all($specialResult, MYSQLI_ASSOC); 
                $specialChunk = array_chunk($specialEvents, 6);
                foreach ($specialChunk as $index => $specialItems): ?>
                    <div class="carousel-item <?php echo $index == 0 ? 'active' : ''; ?>">
                        <div class="d-flex justify-content-center gap-4">
                            <?php foreach ($specialItems as $special): ?>
                                <a href="payment.php?event_id=<?php echo $special['id']; ?>">
                                    <div class="event-card">
                                        <img src="<?php echo (str_starts_with($special['image'], 'http') ? $special['image'] : '../assets/images/' . htmlspecialchars($special['image'])); ?>" alt="<?php echo htmlspecialchars($special['title']); ?>">
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#specialEventSlider" data-bs-slide="prev">
                <i class="bi bi-caret-left-fill"></i>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#specialEventSlider" data-bs-slide="next">
                <i class="bi bi-caret-right-fill"></i>
            </button>
        </div>
    </div>

    <!-- Sự kiện nổi bật -->
    <div class="container">
        <div class="col-md-6 col-lg-6 col-sm-9 col-xs-9 event_type_list mt-5">
            <h5>SỰ KIỆN NỔI BẬT</h5>
        </div>

        <?php if (!empty($featuredEvents)): ?>
            <div class="top-row">
                <?php for ($i = 0; $i < min(2, count($featuredEvents)); $i++): ?>
                    <a href="payment.php?event_id=<?php echo $featuredEvents[$i]['id']; ?>">
                        <img src="<?php echo (str_starts_with($featuredEvents[$i]['image'], 'http') ? $featuredEvents[$i]['image'] : '../assets/images/' . htmlspecialchars($featuredEvents[$i]['image'])); ?>" 
                            alt="<?php echo htmlspecialchars($featuredEvents[$i]['name']); ?>">
                    </a>
                <?php endfor; ?>
            </div>

            <div class="bottom-row mt-2">
                <?php for ($i = 2; $i < min(6, count($featuredEvents)); $i++): ?>
                    <a href="payment.php?event_id=<?php echo $featuredEvents[$i]['id']; ?>">
                        <img src="<?php echo (str_starts_with($featuredEvents[$i]['image'], 'http') ? $featuredEvents[$i]['image'] : '../assets/images/' . htmlspecialchars($featuredEvents[$i]['image'])); ?>" 
                            alt="<?php echo htmlspecialchars($featuredEvents[$i]['name']); ?>">
                    </a>
                <?php endfor; ?>
            </div>
        <?php else: ?>
            <p class="text-center mt-3">Không có sự kiện nổi bật nào.</p>
        <?php endif; ?>

    </div>

    <div class="container">
        <div class="col-md-6 col-lg-6 col-sm-9 col-xs-9 event_type_list mt-5">
            <h5>CA NHẠC</h5>
        </div>

        <?php foreach ($eventChunks as $chunk): ?>
            <div class="event-slider">
                <?php foreach ($chunk as $event): ?>
                    <div class="event-item">
                        <a href="payment.php?event_id=<?= $event['id'] ?>" style="text-decoration: none; color: inherit;">
                            <img src="<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['name']) ?>">
                            <p><?= htmlspecialchars($event['name']) ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>


    <div class="container">
        <div class="col-md-6 col-lg-6 col-sm-9 col-xs-9 event_type_list mt-5">
            <h5>THAM QUAN</h5>
        </div>

        <?php foreach ($visitChunks as $chunk): ?>
            <div class="event-slider">
                <?php foreach ($chunk as $event): ?>
                    <div class="event-item">
                        <a href="payment.php?event_id=<?= $event['id'] ?>" style="text-decoration: none; color: inherit;">
                            <img src="<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['name']) ?>">
                            <p><?= htmlspecialchars($event['name']) ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
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
