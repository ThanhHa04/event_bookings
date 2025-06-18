<?php
session_start();
require_once "../includes/db_connect.php";
if (!isset($_SESSION["user_id"])) {
    die("Chưa đăng nhập hoặc user_id chưa được set trong session.");
}
if (!isset($_SESSION["booking"])) {
    die("Không có dữ liệu đặt vé.");
}
$user_id = $_SESSION["user_id"];
$booking = $_SESSION["booking"];
$event_id = $booking["event_id"];
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
    <title>Chọn ghế</title>
    <link rel="icon" href="../assets/images/icove.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/seat.css">
</head>
<body>
<div class="container mt-5">
    
    <h2 class="mb-4 text-center" style="color: white" ><i class="bi bi-ticket-perforated-fill"></i> Chọn ghế cho sự kiện</h2>

    <div class="booking-info mb-4 text-center">
        <p><strong>Họ tên:</strong> <?= htmlspecialchars($booking["fullname"]) ?> |
           <strong>Email:</strong> <?= htmlspecialchars($booking["email"]) ?> |
           <strong>SĐT:</strong> <?= htmlspecialchars($booking["phone"]) ?> |
           <strong>Phương thức thanh toán:</strong> <?= htmlspecialchars($booking["payment_method"]) ?></p>
    </div>

    <form id="seatForm" method="POST" action="../pages/vn_pay_redirect.php">

        <input type="hidden" name="total_amount" id="totalAmountInput">
        <input type="hidden" name="selected_seats" id="selectedSeatsInput">

        <!-- Màn hình / Sân khấu -->
        <div class="stage">MÀN HÌNH / SÂN KHẤU</div>

        <!-- Ghế -->
        <div class="seat-map mb-4">
            <?php
            $current_row = '';
            foreach ($seats as $seat):
                $seat_row = substr($seat["seat_number"], 0, 1); // N, V, S

                if ($seat_row !== $current_row) {
                    if ($current_row !== '') echo '</div>'; // đóng row cũ
                    echo '<div class="seat-row">';
                    echo '<div class="row-label">' . $seat_row . '</div>';
                    $current_row = $seat_row;
                }

                // Xác định class loại ghế
                $seat_type = strtolower($seat["seat_type"]); // normal, vip, standing
                $is_booked = $seat["sStatus"] === "Đã đặt";

                $seat_class = $is_booked ? "booked" : "available " . $seat_type;
            ?>
                <div class="seat <?= $seat_class ?>"
                    data-seat="<?= $seat["seat_id"] ?>"
                    data-price="<?= $seat["seat_price"] ?>"
                    <?= $is_booked ? "disabled" : "" ?>>
                    <?= htmlspecialchars($seat["seat_number"]) ?>
                </div>
            <?php endforeach; ?>
            </div> <!-- đóng row cuối -->
        </div>

        <!-- Legend -->
        <div class="legend">
            <div class="legend-item">
                <div class="legend-box" style="background-color: #198754;"></div> Ghế thường (N)
            </div>
            <div class="legend-item">
                <div class="legend-box" style="background-color: #d63384;"></div> Ghế VIP (V)
            </div>  
            <div class="legend-item">
                <div class="legend-box" style="background-color: #fd7e14;"></div> Ghế đã chọn
            </div>
            <div class="legend-item">
                <div class="legend-box" style="background-color: #dee2e6;"></div> Ghế đã đặt
            </div>
        </div>


        <div class="mb-4 text-center">
            <p class="total-price">Tổng tiền: <span id="totalPrice">0</span> VND</p>
        </div>

        <div class="text-center mb-5">
            <button type="submit" class="btn btn-white-custom btn-lg px-5">Xác nhận & Thanh toán</button>
        </div>
    </form>
</div>

<script>
    const selected = new Set();
    let totalPrice = 0;

    document.querySelectorAll(".seat.available").forEach(seat => {
        seat.addEventListener("click", function () {
            const seatId = this.getAttribute("data-seat");
            const price = parseFloat(this.getAttribute("data-price"));

            if (selected.has(seatId)) {
                selected.delete(seatId);
                this.classList.remove("selected");
                totalPrice -= price;
            } else {
                selected.add(seatId);
                this.classList.add("selected");
                totalPrice += price;
            }

            // Cập nhật các hidden input:
            document.getElementById("selectedSeatsInput").value = JSON.stringify(Array.from(selected));
            document.getElementById("totalAmountInput").value = totalPrice;

            // Hiển thị tổng tiền:
            document.getElementById("totalPrice").innerText = totalPrice.toLocaleString();
        });
    });
</script>
</body>
</html>
