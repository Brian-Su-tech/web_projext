<?php
    session_start();
    $user_logged_in = isset($_SESSION['user_id']);
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
        header('Location: ../login.php');
        exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>預約課程 - 和樂音樂教室</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles/main.css">

    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    <link href='https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css' rel='stylesheet' />
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
        .calendar-container {
            margin: 20px;
            padding: 20px;
        }
        #calendar {
            max-width: 900px;
            margin: 0 auto;
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
                                <a class="nav-link" href="../logout.php">登出</a>
                        <?php else: ?>
                                <a class="nav-link" href="../login.php">登入</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="booking-form">
        <h2>預約課程</h2>
        <form id="bookingForm">
            <div class="form-group">
                <label>選擇教室：</label>
                <select name="classroom" id="classroom" required>
                    <option value="">請選擇教室</option>
                    <?php
                    require_once '../config.php';
                    $result = $conn->query("SELECT DISTINCT classroom FROM courses");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['classroom'] . "'>" . $row['classroom'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>選擇教師：</label>
                <select name="teacher" id="teacher" required>
                    <option value="">請選擇教師</option>
                    <?php
                    $result = $conn->query("SELECT id, username FROM users WHERE role='admin'");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['username'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>選擇日期：</label>
                <input type="date" name="date" id="date" required>
            </div>
            <div class="form-group">
                <label>選擇時段：</label>
                <select name="time_slot" id="time_slot" required>
                    <option value="">請選擇時段</option>
                    <option value="09:00">09:00-10:00</option>
                    <option value="10:00">10:00-11:00</option>
                    <option value="11:00">11:00-12:00</option>
                    <option value="13:00">13:00-14:00</option>
                    <option value="14:00">14:00-15:00</option>
                    <option value="15:00">15:00-16:00</option>
                </select>
            </div>
            <button type="submit">預約課程</button>
        </form>
    </div>

    <div class="calendar-container">
        <div id="calendar"></div>
    </div>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js'></script>

    <script>
    $(document).ready(function() {
        // 初始化日曆
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            defaultView: 'agendaWeek',
            slotDuration: '01:00:00',
            events: 'get_courses.php',
            eventClick: function(event) {
                alert('課程: ' + event.title + '\n教室: ' + event.classroom + '\n教師: ' + event.teacher);
            }
        });

        // 檢查時段衝突
        function checkConflict() {
            var formData = $('#bookingForm').serialize();
            $.ajax({
                url: 'check_conflict.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.conflict) {
                        toastr.error('該時段已被預約！');
                    }
                }
            });
        }

        // 表單提交處理
        $('#bookingForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'process_booking.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        toastr.success('預約成功！');
                        $('#calendar').fullCalendar('refetchEvents');
                    } else {
                        toastr.error('預約失敗：' + response.message);
                    }
                }
            });
        });

        // 時段選擇變更時檢查衝突
        $('#time_slot, #date').change(checkConflict);
    });
    </script>
</body>
</html>
