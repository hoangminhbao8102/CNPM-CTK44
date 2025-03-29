<?php
session_start();
include('includes/connect.php');
include('includes/header.php');

// Kiểm tra nếu có mã vé truyền về
$ticketID = isset($_GET['ticket']) ? intval($_GET['ticket']) : 0;

if (!$ticketID) {
    echo "<p>Không tìm thấy thông tin vé.</p>";
    include('includes/footer.php');
    exit();
}

// Truy vấn chi tiết vé
$sql = "
    SELECT t.TicketID, t.SeatNumber, t.BookingTime,
           s.DepartureTime, s.ArrivalTime,
           r.StartLocation, r.EndLocation,
           b.BusNumber, b.BusType,
           p.Amount, p.Method
    FROM Tickets t
    JOIN Schedules s ON t.ScheduleID = s.ScheduleID
    JOIN Routes r ON s.RouteID = r.RouteID
    JOIN Buses b ON s.BusID = b.BusID
    LEFT JOIN Payments p ON t.TicketID = p.TicketID
    WHERE t.TicketID = ? AND t.UserID = ?
";
$stmt = sqlsrv_query($conn, $sql, array($ticketID, $_SESSION['userid']));
$ticket = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$ticket) {
    echo "<p>Không tìm thấy vé hoặc bạn không có quyền xem.</p>";
    include('includes/footer.php');
    exit();
}
?>

<link rel="stylesheet" href="assets/css/success.css">

<div class="success-box">
    <h2>✅ Đặt vé thành công!</h2>
    <p><strong>Mã vé:</strong> #<?= $ticket['TicketID'] ?></p>
    <p><strong>Tuyến:</strong> <?= $ticket['StartLocation'] ?> ➡️ <?= $ticket['EndLocation'] ?></p>
    <p><strong>Xe:</strong> <?= $ticket['BusNumber'] ?> (<?= $ticket['BusType'] ?>)</p>
    <p><strong>Số ghế:</strong> <?= $ticket['SeatNumber'] ?></p>
    <p><strong>Khởi hành:</strong> <?= date('d/m/Y H:i', strtotime($ticket['DepartureTime']->format('Y-m-d H:i:s'))) ?></p>
    <p><strong>Phương thức thanh toán:</strong> <?= $ticket['Method'] ?></p>
    <p><strong>Thành tiền:</strong> <?= number_format($ticket['Amount'], 0, ',', '.') ?> đ</p>

    <a href="user/my_tickets.php" class="btn">Xem tất cả vé của tôi</a>
</div>

<?php include('includes/footer.php'); ?>
