<?php
include('includes/connect.php');
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Trang chủ - Đặt vé xe bus</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include('includes/header.php'); ?>
<img src="assets/img/banner.png" alt="Banner" style="width: 100%; border-radius: 10px; margin-bottom: 30px;">
<h2>🚍 Tuyến đường phổ biến</h2>

<?php
// Lấy 5 tuyến được đặt nhiều nhất (TOP 5 Routes theo Tickets)
$sql = "
    SELECT TOP 5 r.RouteID, r.StartLocation, r.EndLocation, COUNT(t.TicketID) AS TotalTickets
    FROM Routes r
    JOIN Schedules s ON r.RouteID = s.RouteID
    JOIN Tickets t ON s.ScheduleID = t.ScheduleID
    GROUP BY r.RouteID, r.StartLocation, r.EndLocation
    ORDER BY TotalTickets DESC
";

$stmt = sqlsrv_query($conn, $sql);

if (sqlsrv_has_rows($stmt)) {
    while ($route = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<div class='route-card'>";
        echo "<h3>{$route['StartLocation']} ➡️ {$route['EndLocation']}</h3>";
        echo "<p>Lượt đặt: {$route['TotalTickets']}</p>";
        echo "<a class='btn' href='search.php?from=" . urlencode($route['StartLocation']) . "&to=" . urlencode($route['EndLocation']) . "'>Đặt vé ngay</a>";
        echo "</div>";
    }
} else {
    echo "<p>Chưa có dữ liệu đặt vé.</p>";
}
?>
<?php include('includes/footer.php'); ?>

</body>
</html>
