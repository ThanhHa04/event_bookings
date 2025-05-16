<?php
require_once "../config.php";
require_once "../includes/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["fullname"] = $user["fullname"]; 
        echo "<script>alert('Đăng nhập thành công!'); window.location.href='../pages/home.php';</script>";
    } else {
        echo "<script>alert('Sai email hoặc mật khẩu!'); window.location.href='../index.php';</script>";
    }
}
?>
