<?php 
session_start();
ob_start();
include_once "connect.php";
include_once "user.php";

$error_message = ""; // Biến để lưu thông báo lỗi

if (isset($_POST['dangnhap']) && $_POST['dangnhap']) {
    $msv = trim($_POST['msv']);
    $pass = trim($_POST['pass']);

    // Kiểm tra thông tin đăng nhập
    $role = checkuser($msv, $pass);

    if ($role !== null) {
        $_SESSION['msv'] = $msv; // Lưu mã sinh viên vào session
        $_SESSION['role'] = $role;
        // Nếu đăng nhập thành công, lưu cookie để nhớ thông tin đăng nhập
        setcookie('msv', $msv, time() + (86400 * 30), "/"); // Lưu cookie trong 30 ngày
        setcookie('role', $role, time() + (86400 * 30), "/"); // Lưu cookie role
        if ($role == 1) {
            header('Location: ../admin/admin.php');
            exit();
        } elseif ($role == 0) {
            header('Location: ../Sinhvien/sv.php');
            exit();
        }
    } else {
        $error_message = "Sai mã số sinh viên hoặc mật khẩu. Vui lòng thử lại."; // Hiển thị thông báo lỗi nếu đăng nhập thất bại
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login QLDSV</title>
    <link rel="stylesheet" href="../css/login.css"> <!-- Liên kết đến file CSS -->
</head>
<body>
    <div class="main">
        <h2>Login QLDSV</h2>
        
        <?php 
        // Hiển thị thông báo lỗi nếu có
        if (!empty($error_message)) {
            echo "<p class='error-message'>$error_message</p>";
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <input type="text" name="msv" placeholder="Mã số sinh viên" required>
            <input type="password" name="pass" placeholder="Mật khẩu" required>
            <input type="submit" name="dangnhap" value="Đăng Nhập">

        </form>
        <div class="help">
            <a href="quenmk.php">Quên mật khẩu</a>
        </div>
    </div>
</body>
</html>
