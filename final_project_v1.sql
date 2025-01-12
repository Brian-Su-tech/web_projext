-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-01-12 10:12:23
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `final_project_v1`
--

-- --------------------------------------------------------

--
-- 資料表結構 `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `booking_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `bookings`
--

INSERT INTO `bookings` (`id`, `student_id`, `course_id`, `booking_time`) VALUES
(9, 5, 29, '2025-01-12 08:04:31'),
(10, 5, 4, '2025-01-12 08:12:24');

-- --------------------------------------------------------

--
-- 資料表結構 `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `teacher_name` varchar(100) NOT NULL,
  `classroom` varchar(50) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `capacity` int(11) NOT NULL,
  `current` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `courses`
--

INSERT INTO `courses` (`id`, `name`, `teacher_name`, `classroom`, `start_time`, `end_time`, `capacity`, `current`) VALUES
(4, '吉他入門與基本技巧', '約翰欣梅爾', 'A1', '2025-01-16 02:30:00', '2025-01-16 16:30:00', 10, 1),
(7, '電吉他搖滾風格', '約翰欣梅爾', 'A2', '2025-01-17 14:30:00', '2025-01-17 16:30:00', 5, 0),
(24, '古典鋼琴演奏技巧', '芬多精', 'A2', '2025-01-14 09:00:00', '2025-01-14 10:00:00', 1, 0),
(25, '古典鋼琴演奏技巧', '芬多精', 'A2', '2025-01-21 09:00:00', '2025-01-21 10:00:00', 1, 0),
(26, '古典鋼琴演奏技巧', '芬多精', 'A2', '2025-01-28 09:00:00', '2025-01-28 10:00:00', 1, 0),
(27, '古典鋼琴演奏技巧', '芬多精', 'A2', '2025-02-04 09:00:00', '2025-02-04 10:00:00', 1, 0),
(28, '古典鋼琴演奏技巧', '芬多精', 'A2', '2025-02-11 09:00:00', '2025-02-11 10:00:00', 1, 0),
(29, '爵士鋼琴即興與創作', '芬多精', 'A2', '2025-01-12 14:00:00', '2025-01-12 15:00:00', 1, 1),
(30, '爵士鋼琴即興與創作', '芬多精', 'A2', '2025-01-19 14:00:00', '2025-01-19 15:00:00', 1, 0),
(31, '爵士鋼琴即興與創作', '芬多精', 'A2', '2025-01-26 14:00:00', '2025-01-26 15:00:00', 1, 0),
(32, '爵士鋼琴即興與創作', '芬多精', 'A2', '2025-02-02 14:00:00', '2025-02-02 15:00:00', 1, 0),
(33, '爵士鋼琴即興與創作', '芬多精', 'A2', '2025-02-09 14:00:00', '2025-02-09 15:00:00', 1, 0),
(34, '電吉他搖滾風格', '約翰欣梅爾', 'A1', '2025-01-12 18:00:00', '2025-01-16 19:00:00', 2, 0),
(35, '古典鋼琴演奏技巧', '芬多精', 'A2', '2025-01-06 16:00:00', '2025-01-06 17:00:00', 1, 0);

-- --------------------------------------------------------

--
-- 資料表結構 `instruments`
--

CREATE TABLE `instruments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `instruments`
--

