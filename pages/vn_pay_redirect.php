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

// Tạo mã đơn hàng (chuẩn hóa giống vnpay_create_payment.php)
$vnp_TxnRef = date('ymd') . '_' . date('His') . '_' . sprintf('%04d', rand(0, 9999));
$vnp_OrderInfo = 'Thanh toán vé sự kiện: ' . $vnp_TxnRef;
$vnp_OrderType = 'billpayment';
$vnp_Amount = $totalAmount * 100;
$vnp_Locale = 'vn';
$vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
$vnp_CreateDate = date('YmdHis');
$vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes'));

// Build dữ liệu gửi đi
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

// Sắp xếp & tạo hash
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

// Lưu session payment để xử lý sau khi trả về
$_SESSION['payment'] = array(
    'order_id' => $vnp_TxnRef,
    'selected_seats' => $selected_seats,
    'total_amount' => $totalAmount,
    'event_id' => $_SESSION['booking']['event_id'],
    'full_name' => $_SESSION['booking']['full_name'],
    'email' => $_SESSION['booking']['email'],
    'phone' => $_SESSION['booking']['phone'],
    'create_date' => $vnp_CreateDate,
    'expire_date' => $vnp_ExpireDate
);


// Debug log (bạn có thể bật nếu muốn kiểm tra URL & thời gian):
/*
echo "<pre>";
echo "vnp_CreateDate: $vnp_CreateDate\n";
echo "vnp_ExpireDate: $vnp_ExpireDate\n";
echo "Redirect URL: $vnp_UrlFull\n";
echo "</pre>";
exit;
*/

// Redirect sang VNPAY
header('Location: ' . $vnp_UrlFull);
exit;
?>
