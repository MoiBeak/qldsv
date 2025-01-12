<?php


function checkuser($msv, $pass) {
    $conn = connect();

    // Sử dụng Prepared Statements để tránh SQL Injection
    $stmt = $conn->prepare("SELECT role FROM tbl_taikhoan WHERE msv = :msv AND pass = :pass");
    $stmt->bindParam(':msv', $msv, PDO::PARAM_STR);
    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);

    // Thực thi truy vấn
    $stmt->execute();

    // Lấy kết quả
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Kiểm tra nếu không có kết quả trả về
    if ($result) {
        return $result['role'];
    } else {
        return null; // Không tìm thấy người dùng
    }
}

?>
