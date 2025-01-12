<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật điểm</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color:rgb(122, 57, 57);
            color: #333;
        }
        h1 {
            color: #444;
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            max-width: 400px;
            margin: 0 auto;
        }
        form label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        form input {
            width: calc(100% - 10px);
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        form button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        form button:hover {
            background-color: #0056b3;
        }
        p {
            text-align: center;
            font-size: 14px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Cập nhật điểm</h1>
        <form method="POST" action="">
            <label for="msv">Mã Sinh Viên:</label>
            <input type="text" id="msv" name="msv" required placeholder="Nhập mã sinh viên">
            
            <label for="mahocphan">Mã Học Phần:</label>
            <input type="text" id="mahocphan" name="mahocphan" required placeholder="Nhập mã học phần">
            
            <label for="malop">Mã Lớp:</label>
            <input type="text" id="malop" name="malop" required placeholder="Nhập mã lớp">
            
            <label for="a">Điểm A:</label>
            <input type="number" id="a" name="a" step="0.01" min="0" max="10" required placeholder="Nhập điểm A">
            
            <label for="b">Điểm B:</label>
            <input type="number" id="b" name="b" step="0.01" min="0" max="10" required placeholder="Nhập điểm B">
            
            <label for="c">Điểm C:</label>
            <input type="number" id="c" name="c" step="0.01" min="0" max="10" required placeholder="Nhập điểm C">
            
            <button type="submit">Cập nhật</button>
        </form>
        <script>
            // Hàm tự động viết hoa
            function toUpperCaseInput(input) {
                input.value = input.value.toUpperCase();
            }
        </script>
        <?php
        session_start();
        include_once('../model/connect.php');

        // Kiểm tra khi gửi form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ form
            $msv = $_POST['msv'] ?? '';
            $mahocphan = $_POST['mahocphan'] ?? '';
            $malop = $_POST['malop'] ?? '';
            $a = $_POST['a'] ?? null;
            $b = $_POST['b'] ?? null;
            $c = $_POST['c'] ?? null;

            // Kiểm tra dữ liệu nhập vào
            if (empty($msv) || empty($mahocphan) || empty($malop) || $a === null || $b === null || $c === null) {
                echo "<p style='color: red;'>Lỗi: Vui lòng điền đầy đủ tất cả các trường!</p>";
            } else {
                try {
                    $conn = connect();

                    // Xóa điểm cũ
                    $sql_delete = "DELETE FROM tbl_diemhocphan WHERE msv = ? AND mahocphan = ?";
                    $stmt_delete = $conn->prepare($sql_delete);
                    $stmt_delete->execute([$msv, $mahocphan]);

                    // Thêm điểm mới
                    $sql_insert = "INSERT INTO tbl_diemhocphan (msv, mahocphan, malop, a, b, c) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->execute([$msv, $mahocphan, $malop, $a, $b, $c]);

                    echo "<p style='color: green;'>Cập nhật điểm thành công!</p>";
                    header("Location: quanlydiem.php");
                      exit();
                } catch (PDOException $e) {
                    error_log($e->getMessage());
                    echo "<p style='color: red;'>Lỗi khi cập nhật điểm: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
            }
        }
        ?>
    </div>
</body>
</html>
