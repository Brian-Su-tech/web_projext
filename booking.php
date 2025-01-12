<?php
    session_start();
    $user_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>預約課程 - 和樂音樂教室</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/booking.css">
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
                        <a class="nav-link active" href="booking.php">預約課程</a>
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
        <h2 class="text-center mb-4">預約課程</h2>

        <!-- 新增日曆導航 -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <button id="prevWeekBtn" class="btn btn-outline-secondary">
                <i class="bi bi-chevron-left"></i> 上一週
            </button>
            <h3 id="currentMonth" class="mb-0"></h3>
            <button id="nextWeekBtn" class="btn btn-outline-secondary">
                下一週 <i class="bi bi-chevron-right"></i>
            </button>
        </div>

        <!-- 日曆視圖容器 -->
        <div class="calendar-grid">
            <div class="row" id="weekDays"></div>
            <div class="row" id="courseGrid"></div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js'></script>

    <script>
        $(document).ready(function() {
            let currentDate = new Date();

            // 獲取一週的日期
            function getWeekDates() {
                const dates = [];
                const firstDayOfWeek = new Date(currentDate);
                firstDayOfWeek.setDate(currentDate.getDate() - currentDate.getDay());

                for (let i = 0; i < 7; i++) {
                    const date = new Date(firstDayOfWeek);
                    date.setDate(firstDayOfWeek.getDate() + i);
                    dates.push(date);
                }
                return dates;
            }

            // 格式化時間
            function formatTime(datetime) {
                return new Date(datetime).toLocaleTimeString('zh-TW', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });
            }
            // 格式化日期
            function formatDate(date) {
                return date.toLocaleDateString('zh-TW', {
                    month: 'numeric',
                    day: 'numeric'
                });
            }

            // 取得當天課程
            function getCoursesForDate(courses, date) {
                return courses.filter(course => {
                    const courseDate = new Date(course.start_time);
                    return courseDate.toDateString() === date.toDateString();
                });
            }

            // 渲染日曆
            function renderCalendar(courses) {
                const weekDays = ['日', '一', '二', '三', '四', '五', '六'];
                const dates = getWeekDates();
                
                // 更新月份顯示
                $('#currentMonth').text(
                    `${currentDate.getFullYear()}年${currentDate.getMonth() + 1}月`
                );

                // 渲染星期標題
                let weekDaysHtml = dates.map((date, index) => `
                    <div class="col">
                        <div class="day-header ${date.toDateString() === new Date().toDateString() ? 'current-day' : ''}">
                            <div>週${weekDays[index]}</div>
                            <div class="fw-bold">${formatDate(date)}</div>
                        </div>
                    </div>
                `).join('');
                $('#weekDays').html(weekDaysHtml);

                // 渲染課程網格
                let courseGridHtml = dates.map(date => `
                    <div class="col day-column">
                        ${getCoursesForDate(courses, date).map(course => `
                            <div class="course-card">
                                <div class="course-time">
                                    <i class="bi bi-clock"></i> 
                                    ${formatTime(course.start_time)} - ${formatTime(course.end_time)}
                                </div>
                                <div class="course-name">${course.name}</div>
                                <div class="course-info">
                                    <div><i class="bi bi-person"></i> ${course.teacher_name}</div>
                                    <div><i class="bi bi-geo-alt"></i> ${course.classroom}</div>
                                    <div><i class="bi bi-people"></i> ${course.current}/${course.capacity}</div>
                                </div>
                                <div class="mt-2">
                                    ${course.is_booked 
                                        ? `<button class="btn btn-danger btn-sm cancel-btn" data-course-id="${course.id}">取消預約</button>`
                                        : `<button class="btn btn-primary btn-sm book-btn" data-course-id="${course.id}">預約課程</button>`
                                    }
                                </div>
                            </div>
                        `).join('') || '<div class="no-courses">尚無課程</div>'}
                    </div>
                `).join('');
                $('#courseGrid').html(courseGridHtml);
            }

            // 載入可預約課程
            function loadAvailableCourses() {
                $.get('module/booking_api.php?action=get_available_courses', function(data) {
                    if (!Array.isArray(data)) {
                        toastr.error('後端數據格式錯誤');
                        return;
                    }
                    renderCalendar(data);
                }).fail(function() {
                    toastr.error('無法載入課程數據，請稍後再試。');
                });
            }

            // 綁定週次切換按鈕事件
            $('#prevWeekBtn').click(function() {
                currentDate.setDate(currentDate.getDate() - 7);
                loadAvailableCourses();
            });
            $('#nextWeekBtn').click(function() {
                currentDate.setDate(currentDate.getDate() + 7);
                loadAvailableCourses();
            });

            // 格式化日期時間
            function formatDateTime(datetime) {
                return new Date(datetime).toLocaleString('zh-TW', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });
            }

            // 預約課程
            function bookCourse(courseId) {
                $.ajax({
                    url: 'module/booking_api.php?action=book',
                    type: 'POST',
                    data: { course_id: courseId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('預約成功！');
                            $('#courseModal').modal('hide');
                            loadAvailableCourses();
                        } else {
                            alert('預約失敗：' + response.message);
                        }
                    }
                });
            }

            // 取消預約課程
            function cancelReservation(courseId) {
                $.ajax({
                    url: 'module/booking_api.php?action=cancel',
                    type: 'POST',
                    data: { course_id: courseId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('已取消預約！');
                            loadAvailableCourses();
                        } else {
                            alert('取消預約失敗：' + response.message);
                        }
                    }
                });
            }

            // 綁定預約按鈕事件
            $(document).on('click', '.book-btn', function() {
                // 檢查是否登入
                <?php if (!$user_logged_in): ?>
                    alert('請先登入才能預約課程！');
                    return;
                <?php else: ?>
                    let courseId = $(this).data('course-id');
                    console.log('課程ID:', courseId); // 測試用
                    bookCourse(courseId);
                <?php endif; ?>
            });

            // 綁定取消預約按鈕事件
            $(document).on('click', '.cancel-btn', function() {
                let courseId = $(this).data('course-id');
                cancelReservation(courseId);
            });

            // 初始載入可預約課程
            loadAvailableCourses();
        });
    </script>
</body>

</html>
