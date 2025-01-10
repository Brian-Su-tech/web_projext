<?php
session_start();
$user_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>樂器購買 - 和樂音樂教室</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/instruments.css">
    <style>
        /* 樂器卡片網格 */
        .instruments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        /* 單個卡片樣式 */
        .instrument-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            padding: 15px;
        }
        .instrument-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        /* 樂器圖片 */
        .instrument-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-bottom: 1px solid #ddd;
        }

        /* 卡片內文字 */
        .instrument-card h3 {
            font-size: 20px;
            color: #333;
            margin: 15px 0 5px;
        }

        .instrument-description {
            font-size: 14px;
            color: #555;
            margin: 0 10px 10px;
            height: 100px;
            overflow: auto;
            text-overflow: ellipsis;
        }

        .instrument-price {
            font-size: 18px;
            font-weight: bold;
            color: #A0522D;
            margin: 5px 0;
        }

        .instrument-stock {
            font-size: 14px;
            color: #888;
        }

        /* 按鈕樣式 */
        .add-to-cart {
            display: inline-block;
            margin: 10px auto 15px;
            padding: 10px 15px;
            background-color: #CD853F;
            color: white;
            border: none;
            border-radius: 20px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .add-to-cart:hover {
            background-color: #A0522D;
        }

        /* 購物車圖示 */
        .cart-icon {
            position: fixed;
            top: 100px;
            right: 30px;
            background: #007bff;
            color: white;
            padding: 15px;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* 遮罩與購物車彈窗 */
        .modal-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .cart-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            width: 90%;
            max-width: 700px; /* 最大寬度設置 */
            max-height: 80vh; /* 高度限制，避免超出視窗 */
            overflow-y: auto; /* 超出時出現滾動條 */
        }

        table {
            width: 100%;
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
                        <a class="nav-link" href="index.html">首頁</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="teachers.html">師資介紹</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="instruments.php">樂器購買</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="booking.php">預約課程</a>
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
        <h2 class="text-center mb-4">樂器購買</h2>
        <div class="filter-section">
            <select id="categoryFilter">
                <option value="">所有類別</option>
                <option value="piano">鋼琴</option>
                <option value="guitar">吉他</option>
                <option value="violin">小提琴</option>
                <option value="drum">鼓</option>
            </select>
            <input type="text" id="searchInput" placeholder="搜尋樂器...">
        </div>

        <div class="instruments-grid">
            <?php
            require_once 'module/config.php';
            $sql = "SELECT * FROM instruments WHERE stock > 0";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                echo '<div class="instrument-card" data-category="'.$row['category'].'">';
                echo '<img src="'.$row['image_url'].'" alt="'.$row['name'].'" class="instrument-img">';
                echo '<h3>'.$row['name'].'</h3>';
                echo '<p class="instrument-description">'.$row['description'].'</p>';
                echo '<p class="instrument-price">NT$ '.number_format($row['price']).'</p>';
                echo '<p class="instrument-stock">庫存: '.$row['stock'].'</p>';
                echo '<button onclick="addToCart('.$row['id'].')" class="add-to-cart">加入購物車</button>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <div class="cart-icon" onclick="showCart()">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-count">0</span>
    </div>

    
    <div class="modal-backdrop"></div>
    <div class="cart-modal">
        <h2>購物車</h2>
        <div id="cartItems" class="mb-2"></div>
        <div id="cartTotal" class="mb-3"></div>
        <?php if (!$user_logged_in): ?>
            <div class="alert alert-warning" role="alert">
                請先登入才能進行結帳！
            </div>
        <?php endif; ?>
        
        <button id="checkoutBtn" onclick="checkout()" <?php echo $user_logged_in ? '' : 'disabled'; ?>>結帳</button>
        <button onclick="hideCart()">關閉</button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
    let cart = {};

    function addToCart(instrumentId) {
        $.ajax({
            url: 'module/cart_api.php?action=add',
            type: 'POST',
            data: { id: instrumentId },
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    if (data.success) {
                        alert('已加入購物車！');
                        updateCartCount();
                    } else {
                        alert('加入購物車失敗：' + data.message);
                    }
                } catch (e) {
                    console.error('JSON 解析失敗', response);
                    alert('伺服器返回了無效的數據，請稍後再試！');
                }
            }
        });
    }

    function updateCartCount() {
    $.ajax({
        url: 'module/cart_api.php?action=get_count',
        type: 'GET',
        success: function(response) {
            try {
                const data = JSON.parse(response);
                if (data.success) {
                    // 更新購物車紅點數字
                    $('.cart-count').text(data.count);
                } else {
                    console.error('獲取購物車數量失敗：' + data.message);
                }
            } catch (e) {
                console.error('JSON 解析失敗', response);
            }
        }
    });
}


    // 顯示購物車
    function showCart() {
        $.ajax({
            url: 'module/cart_api.php?action=get',
            type: 'GET',
            success: function(response) {
                const data = JSON.parse(response);
                let html = '<table>';
                let total = 0;
                data.items.forEach(item => {
                    html += `<tr>
                        <td>${item.name}</td>
                        <td style="width: 10%; text-align: center;">${item.quantity}</td>
                        <td style="width: 20%; text-align: center;">NT$ ${item.price}</td>
                        <td style="width: 30%; text-align: center;">
                            <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                            <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                            <button onclick="removeFromCart(${item.id})">刪除</button>
                        </td>
                    </tr>`;
                    total += item.price * item.quantity;
                });
                html += '</table>';
                $('#cartItems').html(html);
                $('#cartTotal').text(`總計: NT$ ${total}`);

                $('.modal-backdrop, .cart-modal').show();
            }
        });
    }

    function hideCart() {
        $('.modal-backdrop, .cart-modal').hide();
    }

    // 更新商品數量
    function updateQuantity(id, quantity) {
        $.ajax({
            url: 'module/cart_api.php?action=update',
            type: 'POST',
            data: { id: id, quantity: quantity },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    showCart(); // 重新載入購物車
                    updateCartCount();
                } else {
                    alert('更新失敗：' + data.message);
                }
            }
        });
    }

    // 移除商品
    function removeFromCart(id) {
        $.ajax({
            url: 'module/cart_api.php?action=remove',
            type: 'POST',
            data: { id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    showCart(); // 重新載入購物車
                    updateCartCount();
                } else {
                    alert('移除失敗：' + data.message);
                }
            }
        });
    }

    // 結帳
    function checkout() {
         // 若未登入，跳轉至登入頁面
        <?php if (!$user_logged_in): ?>
            window.location.href = 'login.php';
        <?php else: ?>
            // 呼叫結帳的AJAX邏輯
            $.ajax({
                url: 'module/cart_api.php?action=checkout',
                type: 'POST',
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        alert('訂購成功！訂單編號：' + data.order_id);
                        updateCartCount();
                        hideCart();
                    } else {
                        alert('訂購失敗：' + data.message);
                    }
                }
            });
        <?php endif; ?>
    }

    // 篩選功能
    $('#categoryFilter').change(function() {
        const category = $(this).val();
        $('.instrument-card').each(function() {
            if (!category || $(this).data('category') === category) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // 搜尋功能
    $('#searchInput').on('input', function() {
        const searchText = $(this).val().toLowerCase();
        $('.instrument-card').each(function() {
            const name = $(this).find('h3').text().toLowerCase();
            if (name.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    </script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>