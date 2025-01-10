<?php
session_start();
$user_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>師資介紹 - 和樂音樂教室</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles/main.css">
    <!-- Page Specific CSS -->
    <link rel="stylesheet" href="styles/teachers.css">
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
                        <a class="nav-link active" href="teachers.php">師資介紹</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="instruments.php">樂器購買</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="booking.php">預約課程</a>
                    </li>
                    <li class="nav-item">
                        <?php if ($user_logged_in): ?>
                                <a class="nav-link" href="logout.php">登出</a>
                        <?php else: ?>
                                <a class="nav-link" href="login.php">登入</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="text-center mb-5">師資介紹</h2>

        <div class="row g-4">
            <!-- 鋼琴老師 -->
            <div class="col-md-6 col-lg-3">
                <div class="teacher-card">
                    <img src="images/teachers/teacher-piano.jpg" class="teacher-img" alt="芬老師">
                    <div class="teacher-info">
                        <h3>芬多精 老師</h3>
                        <h4>鋼琴教學</h4>
                        <div class="teacher-specialties">
                            <h5>專長領域：</h5>
                            <ul>
                                <li>古典鋼琴</li>
                                <li>爵士鋼琴</li>
                                <li>即興演奏</li>
                            </ul>
                        </div>
                        <div class="teacher-experience">
                            <h5>教學經歷：</h5>
                            <ul>
                                <li>台北市立藝術大學音樂系畢業</li>
                                <li>10年教學經驗</li>
                                <li>多次國際鋼琴比賽獲獎</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 吉他老師 -->
            <div class="col-md-6 col-lg-3">
                <div class="teacher-card">
                    <img src="images/teachers/teacher-guitar.jpg" class="teacher-img" alt="約翰老師">
                    <div class="teacher-info">
                        <h3>約翰欣梅爾 老師</h3>
                        <h4>吉他教學</h4>
                        <div class="teacher-specialties">
                            <h5>專長領域：</h5>
                            <ul>
                                <li>古典吉他</li>
                                <li>電吉他</li>
                                <li>指彈吉他</li>
                            </ul>
                        </div>
                        <div class="teacher-experience">
                            <h5>教學經歷：</h5>
                            <ul>
                                <li>美國柏克萊音樂學院畢業</li>
                                <li>8年教學經驗</li>
                                <li>多次音樂節演出經驗</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 爵士鼓老師 -->
            <div class="col-md-6 col-lg-3">
                <div class="teacher-card">
                    <img src="images/teachers/teacher-drum.jpg" class="teacher-img" alt="冠老師">
                    <div class="teacher-info">
                        <h3>冠軍佑子 老師</h3>
                        <h4>爵士鼓教學</h4>
                        <div class="teacher-specialties">
                            <h5>專長領域：</h5>
                            <ul>
                                <li>爵士鼓</li>
                                <li>拉丁節奏</li>
                                <li>搖滾曲風</li>
                            </ul>
                        </div>
                        <div class="teacher-experience">
                            <h5>教學經歷：</h5>
                            <ul>
                                <li>英國倫敦音樂學院畢業</li>
                                <li>12年教學經驗</li>
                                <li>多個知名樂團演出經驗</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 聲樂老師 -->
            <div class="col-md-6 col-lg-3">
                <div class="teacher-card">
                    <img src="images/teachers/teacher-vocal.jpg" class="teacher-img" alt="邱老師">
                    <div class="teacher-info">
                        <h3>邱雞丁 老師</h3>
                        <h4>聲樂教學</h4>
                        <div class="teacher-specialties">
                            <h5>專長領域：</h5>
                            <ul>
                                <li>流行唱法</li>
                                <li>美聲唱法</li>
                                <li>呼吸技巧</li>
                            </ul>
                        </div>
                        <div class="teacher-experience">
                            <h5>教學經歷：</h5>
                            <ul>
                                <li>義大利米蘭音樂學院畢業</li>
                                <li>15年教學經驗</li>
                                <li>多次音樂劇演出經驗</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>