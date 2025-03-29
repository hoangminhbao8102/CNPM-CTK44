<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['userid']) || $_SESSION['isadmin'] != 1) {
    // Không có quyền truy cập → về trang chủ
    header("Location: /index.php");
    exit();
}
