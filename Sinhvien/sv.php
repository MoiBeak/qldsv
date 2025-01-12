<?php

session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa qua session hoặc cookie
if (!isset($_SESSION['msv']) && isset($_COOKIE['msv'])) {
    // Nếu chưa đăng nhập nhưng có cookie, đăng nhập tự động
    $_SESSION['msv'] = $_COOKIE['msv'];
    $_SESSION['role'] = $_COOKIE['role']; // Lưu thông tin role từ cookie
}

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['msv'])) {
    header('Location: login.php');  // Chuyển hướng về trang đăng nhập nếu chưa đăng nhập
    exit();
}

// Tiếp tục xử lý nếu đã đăng nhập




?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinh Viên </title>
    <link rel="stylesheet" href="../css/admin.css">
    <!-- link boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
</head>
<body>
    <nav class="sidebar">
        <header>
            <div class="image-text">
                <span class = "image" >
                    <img src="logo.png" alt="logo">
                </span>
                <div class="text header-text">
                    <span class="name">SinhVien</span>
                    <span class="profession">Web QLDSV</span>
                </div>
                
            </div>
          
        </header>

        <div class="menu-bar">
            <div class="menu">
                     
                
                <ul class="menu-links">
                    <li class="nav-link">
                        <a href="sv.php">
                            <i class="bx bx-home-alt icon"></i>
                            <span class="text nav-text">Trang chủ</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="dhp.php">
                            <i class="bx bx-book icon"></i>
                            <span class="text nav-text">Điểm học phần</span>
                        </a>
                    </li>
                    
                   
                    
                </ul>
            </div>
            <div class="bottom-centent">
                <li class="nav-link">
                    <a href="../model/logout.php">
                        <i class="bx bx-log-out icon"></i>
                        <span class="text nav-text">Đăng Xuất</span>
                    </a>
                </li>
                
                
            </div>
        </div>
    </nav>
    <section class="home">
        <img src="bbb.png" alt="logo">
        <div class="ttlh">
        <h2>Thông Tin Liên Hệ</h2>
            <p><span>Địa chỉ:</span> lớp DCCTCT67-04A Trường Đại Học Mỏ Địa Chất</p>
            <p><span>Email:</span> moibeak2004@gmail.com</p>
            <p><span>Số điện thoại:</span>0334312193</p>
            <p><span>Giờ làm việc:</span> Thứ Hai - Chủ nhật, 8:00 - 22:00</p>
        </div>
    </section>

        
    
        
        


    
</body>
</html>
