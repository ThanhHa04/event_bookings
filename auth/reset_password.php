<?php
require_once "../includes/db_connect.php";

$token = $_GET['token'] ?? '';
if (!$token) die("Thiếu token!");

$stmt = $pdo->prepare("SELECT user_id, reset_expire FROM users WHERE reset_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user || strtotime($user['reset_expire']) < time()) {
    die("<script>alert('Liên kết không hợp lệ hoặc đã hết hạn!'); window.location.href = '../pages/home.php';</script>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (!$password || $password !== $confirm) {
        echo "<script>alert('Mật khẩu không khớp!');</script>";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expire = NULL WHERE user_id = ?");
        $stmt->execute([$hashed, $user['user_id']]);
        echo "<script>alert('Đổi mật khẩu thành công!'); window.location.href = '../pages/home.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt lại mật khẩu</title>
    <link rel="icon" href="../assets/images/icove.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="d-flex justify-content-center align-items-center" style="min-height: 100vh; background-color: #f6f6f6;">

<div class="modal-dialog modal-sm " style="border: 1px solid #ff672a; box-sizing: border-box; border-radius: 10px; width:300px;">
    <div class="modal-content">
        <!-- Header -->
        <div class="modal-header text-center position-relative" style="background-color: #ff672a; color: white; padding: 20px; display-flex; flex-direction: column; align-items: center;">
            <img src="../assets/images/gaudeptrai2.jpg" alt="Logo" class="header-logo">
            <h4 class="modal-title w-100"><b>Đặt lại mật khẩu</b></h4>
        </div>

        <!-- Form Body -->
        <div class="modal-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label" style="padding:10px 10px 0px 10px;">Mật khẩu mới</label>
                    <input type="password" name="password" class="form-control" placeholder="Mật khẩu mới" style="padding:10px;" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="padding: 0px 10px;">Xác nhận mật khẩu</label>
                    <input type="password" name="confirm" class="form-control" placeholder="Nhập lại mật khẩu" style="padding:10px;" required>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn w-75" style="padding:10px; margin:10px; background-color: #ff672a; color: white;">
                        Cập nhật mật khẩu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
