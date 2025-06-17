<?php
// Cấu hình VNPay
$vnp_TmnCode = '75GHUG8E'; // Mã Website TMNCode của bạn tại VNPAY
$vnp_HashSecret = 'CHNRHFLO3HHQPTACQCPQN7JYON3OVGF6'; // Chuỗi bí mật của bạn
$vnp_Url = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'; // URL thanh toán VNPAY
$vnp_Returnurl = 'http://localhost/event_bookings/pages/vnpay_return.php'; // URL trả về sau khi thanh toán

// (Nếu bạn có IPN sau này, có thể thêm:)
$vnp_Ipnurl = 'http://localhost/WebDatDoAn/vnpay_ipn.php';

// Cấu hình chung
$vnp_Version = '2.1.0';
$vnp_Locale = 'vn';
?>
