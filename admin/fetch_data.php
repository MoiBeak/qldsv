<?php
// Kết nối cơ sở dữ liệu
include_once('../model/connect.php');

header('Content-Type: application/json');

try {
    // Kết nối đến cơ sở dữ liệu
    $conn = connect();

    // Lấy tổng số sinh viên có điểm
    $totalSql = "
        SELECT COUNT(DISTINCT msv) AS total
        FROM tbl_diemhocphan
        WHERE (a IS NOT NULL OR b IS NOT NULL OR c IS NOT NULL);
    ";
    $stmt = $conn->prepare($totalSql);
    $stmt->execute();
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Nếu không có dữ liệu, trả về thông báo
    if ($total == 0) {
        echo json_encode([
            'error' => 'Không có dữ liệu điểm trong hệ thống.'
        ]);
        exit;
    }

    // Lấy số lượng sinh viên theo từng loại điểm
    $gradeSql = "
        SELECT
            CASE
                WHEN (a + b + c) / 3 >= 8.5 THEN 'Giỏi'
                WHEN (a + b + c) / 3 >= 7.0 THEN 'Khá'
                WHEN (a + b + c) / 3 >= 5.5 THEN 'Trung Bình'
                ELSE 'Yếu'
            END AS grade,
            COUNT(DISTINCT msv) AS count
        FROM tbl_diemhocphan
        WHERE (a IS NOT NULL OR b IS NOT NULL OR c IS NOT NULL)
        GROUP BY grade;
    ";
    $stmt = $conn->prepare($gradeSql);
    $stmt->execute();
    $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tính phần trăm ban đầu và tổng phần trăm
    $result = [];
    $totalPercentage = 0;

    foreach ($grades as $index => $grade) {
        $percentage = round(($grade['count'] / $total) * 100, 2);
        $totalPercentage += $percentage;

        $result[] = [
            'grade' => $grade['grade'],
            'percentage' => $percentage
        ];
    }

    // Điều chỉnh sai số để tổng bằng đúng 100%
    $difference = round(100 - $totalPercentage, 2);
    if ($difference !== 0) {
        // Điều chỉnh phần trăm cho loại có phần trăm lớn nhất
        $maxIndex = 0;
        $maxPercentage = $result[0]['percentage'];

        foreach ($result as $index => $item) {
            if ($item['percentage'] > $maxPercentage) {
                $maxIndex = $index;
                $maxPercentage = $item['percentage'];
            }
        }

        $result[$maxIndex]['percentage'] += $difference;
    }

    // Trả về kết quả dưới dạng JSON
    echo json_encode($result);

} catch (PDOException $e) {
    // Xử lý lỗi và trả về thông báo lỗi
    echo json_encode(['error' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
