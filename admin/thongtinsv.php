<?php
// Import file db.php
include '../model/connect.php';

// Kết nối đến cơ sở dữ liệu
$conn = connect();

$msv = '';
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $msv = htmlspecialchars(trim($_POST['search']));
    try {
        // Tìm kiếm thông tin sinh viên theo MSV hoặc tên
        $stmt = $conn->prepare("
            SELECT 
                tk.msv, tk.hoTen, tk.email, tk.MaLop, 
                lc.TenLop, lc.nienKhoa, lc.siSo, lc.maKhoa, kh.TenKhoa 
            FROM tbl_taikhoan tk
            JOIN tbl_lopchuyennganh lc ON tk.MaLop = lc.MaLop
            JOIN tbl_khoa kh ON lc.maKhoa = kh.MaKhoa
            WHERE tk.msv = :search OR tk.hoTen LIKE :searchName
        ");
        $stmt->execute([
            ':search' => $msv,
            ':searchName' => '%' . $msv . '%'
        ]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Lỗi truy vấn: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Sinh Viên</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4; /* Màu nền xám nhạt */
            color: #333; /* Màu chữ xám đậm */
        }

        h1 {
            text-align: center;
            margin: 20px 0;
            color: #555; /* Màu xám trung tính */
        }

        form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 10px;
            font-size: 16px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9; /* Màu nền input */
        }

        button {
            padding: 10px 15px;
            font-size: 16px;
            background-color: #666; /* Màu xám trung tính */
            color: white;
            border: none;
            border-radius: 5px;
            margin-left: 10px;
            cursor: pointer;
        }

        button:hover {
            background-color: #444; /* Màu xám đậm hơn khi hover */
        }

        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background: white;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #666; /* Màu xám trung tính */
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2; /* Màu xám rất nhạt */
        }

        tr:hover {
            background-color: #ddd; /* Màu xám nhạt hơn khi hover */
        }

        .no-result {
            text-align: center;
            font-size: 18px;
            color: #777; /* Màu xám đậm hơn */
            margin-top: 20px;
        }
        .back-button {
            margin: 20px auto; /* Căn giữa nút */
            width: 200px;
            text-align: center; /* Đặt nội dung nút ở giữa */
        }

        .back-button a {
            text-decoration: none; /* Loại bỏ gạch chân mặc định của liên kết */
            padding: 10px 20px; /* Tạo khoảng cách bên trong nút */
            font-size: 16px; /* Cỡ chữ */
            background-color: #666; /* Màu nền xám */
            color: white; /* Màu chữ trắng */
            border-radius: 5px; /* Bo tròn góc nút */
        }

        .back-button a:hover {
            background-color: #444; /* Màu nền đậm hơn khi hover */
        }

    </style>
</head>
<body>
    <h1>Thông Tin Sinh Viên</h1>
    <form method="POST">
        <input type="text" name="search" placeholder="Nhập MSV hoặc Tên" value="<?= htmlspecialchars($msv) ?>">
        <button type="submit">Tìm Kiếm</button>
    </form>

    <?php if (!empty($data)): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>MSV</th>
                    <th>Họ Tên</th>
                    <th>Email</th>
                    <th>Mã Lớp</th>
                    <th>Tên Lớp</th>
                    <th>Niên Khóa</th>
                    <th>Sĩ Số</th>
                    <th>Khoa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['msv']) ?></td>
                        <td><?= htmlspecialchars($row['hoTen']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['MaLop']) ?></td>
                        <td><?= htmlspecialchars($row['TenLop']) ?></td>
                        <td><?= htmlspecialchars($row['nienKhoa']) ?></td>
                        <td><?= htmlspecialchars($row['siSo']) ?></td>
                        <td><?= htmlspecialchars($row['TenKhoa']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <p>Không tìm thấy sinh viên nào!</p>
    <?php endif; ?>
    <div class="back-button">
        <a href="admin.php">Quay lại</a>
    </div>
</body>
</html>
