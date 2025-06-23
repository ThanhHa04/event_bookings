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

        // Cập nhật trạng thái purchased_orders
        $stmtUpdateTicket = $pdo->prepare("
            UPDATE payments 
            SET pStatus = 'paid', vnp_transaction_no = ?, payment_time = NOW()
            WHERE user_id = ? AND vnp_transaction_no = ? AND pStatus = 'pending'
        ");
        $stmtUpdateTicket->execute([
            $vnp_TransactionNo,
            $_SESSION['user_id'],
            $vnp_TxnRef
        ]);

        $selected_seats = $payment_session['selected_seats'];
        $seat_numbers = [];

        // Cập nhật trạng thái ghế
        $stmtUpdateSeat = $pdo->prepare("UPDATE seats SET sStatus = 'Đã đặt' WHERE seat_id = ?");
        $stmtGetSeat = $pdo->prepare("SELECT seat_number FROM seats WHERE seat_id = ?");

        foreach ($selected_seats as $seat_id) {
            $stmtUpdateSeat->execute([$seat_id]);
            $stmtGetSeat->execute([$seat_id]);
            $row = $stmtGetSeat->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $seat_numbers[] = $row['seat_number'];
            }
        }

        // Tạo order_id dạng O0 + số thứ tự
        $stmtCount = $pdo->query("SELECT COUNT(*) AS total FROM orders");
        $totalorders = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
        $order_id = 'O0' . ($totalorders + 1);

        $payment_id = $vnp_TxnRef;
        $created_at = date("Y-m-d H:i:s");
        $quantity = count($selected_seats);

        // Insert vào bảng orders
        $stmtInsertTicket = $pdo->prepare("
            INSERT INTO orders (order_id, payment_id, event_id, created_at, quantity)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmtInsertTicket->execute([
            $order_id,
            $payment_session['payment_id'],
            $event_id,
            $created_at,
            $quantity
        ]);

        $stmtCount = $pdo->query("SELECT COUNT(*) AS total FROM tickets");
        $currentTickets = (int) $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
        $stmtInsertSeat = $pdo->prepare("
            INSERT INTO tickets (ticket_id, order_id, seat_id, tStatus)
            VALUES (?, ?, ?, ?)
        ");
        $index = 1;
        foreach ($selected_seats as $seat_id) {
            $new_ticket_id = 'T0' . ($currentTickets + $index);
            $stmtInsertSeat->execute([
                $new_ticket_id,
                $order_id,
                $seat_id,
                'Thành công'
            ]);
            $index++;
        }

        unset($_SESSION['payment']);
        echo "<script>
            alert('Đặt vé thành công!');
            window.location.href = '../pages/my_tickets.php';
        </script>";
    } else {
        // Thanh toán thất bại hoặc huỷ
        $stmtCancel = $pdo->prepare("
            UPDATE payments 
            SET pStatus = 'cancel'
            WHERE user_id = ? AND payment_id = ? AND pStatus = 'pending'
        ");
        $stmtCancel->execute([
            $_SESSION['user_id'],
            $payment_session['payment_id']
        ]);

        unset($_SESSION['payment']);
        echo "<script>  
            alert('Đã hủy đặt vé!');
            window.location.href = '../pages/home.php';
        </script>";
    }
} else {
    // Trường hợp sai hash hoặc không có session
    $stmtCancel = $pdo->prepare("
        UPDATE purchased_orders 
        SET pStatus = 'cancel'
        WHERE user_id = ? AND payment_id = ? AND pStatus = 'pending'
    ");
    $stmtCancel->execute([
        $_SESSION['user_id'],
        $payment_session['payment_id']
    ]);

    unset($_SESSION['payment']);
    echo "<script>
        alert('Đã hủy đặt vé!');
        window.location.href = '../pages/home.php';
    </script>";
}
?>
