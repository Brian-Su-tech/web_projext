<?php
require_once 'config.php';

// 檢查請求類型
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'save':
            // 檢查必要欄位
            $required_fields = ['name', 'category', 'price', 'stock', 'description'];
            foreach ($required_fields as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    throw new Exception("請填寫所有必要欄位");
                }
            }
            
            $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
            $name = $_POST['name'];
            $category = $_POST['category'];
            $price = (float)$_POST['price'];
            $stock = (int)$_POST['stock'];
            $description = $_POST['description'];
            
            // 處理圖片上傳
            $image_url = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/instruments/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (!in_array($file_extension, $allowed_extensions)) {
                    throw new Exception("只允許上傳 JPG、JPEG、PNG 或 GIF 圖片");
                }
                
                $new_filename = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_url = 'uploads/instruments/' . $new_filename;
                } else {
                    throw new Exception("圖片上傳失敗");
                }
            }
            
            // 新增或更新資料庫
            if ($id) {
                // 更新
                $sql = "UPDATE instruments SET name = ?, category = ?, price = ?, stock = ?, description = ?";
                $params = [$name, $category, $price, $stock, $description];
                $types = "ssdis";
                
                if ($image_url) {
                    $sql .= ", image_url = ?";
                    $params[] = $image_url;
                    $types .= "s";
                }
                
                $sql .= " WHERE id = ?";
                $params[] = $id;
                $types .= "i";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param($types, ...$params);
            } else {
                // 新增
                $sql = "INSERT INTO instruments (name, category, price, stock, description, image_url) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdiss", $name, $category, $price, $stock, $description, $image_url);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("資料庫操作失敗");
            }
            
            echo json_encode(['success' => true]);
            break;

        case 'get':
            if (!isset($_GET['id'])) {
                throw new Exception("缺少樂器 ID");
            }
            
            $id = (int)$_GET['id'];
            $stmt = $conn->prepare("SELECT * FROM instruments WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                echo json_encode($row);
            } else {
                throw new Exception("找不到該樂器");
            }
            break;

        case 'delete':
            if (!isset($_POST['id'])) {
                throw new Exception("缺少樂器 ID");
            }
            
            $id = (int)$_POST['id'];
            
            // 先獲取圖片路徑
            $stmt = $conn->prepare("SELECT image_url FROM instruments WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $instrument = $result->fetch_assoc();
            
            // 刪除資料庫記錄
            $stmt = $conn->prepare("DELETE FROM instruments WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                // 如果有圖片，刪除圖片檔案
                if ($instrument && $instrument['image_url']) {
                    $image_path = '../' . $instrument['image_url'];
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
                echo json_encode(['success' => true]);
            } else {
                throw new Exception("刪除失敗");
            }
            break;

        default:
            throw new Exception("無效的操作");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>