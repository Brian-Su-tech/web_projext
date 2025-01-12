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
    <link rel="stylesheet" href="../styles/manage_course.css">
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

    <div class="container  my-5">
        <h2 class="text-center mb-4">課程管理系統</h2>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <button id="prevWeekBtn" class="btn btn-outline-secondary">
                <i class="bi bi-chevron-left"></i> 上一週
            </button>
            <h3 id="currentMonth" class="mb-0"></h3>
            <button id="nextWeekBtn" class="btn btn-outline-secondary">
                下一週 <i class="bi bi-chevron-right"></i>
            </button>
        </div>

        <button id="addCourseBtn" class="btn btn-primary mb-2">新增課程</button>
        <div class="calendar-grid">
            <div class="row" id="weekDays"></div>
            <div class="row" id="courseGrid"></div>
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
                    <div class="form-group" id="num_classes_div">
                        <label for="num_classes">新增堂數</label>
                        <input type = "number" id="num_classes" min = "1" max="20" step="1" value = "1" checked>
                    </div>
                    <div class="form-buttons">
                        <button type="submit" class="btn btn-primary">儲存</button>
                        <button type="button" class="btn btn-secondary" id="cancelBtn">取消</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="student-list"></div>

    <script>
        // 關閉模態框的腳本
        // const modal = document.getElementById('studentModal');
        const closeBtn = document.querySelector('.close');

        document.addEventListener('click', function(event) {
            if (event.target.matches('.close')) {
                document.getElementById('student-list').style.display = 'none';
            }
        });


        function viewStudents(course_id) {
                // 使用 AJAX 發送請求
                const xhr = new XMLHttpRequest();
                xhr.open('GET', '../module/view_students.php?course_id=' + course_id, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // 顯示學生資訊
                        document.getElementById('student-list').innerHTML = xhr.responseText;
                        document.getElementById('student-list').style.display = 'block'; // 顯示學生資訊區域
                    } else {
                        alert('無法獲取學生資料');
                    }
                };
                xhr.send();
            }

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

            // 添加新的日历相关元素
            const weekDaysContainer = document.getElementById('weekDays');
            const courseGrid = document.getElementById('courseGrid');
            const prevWeekBtn = document.getElementById('prevWeekBtn');
            const nextWeekBtn = document.getElementById('nextWeekBtn');
            const currentMonthEl = document.getElementById('currentMonth');

            let currentDate = new Date();

            const calendarOperations = {
                getWeekDates: () => {
                    const dates = [];
                    const firstDayOfWeek = new Date(currentDate);
                    firstDayOfWeek.setDate(currentDate.getDate() - currentDate.getDay());

                    for (let i = 0; i < 7; i++) {
                        const date = new Date(firstDayOfWeek);
                        date.setDate(firstDayOfWeek.getDate() + i);
                        dates.push(date);
                    }
                    return dates;
                },

                formatTime: (dateString) => {
                    return new Date(dateString).toLocaleTimeString('zh-TW', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    });
                },

                getCoursesForDate: (courses, date) => {
                    return courses.filter(course => {
                        const courseDate = new Date(course.start_time);
                        return courseDate.toDateString() === date.toDateString();
                    });
                },

                renderCalendar: async () => {
                    const weekDays = ['日', '一', '二', '三', '四', '五', '六'];
                    const dates = calendarOperations.getWeekDates();
                    
                    // 更新月份显示
                    currentMonthEl.textContent = `${currentDate.getFullYear()}年${currentDate.getMonth() + 1}月`;

                    // 渲染星期头部
                    weekDaysContainer.innerHTML = dates.map((date, index) => `
                        <div class="col">
                            <div class="day-header ${date.toDateString() === new Date().toDateString() ? 'current-day' : ''}">
                                <div>週${weekDays[index]}</div>
                                <div class="fw-bold">${date.getDate()}</div>
                            </div>
                        </div>
                    `).join('');

                    // 获取课程数据
                    const response = await courseOperations.getCourses();
                    if (!response.success) return;

                    // 渲染课程网格
                    courseGrid.innerHTML = `
                        <div class="row">
                            ${dates.map(date => `
                                <div class="col day-column">
                                    ${calendarOperations.getCoursesForDate(response.data, date)
                                        .map(course => `
                                            <div class="course-card">
                                                <div class="course-time">
                                                    <i class="bi bi-clock"></i>
                                                    ${calendarOperations.formatTime(course.start_time)}~
                                                    ${calendarOperations.formatTime(course.end_time)}
                                                </div>
                                                <div class="course-name"> ${course.name}</div>
                                                <div><i class="bi bi-person"></i> ${course.teacher_name}</div>
                                                <div><i class="bi bi-people course-capacity"></i> ${course.current}/${course.capacity}</div>
                                                <div class="mt-2">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="viewStudents(${course.id})">檢視</button>
                                                    <button class="btn btn-sm btn-outline-secondary" onclick="handleEdit(${course.id})">編輯</button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="handleDelete(${course.id})">刪除</button>
                                                </div>
                                            </div>
                                        `).join('') || '<div class="no-courses">尚無課程</div>'
                                    }
                                </div>
                            `).join('')}
                        </div>
                    `;
                }
            };


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
                                <button class="action-btn view-btn" onclick="viewStudents(${course.id})">檢視</button>
                                <button class="action-btn edit-btn" onclick="handleEdit(${course.id})">編輯</button>
                                <button class="action-btn delete-btn" onclick="handleDelete(${course.id})">刪除</button>
                            </td>
                        </tr>`).join('');
                },

                // 顯示對話框
                showModal: (isEdit = false) => {
                    modalTitle.textContent = isEdit ? '編輯課程' : '新增課程';
                    courseModal.style.display = 'block';

                    const numClassesDiv = document.getElementById('num_classes_div');
                    if (isEdit) {
                        numClassesDiv.style.display = 'none'; // 隱藏
                    } else {
                        numClassesDiv.style.display = 'block'; // 顯示
                    }
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
                    document.getElementById('num_classes').value = course.num_classes;
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
                    capacity: document.getElementById('capacity').value,
                    num_classes: document.getElementById('num_classes').value
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

            // 添加周切换事件监听器
            prevWeekBtn.addEventListener('click', () => {
                currentDate.setDate(currentDate.getDate() - 7);
                calendarOperations.renderCalendar();
            });

            nextWeekBtn.addEventListener('click', () => {
                currentDate.setDate(currentDate.getDate() + 7);
                calendarOperations.renderCalendar();
            });

            // 修改刷新函数
            async function refreshCourseList() {
                await calendarOperations.renderCalendar();
            }

            // 初始化日历
            refreshCourseList();


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