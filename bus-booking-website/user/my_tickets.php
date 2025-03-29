<?php
session_start();
include('../includes/connect.php');
include('../includes/auth_middleware.php'); // Phải đăng nhập
include('../includes/connect.php');
include('../includes/header.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['userid'])) {
    header("Location: ../login.php");
    exit();
}

$userID = $_SESSION['userid'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vé của tôi</title>
    <link rel="stylesheet" href="../css/my_tickets.css">
</head>
<body>

<h2>🎟️ Vé của tôi</h2>

<?php
$sql = "
    SELECT t.TicketID, t.SeatNumber, t.Status, t.BookingTime,
           s.DepartureTime, s.ArrivalTime, s.Price,
           r.StartLocation, r.EndLocation,
           b.BusNumber, b.BusType
    FROM Tickets t
    JOIN Schedules s ON t.ScheduleID = s.ScheduleID
    JOIN Routes r ON s.RouteID = r.RouteID
    JOIN Buses b ON s.BusID = b.BusID
    WHERE t.UserID = ?
    ORDER BY t.BookingTime DESC
";

$stmt = sqlsrv_query($conn, $sql, array($userID));
if (sqlsrv_has_rows($stmt)) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<div class='ticket-card'>";
        echo "<h3>{$row['StartLocation']} ➡️ {$row['EndLocation']}</h3>";
        echo "<p><span class='label'>Xe:</span> {$row['BusNumber']} ({$row['BusType']})</p>";
        echo "<p><span class='label'>Khởi hành:</span> " . date('d/m/Y H:i', strtotime($row['DepartureTime']->format('Y-m-d H:i:s'))) . "</p>";
        echo "<p><span class='label'>Đến nơi:</span> " . date('d/m/Y H:i', strtotime($row['ArrivalTime']->format('Y-m-d H:i:s'))) . "</p>";
        echo "<p><span class='label'>Ghế:</span> {$row['SeatNumber']} | <span class='label'>Giá:</span> " . number_format($row['Price'], 0, ',', '.') . "đ</p>";
        echo "<p><span class='label'>Trạng thái:</span> {$row['Status']} | <span class='label'>Đặt lúc:</span> " . date('d/m/Y H:i', strtotime($row['BookingTime']->format('Y-m-d H:i:s'))) . "</p>";
        echo "</div>";
    }
} else {
    echo "<p>Bạn chưa đặt vé nào.</p>";
}
?>

</body>
</html>
