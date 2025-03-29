<?php
session_start();
include('includes/connect.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$scheduleID = isset($_GET['schedule']) ? intval($_GET['schedule']) : 0;
if (!$scheduleID) {
    die("Lịch trình không hợp lệ.");
}

// Lấy thông tin lịch trình và xe
$sql = "SELECT s.*, b.BusID, b.TotalSeats, b.BusNumber
        FROM Schedules s
        JOIN Buses b ON s.BusID = b.BusID
        WHERE s.ScheduleID = ?";
$stmt = sqlsrv_query($conn, $sql, array($scheduleID));
$schedule = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if (!$schedule) die("Không tìm thấy lịch trình.");

// Lấy danh sách ghế đã đặt
$booked = [];
$seatQuery = sqlsrv_query($conn, "SELECT SeatNumber FROM Tickets WHERE ScheduleID = ?", array($scheduleID));
while ($row = sqlsrv_fetch_array($seatQuery, SQLSRV_FETCH_ASSOC)) {
    $booked[] = $row['SeatNumber'];
}

// Xử lý đặt ghế
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $selected = $_POST['seat'];
    if (!in_array($selected, $booked)) {
        $insert = sqlsrv_query($conn,
            "INSERT INTO Tickets (ScheduleID, UserID, SeatNumber, Status) VALUES (?, ?, ?, N'Đã đặt')",
            array($scheduleID, $_SESSION['userid'], $selected)
        );
        if ($insert) {
            echo "<script>alert('Đặt vé thành công với ghế số $selected!'); window.location='payment.php?schedule=$scheduleID&seat=$selected';</script>';</script>";
            exit();
        } else {
            echo "<script>alert('Lỗi khi đặt vé.');</script>";
        }
    } else {
        echo "<script>alert('Ghế đã được đặt. Vui lòng chọn ghế khác.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đặt vé - Ghế động</title>
    <link rel="stylesheet" href="assets/css/booking.css">
</head>
<body>

<h2>Đặt vé xe - <?php echo $schedule['BusNumber']; ?> (Lịch trình #<?php echo $scheduleID; ?>)</h2>

<form method="post" id="bookingForm">
    <div class="seat-grid">
        <?php
        for ($i = 1; $i <= $schedule['TotalSeats']; $i++) {
            $disabled = in_array($i, $booked) ? 'booked' : '';
            echo "<div class='seat $disabled' data-seat='$i'>$i</div>";
        }
        ?>
    </div>

    <input type="hidden" name="seat" id="seatInput">
    <br>
    <button type="submit">Xác nhận đặt vé</button>
</form>

<script src="assets/js/booking.js"></script>

</body>
</html>
