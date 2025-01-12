<?php
session_start();
require_once 'module/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $role = $_POST['role'];

        // 準備 SQL 語句
        $stmt = mysqli_prepare($conn, 'SELECT * FROM users WHERE email = ?');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        
        // 獲取結果
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['role'] !== $role) {
                $error = '角色不匹配！';
            } else {
                // 登入成功，設置session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                // 根據角色重定向到不同的儀表板
                if ($role === 'student') {
                    header('Location: index.php');
                } elseif ($role === 'admin') {
                    header('Location: admin/manage_order.php');
                }
                exit();
            }
        } else {
            $error = '電子郵件或密碼錯誤！';
        }
    } catch (PDOException $e) {
        $error = '登入失敗：' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登入 - 和樂音樂教室</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .login-section {
            padding: 40px 0;
        }

        .login-title {
            color: var(--wood-primary);
            margin-bottom: 30px;
        }
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
            <a class="navbar-brand" href="index.php">和樂音樂教室</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">首頁</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="teachers.php">師資介紹</a>
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
        <h2 class="text-center mb-4">登入系統</h2>
        <?php if (isset($error)) : ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" class="d-flex flex-column align-items-center">
            <p class="mb-3">電子郵件 <input type="email" class="form-control" name="email" required></p>
            <p class="mb-3">密碼 <input type="password" class="form-control" name="password" required></p>
            <p class="mb-3">
                <label class="form-check-label me-2">
                    <input type="radio" class="form-check-input" name="role" value="student" checked required> 學生
                </label>
                <label class="form-check-label">
                    <input type="radio" class="form-check-input" name="role" value="admin" required> 老師
                </label>
            </p>
            <p><button type="submit" class="btn btn-primary">登入</button></p>
        </form>
        <p class="text-center">還沒有帳號？<a href="register.php">註冊</a></p>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>