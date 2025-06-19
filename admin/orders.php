<?php
session_start();
require_once "../includes/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$search = $_GET['search'] ?? '';
$filter_date = $_GET['filter_date'] ?? '';
$selectedOrderId = $_GET['order_id'] ?? null;

$where = "1=1";
$params = [];

if ($search !== '') {
    $where .= " AND e.event_name LIKE ?";
    $params[] = "%$search%";
}
if ($filter_date !== '') {
    $where .= " AND DATE(o.created_at) = ?";
    $params[] = $filter_date;
}

$sql = "SELECT o.order_id, o.payment_id, o.event_id, o.created_at, o.quantity, e.event_name, p.pStatus FROM orders o JOIN events e ON o.event_id = e.event_id JOIN payments p ON o.payment_id = p.payment_id WHERE $where ORDER BY o.created_at DESC ";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn đặt vé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
  <?php $current_page = 'orders'; include "includes/menu.php"; ?>
  <?php if ($selectedOrderId) include "includes/ticket_modal.php"; ?>

  <div class="container mt-4" style="margin-left: 20px;">
      <h2><i class="bi bi-ticket-perforated"></i> Quản lý đơn đặt vé</h2>
      <div class="d-flex justify-content-between align-items-center flex-wrap my-3">
          <form method="GET" class="d-flex gap-2 align-items-center w-100">
              <input type="date" name="filter_date" value="<?= htmlspecialchars($filter_date) ?>" class="form-control" style="max-width: 200px;">
              <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Tìm kiếm tên sự kiện">
              <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
          </form>
      </div>
      <div class="table-responsive">
          <table class="table table-bordered table-hover table-striped">
              <thead class="table-dark">
                  <tr>
                      <th>Mã đơn</th>
                      <th>Sự kiện</th>
                      <th>Ngày đặt</th>
                      <th>Số lượng</th>
                      <th>Trạng thái thanh toán</th>
                      <th>Thao tác</th>
                  </tr>
              </thead>
              <tbody>
                  <?php if (empty($orders)): ?>
                      <tr><td colspan="6" class="text-center">Không có đơn đặt nào.</td></tr>
                  <?php else: ?>
                      <?php foreach ($orders as $order): ?>
                          <tr>
                              <td><?= htmlspecialchars($order['order_id']) ?></td>
                              <td><?= htmlspecialchars($order['event_name']) ?></td>
                              <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                              <td><?= $order['quantity'] ?></td>
                              <td><?= htmlspecialchars($order['pStatus']) ?></td>
                              <td>
                                  <a href="?order_id=<?= $order['order_id'] ?>" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> Xem
                                </a>
                              </td>
                          </tr>
                      <?php endforeach ?>
                  <?php endif ?>
              </tbody>
          </table>
      </div>
  </div>

  <?php if ($selectedOrderId): ?>
  <script>
      window.addEventListener("load", () => {
          const modal = new bootstrap.Modal(document.getElementById("ticketModal"));
          modal.show();
      });
  </script>
  <?php endif ?>
</body>
</html>
