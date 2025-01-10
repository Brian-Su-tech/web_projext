<?php
session_start();
require_once 'module/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // 密碼加密
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $role = 'student';   // admin從後端手動改

    // 檢查email是否已存在
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $error = '這個電子郵件已經被註冊了！';
    }
    else {
        // 插入新用戶
        $stmt = $conn->prepare('INSERT INTO users (email, password, name, phone, role) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$email, $password, $name, $phone, $role]);

        if ($stmt->execute()) {
            $_SESSION['message'] = "註冊成功！請重新登入!";
            header("Location: login.php");
            exit;  
        } else {
            $error = "註冊失敗：" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>註冊 - 和樂音樂教室</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .form-control{
            width: 300px;
        } 
        .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- 導覽列 -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.html">和樂音樂教室</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.html">首頁</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="teachers.html">師資介紹</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="instruments.php">樂器購買</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="booking.php">預約課程</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="login.php">登入</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="text-center mb-4">註冊帳號</h2>
        <?php if (isset($error)) : ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form id="registerForm" class="d-flex flex-column align-items-center" method="POST" onsubmit="return validateForm()">
            <p class="mb-3">姓名<input type="text" class="form-control" id="name" name="name" required></p>
            <p class="mb-3">電話<input type="tel" class="form-control" id="phone" name="phone" required></p>
            <p class="mb-3">電子郵件<input type="email" class="form-control" id="email" name="email" required></p>
            <p class="mb-3">密碼<input type="password" class="form-control" id="password" name="password" required></p>
            <p class="mb-3">確認密碼<input type="password" class="form-control" id="confirm_password" name="confirm_password" required></p>
            <button type="submit" class="btn btn-primary">註冊</button>
        </form>
        <p class="text-center mb-3">已有帳號？<a href="login.php">登入</a></p>
    </div>

    <script>
    async function validateForm() {
        const form = document.getElementById('registerForm');
        const password = form.password.value;
        const confirmPassword = form.confirm_password.value;
        const email = form.email.value;

        if (password !== confirmPassword) {
            alert('密碼不符合！');
            return false;
        }
        if (password.length < 6) {
            alert('密碼至少需要6個字元！');
            return false;
        }
 
    }
    </script>
</body>
</html>