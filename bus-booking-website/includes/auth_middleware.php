<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['userid'])) {
    // Người dùng chưa đăng nhập → chuyển về trang đăng nhập
    header("Location: /login.php");
    exit();
}