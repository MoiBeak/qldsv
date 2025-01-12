<?php
session_start();  // Khởi tạo session

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['msv'])) {
    header('Location: login.php');  // Nếu chưa đăng nhập, chuyển hướng về trang đăng nhập
    exit();
}

// Nếu muốn đăng xuất, bạn cần hủy session
session_unset();  // Xóa tất cả các biến session
session_destroy();  // Hủy session

// Sau khi đăng xuất, chuyển hướng về trang login
header('Location: login.php');
exit();
?>
