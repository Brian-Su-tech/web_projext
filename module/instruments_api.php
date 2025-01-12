<?php
require_once 'config.php';

// 啟動 session
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

function checkLogin() {
    // 檢查用戶是否登錄
    if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
        return $_SESSION['user_name']; // 返回用戶 ID
    }

    // 未登錄的情況
    header('Location: ../login.php');
    exit();
    return false;
}

// 初始化購物車
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            if (!isset($_POST['id'])) {
                throw new Exception("缺少商品 ID");
            }
            
            $instrument_id = (int)$_POST['id'];
            
            // 檢查商品是否存在且有庫存
            $stmt = $conn->prepare("SELECT stock FROM instruments WHERE id = ?");
            $stmt->bind_param("i", $instrument_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $instrument = $result->fetch_assoc();
            
            if (!$instrument) {
                throw new Exception("商品不存在");
            }
            
            // 檢查購物車中的數量
            $cart_quantity = isset($_SESSION['cart'][$instrument_id]) ? $_SESSION['cart'][$instrument_id] : 0;
            
            if ($cart_quantity >= $instrument['stock']) {
                throw new Exception("庫存不足");
            }
            
            // 更新購物車
            if (isset($_SESSION['cart'][$instrument_id])) {
                $_SESSION['cart'][$instrument_id]++;
            } else {
                $_SESSION['cart'][$instrument_id] = 1;
            }
            
            echo json_encode(['success' => true]);
            break;

        case 'get':
            $items = [];
            $total = 0;
            
            if (!empty($_SESSION['cart'])) {
                $ids = array_keys($_SESSION['cart']);
                $ids_str = implode(',', array_fill(0, count($ids), '?'));
                
                $sql = "SELECT id, name, price FROM instruments WHERE id IN ($ids_str)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($row = $result->fetch_assoc()) {
                    $quantity = $_SESSION['cart'][$row['id']];
                    $items[] = [
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'price' => $row['price'],
                        'quantity' => $quantity
                    ];
                    $total += $row['price'] * $quantity;
                }
            }
            
            $isLoggedIn = isset($_SESSION['user_id']);
            
            echo json_encode([
                'items' => $items,
                'total' => $total,
                'isLoggedIn' => $isLoggedIn
            ]);
            break;

        case 'checkout':
            // 檢查用戶是否登入
            $user_name = checkLogin();
            
            if (empty($_SESSION['cart'])) {
                throw new Exception("購物車是空的");
            }
            
            $conn->begin_transaction();
            
            try {
                // 計算總金額
                $total_amount = 0;
                $order_items = [];
                
                // 檢查所有商品庫存並計算總金額
                foreach ($_SESSION['cart'] as $id => $quantity) {
                    $stmt = $conn->prepare("SELECT id, name, price, stock FROM instruments WHERE id = ? FOR UPDATE");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $instrument = $result->fetch_assoc();
                    
                    if ($quantity > $instrument['stock']) {
                        throw new Exception("商品庫存不足");
                    }
                    
                    // 計算金額
                    $total_amount += $instrument['price'] * $quantity;
                    $order_items[] = [
                        'instrument_id' => $id,
                        'name' => $instrument['name'],
                        'quantity' => $quantity,
                        'price' => $instrument['price']
                    ];
                    
                }
                
                // 插入訂單
                foreach ($order_items as $item) {
                    $stmt = $conn->prepare("INSERT INTO orders (user_name, instrument_name, quantity, price, total_amount) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssidd", 
                        $user_name, 
                        $item['name'], 
                        $quantity, 
                        $item['price'], 
                        $total_amount
                    );
                    $stmt->execute();

                    // 獲取剛插入的訂單 ID
                    $order_id = $conn->insert_id;  // 這會返回插入的訂單的 ID

                    // 更新庫存
                    $stmt = $conn->prepare("UPDATE instruments SET stock = stock - ? WHERE id = ?");
                    $stmt->bind_param("ii", $quantity, $id);
                    $stmt->execute();
                }

                $conn->commit();        
                $_SESSION['cart'] = [];
                echo json_encode(['success' => true, 'order_id' => $order_id]);
                

            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }
            break;

        case 'update':
            if (!isset($_POST['id']) || !isset($_POST['quantity'])) {
                throw new Exception("缺少必要參數");
            }
            
            $instrument_id = (int)$_POST['id'];
            $quantity = (int)$_POST['quantity'];
            
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$instrument_id]);
            } else {
                // 檢查庫存
                $stmt = $conn->prepare("SELECT stock FROM instruments WHERE id = ?");
                $stmt->bind_param("i", $instrument_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $instrument = $result->fetch_assoc();
                
                if ($quantity > $instrument['stock']) {
                    throw new Exception("庫存不足");
                }
                
                $_SESSION['cart'][$instrument_id] = $quantity;
            }
            
            echo json_encode(['success' => true]);
            break;

        case 'get_count':
            try {
                $count = 0;
                if (isset($_SESSION['cart'])) {
                    $count = array_sum($_SESSION['cart']); // 計算購物車商品總數
                }
                echo json_encode(['success' => true, 'count' => $count]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;

        case 'remove':
            if (!isset($_POST['id'])) {
                throw new Exception("缺少商品 ID");
            }
            
            $instrument_id = (int)$_POST['id'];
            unset($_SESSION['cart'][$instrument_id]);
            
            echo json_encode(['success' => true]);
            break;
        case 'get_instruments':
            $sql = "SELECT * FROM instruments WHERE stock > 0"; // Fetch only in-stock items
            $result = $conn->query($sql);

            $instruments = [];
            while ($row = $result->fetch_assoc()) {
                $instruments[] = $row;
            }

            echo json_encode($instruments);
            break;

        default:
            throw new Exception("無效的操作");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>