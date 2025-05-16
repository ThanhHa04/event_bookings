<?php
session_start();
require_once "../config.php";
require_once "../includes/db_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET["event_id"]) || !is_numeric($_GET["event_id"])) {
    echo "Lỗi: Không tìm thấy sự kiện đặc biệt.";
    exit();
}

$event_id = intval($_GET["event_id"]);
$stmt = $pdo->prepare("SELECT * FROM special_events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "Lỗi: Sự kiện đặc biệt không tồn tại.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mua vé đặc biệt - <?php echo htmlspecialchars($event["name"]); ?></title>
    <link rel="icon" href="../assets/images/icove.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php include "../includes/header.php"; ?>

<div class="container mt-3 mb-3">
    <div class="event-image mb-4 text-center">
        <img src="<?php echo (str_starts_with($event['image'], 'http') ? $event['image'] : '../assets/images/' . htmlspecialchars($event['image'])); ?>" class="img-fluid rounded" alt="Special Event Image">
    </div>

    <div class="event-info p-4" style="box-shadow: 0 4px 12px gray; border-radius: 20px;">
        <h2 class="mb-4"><?php echo htmlspecialchars($event["name"]); ?></h2>

        <p><i class="fa fa-calendar" style="color: #ff5722;"></i> <?php echo htmlspecialchars($event["date"]); ?></p>
        <p><i class="bi bi-geo-alt-fill" style="color: #ff5722;"></i> <?php echo htmlspecialchars($event["location"]); ?></p>
        <p><i class="bi bi-cash" style="color: #ff5722;"></i> <?php echo number_format($event["price"], 0, ",", "."); ?> VNĐ</p>

        <button type="button"
                class="btn w-100 openModalBuy" style="background-color: #ff5722; color: white;"
                data-id="<?= $event['id'] ?>"
                data-type="special">
            Mua vé ngay
        </button>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
<?php include "../includes/ticket_modal.php"; ?>
<script>
    $(document).ready(function() {
        let ticketPrice = <?php echo $event["price"]; ?>;

        function updateTotalPrice() {
            let quantity = parseInt($("#quantity").val());
            let totalPrice = ticketPrice * quantity;
            $("#totalPrice").text(new Intl.NumberFormat("vi-VN").format(totalPrice) + " đ");
        }

        $("#increase").click(function() {
            $("#quantity").val(function(i, val) { return +val + 1; });
            updateTotalPrice();
        });

        $("#decrease").click(function() {
            $("#quantity").val(function(i, val) { return Math.max(1, val - 1); });
            updateTotalPrice();
        });

        $("#quantity").on("input", updateTotalPrice);
    });
</script>

</body>
</html>
