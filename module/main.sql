-- 資料庫結構

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    role ENUM('student', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 課程資訊
CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    teacher_name VARCHAR(100) NOT NULL,
    classroom VARCHAR(50) NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    capacity INT NOT NULL,
    current INT NOT NULL
);

CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    course_id INT,
    booking_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
);


-- 樂器資料
CREATE TABLE instruments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    stock INT NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- 訂單明細表
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT, -- 訂單 ID
    user_name VARCHAR(255) NOT NULL,   -- 訂購人姓名
    instrument_name VARCHAR(255) NOT NULL, -- 商品名稱
    quantity INT NOT NULL,             -- 購買數量
    price DECIMAL(10,2) NOT NULL,      -- 單價
    total_amount DECIMAL(10,2) NOT NULL, -- 單筆商品總金額 (quantity * price)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- 訂單建立時間
);