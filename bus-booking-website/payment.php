<?php
session_start();
include('includes/connect.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Nhận dữ liệu đặt vé
$scheduleID = isset($_GET['schedule']) ? intval($_GET['schedule']) : 0;
$seat = isset($_GET['seat']) ? intval($_GET['seat']) : 0;

if (!$scheduleID || !$seat) {
    die("Thiếu thông tin đặt vé.");
}

// Lấy thông tin lịch trình & giá vé
$sql = "SELECT s.Price, r.StartLocation, r.EndLocation, s.DepartureTime, b.BusNumber
        FROM Schedules s
        JOIN Routes r ON s.RouteID = r.RouteID
        JOIN Buses b ON s.BusID = b.BusID
        WHERE s.ScheduleID = ?";
$stmt = sqlsrv_query($conn, $sql, array($scheduleID));
$schedule = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if (!$schedule) die("Không tìm thấy chuyến đi.");

// Khi người dùng nhấn "Thanh toán"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_POST['method'];
    $userID = $_SESSION['userid'];
    $price = $schedule['Price'];

    // Kiểm tra ghế còn trống
    $checkSeat = sqlsrv_query($conn, "SELECT * FROM Tickets WHERE ScheduleID = ? AND SeatNumber = ?", array($scheduleID, $seat));
    if (sqlsrv_has_rows($checkSeat)) {
        die("Ghế này đã được đặt.");
    }

    // 1. Lưu vé
    $insertTicket = sqlsrv_query($conn,
        "INSERT INTO Tickets (ScheduleID, UserID, SeatNumber, Status) VALUES (?, ?, ?, N'Đã thanh toán')",
        array($scheduleID, $userID, $seat)
    );

    if ($insertTicket) {
        // Lấy ID vé vừa tạo
        $ticketID = sqlsrv_query($conn, "SELECT SCOPE_IDENTITY() AS TicketID");
        $ticketRow = sqlsrv_fetch_array($ticketID, SQLSRV_FETCH_ASSOC);
        $ticketID = $ticketRow['TicketID'];

        // 2. Lưu thanh toán
        $pay = sqlsrv_query($conn,
            "INSERT INTO Payments (TicketID, Amount, Method, Status) VALUES (?, ?, ?, N'Thành công')",
            array($ticketID, $price, $method)
        );

        echo "<script>alert('Thanh toán thành công!'); window.location='user/my_tickets.php';</script>";
        header("Location: success.php?ticket=$ticketID");
        exit();
    } else {
        echo "<script>alert('Lỗi trong quá trình thanh toán.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Thanh toán vé</title>
    <link rel="stylesheet" href="assets/css/payment.css">
</head>
<body>

<div class="card">
    <h2>💳 Thanh toán vé</h2>

    <p><strong>Tuyến:</strong> <?php echo $schedule['StartLocation'] . " ➡️ " . $schedule['EndLocation']; ?></p>
    <p><strong>Xe:</strong> <?php echo $schedule['BusNumber']; ?></p>
    <p><strong>Ngày khởi hành:</strong> <?php echo date('d/m/Y H:i', strtotime($schedule['DepartureTime']->format('Y-m-d H:i:s'))); ?></p>
    <p><strong>Ghế số:</strong> <?php echo $seat; ?></p>
    <p><strong>Giá vé:</strong> <?php echo number_format($schedule['Price'], 0, ',', '.') . " đ"; ?></p>

    <form method="post">
        <label>Phương thức thanh toán:</label><br>
        <select name="method" required>
            <option value="">-- Chọn --</option>
            <option value="Momo">Momo</option>
            <option value="VNPay">VNPay</option>
            <option value="Tiền mặt">Tiền mặt</option>
        </select><br><br>
        <button class="btn" type="submit">Xác nhận thanh toán</button>
    </form>
</div>

</body>
</html>
