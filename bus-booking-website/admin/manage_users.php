<?php
session_start();
include('../includes/connect.php');
include('../includes/admin_middleware.php'); // Phải là admin
include('../includes/connect.php');
include('../includes/header.php');

// Bảo vệ quyền admin
if (!isset($_SESSION['userid']) || $_SESSION['isadmin'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Xoá người dùng
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Không cho xoá chính mình
    if ($id !== $_SESSION['userid']) {
        sqlsrv_query($conn, "DELETE FROM Users WHERE UserID = ?", array($id));
    }
    header("Location: manage_users.php");
    exit();
}

// Chuyển quyền User <-> Admin
if (isset($_GET['toggle_role'])) {
    $id = intval($_GET['toggle_role']);
    $stmt = sqlsrv_query($conn, "SELECT IsAdmin FROM Users WHERE UserID = ?", array($id));
    $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    $newRole = $user['IsAdmin'] ? 0 : 1;
    sqlsrv_query($conn, "UPDATE Users SET IsAdmin = ? WHERE UserID = ?", array($newRole, $id));
    header("Location: manage_users.php");
    exit();
}

// Lấy danh sách người dùng
$users = sqlsrv_query($conn, "SELECT * FROM Users ORDER BY CreatedAt DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý người dùng</title>
    <link rel="stylesheet" href="assets/css/manage_users.css">
</head>
<body>

<h2>👤 Quản lý tài khoản người dùng</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>SĐT</th>
            <th>Vai trò</th>
            <th>Ngày tạo</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($u = sqlsrv_fetch_array($users, SQLSRV_FETCH_ASSOC)) : ?>
            <tr>
                <td><?= $u['UserID'] ?></td>
                <td><?= $u['FullName'] ?></td>
                <td><?= $u['Email'] ?></td>
                <td><?= $u['PhoneNumber'] ?></td>
                <td><?= $u['IsAdmin'] ? 'Admin' : 'User' ?></td>
                <td><?= date('d/m/Y H:i', strtotime($u['CreatedAt']->format('Y-m-d H:i:s'))) ?></td>
                <td class="actions">
                    <?php if ($u['UserID'] !== $_SESSION['userid']) : ?>
                        <a class="toggle" href="?toggle_role=<?= $u['UserID'] ?>" title="Đổi vai trò">🔁</a>
                        <a class="delete" href="?delete=<?= $u['UserID'] ?>" onclick="return confirm('Xoá người dùng này?')">❌</a>
                    <?php else: ?>
                        <em>Chính bạn</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
