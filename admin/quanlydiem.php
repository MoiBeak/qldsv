<?php

session_start();
include_once('../model/connect.php');

// Xử lý nhập điểm từ file CSV
if (isset($_POST['import'])) {
   if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] === UPLOAD_ERR_OK) {
       $fileTmpPath = $_FILES['csvFile']['tmp_name'];
       $fileName = $_FILES['csvFile']['name'];
       $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

       if ($fileExtension === 'csv') {
           try {
               $conn = connect();

               // Mở file CSV
               if (($handle = fopen($fileTmpPath, 'r')) !== false) {
                   // Bỏ qua dòng đầu tiên (tiêu đề)
                   fgetcsv($handle, 1000, ',');

                   // Đọc dữ liệu từ file CSV
                   while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                       $msv = $data[0];
                       $hoTen = $data[1];
                       $maLop = $data[2];
                       $maHocPhan = $data[3];
                       $diemA = $data[4];
                       $diemB = $data[5];
                       $diemC = $data[6];

                       // Kiểm tra sinh viên đã tồn tại chưa
                       $sql = "SELECT * FROM tbl_sinhvien WHERE msv = ?";
                       $stmt = $conn->prepare($sql);
                       $stmt->execute([$msv]);
                       $sinhVien = $stmt->fetch(PDO::FETCH_ASSOC);

                       if (!$sinhVien) {
                           // Thêm sinh viên mới nếu chưa tồn tại
                           $sql = "INSERT INTO tbl_sinhvien (msv, hoTen, maLop) VALUES (?, ?, ?)";
                           $stmt = $conn->prepare($sql);
                           $stmt->execute([$msv, $hoTen, $maLop]);
                       }

                       // Thêm hoặc cập nhật điểm
                       $sql = "REPLACE INTO tbl_diemhocphan (msv, MaHocPhan, a, b, c) VALUES (?, ?, ?, ?, ?)";
                       $stmt = $conn->prepare($sql);
                       $stmt->execute([$msv, $maHocPhan, $diemA, $diemB, $diemC]);
                   }

                   fclose($handle);
                   echo "<p>Nhập dữ liệu từ file CSV thành công!</p>";
               } else {
                   echo "<p>Lỗi: Không thể mở file CSV.</p>";
               }
           } catch (PDOException $e) {
               echo "<p>Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
           }
       } else {
           echo "<p>Lỗi: Chỉ hỗ trợ file CSV.</p>";
       }
   } else {
       echo "<p>Lỗi: Vui lòng chọn file hợp lệ.</p>";
   }
}

// Xử lý các chức năng khác...
if (isset($_POST['delete'])) {
   $msv = $_POST['msv'];
   $maHocPhan = $_POST['maHocPhan'];

   try {
       $conn = connect();
       $sql = "DELETE FROM tbl_diemhocphan WHERE msv = ? AND MaHocPhan = ?";
       $stmt = $conn->prepare($sql);
       $stmt->execute([$msv, $maHocPhan]);
       echo "<p>Điểm đã được xóa thành công.</p>";
   } catch (PDOException $e) {
       echo "<p>Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
   }
}


    



