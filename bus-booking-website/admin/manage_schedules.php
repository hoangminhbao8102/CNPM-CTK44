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

// Thêm mới lịch trình
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $routeID = $_POST['route_id'];
    $busID = $_POST['bus_id'];
    $departure = $_POST['departure_time'];
    $arrival = $_POST['arrival_time'];
    $price = $_POST['price'];

    $sql = "INSERT INTO Schedules (RouteID, BusID, DepartureTime, ArrivalTime, Price)
            VALUES (?, ?, ?, ?, ?)";
    sqlsrv_query($conn, $sql, array($routeID, $busID, $departure, $arrival, $price));
}

// Xoá lịch trình
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    sqlsrv_query($conn, "DELETE FROM Schedules WHERE ScheduleID = ?", array($id));
    header("Location: manage_schedules.php");
    exit();
}

// Lấy danh sách Route và Bus
$routes = sqlsrv_query($conn, "SELECT * FROM Routes");
$buses = sqlsrv_query($conn, "SELECT * FROM Buses");

// Lấy lịch trình
$schedules = sqlsrv_query($conn, "
    SELECT s.ScheduleID, r.StartLocation, r.EndLocation,
           s.DepartureTime, s.ArrivalTime, s.Price,
           b.BusNumber
    FROM Schedules s
    JOIN Routes r ON s.RouteID = r.RouteID
    JOIN Buses b ON s.BusID = b.BusID
    ORDER BY s.DepartureTime DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý lịch trình</title>
    <link rel="stylesheet" href="assets/css/manage_schedules.css">
</head>
<body>

<h2>🗓️ Quản lý lịch trình</h2>

<h3>Thêm lịch trình mới</h3>
<form method="post">
    <label>Tuyến đường:</label>
    <select name="route_id" required>
        <option value="">-- Chọn tuyến --</option>
        <?php while ($r = sqlsrv_fetch_array($routes, SQLSRV_FETCH_ASSOC)) {
            echo "<option value='{$r['RouteID']}'>{$r['StartLocation']} ➡️ {$r['EndLocation']}</option>";
        } ?>
    </select><br><br>

    <label>Xe bus:</label>
    <select name="bus_id" required>
        <option value="">-- Chọn xe --</option>
        <?php while ($b = sqlsrv_fetch_array($buses, SQLSRV_FETCH_ASSOC)) {
            echo "<option value='{$b['BusID']}'>{$b['BusNumber']}</option>";
        } ?>
    </select><br><br>

    <label>Thời gian khởi hành:</label>
    <input type="datetime-local" name="departure_time" required><br><br>

    <label>Thời gian đến:</label>
    <input type="datetime-local" name="arrival_time" required><br><br>

    <label>Giá vé (VNĐ):</label>
    <input type="number" name="price" required><br><br>

    <button type="submit" name="add">Thêm lịch trình</button>
</form>

<h3>Danh sách lịch trình</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Tuyến</th>
            <th>Xe</th>
            <th>Khởi hành</th>
            <th>Đến nơi</th>
            <th>Giá vé</th>
            <th>Xoá</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($s = sqlsrv_fetch_array($schedules, SQLSRV_FETCH_ASSOC)) : ?>
            <tr>
                <td><?= $s['ScheduleID'] ?></td>
                <td><?= $s['StartLocation'] ?> ➡️ <?= $s['EndLocation'] ?></td>
                <td><?= $s['BusNumber'] ?></td>
                <td><?= date('d/m/Y H:i', strtotime($s['DepartureTime']->format('Y-m-d H:i:s'))) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($s['ArrivalTime']->format('Y-m-d H:i:s'))) ?></td>
                <td><?= number_format($s['Price'], 0, ',', '.') ?>đ</td>
                <td>
                    <a href="edit_schedule.php?id=<?= $s['ScheduleID'] ?>">✏️</a>
                    <a href="?delete=<?= $s['ScheduleID'] ?>" onclick="return confirm('Xoá lịch trình này?')">
                        ❌
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
