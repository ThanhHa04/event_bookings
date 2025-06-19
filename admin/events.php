<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once "../includes/db_connect.php";

// Xử lý trạng thái
$status = $_GET['status'] ?? 'upcoming';

$stmt = $pdo->prepare("SELECT * FROM events WHERE eStatus = ? ORDER BY start_time DESC");
$stmt->execute([$status === 'ended' ? 'Đã kết thúc' : 'Chưa diễn ra']);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý sự kiện</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
<?php $current_page = 'events'; include "includes/menu.php"; ?>
<?php include "includes/edit_modal.php"; ?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="bi bi-easel2"></i> Danh sách sự kiện</h2>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link <?= $status == 'upcoming' ? 'active' : '' ?>" href="?status=upcoming">
                    <i class="bi bi-calendar-event"></i> Chưa diễn ra
                </a>
            </li>
            <li class="nav-item ms-2">
                <a class="nav-link <?= $status == 'ended' ? 'active' : '' ?>" href="?status=ended">
                    <i class="bi bi-clock-history"></i> Đã kết thúc
                </a>
            </li>
        </ul>
        <a href="create_event.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Tạo mới sự kiện
        </a>
    </div>
    <?php if (empty($events)): ?>
        <div class="alert alert-info">Không có sự kiện nào.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 7%;">Mã</th>
                        <th style="width: 33%;">Sự kiện</th>
                        <th style="width: 7%;">Thời gian</th>
                        <th style="width: 33%;">Địa điểm</th>
                        <th style="width: 10%;" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?= htmlspecialchars($event['event_id']) ?></td>
                            <td><?= htmlspecialchars($event['event_name']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($event['start_time'])) ?></td>
                            <td><?= htmlspecialchars($event['location']) ?></td>
                            <td class="text-center">
                                <?php if ($status == 'upcoming'): ?>
                                    <button class="btn btn-sm btn-warning edit-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editEventModal"
                                    data-id="<?= $event['event_id'] ?>"
                                    data-name="<?= htmlspecialchars($event['event_name'], ENT_QUOTES) ?>"
                                    data-img="<?= htmlspecialchars($event['event_img'], ENT_QUOTES) ?>"
                                    data-start="<?= $event['start_time'] ?>"
                                    data-price="<?= $event['price'] ?>"
                                    data-location="<?= htmlspecialchars($event['location'], ENT_QUOTES) ?>"
                                    data-seats="<?= $event['total_seats'] ?>"
                                    data-type="<?= htmlspecialchars($event['event_type'], ENT_QUOTES) ?>"
                                    data-duration="<?= $event['duration'] ?>"
                                    data-status="<?= $event['eStatus'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                    <a href="delete_event.php?id=<?= $event['event_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xoá sự kiện này?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                    <a href="event_history.php?id=<?= $event['event_id'] ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="event_history.php?id=<?= $event['event_id'] ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const data = this.dataset;

            document.getElementById('eventId').value = data.id;
            document.getElementById('eventName').value = data.name;
            document.getElementById('startTime').value = data.start.slice(0, 16);
            document.getElementById('price').value = data.price;
            document.getElementById('duration').value = data.duration;
            document.getElementById('location').value = data.location;
            document.getElementById('eStatus').value = data.status;
            document.getElementById('totalSeats').value = data.seats;
            document.getElementById('eventType').value = data.type;
            document.getElementById('eventIdDisplay').textContent = data.id;

            const imgPath = data.img.startsWith('http') ? data.img : '../assets/images/' + data.img;
            document.getElementById('eventImagePreview').src = imgPath;
            document.getElementById('eventImageLink').value = imgPath;
        });
    });

    const inputImg = document.getElementById('eventImageInput');
    if (inputImg) {
        inputImg.addEventListener('change', function (event) {
            const [file] = event.target.files;
            if (file) {
                document.getElementById('eventImagePreview').src = URL.createObjectURL(file);
            }
        });
    }
});
</script>
</body>
</html>
