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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `found_items`
--
ALTER TABLE `found_items`
  MODIFY `found_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `found_items`
--
ALTER TABLE `found_items`
  ADD CONSTRAINT `found_items_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`status_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
