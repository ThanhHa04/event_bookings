<?php
session_start();
require_once "../config.php";
require_once "../includes/db_connect.php";

if (!isset($_GET["event_id"]) || empty($_GET["event_id"])) {
    echo "Lỗi: Không tìm thấy sự kiện.";
    exit();
}
$event_id = $_GET["event_id"];
// Lấy thông tin sự kiện
$stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "Lỗi: Sự kiện không tồn tại.";
    exit();
}

if (isset($_SESSION["user_id"])) {
    $_SESSION["booking"] = [
        "event_id" => $event_id,
        "event_name" => $event["event_name"]
    ];
}

$stmt = $pdo->prepare("
    SELECT * FROM seats 
    WHERE event_id = ? 
    ORDER BY LEFT(seat_number, 1), CAST(SUBSTRING(seat_number, 2) AS UNSIGNED)
");
$stmt->execute([$event_id]);
$seats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="vi">
<head> 
    <meta charset="UTF-8">
    <title>Mua vé - <?php echo htmlspecialchars($event["name"]); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/images/icove.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../assets/css/payment.css">
    <link rel="stylesheet" href="../assets/css/seat.css">
</head>
<body>
    <?php include "../includes/header.php"; ?>
    <?php include "../includes/login_modal.php"; ?>
    <?php include "../includes/register_modal.php"; ?>
    <div class="detail-wrapper">
    <div class="detail-left">
        <h1 class="event-title"><?= htmlspecialchars($event['event_name']) ?></h1>

        <div class="event-meta">
        <p><strong><i class="fa-solid fa-clock"></i> Thời gian:</strong> <?= date("H:i d/m/Y", strtotime($event['start_time'])) ?></p>
        <p><strong><i class="fa-solid fa-location-dot"></i> Địa điểm:</strong> <?= htmlspecialchars($event['location']) ?></p>
        </div>

        <div class="price-box">
        <p>🎟 Giá vé từ:</p>
        <h2><?= number_format($event['price']) ?> đ</h2>
        </div>

        <?php if (isset($_SESSION["user_id"])): ?>
            <button type="button"
                    class="btn w-100 openModalBuy" style="background-color: #ff5722; color: white;"
                    data-id="<?= $event['event_id'] ?>"
                    data-type="<?= $event['event_type'] ?>">
                Mua vé ngay
            </button>
        <?php else: ?>
            <a href="#" class="buy-ticket openLogin">MUA VÉ NGAY</a>
        <?php endif; ?>
    </div>

    <div class="detail-right">
        <img src="<?= htmlspecialchars($event['event_img']) ?>" alt="<?= htmlspecialchars($event['event_name']) ?>">
    </div>
    </div>

    <?php include "../includes/ticket_modal.php"; ?>
    <?php include "../includes/footer.php"; ?>

    <script>
        $(document).ready(function () {
            let ticketPrice = <?php echo $event["price"]; ?>;
        
            $("#ticketQty").val(1);

            $("#ticketQty").on("input", function () {
                let quantity = parseInt($(this).val());
                let total = ticketPrice * quantity;
                $(".price-info").text(new Intl.NumberFormat("vi-VN").format(total) + " đ");
            });

            $(".openModalBuy").click(function () {
                $(".price-info").text(new Intl.NumberFormat("vi-VN").format(ticketPrice) + " đ");
            });
        });

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
