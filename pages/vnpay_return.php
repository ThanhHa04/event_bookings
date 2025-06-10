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

// Lấy session payment (đã lưu khi redirect sang VNPAY)
$payment_session = $_SESSION['payment'] ?? null;

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
    if ($i == 1) {
        $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashData .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
}
$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

// Kiểm tra kết quả thanh toán hợp lệ
if ($secureHash === $vnp_SecureHash && $vnp_ResponseCode == '00' && $vnp_TransactionStatus == '00' && $payment_session) {

    // Lấy dữ liệu từ session
    $user_id = $_SESSION['user_id'] ?? null;
    $event_id = $payment_session['event_id'];
    $selected_seats = $payment_session['selected_seats'];
    $total_amount = $payment_session['total_amount'];
    $full_name = $payment_session['full_name'];
    $email = $payment_session['email'];
    $phone = $payment_session['phone'];
    $seat_numbers = [];

    // Cập nhật trạng thái ghế
    $stmtUpdate = $pdo->prepare("UPDATE seats SET is_booked = 1 WHERE id = ?");

    foreach ($selected_seats as $seat_id) {
        $stmtUpdate->execute([$seat_id]);

        // Lấy seat_number để lưu vào purchased_tickets
        $stmtSeat = $pdo->prepare("SELECT seat_number FROM seats WHERE id = ?");
        $stmtSeat->execute([$seat_id]);
        $seat_number_row = $stmtSeat->fetch(PDO::FETCH_ASSOC);
        if ($seat_number_row) {
            $seat_numbers[] = $seat_number_row['seat_number'];
        }
    }

    // Insert vào purchased_tickets
    $stmtInsert = $pdo->prepare("INSERT INTO purchased_tickets 
        (user_id, event_id, quantity, seat_number, full_name, email, phone, amount, payment_status, vnp_transaction_no, payment_time) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

    $stmtInsert->execute([
        $user_id,
        $event_id,
        count($seat_numbers),
        implode(',', $seat_numbers),
        $full_name,
        $email,
        $phone,
        $total_amount,
        'paid',
        $vnp_TransactionNo
    ]);

    // Xóa session payment
    unset($_SESSION['payment']);

    // Hiển thị kết quả thành công
    echo "<h2 style='color: green; text-align: center; margin-top: 50px;'>🎉 Thanh toán thành công! 🎉</h2>";
    echo "<p style='text-align: center;'>Bạn đã mua các ghế: <strong>" . implode(', ', $seat_numbers) . "</strong></p>";
    echo "<p style='text-align: center;'>Tổng tiền: <strong>" . number_format($total_amount) . " VND</strong></p>";
    echo "<p style='text-align: center;'>Cảm ơn bạn đã sử dụng dịch vụ!</p>";
    echo "<p style='text-align: center; margin-top: 20px;'><a href='../pages/my_tickets.php'>👉 Xem vé đã mua</a></p>";

} else {
    // Giao dịch thất bại hoặc không hợp lệ
    echo "<h2 style='color: red; text-align: center; margin-top: 50px;'>❌ Thanh toán thất bại hoặc không hợp lệ!</h2>";
    echo "<p style='text-align: center;'>Vui lòng thử lại.</p>";
    echo "<p style='text-align: center; margin-top: 20px;'><a href='../index.php'>Quay về trang chủ</a></p>";
}
?>
