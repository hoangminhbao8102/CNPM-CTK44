<?php
include('includes/connect.php');
session_start();
$errors = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors = "Vui lòng nhập đầy đủ email và mật khẩu.";
    } else {
        $stmt = sqlsrv_query($conn, "SELECT * FROM Users WHERE Email = ?", array($email));
        if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            if (password_verify($password, $row['PasswordHash'])) {
                $_SESSION['userid'] = $row['UserID'];
                $_SESSION['fullname'] = $row['FullName'];
                $_SESSION['isadmin'] = $row['IsAdmin'];

                if ($row['IsAdmin']) {
                    header("Location: admin/index.php");
                } else {
                    header("Location: user/dashboard.php");
                }
                exit();
            } else {
                $errors = "Sai mật khẩu.";
            }
        } else {
            $errors = "Email không tồn tại.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đăng nhập</title>
</head>
<body>
    <h2>Đăng nhập</h2>
    <?php if (isset($_GET['success'])) echo "<p style='color:green;'>Đăng ký thành công, vui lòng đăng nhập.</p>"; ?>
    <?php if ($errors) echo "<p style='color:red;'>$errors</p>"; ?>
    <form method="post">
        Email: <input type="email" name="email"><br>
        Mật khẩu: <input type="password" name="password"><br>
        <button type="submit">Đăng nhập</button>
    </form>
    <p>Chưa có tài khoản? <a href="register.php">Đăng ký</a></p>
</body>
</html>
