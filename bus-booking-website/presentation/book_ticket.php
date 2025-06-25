<?php
require_once '../business/TicketService.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $MaLichTrinh = $_POST['MaLichTrinh'];
    $MaKH = $_POST['MaKH'];
    $SoGhe = $_POST['SoGhe'];

    $service = new TicketService();
    $message = $service->bookTicket($MaLichTrinh, $MaKH, $SoGhe)
        ? "✅ Đặt vé thành công!"
        : "❌ Lỗi khi đặt vé.";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt Vé Xe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h2 class="mb-4">Đặt Vé Xe</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="post" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Mã lịch trình</label>
            <input type="number" class="form-control" name="MaLichTrinh" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Mã khách hàng</label>
            <input type="number" class="form-control" name="MaKH" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Số ghế</label>
            <input type="number" class="form-control" name="SoGhe" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Đặt vé</button>
        </div>
    </form>
</body>
</html>
