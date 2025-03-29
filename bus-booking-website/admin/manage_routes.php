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

// Thêm tuyến mới
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $start = $_POST['start_location'];
    $end = $_POST['end_location'];
    $distance = $_POST['distance'];
    $time = $_POST['estimated_time'];

    sqlsrv_query($conn, "
        INSERT INTO Routes (StartLocation, EndLocation, DistanceKM, EstimatedTime)
        VALUES (?, ?, ?, ?)
    ", array($start, $end, $distance, $time));
}

// Xoá tuyến
if (isset($_GET['delete'])) {
    $routeID = intval($_GET['delete']);
    sqlsrv_query($conn, "DELETE FROM Routes WHERE RouteID = ?", array($routeID));
    header("Location: manage_routes.php");
    exit();
}

// Lấy danh sách tuyến
$routes = sqlsrv_query($conn, "SELECT * FROM Routes ORDER BY StartLocation, EndLocation");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý tuyến đường</title>
    <link rel="stylesheet" href="assets/css/manage_routes.css">
</head>
<body>

<h2>🛣️ Quản lý tuyến đường</h2>

<h3>Thêm tuyến mới</h3>
<form method="post">
    <input type="text" name="start_location" placeholder="Điểm đi" required>
    <input type="text" name="end_location" placeholder="Điểm đến" required>
    <input type="number" step="0.1" name="distance" placeholder="Khoảng cách (km)">
    <input type="time" name="estimated_time" placeholder="Thời gian dự kiến">
    <button type="submit" name="add">Thêm tuyến</button>
</form>

<h3>Danh sách tuyến đường</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Điểm đi</th>
            <th>Điểm đến</th>
            <th>Khoảng cách (km)</th>
            <th>Thời gian dự kiến</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($r = sqlsrv_fetch_array($routes, SQLSRV_FETCH_ASSOC)) : ?>
            <tr>
                <td><?= $r['RouteID'] ?></td>
                <td><?= $r['StartLocation'] ?></td>
                <td><?= $r['EndLocation'] ?></td>
                <td><?= $r['DistanceKM'] ?></td>
                <td><?= $r['EstimatedTime']->format('H:i') ?></td>
                <td class="actions">
                    <a href="edit_route.php?id=<?= $r['RouteID'] ?>">✏️</a>
                    <a class="delete" href="?delete=<?= $r['RouteID'] ?>" onclick="return confirm('Bạn chắc chắn muốn xoá tuyến này?')">❌</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>