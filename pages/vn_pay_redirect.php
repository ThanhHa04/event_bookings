<?php
// Cấu hình error & timezone
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
date_default_timezone_set('Asia/Ho_Chi_Minh');

session_start();
require_once "../includes/db_connect.php";
require_once "../includes/vnpay_config.php";

// Kiểm tra dữ liệu từ form
$selected_seats = isset($_POST['selected_seats']) ? json_decode($_POST['selected_seats'], true) : [];
$totalAmount = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0;

if (empty($selected_seats) || $totalAmount <= 0) {
    die("Dữ liệu không hợp lệ!");
}

// Tạo mã đơn hàng
$vnp_TxnRef = date('ymd') . '_' . date('His') . '_' . sprintf('%04d', rand(0, 9999));
$vnp_OrderInfo = 'Thanh toán vé sự kiện: ' . $vnp_TxnRef;
$vnp_OrderType = 'billpayment';
$vnp_Amount = $totalAmount * 100;
$vnp_Locale = 'vn';
$vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
$vnp_CreateDate = date('YmdHis');
$vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes'));

// Thông tin người dùng từ session
$user_id = $_SESSION['user_id'] ?? null;
$booking = $_SESSION['booking'] ?? null;

if (!$user_id || !$booking) {
    die("Bạn cần đăng nhập và chọn sự kiện trước khi thanh toán.");
}

$event_id = $booking['event_id'];
$fullname = $booking['fullname'];
$email = $booking['email'];
$phone = $booking['phone'];

// Cập nhật trạng thái ghế tạm thời (chưa có hiệu lực DB)
// Lấy danh sách seat_number
$seat_numbers = [];

$stmtGetSeatNumber = $pdo->prepare("SELECT seat_number FROM seats WHERE seat_id = ?");
foreach ($selected_seats as $seat_id) {
    $stmtGetSeatNumber->execute([$seat_id]);
    $row = $stmtGetSeatNumber->fetch(PDO::FETCH_ASSOC);
    if ($row) { 
        $seat_numbers[] = $row['seat_number'];
    }
}

// Tạo payment_id mới
$stmtMax = $pdo->query("SELECT MAX(CAST(SUBSTRING(payment_id, 3) AS UNSIGNED)) AS max_index FROM payments");
$maxRow = $stmtMax->fetch(PDO::FETCH_ASSOC);
$nextIndex = isset($maxRow['max_index']) ? (int)$maxRow['max_index'] + 1 : 1;
$payment_id = 'P0' . $nextIndex;

// Ghi dữ liệu "chờ thanh toán" vào `payments`
$stmtInsertPayment = $pdo->prepare("
    INSERT INTO payments (
        payment_id, user_id, payment_at, method, amount, fullname, email, phone, pStatus, vnp_transaction_no
    ) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?)
");

$stmtInsertPayment->execute([
    $payment_id,
    $user_id,
    'vnpay',           // Hoặc 'momo', tuỳ theo cổng thanh toán
    $totalAmount,
    $fullname,
    $email,
    $phone,
    'pending',
    $vnp_TxnRef
]);

// Lưu session cho bước xác nhận thanh toán thành công
$_SESSION['payment'] = array(
    'payment_id' => $payment_id,
    'vnp_transaction_no' => $vnp_TxnRef,
    'selected_seats' => $selected_seats,
    'total_amount' => $totalAmount,
    'event_id' => $event_id,
    'fullname' => $fullname,
    'email' => $email,
    'phone' => $phone,
    'create_date' => $vnp_CreateDate,
    'expire_date' => $vnp_ExpireDate
);

// Build dữ liệu gửi đi VNPAY
$inputData = array(
    "vnp_Version" => "2.1.0",
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_Amount" => $vnp_Amount,
    "vnp_Command" => "pay",
    "vnp_CreateDate" => $vnp_CreateDate,
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $vnp_IpAddr,
    "vnp_Locale" => $vnp_Locale,
    "vnp_OrderInfo" => $vnp_OrderInfo,
    "vnp_OrderType" => $vnp_OrderType,
    "vnp_ReturnUrl" => $vnp_Returnurl,
    "vnp_TxnRef" => $vnp_TxnRef,
    "vnp_ExpireDate" => $vnp_ExpireDate
);

ksort($inputData);
$hashdata = '';
$query = '';
$i = 0;
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
    $query .= urlencode($key) . "=" . urlencode($value) . '&';
}

$vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
$vnp_UrlFull = $vnp_Url . '?' . $query . 'vnp_SecureHash=' . $vnpSecureHash;

// Redirect sang VNPAY
header('Location: ' . $vnp_UrlFull);
exit;

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>
