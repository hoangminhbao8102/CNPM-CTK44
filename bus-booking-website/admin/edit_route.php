<?php
session_start();
include('../includes/connect.php');
include('../includes/admin_middleware.php'); // Phải là admin
include('../includes/connect.php');
include('../includes/header.php');

// Kiểm tra quyền Admin
if (!isset($_SESSION['userid']) || $_SESSION['isadmin'] != 1) {
    header("Location: ../login.php");
    exit();
}

$routeID = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$routeID) {
    die("Không tìm thấy tuyến.");
}

// Lấy dữ liệu tuyến hiện tại
$stmt = sqlsrv_query($conn, "SELECT * FROM Routes WHERE RouteID = ?", array($routeID));
$route = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if (!$route) die("Tuyến không tồn tại.");

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start = $_POST['start_location'];
    $end = $_POST['end_location'];
    $distance = $_POST['distance'];
    $time = $_POST['estimated_time'];

    $update = sqlsrv_query($conn, "
        UPDATE Routes SET StartLocation = ?, EndLocation = ?, DistanceKM = ?, EstimatedTime = ?
        WHERE RouteID = ?
    ", array($start, $end, $distance, $time, $routeID));

    if ($update) {
        header("Location: manage_routes.php");
        exit();
    } else {
        echo "<script>alert('Lỗi khi cập nhật tuyến.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sửa tuyến đường</title>
    <link rel="stylesheet" href="assets/css/edit_routes.css">
</head>
<body>

<h2 style="text-align:center;">🛠️ Sửa tuyến đường</h2>

<form method="post">
    <label>Điểm đi:</label>
    <input type="text" name="start_location" value="<?= $route['StartLocation'] ?>" required>

    <label>Điểm đến:</label>
    <input type="text" name="end_location" value="<?= $route['EndLocation'] ?>" required>

    <label>Khoảng cách (km):</label>
    <input type="number" step="0.1" name="distance" value="<?= $route['DistanceKM'] ?>">

    <label>Thời gian dự kiến:</label>
    <input type="time" name="estimated_time" value="<?= $route['EstimatedTime']->format('H:i') ?>">

    <button type="submit">Lưu thay đổi</button>
</form>

<div style="text-align:center;">
    <a href="manage_routes.php">← Quay lại danh sách tuyến</a>
</div>

</body>
</html>
