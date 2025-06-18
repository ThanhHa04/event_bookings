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

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["fullname"] = $user["fullname"]; 
        header("Location: ../pages/home.php");
        exit();
    } else {
        header("Location: ../index.php?error=1");
        exit();
    }
}
?>
