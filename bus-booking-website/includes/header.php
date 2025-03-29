<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$isAdmin = isset($_SESSION['isadmin']) && $_SESSION['isadmin'] == 1;
$fullname = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : null;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bus Ticket Website</title>
    <link rel="stylesheet" href="assets/css/header.css">
</head>
<body>

<header>
    <div style="display: flex; align-items: center;">
        <img src="/assets/img/logo.png" alt="Logo" style="height: 40px; margin-right: 15px;">
        <h1>Bus Ticket Booking</h1>
    </div>
    <nav>
        <a href="/index.php">Trang chủ</a>
        <?php if ($fullname): ?>
            <?php if ($isAdmin): ?>
                <a href="/admin/index.php">Quản trị</a>
            <?php else: ?>
                <a href="/user/my_tickets.php">Vé của tôi</a>
            <?php endif; ?>
            <span style="margin-left:10px;">👋 Xin chào, <?= htmlspecialchars($fullname) ?></span>
            <a href="/logout.php">Đăng xuất</a>
        <?php else: ?>
            <a href="/login.php">Đăng nhập</a>
            <a href="/register.php">Đăng ký</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">
