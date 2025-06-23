<?php
session_start();
require_once "../config.php";
require_once "../includes/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        // Email không tồn tại
        header("Location: ../index.php?error=email");
        exit();
    }

    if (!password_verify($password, $user["password"])) {
        // Sai mật khẩu
        header("Location: ../index.php?error=password");
        exit();
    }

    // Đăng nhập thành công
    $_SESSION["user_id"] = $user["user_id"];
    $_SESSION["fullname"] = $user["fullname"]; 
    header("Location: ../pages/home.php");
    exit();
}
?>
