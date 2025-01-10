<?php
// 設定資料庫連線
define('DB_HOST', 'localhost');
define('DB_USER', '1111442');
define('DB_PASS', '001019');
define('DB_NAME', 'final_project_v1');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
