<?php
include('includes/connect.php');
session_start();

$results = [];
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['from']) && isset($_GET['to']) && isset($_GET['date'])) {
    $from = $_GET['from'];
    $to = $_GET['to'];
    $date = $_GET['date'];

    $sql = "
        SELECT s.ScheduleID, r.StartLocation, r.EndLocation, s.DepartureTime, s.ArrivalTime, s.Price, b.BusNumber, b.BusType
        FROM Schedules s
        JOIN Routes r ON s.RouteID = r.RouteID
        JOIN Buses b ON s.BusID = b.BusID
        WHERE r.StartLocation = ? AND r.EndLocation = ? AND CAST(s.DepartureTime AS DATE) = ?
        ORDER BY s.DepartureTime ASC
    ";
    $stmt = sqlsrv_query($conn, $sql, array($from, $to, $date));

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $results[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tìm chuyến xe</title>
    <link rel="stylesheet" href="assets/css/search.css">
</head>
<body>

<h2>🔍 Tìm tuyến đường</h2>

<form class="search-form" method="get">
    <div class="form-group">
        <label>Điểm đi:</label>
        <select name="from" required>
            <option value="">-- Chọn --</option>
            <option value="Hà Nội">Hà Nội</option>
            <option value="Hồ Chí Minh">Hồ Chí Minh</option>
            <option value="Đà Nẵng">Đà Nẵng</option>
        </select>
    </div>

    <div class="form-group">
        <label>Điểm đến:</label>
        <select name="to" required>
            <option value="">-- Chọn --</option>
            <option value="Hải Phòng">Hải Phòng</option>
            <option value="Cần Thơ">Cần Thơ</option>
            <option value="Huế">Huế</option>
        </select>
    </div>

    <div class="form-group">
        <label>Ngày đi:</label>
        <input type="date" name="date" required>
    </div>

    <button class="btn" type="submit">Tìm chuyến</button>
</form>

<?php if (!empty($results)): ?>
<div class="results">
    <h3>🎫 Kết quả tìm kiếm:</h3>
    <?php foreach ($results as $trip): ?>
        <div class="card">
            <strong><?php echo $trip['StartLocation'] . " ➡️ " . $trip['EndLocation']; ?></strong><br>
            Thời gian: <?php echo date('H:i', strtotime($trip['DepartureTime']->format('Y-m-d H:i:s'))); ?>
            → <?php echo date('H:i', strtotime($trip['ArrivalTime']->format('Y-m-d H:i:s'))); ?><br>
            Xe: <?php echo $trip['BusNumber'] . " - " . $trip['BusType']; ?><br>
            Giá: <?php echo number_format($trip['Price'], 0, ',', '.') . "đ"; ?><br>
            <a class="btn" href="booking/booking.php?php echo $trip['ScheduleID']; ?>">Đặt vé</a>
        </div>
    <?php endforeach; ?>
</div>
<?php elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['from'])): ?>
    <p>Không tìm thấy chuyến xe phù hợp.</p>
<?php endif; ?>

</body>
</html>
