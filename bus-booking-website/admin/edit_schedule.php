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

// Lấy ID lịch trình
$scheduleID = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$scheduleID) die("Không có lịch trình.");

// Xử lý cập nhật khi gửi form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $routeID = $_POST['route_id'];
    $busID = $_POST['bus_id'];
    $departure = $_POST['departure_time'];
    $arrival = $_POST['arrival_time'];
    $price = $_POST['price'];

    $update = sqlsrv_query($conn, "
        UPDATE Schedules
        SET RouteID = ?, BusID = ?, DepartureTime = ?, ArrivalTime = ?, Price = ?
        WHERE ScheduleID = ?
    ", array($routeID, $busID, $departure, $arrival, $price, $scheduleID));

    if ($update) {
        header("Location: manage_schedules.php");
        exit();
    } else {
        echo "<script>alert('Lỗi khi cập nhật lịch trình.');</script>";
    }
}

// Lấy lịch trình hiện tại
$stmt = sqlsrv_query($conn, "
    SELECT * FROM Schedules WHERE ScheduleID = ?
", array($scheduleID));
$data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if (!$data) die("Không tìm thấy lịch trình.");

// Lấy danh sách tuyến và xe
$routes = sqlsrv_query($conn, "SELECT * FROM Routes");
$buses = sqlsrv_query($conn, "SELECT * FROM Buses");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sửa lịch trình</title>
    <link rel="stylesheet" href="assets/css/edit_schedules.css">
</head>
<body>

<h2 style="text-align:center;">📝 Sửa lịch trình #<?php echo $scheduleID; ?></h2>

<form method="post">
    <label>Tuyến đường:</label>
    <select name="route_id" required>
        <option value="">-- Chọn tuyến --</option>
        <?php while ($r = sqlsrv_fetch_array($routes, SQLSRV_FETCH_ASSOC)) {
            $selected = ($r['RouteID'] == $data['RouteID']) ? 'selected' : '';
            echo "<option value='{$r['RouteID']}' $selected>{$r['StartLocation']} ➡️ {$r['EndLocation']}</option>";
        } ?>
    </select>

    <label>Xe bus:</label>
    <select name="bus_id" required>
        <option value="">-- Chọn xe --</option>
        <?php while ($b = sqlsrv_fetch_array($buses, SQLSRV_FETCH_ASSOC)) {
            $selected = ($b['BusID'] == $data['BusID']) ? 'selected' : '';
            echo "<option value='{$b['BusID']}' $selected>{$b['BusNumber']}</option>";
        } ?>
    </select>

    <label>Thời gian khởi hành:</label>
    <input type="datetime-local" name="departure_time" value="<?php echo date('Y-m-d\TH:i', strtotime($data['DepartureTime']->format('Y-m-d H:i:s'))); ?>" required>

    <label>Thời gian đến:</label>
    <input type="datetime-local" name="arrival_time" value="<?php echo date('Y-m-d\TH:i', strtotime($data['ArrivalTime']->format('Y-m-d H:i:s'))); ?>" required>

    <label>Giá vé:</label>
    <input type="number" name="price" value="<?php echo $data['Price']; ?>" required>

    <button type="submit">Lưu thay đổi</button>
</form>

<div style="text-align:center;">
    <a href="manage_schedules.php">← Quay lại danh sách</a>
</div>

</body>
</html>