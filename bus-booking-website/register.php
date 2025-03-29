<?php
include('includes/connect.php');
$errors = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if (empty($fullname) || empty($email) || empty($phone) || empty($password)) {
        $errors = "Vui lòng nhập đầy đủ thông tin.";
    } elseif ($password !== $confirm) {
        $errors = "Mật khẩu không khớp.";
    } else {
        // Kiểm tra email đã tồn tại
        $check = sqlsrv_query($conn, "SELECT * FROM Users WHERE Email = ?", array($email));
        if (sqlsrv_has_rows($check)) {
            $errors = "Email đã tồn tại.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = sqlsrv_query($conn,
                "INSERT INTO Users (FullName, Email, PhoneNumber, PasswordHash) VALUES (?, ?, ?, ?)",
                array($fullname, $email, $phone, $hashed)
            );
            if ($stmt) {
                header("Location: login.php?success=1");
                exit();
            } else {
                $errors = "Lỗi khi tạo tài khoản.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đăng ký</title>
</head>
<body>
    <h2>Đăng ký tài khoản</h2>
    <?php if ($errors) echo "<p style='color:red;'>$errors</p>"; ?>
    <form method="post">
        Họ tên: <input type="text" name="fullname"><br>
        Email: <input type="email" name="email"><br>
        SĐT: <input type="text" name="phone"><br>
        Mật khẩu: <input type="password" name="password"><br>
        Nhập lại mật khẩu: <input type="password" name="confirm"><br>
        <button type="submit">Đăng ký</button>
    </form>
    <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
</body>
</html>