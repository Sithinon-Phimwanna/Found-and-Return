-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 19 ก.พ. 2025 เมื่อ 03:03 PM
-- เวอร์ชันของเซิร์ฟเวอร์: 8.0.41-0ubuntu0.20.04.1
-- PHP Version: 7.4.3-4ubuntu2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `6412231023_Lostitem`
--

-- --------------------------------------------------------

--
-- โครงสร้างตาราง `found_items`
--

CREATE TABLE `found_items` (
  `found_id` int NOT NULL,
  `finder_name` varchar(255) DEFAULT NULL,
  `finder_contact` varchar(255) DEFAULT NULL,
  `found_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `found_description` text,
  `found_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `found_location` varchar(255) DEFAULT NULL,
  `consignee` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `found_image` varchar(255) DEFAULT NULL,
  `status_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- dump ตาราง `found_items`
--

INSERT INTO `found_items` (`found_id`, `finder_name`, `finder_contact`, `found_name`, `found_description`, `found_date`, `found_location`, `consignee`, `found_image`, `status_id`) VALUES
(5, 'ทดสอบ', '123-456-7890', 'ทดสอบ', 'ทดสอบ', '2025-01-06 05:17:00', '2', '', '03-02-2025_09-41-06_k1.jpg,03-02-2025_09-41-06_Prod_2.png', 2),
(54, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', 'กระเป๋า', 'กระเป๋า', '2025-01-28 08:22:00', '2', '', '03-02-2025_09-31-31_Prod_2.png', 3),
(55, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', 'ทดสอบ', 'ทดสอบ', '2025-01-29 03:27:00', '3', '', '2025-01-29_11-45-55_3.png', 2),
(56, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', 'กุญแจ', 'กุญแจ', '2025-01-30 04:47:00', '7', '', '30-01-2025_09-20-21_003.jpg', 2),
(60, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', '00', '0', '2025-02-13 03:40:00', '5', '', '2025-02-13_10-40-30_im.jpg', 2),
(61, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', '00', '5', '2025-02-13 03:42:01', '5', '', '2025-02-13_10-42-01_003.jpg', 3),
(77, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', 'ทดสอบ', '00', '2025-02-14 05:26:56', '4', '', '2025-02-14_12-26-56_1739510777_compressed.jpg', 2),
(78, 'staff_test1', 'staff_test1@psru.ac.th', 'อ', '0', '2025-02-14 08:21:32', '9', '', NULL, 2),
(79, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', 'ทดสอบ', 'ทดสอบรูปภาพและความยาวตาราง', '2025-02-18 01:31:48', '3', '', NULL, 2),
(80, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', 'ทดสอบ', 'ทดสอบรูปภาพและความยาวตาราง', '2025-02-18 01:32:33', '5', '', '2025-02-18_08-32-33_00.jpg', 2),
(81, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', 'กุญแจ', 'กุญแจมีตุ๊กตาหมี', '2025-02-18 02:15:00', '3', '', '2025-02-18_09-15-29_1739844852_thump.jpg', 1),
(82, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', '00', '00', '2025-02-18 03:50:35', '8', '', '2025-02-18_10-50-35_1739842416_thump.jpg', 1),
(83, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', 'lige', 'ghyt', '2025-02-18 08:24:08', '8', '', '2025-02-18_15-24-08_1739842368_thump.jpg', 1),
(84, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', '0', '0', '2025-02-19 04:36:33', '7', '', '2025-02-19_11-36-33_1739778128_thump.jpg', 1),
(85, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', '0', '0', '2025-02-19 06:10:22', '8', NULL, '2025-02-19_13-10-22_1739842368_thump.jpg', 1),
(86, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', '0', '0', '2025-02-19 06:18:00', '4', 'นายทดสอบ ระบบสารสนเทศ', '2025-02-19_13-18-07_1739842368_thump.jpg', 1),
(87, 'Michael Williams', '+49 258-627-6644', '0', '2', '2025-02-19 06:39:00', '8', 'นายทดสอบ ระบบสารสนเทศ', '2025-02-19_13-39-12_1739842368_thump.jpg', 1);

-- --------------------------------------------------------

--
-- โครงสร้างตาราง `location`
--

CREATE TABLE `location` (
  `location_id` int NOT NULL,
  `location_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- dump ตาราง `location`
--

INSERT INTO `location` (`location_id`, `location_name`) VALUES
(1, 'ชั้น1 โซนสำนักงาน'),
(2, 'ชั่น1 โซนธนาคาร'),
(3, 'ชั้น 1 ห้อง 24 ชม.'),
(4, 'ชั้น 2 โซน A'),
(5, 'ชั้น 2 โซน B'),
(6, 'ชั้น 3 โซน A'),
(7, 'ชั้น 3 โซน B'),
(8, 'ชั้น 4 โซน A'),
(9, 'ชั้น 4 โซน B');

-- --------------------------------------------------------

--
-- โครงสร้างตาราง `lost_items`
--

CREATE TABLE `lost_items` (
  `item_id` int NOT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `owner_contact` varchar(255) DEFAULT NULL,
  `item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `item_description` text,
  `lost_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lost_location` varchar(255) DEFAULT NULL,
  `item_image` varchar(255) DEFAULT NULL,
  `finder_image` varchar(255) DEFAULT NULL,
  `deliverer` varchar(100) DEFAULT NULL,
  `status_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- dump ตาราง `lost_items`
--

INSERT INTO `lost_items` (`item_id`, `owner_name`, `owner_contact`, `item_name`, `item_description`, `lost_date`, `lost_location`, `item_image`, `finder_image`, `deliverer`, `status_id`) VALUES
(6, 'นาย สตาฟ ทดสอบ', 'staff@psru.ac.th', 'กระเป๋า', 'กระเป๋าสีเหลือง', '2025-01-16 02:32:00', '3', '03-02-2025_09-52-48_k1.jpg,03-02-2025_09-52-48_Prod_2.png', '03-02-2025_10-36-41_k1.jpg', 'นายทดสอบ ระบบสารสนเทศ', 2),
(7, 'นาย สตาฟ ทดสอบ', 'staff@psru.ac.th', 'กระเป๋า', 'กระเป๋า', '2025-01-16 07:39:00', '3', '29-01-2025_16-18-14_3.png', '29-01-2025_16-18-14_k1.jpg', 'นาย สตาฟ ทดสอบ', 2),
(8, 'นาย สตาฟ ทดสอบ', 'staff@psru.ac.th', 'กุญแจ2', 'กุญแจ2', '2025-01-16 07:39:00', '5', '30-01-2025_09-12-44_003.jpg', '30-01-2025_09-18-47_k1.jpg', 'นายทดสอบ ระบบสารสนเทศ', 2),
(10, 'นาย สตาฟ ทดสอบ', 'staff@psru.ac.th', 'รูปภาพ', 'รูปภาพ8 บิต', '2025-01-17 04:36:00', '2', '30-01-2025_09-32-55_im.jpg', '30-01-2025_09-36-52_k1.jpg', 'นายทดสอบ ระบบสารสนเทศ', 2),
(14, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', 'กระเป๋า', 'กระเป๋า', '2025-01-28 06:42:00', '2', '29-01-2025_16-19-15_3.png', '03-02-2025_10-41-22_k1.jpg,03-02-2025_10-41-22_Prod_2.png', 'นาย สตาฟ ทดสอบ', 2),
(15, 'นาย สตาฟ ทดสอบ', 'staff@psru.ac.th', 'กุญแจ', 'กุญแจ', '2025-01-30 02:34:00', '6', '2025-01-30_09-34-19_003.jpg', '03-02-2025_10-40-25_k1.jpg,03-02-2025_10-40-25_Prod_2.png', 'นายทดสอบ ระบบสารสนเทศ', 2),
(16, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', '0', '0', '2025-02-13 03:00:23', '3', '2025-02-13_10-00-23_im.jpg', NULL, NULL, 1),
(18, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', 'ทดสอบรูปภาพและความยาวตาราง', 'นาย สิทธินนท์ พิมวันนา เป็นผู้ทดสอบ', '2025-02-14 06:21:00', '5', '2025-02-14_13-21-30_003.jpg,2025-02-14_13-21-30_3.png,2025-02-14_13-21-30_im.jpg', '', '', 1),
(19, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', 'ทดสอบรูปภาพและความยาวตาราง', 'ทดสอบรูปภาพและความยาวตาราง', '2025-02-14 08:47:00', '8', '2025-02-14_15-47-11_1739520001_thump.jpg', '18-02-2025_14-38-09_1739842368_thump.jpg', 'นาย สตาฟ ทดสอบ', 1),
(20, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', 'ทดสอบ', 'ทดสอบรูปภาพและความยาวตาราง', '2025-02-18 01:34:02', '9', '2025-02-18_08-34-02_1739842416_thump.jpg', NULL, NULL, 1),
(21, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', 'ทดสอบรูปภาพและความยาวตาราง', 'ทดสอบรูปภาพและความยาวตาราง', '2025-02-18 01:35:00', '5', '2025-02-18_08-35-11_00.jpg', '18-02-2025_13-44-23_1739842368_thump.jpg', 'นายทดสอบ ระบบสารสนเทศ', 1),
(22, 'นายทดสอบ ระบบสารสนเทศ', 'somphob.m@psru.ac.th', 'opop', 'popo', '2025-02-18 08:24:49', '5', '2025-02-18_15-24-49_1739842368_thump.jpg', NULL, NULL, 1),
(24, 'Michael Williams', 'michael.williams@x.dummyjson.com', 'opop', '00', '2025-02-19 02:25:40', '8', 'img_67b54124a84be1.92704130.jpg', NULL, NULL, 1),
(25, 'Michael Williams', 'michael.williams@x.dummyjson.com', '2', '2', '2025-02-19 02:26:08', '7', 'img_67b541409df0b3.75404155.jpg', NULL, NULL, 1),
(26, 'Emily Johnson', 'emily.johnson@x.dummyjson.com', 'opop', '1234', '2025-02-19 02:32:34', '6', 'img_67b542c2a1d737.34565450.jpg', NULL, NULL, 1),
(27, 'Michael Williams', 'michael.williams@x.dummyjson.com', '2', '2', '2025-02-19 02:35:06', '7', NULL, NULL, NULL, 1),
(28, 'Emily Johnson', 'emily.johnson@x.dummyjson.com', '1', '1', '2025-02-19 02:37:16', '7', NULL, NULL, NULL, 1),
(29, 'James Davis', 'james.davis@x.dummyjson.com', '0', '0', '2025-02-19 02:40:29', '7', '20250219094029_1739842368_thump.jpg', NULL, NULL, 1),
(30, 'Emma Miller', 'emma.miller@x.dummyjson.com', '22', '22', '2025-02-19 02:40:55', '4', NULL, NULL, NULL, 1),
(32, 'Michael Williams', 'michael.williams@x.dummyjson.com', '2', '2', '2025-02-19 03:06:07', '6', '20250219100607_1739842368_thump.jpg', NULL, NULL, 1),
(33, 'Emily Johnson', 'emily.johnson@x.dummyjson.com', '2', '2', '2025-02-19 04:43:10', '8', '20250219114310_1739524412_thump.jpg', NULL, NULL, 1),
(34, 'Emily Johnson', '+81 965-431-3024', '1', '1', '2025-02-19 04:53:00', '3', '20250219115308_1739778128_thump.jpg', '', 'นาง ทดสอบ สตาฟ', 1);

-- --------------------------------------------------------

--
-- โครงสร้างตาราง `statuses`
--

CREATE TABLE `statuses` (
  `status_id` int NOT NULL,
  `status_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- dump ตาราง `statuses`
--

INSERT INTO `statuses` (`status_id`, `status_name`) VALUES
(1, 'แจ้งพบ'),
(2, 'ได้รับคืนแล้ว'),
(3, 'แจ้งพบเกิน 1 สัปดาห์');

-- --------------------------------------------------------

--
-- โครงสร้างตาราง `status_lost`
--

CREATE TABLE `status_lost` (
  `status_id` int NOT NULL,
  `status_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- dump ตาราง `status_lost`
--

INSERT INTO `status_lost` (`status_id`, `status_name`) VALUES
(1, 'แจ้งหาย'),
(2, 'ได้รับคืนแล้ว'),
(3, 'ยังไม่พบทรัพย์สิน');

-- --------------------------------------------------------

--
-- โครงสร้างตาราง `users`
--

CREATE TABLE `users` (
  `id` double NOT NULL,
  `UserAdminID` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT 'username',
  `Password` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT 'password',
  `UserAdminName` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT 'ชื่อ-นามสกุล',
  `position_id` int NOT NULL COMMENT 'รหัสตำแหน่ง',
  `group_id` int NOT NULL COMMENT 'รหัสกลุ่มงาน',
  `level_id` int NOT NULL COMMENT 'รหัสระดับ',
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT 'อีเมล์'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- dump ตาราง `users`
--

INSERT INTO `users` (`id`, `UserAdminID`, `Password`, `UserAdminName`, `position_id`, `group_id`, `level_id`, `email`) VALUES
(1, 'admin_test', '5701e1fc38a45821bc7687a3d8530720', 'นายทดสอบ ระบบสารสนเทศ', 9, 5, 3, 'somphob.m@psru.ac.th'),
(2, 'staff_test', '385e7586e0ecf59c4db7d550d7ba7b65', 'นาย สตาฟ ทดสอบ', 8, 5, 2, 'staff@psru.ac.th'),
(3, 'test_test', 'f032f27ee18f9de67a3bb9c16eae57b3', 'นายเทส สุดหล่อ', 7, 6, 2, 'test_test@psru.ac.th'),
(4, 'staff_2', '56e1fa5fa1930606888b1a9ec5b60198', 'นางสตาฟ สุดสวย', 7, 6, 2, 'x_235@hotmail.com'),
(5, 'test', '098f6bcd4621d373cade4e832627b4f6', 'นายเทสดี สุดแสนส่องแสง', 6, 2, 2, 'test@psru.ac.th'),
(7, 'staff_test1', '037ae16dcb2d9a37f66277d831f13ce6', 'staff_test1', 2, 2, 1, 'staff_test1@psru.ac.th'),
(8, 'staff_test2', '81dc9bdb52d04dc20036dbd8313ed055', 'นาง ทดสอบ สตาฟ', 2, 2, 2, 'staff_test2@gmail.com');

-- --------------------------------------------------------

--
-- โครงสร้างตาราง `visitor_counter`
--

CREATE TABLE `visitor_counter` (
  `id` int NOT NULL,
  `visit_date` date NOT NULL,
  `visit_count` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- dump ตาราง `visitor_counter`
--

INSERT INTO `visitor_counter` (`id`, `visit_date`, `visit_count`) VALUES
(1, '2025-01-28', 8),
(2, '2025-01-29', 1),
(3, '2025-01-30', 7),
(4, '2025-01-31', 1),
(5, '2025-02-03', 2),
(6, '2025-02-04', 3),
(7, '2025-02-05', 1),
(8, '2025-02-06', 1),
(9, '2025-02-07', 4),
(10, '2025-02-10', 1),
(11, '2025-02-11', 4),
(12, '2025-02-13', 2),
(13, '2025-02-14', 2),
(14, '2025-02-17', 2),
(15, '2025-02-18', 4),
(16, '2025-02-19', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `found_items`
--
ALTER TABLE `found_items`
  ADD PRIMARY KEY (`found_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `lost_items`
--
ALTER TABLE `lost_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `fk_status` (`status_id`);

--
-- Indexes for table `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `status_lost`
--
ALTER TABLE `status_lost`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UserAdminID` (`UserAdminID`);

--
-- Indexes for table `visitor_counter`
--
ALTER TABLE `visitor_counter`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `found_items`
--
ALTER TABLE `found_items`
  MODIFY `found_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `location_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `lost_items`
--
ALTER TABLE `lost_items`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` double NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `visitor_counter`
--
ALTER TABLE `visitor_counter`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `found_items`
--
ALTER TABLE `found_items`
  ADD CONSTRAINT `found_items_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`status_id`);

--
-- Constraints for table `lost_items`
--
ALTER TABLE `lost_items`
  ADD CONSTRAINT `fk_status` FOREIGN KEY (`status_id`) REFERENCES `status_lost` (`status_id`),
  ADD CONSTRAINT `lost_items_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`status_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
