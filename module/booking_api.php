<?php
require_once 'config.php';

session_start();

// Set header for JSON response
header('Content-Type: application/json');

// Handle different API actions
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch($action) {
    case 'get':
        // Get all courses for calendar
        $query = "SELECT c.*, u.username as teacher_name, 
                (SELECT COUNT(*) FROM bookings WHERE course_id = c.id) as booked_count 
                FROM courses c 
                LEFT JOIN users u ON c.teacher_id = u.id";
                
        $result = $conn->query($query);
        $events = array();
        
        while ($row = $result->fetch_assoc()) {
            $events[] = array(
                'id' => $row['id'],
                'title' => $row['name'] . ' - ' . $row['teacher_name'],
                'start' => $row['start_time'],
                'end' => $row['end_time'],
                'classroom' => $row['classroom'],
                'teacher' => $row['teacher_name'],
                'capacity' => $row['capacity'],
                'current' => $row['current'],
                'remaining' => $row['capacity'] - $row['current']
            );
        }
        
        echo json_encode($events);
        break;
        
    case 'check':
        // Check course availability
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['course_id'])) {
                return ['error' => true, 'message' => '課程ID不能為空'];
            }
            
            $course_id = $conn->real_escape_string($_POST['course_id']);
            
            $query = "SELECT capacity, current FROM courses WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $course_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            
            if (!$result) {
                return ['available' => false, 'message' => '課程不存在'];
            }
            
           

            echo json_encode([
                'available' => $result['current'] < $result['capacity'],
                'remaining' => $result['capacity'] - $result['current']
            ]);
        }
        break;
        
    case 'book':
        // Process booking
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id'])) {
                echo json_encode( ['success' => false, 'message' => '請先登入']);
                exit;
            }

            if (!isset($_POST['course_id'])) {
                echo json_encode( ['success' => false, 'message' => '課程ID不能為空']);
                exit;
            }
            
            // Start transaction
            $conn->begin_transaction();
            
            try {
                // Check availability again
                $course_id = $conn->real_escape_string($_POST['course_id']);
                $query = "SELECT id, capacity, current, start_time FROM courses WHERE id = ? FOR UPDATE";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $course_id);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                
                if ($result['current'] >= $result['capacity']) {
                    $conn->rollback();
                    echo json_encode( ['success' => false, 'message' => '課程已額滿']);
                    exit;
                }

                $course_start_time = new DateTime($result['start_time']);
                $current_time = new DateTime();

                // 檢查課程是否在未來
                if ($course_start_time <= $current_time) {
                    echo json_encode(['success' => false, 'message' => '無法預約過去的課程']);
                    exit;
                }

                
                // Check if student already booked this course
                $check_query = "SELECT COUNT(*) as count FROM bookings WHERE student_id = ? AND course_id = ?";
                $check_stmt = $conn->prepare($check_query);
                $check_stmt->bind_param("ii", $_SESSION['user_id'], $course_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result()->fetch_assoc();
                
                if ($check_result['count'] > 0) {
                    $conn->rollback();
                    echo json_encode(['success' => false, 'message' => '您已預約過此課程']);
                    exit;
                }
                
                // Insert booking
                $booking_query = "INSERT INTO bookings (student_id, course_id) VALUES (?, ?)";
                $booking_stmt = $conn->prepare($booking_query);
                $booking_stmt->bind_param("ii", $_SESSION['user_id'], $course_id);
                
                if (!$booking_stmt->execute()) {
                    $conn->rollback();
                    echo json_encode(['success' => false, 'message' => '預約失敗：' . $conn->error]);
                    exit;
                }
                
                // Update current count
                $update_query = "UPDATE courses SET current = current + 1 WHERE id = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("i", $course_id);
                
                if (!$update_stmt->execute()) {
                    $conn->rollback();
                    echo json_encode(['success' => false, 'message' => '更新課程人數失敗']);
                    exit;
                }
                
                $conn->commit();
                echo json_encode(['success' => true, 'message' => '預約成功']);
                exit;
            } catch (Exception $e) {
                $conn->rollback();
                echo json_encode(['success' => false, 'message' => '預約失敗：' . $e->getMessage()]);
            };
        }
        break;
        
    case 'get_teachers':
        // Get all teachers
        $teachers = array();
        $result = $conn->query("SELECT id, username FROM users WHERE role='teacher'");
        while ($row = $result->fetch_assoc()) {
            $teachers[] = $row;
        }
        echo json_encode($teachers);
        break;
        
    case 'get_classrooms':
        // Get all classrooms
        $classrooms = array();
        $result = $conn->query("SELECT DISTINCT classroom FROM courses");
        while ($row = $result->fetch_assoc()) {
            $classrooms[] = $row;
        }
        echo json_encode($classrooms);
        break;

    case 'get_available_courses':
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        // 查詢課程並檢查用戶是否已預約
        $query = "
            SELECT 
                c.id, 
                c.name, 
                c.teacher_name,  -- 使用 c.teacher_name 來代替 u.name
                c.classroom, 
                c.start_time, 
                c.end_time, 
                c.capacity, 
                c.current, 
                COUNT(b.student_id) AS enrolled_students,
                -- 檢查用戶是否已預約
                IFNULL((SELECT 1 FROM bookings WHERE student_id = ? AND course_id = c.id LIMIT 1), 0) AS is_booked
            FROM 
                courses c
            LEFT JOIN 
                bookings b ON c.id = b.course_id 
            GROUP BY 
                c.id
        ";
                            
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id); // 綁定用戶ID
        $stmt->execute();
        $result = $stmt->get_result();
        $courses = array();
    
        if (!$result) {
            echo json_encode(['error' => true, 'message' => 'SQL Error: ' . $conn->error]);
            exit;
        }
    
        while ($row = $result->fetch_assoc()) {
            $courses[] = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'teacher_name' => $row['teacher_name'],
                'classroom' => $row['classroom'],
                'start_time' => $row['start_time'],
                'end_time' => $row['end_time'],
                'capacity' => $row['capacity'],
                'current' => $row['current'],
                'remaining' => $row['capacity'] - $row['current'],
                'is_booked' => (bool) $row['is_booked'] // 如果已預約，is_booked 為 1，否則為 0
            );
        }
    
        echo json_encode($courses);
        break;
        
    case 'cancel':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => '請先登入']);
            exit;
        }

        if (!isset($_POST['course_id'])) {
            echo json_encode(['success' => false, 'message' => '課程ID不能為空']);
            exit;
        }

        $course_id = $conn->real_escape_string($_POST['course_id']);
        $student_id = $_SESSION['user_id'];

        // 取得課程的開始時間
        $course_query = "SELECT start_time FROM courses WHERE id = ?";
        $course_stmt = $conn->prepare($course_query);
        $course_stmt->bind_param("i", $course_id);
        $course_stmt->execute();
        $course_result = $course_stmt->get_result()->fetch_assoc();

        if (!$course_result) {
            echo json_encode(['success' => false, 'message' => '課程不存在']);
            exit;
        }

        $course_start_time = new DateTime($course_result['start_time']);
        $current_time = new DateTime();

        // 檢查課程是否已經開始，不能取消當日或過期的課程
        $interval = $current_time->diff($course_start_time);
        if ($interval->h < 24 && $interval->days == 0) {
            echo json_encode(['success' => false, 'message' => '至少需要提前一天取消課程']);
            exit;
        }


        // 開始交易
        $conn->begin_transaction();

        try {
            // 檢查該用戶是否已預約此課程
            $check_query = "SELECT COUNT(*) as count FROM bookings WHERE student_id = ? AND course_id = ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param("ii", $student_id, $course_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result()->fetch_assoc();

            if ($check_result['count'] == 0) {
                $conn->rollback();
                echo json_encode(['success' => false, 'message' => '您尚未預約此課程']);
                exit;
            }

            // 刪除該預約
            $cancel_query = "DELETE FROM bookings WHERE student_id = ? AND course_id = ?";
            $cancel_stmt = $conn->prepare($cancel_query);
            $cancel_stmt->bind_param("ii", $student_id, $course_id);
            if (!$cancel_stmt->execute()) {
                $conn->rollback();
                echo json_encode(['success' => false, 'message' => '取消預約失敗：' . $conn->error]);
                exit;
            }

            // 更新課程已預約人數
            $update_query = "UPDATE courses SET current = current - 1 WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("i", $course_id);
            if (!$update_stmt->execute()) {
                $conn->rollback();
                echo json_encode(['success' => false, 'message' => '更新課程人數失敗']);
                exit;
            }

            $conn->commit();
            echo json_encode(['success' => true, 'message' => '取消預約成功']);
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => '取消預約失敗：' . $e->getMessage()]);
        }
        
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}

?>