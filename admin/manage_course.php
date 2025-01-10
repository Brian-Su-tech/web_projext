<?php
    session_start();
    $user_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>課程管理 - 和樂音樂教室</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles/main.css">
    <style>
        /* 按鈕樣式 */
        .btn {
            padding: 8px 16px;
            align-items: center;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background-color: #45a049;
        }

        .btn-secondary {
            background-color: #f44336;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #da190b;
        }

        /* 表格樣式 */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        /* 操作按鈕樣式 */
        .action-btn {
            padding: 4px 8px;
            margin: 0 4px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .edit-btn {
            background-color: #2196F3;
            color: white;
        }

        .delete-btn {
            background-color: #f44336;
            color: white;
        }

        /* 對話框樣式 */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            width: 90%;
            max-width: 500px;
            border-radius: 4px;
            position: relative;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
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
                        <a class="nav-link active" href="dashboard.php">樂器訂單</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_course.php">預約課程</a>
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

    <div class="container  my-5">
        <h2 class="text-center mb-4">課程管理系統</h2>
        <button id="addCourseBtn" class="btn btn-primary mb-2">新增課程</button>
        <div class="table-responsive">
            <table id="courseTable">
                <thead>
                    <tr>
                        <th>課程名稱</th>
                        <th>教室</th>
                        <th>老師</th>
                        <th>開始時間</th>
                        <th>結束時間</th>
                        <th>容納人數</th>
                        <th>目前人數</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody id="courseList">
                    <!-- 課程資料將由 JavaScript 動態插入 -->
                </tbody>
            </table>
        </div>

        <!-- 新增/編輯課程的對話框 -->
        <div id="courseModal" class="modal">
            <div class="modal-content">
                <h2 id="modalTitle">新增課程</h2>
                <form id="courseForm">
                    <input type="hidden" id="courseId">
                    <div class="form-group">
                        <label for="name">課程名稱</label>
                        <input type="text" id="name" required>
                    </div>
                    <div class="form-group">
                        <label for="classroom">教室</label>
                        <input type="text" id="classroom" required>
                    </div>
                    <div class="form-group">
                        <label for="teacher_name">老師</label>
                        <input type="text" id="teacher_name" required>
                    </div>
                    <div class="form-group">
                        <label for="startTime">開始時間</label>
                        <input type="datetime-local" id="startTime" required>
                    </div>
                    <div class="form-group">
                        <label for="endTime">結束時間</label>
                        <input type="datetime-local" id="endTime" required>
                    </div>
                    <div class="form-group">
                        <label for="capacity">容納人數</label>
                        <input type="number" id="capacity" required min="1">
                    </div>
                    <div class="form-buttons">
                        <button type="submit" class="btn btn-primary">儲存</button>
                        <button type="button" class="btn btn-secondary" id="cancelBtn">取消</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // 等待 DOM 完全載入後再執行所有操作
        document.addEventListener('DOMContentLoaded', function() {
            // DOM 元素
            const courseTable = document.getElementById('courseTable');
            const courseList = document.getElementById('courseList');
            const addCourseBtn = document.getElementById('addCourseBtn');
            const courseModal = document.getElementById('courseModal');
            const courseForm = document.getElementById('courseForm');
            const modalTitle = document.getElementById('modalTitle');
            const cancelBtn = document.getElementById('cancelBtn');

            // 課程相關操作
            const courseOperations = {
                // 獲取所有課程
                getCourses: async (courseData) => { 
                    try { 
                        const response = await fetch('../module/manage_course_api.php?action=getCourses', {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                            }
                        }); 
                        const text = await response.text(); 
                        try { 
                            const data = JSON.parse(text); 
                            return data; 
                        } catch (jsonError) { 
                            alert('JSON parse error:', jsonError); 
                            alert('Response text:', text); 
                            return []; 
                        } 
                    } catch (error) { 
                        console.error('Error:', error); 
                        return []; 
                    } 
                },

                // 新增課程
                addCourse: async (courseData) => {
                    try {
                        const response = await fetch('../module/manage_course_api.php?action=addCourse', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(courseData)
                        });
                        return await response.json();
                    } catch (error) {
                        alert('Error:', error);
                        return { success: false, message: '新增失敗' };
                    }
                },

                // 更新課程
                updateCourse: async (courseData) => {
                    try {
                        const response = await fetch('../module/manage_course_api.php?action=updateCourse', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(courseData)
                        });
                        return await response.json();
                    } catch (error) {
                        console.error('Error:', error);
                        return { success: false, message: '更新失敗' };
                    }
                },

                // 刪除課程
                deleteCourse: async (courseId) => {
                    try {
                        const response = await fetch('../module/manage_course_api.php?action=deleteCourse', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ id: courseId })
                        });
                        return await response.json();
                    } catch (error) {
                        alert('Error:', error);
                        return { success: false, message: '刪除失敗' };
                    }
                }
            };

            // UI 相關操作
            const uiOperations = {
                // 渲染課程列表
                renderCourses: (courses) => {
                  
                    if (!Array.isArray(courses)) {
                                console.error('Invalid courses data:', courses);
                                courseList.innerHTML = '<tr><td colspan="7">無有效資料</td></tr>';
                    }

                    // 使用 .map() 遍歷課程資料並渲染到 HTML
                    courseList.innerHTML = courses.map(course => `   
                        <tr>
                            <td>${course.name}</td>
                            <td>${course.classroom}</td>
                            <td>${course.teacher_name}</td>
                            <td>${new Date(course.start_time).toLocaleString()}</td>
                            <td>${new Date(course.end_time).toLocaleString()}</td>
                            <td>${course.capacity}</td>
                            <td>${course.current}/${course.capacity}</td>
                            <td>
                                <button class="action-btn edit-btn" onclick="handleEdit(${course.id})">編輯</button>
                                <button class="action-btn delete-btn" onclick="handleDelete(${course.id})">刪除</button>
                            </td>
                        </tr>`).join('');
                },

                // 顯示對話框
                showModal: (isEdit = false) => {
                    modalTitle.textContent = isEdit ? '編輯課程' : '新增課程';
                    courseModal.style.display = 'block';
                },

                // 隱藏對話框
                hideModal: () => {
                    courseModal.style.display = 'none';
                    courseForm.reset();
                },

                // 填充表單數據
                fillForm: (course) => {
                    document.getElementById('courseId').value = course.id;
                    document.getElementById('name').value = course.name;
                    document.getElementById('teacher_name').value = course.teacher_name;
                    document.getElementById('classroom').value = course.classroom;
                    document.getElementById('startTime').value = course.start_time.slice(0, 16);
                    document.getElementById('endTime').value = course.end_time.slice(0, 16);
                    document.getElementById('capacity').value = course.capacity;
                }
            };

            // 事件處理函數
            window.handleEdit = async function(courseId) {
                const courses = await courseOperations.getCourses();
               
                const course = courses.data.find(c => c.id === courseId);
                if (course) {
                    uiOperations.fillForm(course);
                    uiOperations.showModal(true);
                }
            };

            window.handleDelete = async function(courseId) {
                if (confirm('確定要刪除這個課程嗎？')) {
                    const result = await courseOperations.deleteCourse(courseId);
                    if (result.success) {
                        refreshCourseList();
                    } else {
                        alert(result.message);
                    }
                }
            };

            async function handleSubmit(event) {
                event.preventDefault();
                const courseId = document.getElementById('courseId').value;
                const courseData = {
                    name: document.getElementById('name').value,
                    teacher_name: document.getElementById('teacher_name').value,
                    classroom: document.getElementById('classroom').value,
                    start_time: document.getElementById('startTime').value,
                    end_time: document.getElementById('endTime').value,
                    capacity: document.getElementById('capacity').value
                };

                if (courseId) {
                    courseData.id = courseId;
                    const result = await courseOperations.updateCourse(courseData);
                    if (result.success) {
                        alert("更新成功!");
                        refreshCourseList();
                        uiOperations.hideModal();
                    } else {
                        alert(result.message);
                    }
                } else {
                    const result = await courseOperations.addCourse(courseData);
                    if (result.success) {
                        refreshCourseList();
                        uiOperations.hideModal();
                    } else {
                        alert(result.message);
                    }
                }
            }

            async function refreshCourseList() {
                const response = await courseOperations.getCourses();
                if (response.success) {
                    uiOperations.renderCourses(response.data);
                } else {
                    console.error('Failed to fetch courses:', response);
                    courseList.innerHTML = '<tr><td colspan="7">無有效資料</td></tr>';
                }
            }

            // 事件監聽器
            addCourseBtn.addEventListener('click', () => uiOperations.showModal());
            cancelBtn.addEventListener('click', () => uiOperations.hideModal());
            courseForm.addEventListener('submit', handleSubmit);

            // 初始化
            refreshCourseList();
        });
    </script>
</body>
</html>