INSERT INTO `instruments` (`id`, `name`, `category`, `price`, `description`, `stock`, `image_url`, `created_at`) VALUES
(2, 'YAMAHA YUS5', 'piano', 388000.00, '最頂級製琴原料、一絲不苟的工藝精神，孕育出 YUS5 精緻典雅的立式鋼琴設計，演繹表現力與平台鋼琴相似的超凡絕妙音色。', 15, 'uploads/instruments/677a9c0177196.jpg', '2025-01-05 13:12:06'),
(3, 'KAWAI CN201', 'piano', 49800.00, '由KAWAI R&D經驗豐富的音頻工程團隊開發,CN201採用全新設計的主機板和揚聲系統,以及全新高品質電源變壓器。這些零件互相結合,提供一流的音頻品質,產生極豐富、清晰的聲音,最低的失真度。', 10, 'uploads/instruments/677ad4629eb36.jpg', '2025-01-05 18:50:10'),
(4, 'Fender Vintera II \'60s Telecaster', 'guitar', 47000.00, 'Vintera|I\'60sTelecaster配備Alder琴身及Maple琴頸，搭配Rose-wood指板，帶來強勁且兼具清晰度的經典Fender音色。60年代 早期的「C」型琴頸基於60年代的經典輪廓，帶來直觀且討喜 的感受，而7.25英寸的指板弧度和復古高琴衍重現當時經典設 計的舒適度，同時能在大幅度推弦時提供充足的空間，且能在\r\n顫音時有更生動的表現。', 30, 'uploads/instruments/677ad4caba488.jpg', '2025-01-05 18:51:54'),
(5, 'Taylor GS Mini-E-RW-Plus', 'guitar', 50000.00, 'GS Mini 吉他是基於 Grand Symphony 琴身的縮小版本，擁有迷你的琴身，卻擁有強力的聲音，擁有堅實的雲杉木面板，印度玫瑰木背/側板，它美妙的演奏性和小尺吋攜帶方便，已經成為GS Mini 系列中受歡迎的款式之一。', 40, 'uploads/instruments/677ad4f361b9d.png', '2025-01-05 18:52:35'),
(6, 'VAD-306 Roland電子鼓', 'drum', 139000.00, '省空間的V-Drums Acoustic Design套鼓，TD-17音源搭載豐富表現力和動態的音色，易用的實體操作介面可用於選擇和自訂鼓聲。\r\n', 3, 'uploads/instruments/677ad53695040.jpg', '2025-01-05 18:53:42'),
(7, 'Alesis Command Mesh 電子鼓', 'drum', 44800.00, '這是一套Nitro Mesh Kit的升級版，不但是全網面，連大鼓也是網面。Nitro Mesh的中鼓是單音域，而Command Mesh成為雙音域。小鼓也由8吋升級為10吋，Ride鈸與Crash鈸都是10吋也都有靜音(choke)功能，Hi-Hat也同時為10吋。所以你會看到一套較大尺寸的電子鼓，幾乎就是傳統鼓的大小。Command Mesh的音源機有更多的74個鼓組，671個音色。此外，有60個可直接播出的音軌與更進階錄音功能。', 20, 'uploads/instruments/6782731bb3ffe.jpg', '2025-01-11 13:33:15');

-- --------------------------------------------------------

--
-- 資料表結構 `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `instrument_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `orders`
--

INSERT INTO `orders` (`id`, `user_name`, `instrument_name`, `quantity`, `price`, `total_amount`, `created_at`) VALUES
(8, '123', 'Taylor GS Mini-E-RW-Plus', 1, 50000.00, 50000.00, '2025-01-11 20:33:09');

-- --------------------------------------------------------

--
-- 資料表結構 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `role` enum('student','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `name`, `phone`, `role`, `created_at`) VALUES
(1, 'x59271648@gmail.com', '$2y$10$6EvjupA.JIQhJVtPK23XDeetpttrllqzwFF.Y7WQfsQdwz6TpZD0O', 'Jiaying Sung', '0968622710', 'admin', '2025-01-04 15:36:06'),
(5, 's1111442@mail.yzu.edu.tw', '$2y$10$b/bMVPVHSRgnPliv7AgTfOxHm5HjBHSit/7skfBnedZG99vsESmxm', '123', '0968622710', 'student', '2025-01-04 15:46:08'),
(9, '123@gmail.com', '$2y$10$LWyv3jKsT8OFXyKTD60cfeqFJUG1.krPp8Qu4iLqF.j..M.oTqJlC', 'bob', '0968622710', 'student', '2025-01-11 10:18:04'),
(14, 'x59271649@gmail.com', '$2y$10$Q/iEIWulA41oIfRvyO9ceuWQTwpsCPZaV2hO4fNd0p47NKJhHi2mu', 'Jiaying Sung', '0968622710', 'student', '2025-01-11 10:29:33');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- 資料表索引 `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `instruments`
--
ALTER TABLE `instruments`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `instruments`
--
ALTER TABLE `instruments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