// Xóa điểm
if (isset($_POST['delete'])) {
    $msv = $_POST['msv'];
    $maHocPhan = $_POST['maHocPhan'];

    try {
        $conn = connect();
        $sql = "DELETE FROM tbl_diemhocphan WHERE msv = ? AND MaHocPhan = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$msv, $maHocPhan]);
        echo "<p>Điểm đã được xóa thành công.</p>";
    } catch (PDOException $e) {
        echo "<p>Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Điểm</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
  <style>
        
    /* Styling bảng */
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            font-size: 16px;
            text-align: left;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        table th {
            background-color: #4CAF50;
            color: white;
            text-align: center;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #ddd;
        }

        table td {
            text-align: center;
        }

        /* Nút trong bảng */
        button {
            padding: 8px 12px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 2px;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        button[type="button"] {
            background-color: #f44336;
            color: white;
        }

        button[type="button"]:hover {
            background-color: #e53935;
        }

        /* Nút "Thêm" và "Xuất PDF" */
        button a {
            text-decoration: none;
            color: white;
        }

        button a:hover {
            color: #f0f0f0;
        }

        button {
            background-color: #008CBA; /* Màu xanh dương */
            color: white;
            border-radius: 5px;
            margin: 5px;
        }

        button:hover {
            background-color: #007bb5; /* Màu xanh dương đậm hơn */
        }

        /* Nút Quay lại */
        .back-button {
            margin: 20px auto;
            text-align: center;
        }

        .back-button a {
            text-decoration: none;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #666;
            color: white;
            border-radius: 5px;
        }

        .back-button a:hover {
            background-color: #444;
        }
        form {
            display: inline-block; /* Hiển thị các nút trên cùng một dòng */
            margin: 0 5px; /* Điều chỉnh khoảng cách giữa các form */
        }

        button {
            margin: 0 5px; /* Điều chỉnh khoảng cách giữa các nút */
        }
        
  </style>
   

</head>
<body>
    <nav class="sidebar">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="logo.png" alt="logo">
                </span>
                <div class="text header-text">
                    <span class="name">Admin</span>
                    <span class="profession">Web QLDSV</span>
                </div>
            </div>
        </header>
        <div class="menu-bar">
            <ul class="menu-links">
                <li class="nav-link"><a href="admin.php"><i class="bx bx-home-alt icon"></i><span class="text nav-text">Trang chủ</span></a></li>
                <li class="nav-link"><a href="thongtinsv.php"><i class="bx bx-search icon"></i><span class="text nav-text">Search</span></a></li>
                <li class="nav-link"><a href="thongke.php"><i class="bx bx-bar-chart-alt-2 icon"></i><span class="text nav-text">Thống kê</span></a></li>
                <li class="nav-link"><a href="quanlydiem.php"><i class="bx bx-book icon"></i><span class="text nav-text">Quản lý điểm</span></a></li>
            </ul>
            <div class="bottom-content">
                <li class="nav-link"><a href="../model/logout.php"><i class="bx bx-log-out icon"></i><span class="text nav-text">Đăng Xuất</span></a></li>
            </div>
        </div>
    </nav>

    <section class="home">
        <h1 class="tieude">Quản lý điểm sinh viên</h1>

      <!-- Form nhập CSV -->
        <form action="quanlydiem.php" method="post" enctype="multipart/form-data">
            <h3>Nhập điểm từ tệp CSV</h3>
            <label for="csvFile">Chọn tệp CSV:</label>
            <input type="file" name="csvFile" id="csvFile" required>
            <button type="submit" name="import">Nhập điểm từ CSV</button>
        </form>


       <!-- Thêm sinh viên mới -->
       <button><a href="themdiem.php">Thêm</a></button>
        <!-- Bảng hiển thị điểm -->
        <table id="dataTable" border="1">
            <thead>
                <tr>
                    <th>Mã SV</th>
                    <th>Họ tên</th>
                    <th>Mã lớp</th>
                    <th>Tên lớp</th>
                    <th>Mã học phần</th>
                    <th>Tên học phần</th>
                    <th>Điểm A</th>
                    <th>Điểm B</th>
                    <th>Điểm C</th>
                    <th>Chức năng</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include_once('../model/connect.php');
                try {
                    $conn = connect();
                    $sql = "
                        SELECT 
                            tk.msv, tk.hoTen, lcn.MaLop, lcn.TenLop, hp.MaHocPhan, 
                            hp.TenHocPhan, dhp.a AS DiemA, dhp.b AS DiemB, dhp.c AS DiemC
                        FROM tbl_taikhoan tk
                        JOIN tbl_lopchuyennganh lcn ON tk.MaLop = lcn.MaLop
                        JOIN tbl_lophocphan lhp ON lcn.MaLop = lhp.MaLop
                        JOIN tbl_hocphan hp ON lhp.MaHocPhan = hp.MaHocPhan
                        LEFT JOIN tbl_diemhocphan dhp ON tk.msv = dhp.msv AND hp.MaHocPhan = dhp.MaHocPhan
                        WHERE (dhp.a IS NOT NULL OR dhp.b IS NOT NULL OR dhp.c IS NOT NULL);
                    ";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                            <td>" . htmlspecialchars($row['msv']) . "</td>
                            <td>" . htmlspecialchars($row['hoTen']) . "</td>
                            <td>" . htmlspecialchars($row['MaLop']) . "</td>
                            <td>" . htmlspecialchars($row['TenLop']) . "</td>
                            <td>" . htmlspecialchars($row['MaHocPhan']) . "</td>
                            <td>" . htmlspecialchars($row['TenHocPhan']) . "</td>
                            <td>" . htmlspecialchars($row['DiemA']) . "</td>
                            <td>" . htmlspecialchars($row['DiemB']) . "</td>
                            <td>" . htmlspecialchars($row['DiemC']) . "</td>
                            <td>
                                <form action='quanlydiem.php' method='post'>
                                    <input type='hidden' name='msv' value='" . $row['msv'] . "'>
                                    <input type='hidden' name='maHocPhan' value='" . $row['MaHocPhan'] . "'>
                                    <button type='submit' name='delete'>Xóa</button>
                                </form>
                                <form action='suadiem.php' method='post'>
                                    <input type='hidden' name='msv' value='" . $row['msv'] . "'>
                                    <input type='hidden' name='maHocPhan' value='" . $row['MaHocPhan'] . "'>
                                    <button type='submit' name='update'>sửa</button>
                                </form>
                            </td>
                        </tr>";
                    }
                    $conn = null;
                } catch (PDOException $e) {
                    echo "<p>Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
                ?>
            </tbody>
        </table>

        <!-- Nút xuất PDF -->
        <button id="dl-pdf">Xuất thành PDF</button>

        <script>
            // Sự kiện xuất PDF
            document.getElementById('dl-pdf').onclick = function() {
                var element = document.getElementById('dataTable'); // Bảng dữ liệu
                var options = {
                    margin: 1,
                    filename: 'danh_sach_hoc_phan.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
                };
                html2pdf().from(element).set(options).save(); // Xuất thành PDF
            };
        </script>

        <script>
            // Sử dụng DataTable
            $(document).ready(function() {
                $('#dataTable').DataTable();

                // Hiển thị form thêm mới
                $('#add-btn').on('click', function() {
                    $('#msv').val('');
                    $('#hoTen').val('');
                    $('#maLop').val('');
                    $('#tenLop').val('');
                    $('#maHocPhan').val('');
                    $('#tenHocPhan').val('');
                    $('#diemA').val('');
                    $('#diemB').val('');
                    $('#diemC').val('');
                    $('#form-sua-diem').show();
                });
            });
        </script>
      
        <div id="form-sua-diem" style="display: none;">
        <form id="form-diem">
            <div class="form-row">
                <div class="form-group">
                    <label for="msv">Mã SV:</label>
                    <input type="text" id="msv" name="msv" readonly>
                </div>

                <div class="form-group">
                    <label for="hoTen">Họ tên:</label>
                    <input type="text" id="hoTen" name="hoTen" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="maLop">Mã lớp:</label>
                    <input type="text" id="maLop" name="maLop" readonly>
                </div>

                <div class="form-group">
                    <label for="tenLop">Tên lớp:</label>
                    <input type="text" id="tenLop" name="tenLop" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="maHocPhan">Mã học phần:</label>
                    <input type="text" id="maHocPhan" name="maHocPhan" readonly>
                </div>

                <div class="form-group">
                    <label for="tenHocPhan">Tên học phần:</label>
                    <input type="text" id="tenHocPhan" name="tenHocPhan" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="diemA">Điểm A:</label>
                    <input type="number" id="diemA" name="diemA" step="0.01">
                </div>

                <div class="form-group">
                    <label for="diemB">Điểm B:</label>
                    <input type="number" id="diemB" name="diemB" step="0.01">
                </div>

                <div class="form-group">
                    <label for="diemC">Điểm C:</label>
                    <input type="number" id="diemC" name="diemC" step="0.01">
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit">Lưu</button>
                <button type="button" id="cancel-btn">Hủy</button>
            </div>
        </form>
    </div>


      
    </section>
   
</body>
</html>
