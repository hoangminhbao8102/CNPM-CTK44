<?php
require_once '../business/CustomerService.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['HoTen'];
    $email = $_POST['Email'];
    $phone = $_POST['SoDienThoai'];

    $service = new CustomerService();
    $message = $service->register($name, $email, $phone)
        ? "✅ Đăng ký thành công!"
        : "❌ Đăng ký thất bại.";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Ký Khách Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h2 class="mb-4">Đăng Ký Khách Hàng</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="post" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Họ tên</label>
            <input type="text" class="form-control" name="HoTen" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="Email" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Số điện thoại</label>
            <input type="text" class="form-control" name="SoDienThoai" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">Đăng ký</button>
        </div>
    </form>
</body>
</html>
