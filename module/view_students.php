<?php
require_once 'config.php';

// 顯示所有預約該課程的學生
if (isset($_GET['course_id'])) {
     // 準備SQL查詢

    $course_id = (int)$_GET['course_id'];

    $sql = "SELECT u.id, u.name, u.email, u.phone 
    FROM bookings b 
    JOIN users u ON b.student_id = u.id 
    WHERE b.course_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $course_id);

    $stmt->execute();
    $result = $stmt->get_result();
    echo '<div class="modal-content" style="position:relative; margin:10% auto; padding:20px; width:50%; background:white; border-radius:8px;">';
    echo '<span class="close" style="position:absolute; top:10px; right:20px; cursor:pointer; font-size:24px;">&times;</span>';
    echo '<h4>預約該課程的學生：</h4>';
    echo '<table class="table table-bordered">';
    echo '<tr><th>姓名</th><th>電子郵件</th><th>電話</th></tr>';

    if ($result && $result->num_rows > 0) {

        // 顯示學生資料
        while ($student = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $student['name'] . '</td>';
            echo '<td>' . $student['email'] . '</td>';
            echo '<td>' . $student['phone'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<td colspan = "3">沒有學生預約此課程</td>';
    }

    echo '</table>';
    echo '</div>';

    $stmt->close();
} else {
    // 如果未提供有效的課程ID
    echo "無效的課程ID";
    exit;
}
?>
