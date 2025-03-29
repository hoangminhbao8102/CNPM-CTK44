<?php
session_start();
include('../includes/connect.php');
include('../includes/admin_middleware.php'); // Phải là admin
include('../includes/connect.php');
include('../includes/header.php');

// Bảo vệ admin
if (!isset($_SESSION['userid']) || $_SESSION['isadmin'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Lấy ID xe
$busID = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$busID) {
    die("Không tìm thấy xe.");
}

// Lấy thông tin xe hiện tại
$stmt = sqlsrv_query($conn, "SELECT * FROM Buses WHERE BusID = ?", array($busID));
$bus = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if (!$bus) die("Xe không tồn tại.");

// Cập nhật khi submit form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $number = $_POST['bus_number'];
    $type = $_POST['bus_type'];
    $seats = $_POST['total_seats'];
    $status = $_POST['status'];

    $update = sqlsrv_query($conn, "
        UPDATE Buses
        SET BusNumber = ?, BusType = ?, TotalSeats = ?, Status = ?
        WHERE BusID = ?
    ", array($number, $type, $seats, $status, $busID));

    if ($update) {
        header("Location: manage_buses.php");
        exit();
    } else {
        echo "<script>alert('Lỗi khi cập nhật xe.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sửa thông tin xe</title>
    <link rel="stylesheet" href="assets/css/edit_bus.css">
</head>
<body>

<h2 style="text-align:center;">🚌 Sửa thông tin xe</h2>

<form method="post">
    <label>Biển số xe:</label>
    <input type="text" name="bus_number" value="<?= $bus['BusNumber'] ?>" required>

    <label>Loại xe:</label>
    <select name="bus_type" required>
        <option value="">-- Chọn loại xe --</option>
        <option value="Ghế ngồi" <?= $bus['BusType'] == 'Ghế ngồi' ? 'selected' : '' ?>>Ghế ngồi</option>
        <option value="Giường nằm" <?= $bus['BusType'] == 'Giường nằm' ? 'selected' : '' ?>>Giường nằm</option>
        <option value="Limousine" <?= $bus['BusType'] == 'Limousine' ? 'selected' : '' ?>>Limousine</option>
    </select>

    <label>Số ghế:</label>
    <input type="number" name="total_seats" value="<?= $bus['TotalSeats'] ?>" required>

    <label>Trạng thái:</label>
    <select name="status">
        <option value="Đang hoạt động" <?= $bus['Status'] == 'Đang hoạt động' ? 'selected' : '' ?>>Đang hoạt động</option>
        <option value="Ngừng hoạt động" <?= $bus['Status'] == 'Ngừng hoạt động' ? 'selected' : '' ?>>Ngừng hoạt động</option>
    </select>

    <button type="submit">Lưu thay đổi</button>
</form>

<div style="text-align:center;">
    <a href="manage_buses.php">← Quay lại danh sách xe</a>
</div>

</body>
</html>
