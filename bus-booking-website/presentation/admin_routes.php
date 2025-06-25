<?php
require_once '../business/RouteService.php';
$service = new RouteService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start = $_POST['DiemDi'];
    $end = $_POST['DiemDen'];
    $service->addNewRoute($start, $end);
}

$routes = $service->listRoutes();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý tuyến xe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h2 class="mb-4">Quản lý tuyến xe</h2>

    <form method="post" class="row g-3 mb-4">
        <div class="col-md-5">
            <label class="form-label">Điểm đi</label>
            <input type="text" class="form-control" name="DiemDi" required>
        </div>
        <div class="col-md-5">
            <label class="form-label">Điểm đến</label>
            <input type="text" class="form-control" name="DiemDen" required>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Thêm tuyến</button>
        </div>
    </form>

    <h4>Danh sách tuyến xe:</h4>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Mã tuyến</th>
                <th>Điểm đi</th>
                <th>Điểm đến</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($routes as $route): ?>
            <tr>
                <td><?= $route['MaTuyen'] ?></td>
                <td><?= $route['DiemDi'] ?></td>
                <td><?= $route['DiemDen'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
