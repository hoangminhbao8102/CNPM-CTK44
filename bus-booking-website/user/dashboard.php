<?php
include('../includes/auth_middleware.php'); // bắt buộc đăng nhập
include('../includes/connect.php');
include('../includes/header.php');

$userID = $_SESSION['userid'];

// Lấy tổng số vé
$ticketsCount = 0;
$stmt = sqlsrv_query($conn, "SELECT COUNT(*) AS Total FROM Tickets WHERE UserID = ?", array($userID));
if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $ticketsCount = $row['Total'];
}

// Lấy tổng tiền đã thanh toán
$totalPaid = 0;
$stmt = sqlsrv_query($conn, "
    SELECT ISNULL(SUM(p.Amount), 0) AS Total
    FROM Payments p
    JOIN Tickets t ON p.TicketID = t.TicketID
    WHERE t.UserID = ? AND p.Status = N'Thành công'
", array($userID));
if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $totalPaid = $row['Total'];
}

// Lấy vé gần đây nhất
$latest = null;
$stmt = sqlsrv_query($conn, "
    SELECT TOP 1 t.TicketID, r.StartLocation, r.EndLocation, s.DepartureTime, t.SeatNumber
    FROM Tickets t
    JOIN Schedules s ON t.ScheduleID = s.ScheduleID
    JOIN Routes r ON s.RouteID = r.RouteID
    WHERE t.UserID = ?
    ORDER BY t.BookingTime DESC
", array($userID));
$latest = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
?>

<link rel="stylesheet" href="assets/css/dashboard.css">

<h2>👋 Xin chào, <?= $_SESSION['fullname'] ?></h2>

<div class="dashboard">
    <div class="card">
        <h3>Tổng vé đã đặt</h3>
        <p><?= $ticketsCount ?></p>
    </div>
    <div class="card">
        <h3>Tổng tiền đã thanh toán</h3>
        <p><?= number_format($totalPaid, 0, ',', '.') ?> đ</p>
    </div>
    <div class="card">
        <h3>Vé gần nhất</h3>
        <?php if ($latest): ?>
            <p><?= $latest['StartLocation'] ?> ➡️ <?= $latest['EndLocation'] ?><br>
            Ghế <?= $latest['SeatNumber'] ?><br>
            <?= date('d/m/Y H:i', strtotime($latest['DepartureTime']->format('Y-m-d H:i:s'))) ?></p>
        <?php else: ?>
            <p>Chưa có vé</p>
        <?php endif; ?>
    </div>
</div>

<div class="links">
    <a href="../user/my_tickets.php">🎫 Xem vé của tôi</a>
    <a href="../search.php">🔍 Tìm chuyến xe</a>
    <a href="../logout.php">🚪 Đăng xuất</a>
</div>

<?php include('../includes/footer.php'); ?>
