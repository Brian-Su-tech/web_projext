<?php
    session_start();
    $user_logged_in = isset($_SESSION['user_id']);
 
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>樂器訂單 - 和樂音樂教室</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="../styles/main.css">
    <style>
        .table-responsive {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">和樂音樂教室</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">首頁</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="teachers.php">師資介紹</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_instruments.php">樂器購買</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="manage_order.php">樂器訂單</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_course.php">課程管理</a>
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

    <div class="container my-5">
        <h2 class="text-center">樂器訂單列表</h2>
        <div class="table-responsive">
            <table class="table" id="ordersTable">
                <thead>
                    <tr>
                        <th>訂單編號</th>
                        <th>客戶名稱</th>
                        <th>樂器名稱</th>
                        <th>數量</th>
                        <th>總價</th>
                        <th>訂單時間</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // 初始化訂單列表
            fetchOrders();
        });

        // 刪除訂單的函式
        function deleteOrder(orderId) {
            if (confirm("確定刪除此訂單嗎？")) {
                $.ajax({
                    url: '../module/manage_order_api.php?action=delete',
                    type: 'POST',
                    data: { id: orderId },
                    success: function(response) {
                        var data = JSON.parse(response);
                        console.log("data.success:",data.success);
                        if (data.success) {
                            alert(data.message);
                            fetchOrders();
                        } else {
                            alert(data.message);
                        }
                    },
                    error: function() {
                        alert('刪除訂單時發生錯誤');
                    }
                });
            }
        }

        // 獲取訂單列表
        function fetchOrders() {
                $.ajax({
                    url: '../module/manage_order_api.php?action=getOrders',
                    type: 'GET',
                    success: function(response) {
                        console.log("API 回應：", response); 

                        let data;
                        try {
                            data = typeof response === "string" ? JSON.parse(response) : response;
                        } catch (error) {
                            console.error("JSON 解析錯誤：", error);
                            alert("獲取訂單數據時發生錯誤");
                            return;
                        }

                        console.log("data.success：", data.success); 
                        if (data.success) {
                            const orders = data.data;
                            const tbody = $('#ordersTable tbody');
                            tbody.empty();
 
                            if (orders.length > 0) {
                                orders.forEach(order => {
                                    console.log("插入訂單數據：", order);
                                    tbody.append(`
                                        <tr>
                                            <td>${order.id}</td>
                                            <td>${order.user_name}</td>
                                            <td>${order.instrument_name}</td>
                                            <td>${order.quantity}</td>
                                            <td>${order.total_amount}</td>
                                            <td>${order.created_at}</td>
                                            <td>
                                                <button class="btn btn-danger btn-sm" onclick="deleteOrder(${order.id})">刪除</button>
                                            </td>
                                        </tr>
                                    `);
                                });
                            } else {
                                tbody.append('<tr><td colspan="7" class="text-center">目前沒有訂單</td></tr>');
                            }
                            console.log("已顯示訂單"); 
                        } else {
                            alert(data.message);
                        }
                    },
                    error: function() {
                        alert('獲取訂單列表時發生錯誤');
                    }
                });
            }
    </script>
</body>
</html>
