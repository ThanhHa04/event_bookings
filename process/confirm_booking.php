<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Phương thức không hợp lệ.");
}

// Kiểm tra dữ liệu cần thiết
$required = ["event_id", "type", "fullname", "email", "phone", "method"];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        die("Thiếu thông tin: $field");
    }
}

$_SESSION["user_id"] = $_SESSION["user_id"];

// Lưu thông tin tạm vào session
$_SESSION["booking"] = [
    "event_id" => $_POST["event_id"],
    "event_type" => $_POST["type"],
    "fullname" => $_POST["fullname"],
    "email" => $_POST["email"],
    "phone" => $_POST["phone"],
    "payment_method" => $_POST["method"]
];

// Chuyển hướng sang bước chọn ghế
header("Location: ../pages/select_seats.php");
exit();
