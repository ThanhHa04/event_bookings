<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once "../includes/db_connect.php";

$status = $_GET['status'] ?? 'upcoming';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$search = $_GET['search'] ?? '';
$filter_date = $_GET['filter_date'] ?? '';
$limit = 15;
$offset = ($page - 1) * $limit;

$params = [];
$where = "eStatus = ?";
$params[] = ($status === 'ended') ? 'Đã kết thúc' : 'Chưa diễn ra';

if ($search !== '') {
    $where .= " AND event_name LIKE ?";
    $params[] = "%$search%";
}
if ($filter_date !== '') {
    $where .= " AND DATE(start_time) = ?";
    $params[] = $filter_date;
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM events WHERE $where");
$countStmt->execute($params);
$total_events = $countStmt->fetchColumn();
$total_pages = ceil($total_events / $limit);

$query = "SELECT * FROM events WHERE $where ORDER BY start_time DESC ";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

$bookedStmt = $pdo->query("SELECT DISTINCT event_id FROM seats WHERE sStatus != 'Còn trống'");
$eventIdsWithBookedSeats = $bookedStmt->fetchAll(PDO::FETCH_COLUMN);

$maxIdStmt = $pdo->query("SELECT MAX(CAST(SUBSTRING(event_id, 2) AS UNSIGNED)) FROM events");
$maxId = $maxIdStmt->fetchColumn();
$nextEventId = 'E0' . str_pad((int)$maxId + 1, 2,'0', STR_PAD_LEFT);

$selectedEvent = null;
$seats = [];

if (isset($_GET['view'])) {
    $viewEventId = $_GET['view'];
    foreach ($events as $e) {
        if ($e['event_id'] === $viewEventId) {
            $selectedEvent = $e;
            break;
        }
    }

    if ($selectedEvent) {
        $stmtSeats = $pdo->prepare("SELECT * FROM seats WHERE event_id = ?");
        $stmtSeats->execute([$viewEventId]);
        $seats = $stmtSeats->fetchAll(PDO::FETCH_ASSOC);
    }
}
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
    <?php include "includes/event_modal.php"; ?>
    <?php if ($selectedEvent): ?>
        <?php include "includes/detail_modal.php"; ?>
    <?php endif ?>

    <div class="container mt-4" style="margin-left: 20px;">
        <h2 class="mb-4"><i class="bi bi-easel2"></i> Danh sách sự kiện</h2>
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <ul class="nav nav-pills">
                <li class="nav-item"><a class="nav-link <?= $status == 'upcoming' ? 'active' : '' ?>" href="?status=upcoming"><i class="bi bi-calendar-event"></i> Chưa diễn ra</a>    </li>
                <li class="nav-item ms-2"><a class="nav-link <?= $status == 'ended' ? 'active' : '' ?>" href="?status=ended"><i class="bi bi-clock-history"></i> Đã kết thúc</a></li>
            </ul>
            <form class="d-flex align-items-center gap-2" method="GET" action="" style="flex: 1; justify-content: flex-end;">
                <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
                <input type="date" class="form-control flex-shrink-0" name="filter_date" value="<?= htmlspecialchars($filter_date) ?>" style="max-width: 150px;">
                <input type="text" class="form-control" name="search" style="max-width: 300px;" placeholder="Tìm kiếm tên sự kiện" value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-outline-primary flex-shrink-0" type="submit"><i class="bi bi-search"></i></button>
                <button type="button" class="btn btn-success flex-shrink-0" id="createEventBtn" data-bs-toggle="modal" data-bs-target="#editEventModal"><i class="bi bi-plus-circle"></i> Sự kiện</button>
            </form>
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
                    <tbody id="event-body">
                        <?php foreach ($events as $event): ?>
                            <tr class="event-row">
                                <td><?= htmlspecialchars($event['event_id']) ?></td>
                                <td><?= htmlspecialchars($event['event_name']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($event['start_time'])) ?></td>
                                <td><?= htmlspecialchars($event['location']) ?></td>
                                <td class="text-center">
                                    <?php if ($status == 'upcoming'): ?>
                                        <?php $isBooked = in_array($event['event_id'], $eventIdsWithBookedSeats); ?>
                                        <button class="btn btn-sm btn-warning edit-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editEventModal"
                                            data-id="<?= $event['event_id'] ?>"
                                            data-name="<?= htmlspecialchars($event['event_name'], ENT_QUOTES) ?>"
                                            data-img="<?= htmlspecialchars($event['event_img']) ?>"
                                            data-start="<?= $event['start_time'] ?>"
                                            data-price="<?= $event['price'] ?>"
                                            data-location="<?= htmlspecialchars($event['location'], ENT_QUOTES) ?>"
                                            data-seats="<?= $event['total_seats'] ?>"
                                            data-type="<?= htmlspecialchars($event['event_type'], ENT_QUOTES) ?>"
                                            data-duration="<?= $event['duration'] ?>"
                                            data-status="<?= $event['eStatus'] ?>"
                                            data-booked="<?= $isBooked ? '1' : '0' ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="delete_event.php?event_id=<?= $event['event_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xoá sự kiện này?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                        <a href="events.php?status=<?= $status ?>&view=<?= $event['event_id'] ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                    <?php else: ?>
                                        <a href="events.php?status=<?= $status ?>&view=<?= $event['event_id'] ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <nav><ul class="pagination justify-content-center" id="pagination-container"></ul></nav>
        <?php endif ?>
    </div>

<script>
    const nextEventId = <?= json_encode($nextEventId) ?>;
    document.addEventListener('DOMContentLoaded', function () {
        const totalSeatsField = document.getElementById('totalSeats');
        const priceField = document.getElementById('price');
        const seatWarning = document.getElementById('seats-warning');

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

            seatWarning.innerHTML = '';
            if (data.booked === '1') {
                totalSeatsField.disabled = true;
                priceField.disabled = true;
                const warning = document.createElement('div');
                warning.className = 'text-danger mt-1 fw-semibold';
                warning.textContent = 'Không thể thay đổi số lượng ghế và giá vé vì đã có người đặt.';
                seatWarning.appendChild(warning);
            } else {
                totalSeatsField.disabled = false;
                priceField.disabled = false;
            }
            });
        });

        const nextEventId = "<?= $nextEventId ?>";
        document.getElementById('createEventBtn').addEventListener('click', () => {
            const form = document.querySelector('#editEventModal form');
            form.reset();

            document.getElementById('eventId').value = nextEventId;
            document.getElementById('eventIdDisplay').textContent = nextEventId;
            document.getElementById('oldEventImg').value = '';
            document.getElementById('eventImagePreview').src = '';
            document.getElementById('eventImageLink').value = '';
            document.getElementById('totalSeats').disabled = false;
            document.getElementById('price').disabled = false;
            document.getElementById('seats-warning').innerHTML = '';
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rows = document.querySelectorAll('.event-row');
    const rowsPerPage = 15;
    const totalPages = Math.ceil(rows.length / rowsPerPage);
    const pagination = document.getElementById('pagination-container');

    function showPage(page) {
        rows.forEach((row, index) => {
            row.style.display = (index >= (page - 1) * rowsPerPage && index < page * rowsPerPage) ? '' : 'none';
        });

        pagination.innerHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = 'page-item' + (i === page ? ' active' : '');
            li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            li.addEventListener('click', (e) => {
                e.preventDefault();
                showPage(i);
            });
            pagination.appendChild(li);
        }
    }

    showPage(1); // Bắt đầu từ trang 1
});
</script>

</body>
</html>
