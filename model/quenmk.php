<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

session_start();
include_once "connect.php";

$error_message = "";
$success_message = "";

// Hàm tạo mật khẩu ngẫu nhiên (đã được cải tiến)
function generateRandomPassword($length = 12) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()-_+='; // Thêm các ký tự đặc biệt
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)]; // Sử dụng random_int() cho tính ngẫu nhiên tốt hơn
    }
    return $randomString;
}

if (isset($_POST['gui'])) {
    $email = trim($_POST['email']);

    $conn = connect();
    try {
        $stmt = $conn->prepare("SELECT msv FROM tbl_taikhoan WHERE email = :email"); // Chỉ cần lấy msv
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $msv = $result['msv'];
            $newPassword = generateRandomPassword(); // Tạo mật khẩu ngẫu nhiên
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT); // Băm mật khẩu

            // Cập nhật mật khẩu đã băm vào CSDL
            $updateStmt = $conn->prepare("UPDATE tbl_taikhoan SET pass = :pass WHERE msv = :msv");
            $updateStmt->bindParam(':pass', $newPasswordHash, PDO::PARAM_STR);
            $updateStmt->bindParam(':msv', $msv, PDO::PARAM_INT);
            $updateStmt->execute();

            $mail = new PHPMailer(true);
            try {
                // ... (Cấu hình PHPMailer như trước)

                $mail->Subject = 'Mật khẩu mới của bạn';
                $mail->Body = "
                    <p>Chào bạn,</p>
                    <p>Mật khẩu mới của bạn là: <b>" . $newPassword . "</b></p>
                    <p>Vui lòng đăng nhập và thay đổi mật khẩu ngay sau khi đăng nhập.</p>
                    <p>Trân trọng,</p>
                    <p>Đội ngũ QLDSV</p>
                ";

                if (!$mail->send()) {
                    $error_message = 'Lỗi gửi email: ' . $mail->ErrorInfo;
                } else {
                    $success_message = "Mật khẩu mới đã được gửi tới email của bạn.";
                }
            } catch (Exception $e) {
                $error_message = "Lỗi gửi email: " . $e->getMessage(); // Để debug
                // $error_message = "Không thể gửi email. Vui lòng thử lại sau."; // Cho người dùng cuối
            }
        } else {
            $error_message = "Email không tồn tại trong hệ thống.";
        }
    } catch (PDOException $e) {
        $error_message = "Lỗi CSDL: " . $e->getMessage();
    } finally {
        $conn = null;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="../css/login.css"> <!-- Liên kết đến file CSS -->
</head>
<body>
    <div class="main">
        <h2>Quên Mật Khẩu</h2>

        <?php 
        // Hiển thị thông báo lỗi nếu có
        if (!empty($error_message)) {
            echo "<p class='error-message'>" . htmlspecialchars($error_message) . "</p>";
        }

        // Hiển thị thông báo thành công nếu có
        if (!empty($success_message)) {
            echo "<p class='success-message'>" . htmlspecialchars($success_message) . "</p>";
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <input type="email" name="email" placeholder="Nhập email của bạn" required>
            <input type="submit" name="gui" value="Gửi yêu cầu">
        </form>
        <div class="help">
            <a href="login.php">Quay lại đăng nhập</a>
        </div>
    </div>
</body>
</html>
