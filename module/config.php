<?php
// 設定資料庫連線
define('DB_HOST', 'localhost');
define('DB_USER', 'CS380B');
define('DB_PASS', 'YZUCS380B');
define('DB_NAME', 'CS380B');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
