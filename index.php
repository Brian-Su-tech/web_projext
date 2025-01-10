<?php
session_start();
$user_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>和樂音樂教室</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles/main.css">
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
                        <a class="nav-link active" href="index.php">首頁</a>
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

    <!-- 輪播圖片 -->
    <div id="classroomCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#classroomCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#classroomCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#classroomCarousel" data-bs-slide-to="2"></button>
            <button type="button" data-bs-target="#classroomCarousel" data-bs-slide-to="3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="images/rooms/piano-room.jpg" class="d-block w-100" alt="鋼琴教室">
                <div class="carousel-caption">
                    <h3>鋼琴教室</h3>
                </div>
            </div>
            <div class="carousel-item">
                <img src="images/rooms/drum-room.jpg" class="d-block w-100" alt="打鼓教室">
                <div class="carousel-caption">
                    <h3>打鼓教室</h3>
                </div>
            </div>
            <div class="carousel-item">
                <img src="images/rooms/vocal-room.jpg" class="d-block w-100" alt="歌唱教室">
                <div class="carousel-caption">
                    <h3>歌唱教室</h3>
                </div>
            </div>
            <div class="carousel-item">
                <img src="images/rooms/guitar-room.jpg" class="d-block w-100" alt="吉他教室">
                <div class="carousel-caption">
                    <h3>吉他教室</h3>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#classroomCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#classroomCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- 教室介紹 -->
    <div class="container my-5">
        <h2 class="text-center mb-4">關於和樂音樂教室</h2>
        <div class="row">
            <div class="col-md-6">
                <p class="lead">
                    和樂音樂教室成立於2015年，我們致力於提供優質的音樂教育環境，使學員能夠在愉悅的氛圍中學習。此外還擁有專業的教學團隊和完善的設備，
                    為學員打造最佳的學習體驗。
                </p>
                <h4>教室特色：</h4>
                <ul>
                    <li>每間教室有專業隔音設計，不用擔心聲音太大聲影響到其他學員。</li>
                    <li>教室都有配備專業樂器並且專人定期維護，沒有樂器的學員也不用擔心樂器問題。</li>
                    <li>小班制教學，讓學員可以有更多時間和老師互動學習。</li>
                    <li>彈性課程安排，讓學員能夠依照自己的時間安排課程。</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h4>聯絡資訊</h4>
                <p>
                    <i class="bi bi-geo-alt"></i> 地址：台北市中山區音樂街一段123號4樓<br><br>
                    <i class="bi bi-telephone"></i> 電話：(02) 2345-6789<br><br>
                    <i class="bi bi-envelope"></i> Email：contactus@music.com<br><br>
                    <i class="bi bi-clock"></i> 營業時間：週一至週日 10:00-22:00
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>