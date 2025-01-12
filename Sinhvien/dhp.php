




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SINH VIÊN </title>
    <link rel="stylesheet" href="../css/admin.css">
    <!-- link boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="../html2pdf.js-main/dist/html2pdf.bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
</head>
  
    <script src="../javaScript/script.js"></script>
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
                            <span class="text nav-text">Thông Tin Học Tập  </span>
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
        <h1 class="tieude">Danh sách học phần</h1>
        
            
        
        <table id="dataTable" border="1">
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã học phần</th>
                <th>Tên học phần</th>
                <th>Nhóm</th>
                <th>Tín chỉ</th>
                <th>Điểm A</th>
                <th>Điểm B</th>
                <th>Điểm C</th>
                <th>Điểm hệ 10</th>
                <th>GPA Môn học</th>
            </tr>
        </thead>
        <tbody>
        <?php
            session_start();
            include_once('../model/connect.php');

            if (isset($_SESSION['msv'])) {
                $msv = $_SESSION['msv'];

                try {
                    $conn = connect();

                    $sql = "
                    SELECT 
                        hp.MaHocPhan,
                        hp.TenHocPhan,
                        lhp.nhom,
                        hp.TinChi,
                        AVG(COALESCE(dh.a, 0)) AS DiemA,
                        AVG(COALESCE(dh.b, 0)) AS DiemB,
                        AVG(COALESCE(dh.c, 0)) AS DiemC
                    FROM tbl_taikhoan tk
                    JOIN tbl_lopchuyennganh lcn ON tk.MaLop = lcn.MaLop
                    JOIN tbl_lophocphan lhp ON lcn.MaLop = lhp.MaLop
                    JOIN tbl_hocphan hp ON lhp.MaHocPhan = hp.MaHocPhan
                    LEFT JOIN tbl_diemhocphan dh ON dh.MaHocPhan = hp.MaHocPhan AND dh.msv = tk.msv
                    WHERE tk.msv = ?
                    GROUP BY hp.MaHocPhan, lhp.nhom, hp.TinChi;
                    ";

                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$msv]);

                    // Tính GPA
                    $tongGPA = 0;
                    $tongTinChi = 0;

                    function quyDoiDiemSangGPA($diem) {
                        if ($diem >= 8.5) {
                            return 4.0;
                        } elseif ($diem >= 7.0) {
                            return 3.0;
                        } elseif ($diem >= 5.5) {
                            return 2.0;
                        } elseif ($diem >= 4.0) {
                            return 1.0;
                        } else {
                            return 0.0;
                        }
                    }

                    $id = 1;

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $tinChi = $row['TinChi'];
                        $diemA = $row['DiemA'];
                        $diemB = $row['DiemB'];
                        $diemC = $row['DiemC'];
                        $diemTB = $diemA * 0.6 + $diemB * 0.3 + $diemC * 0.1; // Điểm trung bình môn
                        $gpaMonHoc = quyDoiDiemSangGPA($diemTB); // Quy đổi sang GPA thang 4.0

                        // Cộng dồn GPA và số tín chỉ
                        $tongGPA += $gpaMonHoc * $tinChi;
                        $tongTinChi += $tinChi;

                        echo "<tr>
                            <td>{$id}</td>
                            <td>{$row['MaHocPhan']}</td>
                            <td>{$row['TenHocPhan']}</td>
                            <td>{$row['nhom']}</td>
                            <td>{$tinChi}</td>
                            <td>" . number_format($diemA, 2) . "</td>
                            <td>" . number_format($diemB, 2) . "</td>
                            <td>" . number_format($diemC, 2) . "</td>
                            <td>" . number_format($diemTB, 2) . "</td>
                            <td>" . number_format($gpaMonHoc, 2) . "</td>
                        </tr>";
                        $id++;
                    }

                    echo '</tbody></table>';

                    // Tính toán GPA trung bình
                    $gpaTrungBinh = $tongTinChi > 0 ? $tongGPA / $tongTinChi : 0;

                    // Xếp loại học lực dựa trên GPA trung bình
                    $xepLoai = match (true) {
                        $gpaTrungBinh >= 3.6 => 'Xuất sắc',
                        $gpaTrungBinh >= 3.2 => 'Giỏi',
                        $gpaTrungBinh >= 2.5 => 'Khá',
                        default => 'Trung bình'
                    };

                    echo '<h3>GPA Trung bình: ' . number_format($gpaTrungBinh, 2) . '</h3>';
                    echo '<h3>Xếp loại học tập: ' . $xepLoai . '</h3>';
                    echo '</section>';

                    $conn = null;
                } catch (PDOException $e) {
                    echo "<p>Lỗi kết nối hoặc truy vấn: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
            } else {
                echo "<p>Bạn chưa đăng nhập hoặc không có mã sinh viên.</p>";
            }
            ?>


        </tbody>
    </table>

        </section>
            
        <button id="dl-pdf">Xuất thành PDF</button>
            
            <script type="text/javascript">
            document.getElementById('dl-pdf').onclick = function(){
            var element = document.getElementById('dataTable');//bảng 
            var options = {
                margin: 1,
                filename: 'danh_sach_hoc_phan.pdf',
                image: {type: 'jpeg', quality: 0.98},
                jsPDF: {unit: 'in', format: 'letter', orientation: 'portrait'}
            };
            
            html2pdf(element, options);  // Sử dụng html2pdf
            };
        </script>

        

            
        
            
            


        
        <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>
    
    
</body>
</html>
