<?php
require_once '../business/TicketService.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $MaVe = $_POST['MaVe'];

    $service = new TicketService();
    $message = $service->cancelTicket($MaVe)
        ? "✅ Hủy vé thành công!"
        : "❌ Hủy vé thất bại.";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hủy Vé Xe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h2 class="mb-4">Hủy Vé Xe</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="post" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Mã vé</label>
            <input type="number" class="form-control" name="MaVe" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-danger">Hủy vé</button>
        </div>
    </form>
</body>
</html>
