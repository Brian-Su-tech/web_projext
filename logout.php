<?php
session_start();
// 清除所有會話變數
session_unset();
// 摧毀會話
session_destroy();
// 重定向到登入頁或首頁
header("Location: login.php");
exit();
?>
