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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lost_items`
--
ALTER TABLE `lost_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `fk_status` (`status_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lost_items`
--
ALTER TABLE `lost_items`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Constraints for dumped tables
--

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
