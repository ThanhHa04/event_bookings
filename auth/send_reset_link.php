<?php
require_once "../includes/db_connect.php";

$email = $_POST['email'] ?? '';
if (!$email) die("Thiếu email!");

$stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    echo "<script>alert('Email không tồn tại!'); history.back();</script>";
    exit;
}

// Tạo token và hạn dùng
$token = bin2hex(random_bytes(16));
$expire = date("Y-m-d H:i:s", time() + 3600); // 1 tiếng

$stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expire = ? WHERE user_id = ?");
$stmt->execute([$token, $expire, $user['user_id']]);

// Link đặt lại mật khẩu
$link = "http://localhost/event_bookings/auth/reset_password.php?token=$token";
$subject = "Khôi phục mật khẩu TicketBox";
$message = "
    <h3>Yêu cầu khôi phục mật khẩu</h3>
    <p>Nhấn vào liên kết sau để đặt lại mật khẩu (hiệu lực trong 1 giờ):</p>
    <a href='$link'>$link</a>
";
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: TicketBox <halun05062004@gmail.com>";

if (mail($email, $subject, $message, $headers)) {
    echo "<script>alert('Liên kết khôi phục đã được gửi!'); window.location.href = '../pages/home.php';</script>";
} else {
    echo "<script>alert('Gửi email thất bại!'); history.back();</script>";
}
?>
