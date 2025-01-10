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
    <style>
        .booking-form {
            margin: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .day-container {
            margin-bottom: 20px;
        }
        .day-title {
            font-size: 1.5rem;
            margin-bottom: 10px;
            font-weight: bold;
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

        <div id="availableCourses" class="booking-form">
            <!-- 可預約課程將由 JavaScript 動態載入並依照星期分類 -->
        </div>

        <!-- 修改 Modal 用於顯示課程詳情 -->
        <div class="modal fade" id="courseModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">課程詳情</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- 課程詳情將由 JavaScript 填充 -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                        <button type="button" class="btn btn-primary" id="bookCourse">預約課程</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js'></script>

    <script>
        $(document).ready(function() {
            // 載入可預約課程
            function loadAvailableCourses() {
                $.get('booking_api.php?action=get_available_courses', function(data) {
                    const daysOfWeek = ["星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"];
                    let coursesByDay = {};

                    // 將課程分類到對應的星期
                    data.forEach(function(course) {
                        const dayOfWeek = daysOfWeek[new Date(course.start_time).getDay() - 1];
                        if (!coursesByDay[dayOfWeek]) {
                            coursesByDay[dayOfWeek] = [];
                        }
                        coursesByDay[dayOfWeek].push(course);
                    });

                    // 動態生成課程列表
                    let html = '';
                    daysOfWeek.forEach(function(day) {
                        if (coursesByDay[day]) {
                            html += `
                                <div class="day-container">
                                    <div class="day-title">${day}</div>
                                    ${coursesByDay[day].map(course => `
                                        <div class="course-info">
                                            <h5>${course.name}</h5>
                                            <p>
                                                教師: ${course.teacher}<br>
                                                教室: ${course.classroom}<br>
                                                時間: ${formatDateTime(course.start_time)} - ${formatDateTime(course.end_time)}<br>
                                                剩餘名額: ${course.remaining}/${course.capacity}
                                            </p>
                                            <button class="btn btn-primary book-btn" data-course-id="${course.id}">
                                                預約課程
                                            </button>
                                        </div>
                                    `).join('')}
                                </div>
                            `;
                        }
                    });
                    $('#availableCourses').html(html);
                });
            }

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

            // 顯示課程詳情
            function showCourseDetail(courseId) {
                $.get(`booking_api.php?action=get_course&id=${courseId}`, function(course) {
                    let modal = $('#courseModal');
                    modal.find('.modal-title').text(course.name);
                    modal.find('.modal-body').html(`
                        <p>教室: ${course.classroom}</p>
                        <p>教師: ${course.teacher}</p>
                        <p>開始時間: ${formatDateTime(course.start_time)}</p>
                        <p>結束時間: ${formatDateTime(course.end_time)}</p>
                        <p>剩餘名額: ${course.remaining}/${course.capacity}</p>
                    `);
                    modal.find('#bookCourse').data('course-id', course.id);
                    modal.modal('show');
                });
            }

            // 預約課程
            function bookCourse(courseId) {
                $.ajax({
                    url: 'booking_api.php?action=book',
                    type: 'POST',
                    data: { course_id: courseId },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('預約成功！');
                            $('#courseModal').modal('hide');
                            loadAvailableCourses();
                        } else {
                            toastr.error('預約失敗：' + response.message);
                        }
                    }
                });
            }

            // 綁定預約按鈕事件
            $(document).on('click', '.book-btn', function() {
                let courseId = $(this).data('course-id');
                showCourseDetail(courseId);
            });

            // 表單提交處理
            $('#bookCourse').click(function() {
                let courseId = $(this).data('course-id');
                bookCourse(courseId);
            });

            // 初始載入可預約課程
            loadAvailableCourses();
        });
    </script>
</body>

</html>
