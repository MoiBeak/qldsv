<?php
session_start();
include_once('../model/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu và tự động chuyển sang viết hoa các trường mã sinh viên, mã lớp, mã học phần
    $msv = strtoupper(trim($_POST['msv']));
    $hoTen = trim($_POST['hoTen']);
    $maLop = strtoupper(trim($_POST['maLop']));
    $maHocPhan = strtoupper(trim($_POST['maHocPhan']));
    $diemA = $_POST['diemA'];
    $diemB = $_POST['diemB'];
    $diemC = $_POST['diemC'];

    try {
        $conn = connect();

        // Kiểm tra sinh viên đã tồn tại chưa
        $sql = "SELECT * FROM tbl_taikhoan WHERE msv = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$msv]);
        $sinhVien = $stmt->fetch(PDO::FETCH_ASSOC);

        // Nếu sinh viên chưa tồn tại, thêm mới
        if (!$sinhVien) {
            $sql = "INSERT INTO tbl_taikhoan (msv, hoTen, maLop) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$msv, $hoTen, $maLop]);
        } else {
            // Nếu sinh viên đã tồn tại, cập nhật mã lớp nếu cần
            $sql = "UPDATE tbl_taikhoan SET maLop = ? WHERE msv = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$maLop, $msv]);
        }

        // Thêm điểm học phần vào bảng tbl_diemhocphan (bao gồm maLop)
        $sql = "INSERT INTO tbl_diemhocphan (msv, MaHocPhan, MaLop, a, b, c) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$msv, $maHocPhan, $maLop, $diemA, $diemB, $diemC]);

        $_SESSION['success'] = "Điểm đã được thêm thành công.";
        header("Location: quanlydiem.php"); // Chuyển hướng đến trang quản lý điểm
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Lỗi: " . htmlspecialchars($e->getMessage());
        header("Location: themdiem.php"); // Nếu có lỗi, chuyển hướng lại trang thêm điểm
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Điểm</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 20px;
        }

        .home {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .tieude {
            text-align: center;
            margin-bottom: 20px;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <section class="home">
        <h1 class="tieude">Thêm Điểm Sinh Viên</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form action="themdiem.php" method="post">
            <div class="form-group">
                <label for="msv">Mã Sinh Viên:</label>
                <input type="text" id="msv" name="msv" required>
            </div>

            <div class="form-group">
                <label for="hoTen">Họ và Tên:</label>
                <input type="text" id="hoTen" name="hoTen" required>
            </div>

            <div class="form-group">
                <label for="maLop">Mã Lớp:</label>
                <input type="text" id="maLop" name="maLop" required>
            </div>

            <div class="form-group">
                <label for="maHocPhan">Mã Học Phần:</label>
                <input type="text" id="maHocPhan" name="maHocPhan" required>
            </div>

            <div class="form-group">
                <label for="diemA">Điểm A:</label>
                <input type="number" id="diemA" name="diemA" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="diemB">Điểm B:</label>
                <input type="number" id="diemB" name="diemB" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="diemC">Điểm C:</label>
                <input type="number" id="diemC" name="diemC" step="0.01" required>
            </div>

            <button type="submit">Thêm Điểm</button>
            <button type="button" onclick="window.location.href='quanlydiem.php'">Hủy</button>
        </form>
    </section>
</body>
</html>
