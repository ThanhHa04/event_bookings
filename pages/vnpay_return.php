<?php
// Cấu hình error & timezone
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
date_default_timezone_set('Asia/Ho_Chi_Minh');

session_start();
require_once "../includes/db_connect.php";
require_once "../includes/vnpay_config.php";

// Lấy dữ liệu từ VNPAY trả về
$vnp_TxnRef = $_GET['vnp_TxnRef'] ?? '';
$vnp_Amount = $_GET['vnp_Amount'] ?? '';
$vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';
$vnp_TransactionStatus = $_GET['vnp_TransactionStatus'] ?? '';
$vnp_SecureHash = $_GET['vnp_SecureHash'] ?? '';
$vnp_TransactionNo = $_GET['vnp_TransactionNo'] ?? '';

$payment_session = $_SESSION['payment'] ?? null;
$event_id = $payment_session['event_id'] ?? null;

// Verify vnp_SecureHash
$inputData = [];
foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}
unset($inputData['vnp_SecureHash']);
ksort($inputData);

$hashData = '';
$i = 0;
foreach ($inputData as $key => $value) {
    $hashData .= ($i++ ? '&' : '') . urlencode($key) . "=" . urlencode($value);
}
$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

if ($secureHash === $vnp_SecureHash && $payment_session) {
    if ($vnp_ResponseCode == '00' && $vnp_TransactionStatus == '00') {
        // Thanh toán thành công
        $stmtUpdateTicket = $pdo->prepare("
            UPDATE purchased_tickets 
            SET payment_status = 'paid', vnp_transaction_no = ?, payment_time = NOW()
            WHERE user_id = ? AND vnp_transaction_no = ? AND payment_status = 'pending'
        ");
        $stmtUpdateTicket->execute([
            $vnp_TransactionNo,
            $_SESSION['user_id'],
            $vnp_TxnRef
        ]);

        $selected_seats = $payment_session['selected_seats'];
        $seat_numbers = [];

        $stmtUpdateSeat = $pdo->prepare("UPDATE seats SET is_booked = 1 WHERE id = ?");
        $stmtGetSeat = $pdo->prepare("SELECT seat_number FROM seats WHERE id = ?");

        foreach ($selected_seats as $seat_id) {
            $stmtUpdateSeat->execute([$seat_id]);
            $stmtGetSeat->execute([$seat_id]);
            $row = $stmtGetSeat->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $seat_numbers[] = $row['seat_number'];
            }
        }

        unset($_SESSION['payment']);
        echo "<script>
            alert('Đặt vé thành công!');
            window.location.href = '../pages/my_tickets.php';
        </script>";

    } else {
        // Thanh toán thất bại hoặc huỷ
        $stmtCancel = $pdo->prepare("
            UPDATE purchased_tickets 
            SET payment_status = 'cancel'
            WHERE user_id = ? AND event_id = ? AND payment_status = 'pending'
        ");
        $stmtCancel->execute([
            $_SESSION['user_id'],
            $event_id
        ]);

        unset($_SESSION['payment']);
        echo "<script>
            alert('Đã hủy đặt vé!');
            window.location.href = '../pages/home.php';
        </script>";
    }
} else {
    // Thanh toán thất bại hoặc huỷ
    $stmtCancel = $pdo->prepare("
        UPDATE purchased_tickets 
        SET payment_status = 'cancel'
        WHERE user_id = ? AND event_id = ? AND payment_status = 'pending'
    ");
    $stmtCancel->execute([
        $_SESSION['user_id'],
        $event_id
    ]);

    unset($_SESSION['payment']);
    echo "<script>
            alert('Đã hủy đặt vé!');
            window.location.href = '../pages/home.php';
        </script>";

}
?>
