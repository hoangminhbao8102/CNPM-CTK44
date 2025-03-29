<?php
$serverName = "localhost"; // hoặc 'localhost\\SQLEXPRESS'
$connectionInfo = array(
    "Database" => "BusTicketBooking",
    "UID" => "sa",
    "PWD" => "minhbao8102", // <-- Thay bằng mật khẩu thật
    "TrustServerCertificate" => "true",
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($serverName, $connectionInfo);
if (!$conn) {
    die("Kết nối thất bại: " . print_r(sqlsrv_errors(), true));
}
else {
    echo "Kết nối thành công";
}
?>
