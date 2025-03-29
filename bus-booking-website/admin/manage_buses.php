<?php
session_start();
include('../includes/connect.php');
include('../includes/admin_middleware.php'); // Phải là admin
include('../includes/connect.php');
include('../includes/header.php');

// Kiểm tra quyền admin
if (!isset($_SESSION['userid']) || $_SESSION['isadmin'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Thêm mới xe bus
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $number = $_POST['bus_number'];
    $type = $_POST['bus_type'];
    $seats = $_POST['total_seats'];

    sqlsrv_query($conn, "
        INSERT INTO Buses (BusNumber, BusType, TotalSeats)
        VALUES (?, ?, ?)
    ", array($number, $type, $seats));
}

// Đổi trạng thái xe bus
if (isset($_GET['toggle'])) {
    $busID = intval($_GET['toggle']);
    $getStatus = sqlsrv_query($conn, "SELECT Status FROM Buses WHERE BusID = ?", array($busID));
    $row = sqlsrv_fetch_array($getStatus, SQLSRV_FETCH_ASSOC);
    $newStatus = ($row['Status'] == 'Đang hoạt động') ? 'Ngừng hoạt động' : 'Đang hoạt động';

    sqlsrv_query($conn, "UPDATE Buses SET Status = ? WHERE BusID = ?", array($newStatus, $busID));
    header("Location: manage_buses.php");
    exit();
}

// Lấy danh sách xe
$buses = sqlsrv_query($conn, "SELECT * FROM Buses ORDER BY BusNumber");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý xe bus</title>
    <link rel="stylesheet" href="assets/css/manage_buses.css">
</head>
<body>

<h2>🚌 Quản lý xe bus</h2>

<h3>Thêm xe mới</h3>
<form method="post">
    <input type="text" name="bus_number" placeholder="Biển số xe (VD: 51A-12345)" required>
    <select name="bus_type" required>
        <option value="">-- Chọn loại xe --</option>
        <option value="Ghế ngồi">Ghế ngồi</option>
        <option value="Giường nằm">Giường nằm</option>
        <option value="Limousine">Limousine</option>
    </select>
    <input type="number" name="total_seats" placeholder="Tổng số ghế" required>
    <button type="submit" name="add">Thêm xe</button>
</form>

<h3>Danh sách xe hiện có</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Biển số</th>
            <th>Loại xe</th>
            <th>Số ghế</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($bus = sqlsrv_fetch_array($buses, SQLSRV_FETCH_ASSOC)) : ?>
            <tr>
                <td><?= $bus['BusID'] ?></td>
                <td><?= $bus['BusNumber'] ?></td>
                <td><?= $bus['BusType'] ?></td>
                <td><?= $bus['TotalSeats'] ?></td>
                <td><?= $bus['Status'] ?></td>
                <td class="actions">
                    <a href="edit_bus.php?id=<?= $bus['BusID'] ?>">✏️</a>
                    <a class="toggle" href="?toggle=<?= $bus['BusID'] ?>" onclick="return confirm('Bạn muốn đổi trạng thái xe này?')">🔁</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
