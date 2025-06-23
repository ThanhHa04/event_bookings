<?php
session_start();
require_once "../includes/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
$search = $_GET['search'] ?? '';

$where = "1=1";
$params = [];

if ($search !== '') {
    $where .= " AND (user_id LIKE ? OR fullname LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$stmt = $pdo->prepare("SELECT user_id, fullname, email FROM users WHERE $where ORDER BY user_id DESC");
$stmt->execute($params);
$users = $stmt->fetchAll();


?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
<?php $current_page = 'users'; include "includes/menu.php"; ?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="bi bi-person"></i> Quản lý tài khoản</h2>
    <div class="d-flex justify-content-between align-items-center mb-3 w-100">
        <form method="GET" class="d-flex gap-2 w-100">
            <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="form-control" placeholder="Tìm theo ID hoặc tên người dùng">
            <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <table class="table table-bordered table-hover table-striped">
        <thead class="table-dark">
        <tr>
            <th>User id</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th class="text-center">Thao tác</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['user_id']) ?></td>
                <td><?= htmlspecialchars($user['fullname']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                
                <td class="text-center">
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal_<?= $user['user_id'] ?>">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <a href="delete_user.php?user_id=<?= $user['user_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa người dùng?')">
                        <i class="bi bi-trash"></i>
                    </a>
                </td>
            </tr>

            <div class="modal fade" id="editModal_<?= $user['user_id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="post" action="update_user.php">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Chỉnh sửa tài khoản người dùng</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']) ?>">
                                <div class="mb-3">
                                    <label class="form-label">Họ tên</label>
                                    <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Lưu</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
