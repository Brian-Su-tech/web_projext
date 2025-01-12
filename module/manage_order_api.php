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
        case 'getOrders':
            $sql = "SELECT * FROM orders ORDER BY created_at";
            $result = $conn->query($sql);

            $courses = [];
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $courses[] = [
                        'id' => (int)$row['id'],
                        'user_name' => $row['user_name'],
                        'instrument_name' => $row['instrument_name'],
                        'quantity' => $row['quantity'],
                        'price' => $row['price'],
                        'total_amount' => $row['total_amount'],
                        'created_at' => $row['created_at']
                    ];
                }
            }

            echo json_encode(['success' => true, 'data' => $courses, '$result->num_rows' => $result->num_rows]);
            break;

        case 'delete':
            // $data = json_decode(file_get_contents('php://input'), true);

            // 驗證必要欄位
            if (!isset($_POST['id'])) {
                echo json_encode([
                    'success' => false,
                    'message' => '缺少訂單ID'
                ]);
                return;
            }
           
            // 執行刪除
            $sql = "DELETE FROM orders WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_POST['id']);
            
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