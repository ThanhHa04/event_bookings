<?php
if (!isset($_GET['order_id'])) return;

require_once "../includes/db_connect.php";
$order_id = $_GET['order_id'];

$stmt = $pdo->prepare("
    SELECT e.event_name, e.start_time, e.event_img, e.eStatus,
           t.ticket_id, t.tStatus, s.seat_number, t.seat_id,
           p.fullname, p.email, p.phone
    FROM tickets t
    JOIN orders o ON t.order_id = o.order_id
    JOIN seats s ON t.seat_id = s.seat_id
    JOIN events e ON o.event_id = e.event_id
    JOIN payments p ON o.payment_id = p.payment_id
    WHERE t.order_id = ?
");
$stmt->execute([$order_id]);
$ticketDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($ticketDetails)) return;
?>

<!-- Modal hiển thị dạng thẻ -->
<div class="modal fade show" id="ticketModal" style="display:block; background: rgba(0,0,0,0.5);" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Chi tiết đơn hàng: <?= htmlspecialchars($order_id) ?></h5>
        <a href="orders.php" class="btn-close"></a>
      </div>
      <div class="modal-body">
        <?php foreach ($ticketDetails as $ticket): ?>
        <div class="card mb-3 shadow-sm border">
          <div class="row g-0">
            <div class="col-md-4">
              <img src="<?= htmlspecialchars($ticket['event_img']) ?>" class="img-fluid rounded-start" alt="event">
            </div>  
            <div class="col-md-8 position-relative">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($ticket['event_name']) ?></h5>
                <p class="card-text">
                  <strong>Ngày tổ chức:</strong> <?= $ticket['start_time'] ?><br>
                  <strong>Trạng thái sự kiện:</strong> <?= htmlspecialchars($ticket['eStatus']) ?><br>
                  <strong>Ghế:</strong> <?= htmlspecialchars($ticket['seat_number']) ?><br>
                  <strong>Người mua:</strong> <?= htmlspecialchars($ticket['fullname']) ?><br>
                  <strong>Email:</strong> <?= htmlspecialchars($ticket['email']) ?> |
                  <strong>SDT:</strong> <?= htmlspecialchars($ticket['phone']) ?>
                </p>

                <!-- Form cập nhật trạng thái vé -->
                <form method="POST" action="update_ticket.php?order_id=<?= htmlspecialchars($order_id) ?>" class="d-flex align-items-center gap-2 mt-2">
                  <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticket['ticket_id']) ?>">
                  <input type="hidden" name="seat_id" value="<?= htmlspecialchars($ticket['seat_id']) ?>">
                  <select name="new_status" class="form-select form-select-sm" style="width: auto;">
                      <option value="Thành công" <?= $ticket['tStatus'] == 'Thành công' ? 'selected' : '' ?>>Thành công</option>
                      <option value="Đã hủy" <?= $ticket['tStatus'] == 'Đã hủy' ? 'selected' : '' ?>>Đã hủy</option>
                  </select>
                  <button class="btn btn-sm btn-primary" type="submit">Cập nhật</button>
                </form>
              </div>

              <!-- Hiển thị badge trạng thái vé -->
              <div class="position-absolute top-0 end-0 p-2">
                <?php
                  $badgeClass = 'bg-secondary';
                  if ($ticket['tStatus'] == 'Thành công') $badgeClass = 'bg-info';
                  elseif ($ticket['tStatus'] == 'Đã hủy') $badgeClass = 'bg-danger';
                ?>
                <span class="badge <?= $badgeClass ?> px-3 py-2">
                  <?= htmlspecialchars($ticket['tStatus']) ?>
                </span>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach ?>
      </div>
    </div>
  </div>
</div>
