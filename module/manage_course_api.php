<?php
require_once 'config.php';

// 確保資料庫連線成功
if (!$conn) {
    echo json_encode(['success' => false, 'message' => '資料庫連線失敗']);
    exit;
}

// 檢查請求類型
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'getCourses':
            $sql = "SELECT * FROM courses ORDER BY start_time";
            $result = $conn->query($sql);

            $courses = [];
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $courses[] = [
                        'id' => (int)$row['id'],
                        'name' => $row['name'],
                        'teacher_name' => $row['teacher_name'],
                        'classroom' => $row['classroom'],
                        'start_time' => $row['start_time'],
                        'end_time' => $row['end_time'],
                        'capacity' => (int)$row['capacity'],
                        'current' => (int)$row['current']
                    ];
                }
            }

            echo json_encode(['success' => true, 'data' => $courses]);
            break;

        case 'addCourse':
            // 驗證必要欄位
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['name'], $data['teacher_name'],$data['classroom'], $data['start_time'], $data['end_time'], $data['capacity'])) {
                echo json_encode(['success' => false, 'message' => '缺少必要欄位']);
                break;
            }

            $sql = "INSERT INTO courses (name, teacher_name, classroom, start_time, end_time, capacity, current) VALUES (?, ?, ?, ?, ?, 0)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", 
                $data['name'], 
                $data['teacher_name'], 
                $data['classroom'], 
                $data['start_time'], 
                $data['end_time'], 
                $data['capacity']
            );

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => '課程新增成功', 'id' => $stmt->insert_id]);
            } else {
                echo json_encode(['success' => false, 'message' => '課程新增失敗: ' . $stmt->error]);
            }

            $stmt->close();

            break;

        case 'updateCourse':
            // 驗證必要欄位
            $data = json_decode(file_get_contents('php://input'), true);

            // 驗證必要欄位
            if (!isset($data['name']) || !isset($data['classroom']) || !isset($data['start_time']) || 
                !isset($data['end_time']) || !isset($data['capacity'])) {
                echo json_encode([
                    'success' => false,
                    'message' => '缺少必要欄位'
                ]);
                return;
            }
            
            $sql = "UPDATE courses SET 
                    name = ?,
                    teacher_name = ?,
                    classroom = ?,
                    start_time = ?,
                    end_time = ?,
                    capacity = ?
                    WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssii",
                $data['name'],
                $data['teacher_name'],
                $data['classroom'],
                $data['start_time'],
                $data['end_time'],
                $data['capacity'],
                $data['id']
            );
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => '課程更新成功'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => '課程更新失敗: ' . $stmt->error
                ]);
            }
            
            $stmt->close();
            break;

        case 'deleteCourse':
            $data = json_decode(file_get_contents('php://input'), true);

            // 驗證必要欄位
            if (!isset($data['id'])) {
                echo json_encode([
                    'success' => false,
                    'message' => '缺少課程ID'
                ]);
                return;
            }
            
            // 檢查是否有學生預約該課程
            $checkSql = "SELECT COUNT(*) as count FROM bookings WHERE course_id = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("i", $data['id']);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row['count'] > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => '無法刪除：該課程已有學生預約'
                ]);
                return;
            }
            
            // 執行刪除
            $sql = "DELETE FROM courses WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $data['id']);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => '課程刪除成功'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => '課程刪除失敗: ' . $stmt->error
                ]);
            }
            
            $stmt->close();
            $checkStmt->close();
            break;

        default:
            throw new Exception("無效的操作");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '伺服器錯誤: ' . $e->getMessage()]);
} finally {
    $conn->close();
}


?>