<?php
require_once "../includes/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST["fullname"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        echo "<script>alert('Email đã tồn tại!'); window.location.href='../index.php';</script>";
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$fullname, $email, $password])) {
        echo "<script>alert('Đăng ký thành công! Vui lòng đăng nhập.'); window.location.href='../index.php';</script>";
    } else {
        echo "<script>alert('Lỗi đăng ký!'); window.location.href='../index.php';</script>";
    }
}
?>
