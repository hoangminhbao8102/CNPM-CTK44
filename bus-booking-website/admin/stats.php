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

// Hàm thống kê đơn giản
function getSingleValue($conn, $sql, $params = []) {
    $stmt = sqlsrv_query($conn, $sql, $params);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    return array_values($row)[0];
}

// Tổng doanh thu
$totalRevenue = getSingleValue($conn, "SELECT ISNULL(SUM(Amount), 0) AS Total FROM Payments WHERE Status = N'Thành công'");

// Tổng số vé đặt
$totalTickets = getSingleValue($conn, "SELECT COUNT(*) FROM Tickets");

// Số người dùng
$totalUsers = getSingleValue($conn, "SELECT COUNT(*) FROM Users");

// Số chuyến đã chạy (trước thời điểm hiện tại)
$totalSchedules = getSingleValue($conn, "SELECT COUNT(*) FROM Schedules WHERE DepartureTime < GETDATE()");

// Vé đặt trong hôm nay
$ticketsToday = getSingleValue($conn, "
    SELECT COUNT(*) FROM Tickets WHERE CAST(BookingTime AS DATE) = CAST(GETDATE() AS DATE)
");

// (Lượt truy cập có thể lưu vào 1 bảng truy cập theo IP/time)
?>

<!DOCTYPE html>
<html>
<head>
    <title>Thống kê hệ thống</title>
    <link rel="stylesheet" href="assets/css/stats.css">
</head>
<body>

<h2>📊 Thống kê hệ thống</h2>

<div class="stats">
    <div class="card">
        <h3>💰 Tổng doanh thu</h3>
        <p><?= number_format($totalRevenue, 0, ',', '.') ?> đ</p>
    </div>
    <div class="card">
        <h3>🎫 Tổng vé đã đặt</h3>
        <p><?= $totalTickets ?></p>
    </div>
    <div class="card">
        <h3>👥 Người dùng</h3>
        <p><?= $totalUsers ?></p>
    </div>
    <div class="card">
        <h3>🚌 Chuyến đã chạy</h3>
        <p><?= $totalSchedules ?></p>
    </div>
    <div class="card">
        <h3>📅 Vé đặt hôm nay</h3>
        <p><?= $ticketsToday ?></p>
    </div>
</div>

</body>
</html>
