<?php
session_start();
require_once "../includes/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $verified = isset($_POST['verified']) ? date('Y-m-d H:i:s') : null;

    $stmt = $pdo->prepare("UPDATE users SET fullname = ?, email = ?, email_verified_at = ? WHERE user_id = ?");
    $stmt->execute([$fullname, $email, $verified, $user_id]);

    $_SESSION['success'] = "Cập nhật thành công.";
    header("Location: users.php");
    exit();
}
?>
