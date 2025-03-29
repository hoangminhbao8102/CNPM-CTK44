<?php
session_start();
include('../includes/connect.php');
include('../includes/admin_middleware.php'); // Phải là admin
include('../includes/connect.php');
include('../includes/header.php');

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['userid']) || $_SESSION['isadmin'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Thống kê cơ bản
function getCount($conn, $table) {
    $query = "SELECT COUNT(*) AS Total FROM $table";
    $stmt = sqlsrv_query($conn, $query);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    return $row['Total'];
}

$totalUsers = getCount($conn, 'Users');
$totalSchedules = getCount($conn, 'Schedules');
$totalRoutes = getCount($conn, 'Routes');
$totalTickets = getCount($conn, 'Tickets');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản trị hệ thống</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<?php include('includes/header.php'); ?>
<h2>🎛️ Giao diện quản trị</h2>

<div class="stats">
    <div class="card">
        <h3>Người dùng</h3>
        <p><?php echo $totalUsers; ?></p>
    </div>
    <div class="card">
        <h3>Lịch trình</h3>
        <p><?php echo $totalSchedules; ?></p>
    </div>
    <div class="card">
        <h3>Tuyến đường</h3>
        <p><?php echo $totalRoutes; ?></p>
    </div>
    <div class="card">
        <h3>Vé đã đặt</h3>
        <p><?php echo $totalTickets; ?></p>
    </div>
</div>

<div class="nav">
    <a href="manage_schedules.php">📅 Quản lý Lịch trình</a>
    <a href="manage_routes.php">🛣️ Quản lý Tuyến đường</a>
    <a href="manage_buses.php">🚌 Quản lý Xe</a>
    <a href="manage_users.php">👤 Quản lý Người dùng</a>
</div>
<?php include('includes/footer.php'); ?>

</body>
</html>
