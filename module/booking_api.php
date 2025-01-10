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
        echo json_encode(getCourses($conn));
        break;
        
    case 'check':
        // Check course availability
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo json_encode(checkCourseAvailability($conn, $_POST));
        }
        break;
        
    case 'book':
        // Process booking
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo json_encode(processBooking($conn, $_POST));
        }
        break;
        
    case 'get_teachers':
        // Get all teachers
        echo json_encode(getTeachers($conn));
        break;
        
    case 'get_classrooms':
        // Get all classrooms
        echo json_encode(getClassrooms($conn));
        break;

    case 'get_available_courses':
        // Get available courses
        echo json_encode(getAvailableCourses($conn));
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}

function getClassrooms($conn) {
    $classrooms = array();
    $result = $conn->query("SELECT DISTINCT classroom FROM courses");
    while ($row = $result->fetch_assoc()) {
        $classrooms[] = $row;
    }
    return $classrooms;
}

function getTeachers($conn) {
    $teachers = array();
    $result = $conn->query("SELECT id, username FROM users WHERE role='teacher'");
    while ($row = $result->fetch_assoc()) {
        $teachers[] = $row;
    }
    return $teachers;
}

function checkCourseAvailability($conn, $data) {
    if (!isset($data['course_id'])) {
        return ['error' => true, 'message' => '課程ID不能為空'];
    }
    
    $course_id = $conn->real_escape_string($data['course_id']);
    
    $query = "SELECT capacity, current FROM courses WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if (!$result) {
        return ['available' => false, 'message' => '課程不存在'];
    }
    
    return [
        'available' => $result['current'] < $result['capacity'],
        'remaining' => $result['capacity'] - $result['current']
    ];
}

function processBooking($conn, $data) {
    if (!isset($_SESSION['user_id'])) {
        return ['success' => false, 'message' => '請先登入'];
    }
    
    if (!isset($data['course_id'])) {
        return ['success' => false, 'message' => '課程ID不能為空'];
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Check availability again
        $course_id = $conn->real_escape_string($data['course_id']);
        $query = "SELECT capacity, current FROM courses WHERE id = ? FOR UPDATE";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result['current'] >= $result['capacity']) {
            $conn->rollback();
            return ['success' => false, 'message' => '課程已額滿'];
        }
        
        // Check if student already booked this course
        $check_query = "SELECT COUNT(*) as count FROM bookings WHERE student_id = ? AND course_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ii", $_SESSION['user_id'], $course_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result()->fetch_assoc();
        
        if ($check_result['count'] > 0) {
            $conn->rollback();
            return ['success' => false, 'message' => '您已預約過此課程'];
        }
        
        // Insert booking
        $booking_query = "INSERT INTO bookings (student_id, course_id) VALUES (?, ?)";
        $booking_stmt = $conn->prepare($booking_query);
        $booking_stmt->bind_param("ii", $_SESSION['user_id'], $course_id);
        
        if (!$booking_stmt->execute()) {
            $conn->rollback();
            return ['success' => false, 'message' => '預約失敗：' . $conn->error];
        }
        
        // Update current count
        $update_query = "UPDATE courses SET current = current + 1 WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $course_id);
        
        if (!$update_stmt->execute()) {
            $conn->rollback();
            return ['success' => false, 'message' => '更新課程人數失敗'];
        }
        
        $conn->commit();
        return ['success' => true, 'message' => '預約成功'];
        
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => '預約失敗：' . $e->getMessage()];
    }
}

function getCourses($conn) {
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
    
    return $events;
}

function getAvailableCourses($conn) {
    $query = "SELECT c.*, u.username as teacher_name 
              FROM courses c 
              LEFT JOIN users u ON c.teacher_id = u.id 
              WHERE c.current < c.capacity 
              AND c.start_time > NOW() 
              ORDER BY c.start_time";
              
    $result = $conn->query($query);
    $courses = array();
    
    while ($row = $result->fetch_assoc()) {
        $courses[] = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'teacher' => $row['teacher_name'],
            'classroom' => $row['classroom'],
            'start_time' => $row['start_time'],
            'end_time' => $row['end_time'],
            'capacity' => $row['capacity'],
            'current' => $row['current'],
            'remaining' => $row['capacity'] - $row['current']
        );
    }
    
    return $courses;
}
?>