<?php
session_start();
$user_logged_in = isset($_SESSION['user_id']);

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>樂器管理 - 和樂音樂教室</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles/main.css">
    <style>
        .instrumentform {
            max-width: 600px;
            margin: 20px auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        #add-instrument{
            position: relative;
        }

        .modal-body form input,
        .modal-body form select,
        .modal-body form textarea {
            width: 100%;
        }
        .table-container {
            margin-top: 30px;
        }

        .d-flex .form-control,
        .d-flex .form-select {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <!-- 導覽列 -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">和樂音樂教室</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">首頁</a>
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
        <h2 class="text-center mb-4">樂器管理</h2>

        <!-- 搜尋樂器名稱與分類篩選 -->
        <div class="d-flex mb-4">
            <div class="d-flex justify-content-start">
                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#instrumentModal" id="add-instrument">新增樂器</button>
            </div>
            <div class="col-md-2 d-flex">
                <select id="searchCategory" class="form-select">
                    <option value="">選擇分類</option>
                    <option value="piano">鋼琴</option>
                    <option value="guitar">吉他</option>
                    <option value="violin">小提琴</option>
                    <option value="drum">鼓</option>
                </select>   
            </div>
            <div class="col-md-4 d-flex">
                <input type="text" id="searchName" class="form-control" placeholder="搜尋樂器名稱">
            </div>  
        </div>
        

        <!-- Modal -->
        <div class="modal fade" id="instrumentModal" tabindex="-1" aria-labelledby="instrumentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="instrumentModalLabel">編輯樂器</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="instrumentForm" enctype="multipart/form-data">
                            <input type="hidden" name="id" id="instrumentId">
                            <div class="form-group mb-3">
                                <label>名稱：<input type="text" name="name" required></label>
                            </div>
                            <div class="form-group mb-3">
                                <label>類別：<select name="category" required>
                                    <option value="piano">鋼琴</option>
                                    <option value="guitar">吉他</option>
                                    <option value="violin">小提琴</option>
                                    <option value="drum">鼓</option>
                                </select></label>
                            </div>
                            <div class="form-group mb-3">
                                <label>價格：<input type="number" name="price" required></label>
                            </div>
                            <div class="form-group mb-3">
                                <label>庫存：<input type="number" name="stock" required></label>
                            </div>
                            <div class="form-group mb-3">
                                <label>描述：<textarea name="description" rows="4" required></textarea></label>
                            </div>
                            <div class="form-group mb-3">
                                <label>圖片：<input type="file" name="image"></label>
                            </div>
                            <button type="submit" class="btn btn-primary">儲存</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="instruments-list">
            <table>
                <thead>
                    <tr>
                        <th>名稱</th>
                        <th>類別</th>
                        <th>價格</th>
                        <th>庫存</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody  id="instrumentTableBody">
                    <?php
                    require_once '../module/config.php';
                    $result = $conn->query("SELECT * FROM instruments");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$row['name']."</td>";
                        echo "<td>".$row['category']."</td>";
                        echo "<td>NT$ ".number_format($row['price'])."</td>";
                        echo "<td>".$row['stock']."</td>";
                        echo "<td>
                            <button onclick='editInstrument(".$row['id'].")'>編輯</button>
                            <button onclick='deleteInstrument(".$row['id'].")'>刪除</button>
                        </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        // 搜尋與分類篩選
        $('#searchName, #searchCategory').on('input change', function() {
            let name = $('#searchName').val().toLowerCase();
            let category = $('#searchCategory').val();
            $('#instrumentTableBody tr').each(function() {
                let nameMatch = $(this).find('td:nth-child(1)').text().toLowerCase().includes(name);
                let categoryMatch = category ? $(this).find('td:nth-child(2)').text() === category : true;
                $(this).toggle(nameMatch && categoryMatch);
            });
        });


        $('#instrumentForm').submit(function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            $.ajax({
                url: '../module/manage_instruments_api.php?action=save',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        alert('儲存成功！');
                        location.reload();
                    } else {
                        alert('儲存失敗：' + data.message);
                    }
                }
            });
        });

        function editInstrument(id) {
            $.ajax({
                url: '../module/manage_instruments_api.php?action=get&id=' + id,
                type: 'GET',
                data: { id: id },
                success: function(response) {
                    const data = JSON.parse(response);
                    const form = $('#instrumentForm')[0];
                    form.id.value = data.id;
                    form.name.value = data.name;
                    form.category.value = data.category;
                    form.price.value = data.price;
                    form.stock.value = data.stock;
                    form.description.value = data.description;
                    $('#instrumentModal').modal('show'); // 顯示Modal
                }
            });
        }

        function deleteInstrument(id) {
            if (confirm('確定要刪除這項樂器嗎？')) {
                $.ajax({
                    url: '../module/manage_instruments_api.php?action=delete',
                    type: 'POST',
                    data: { id: id },
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.success) {
                            alert('刪除成功！');
                            location.reload();
                        } else {
                            alert('刪除失敗：' + data.message);
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>