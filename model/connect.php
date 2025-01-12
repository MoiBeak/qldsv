<?php
function connect() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "2221050613";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password); // utf8mb4
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Kết nối PDO thất bại: " . $e->getMessage());
    }
}
?>