-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 06 ม.ค. 2025 เมื่อ 03:04 PM
-- เวอร์ชันของเซิร์ฟเวอร์: 8.0.40-0ubuntu0.20.04.1
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
  `item_type` varchar(255) DEFAULT NULL,
  `item_description` text,
  `lost_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lost_location` varchar(255) DEFAULT NULL,
  `item_image` varchar(255) DEFAULT NULL,
  `finder_image` varchar(255) DEFAULT NULL,
  `status_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- dump ตาราง `lost_items`
--

INSERT INTO `lost_items` (`item_id`, `owner_name`, `owner_contact`, `item_type`, `item_description`, `lost_date`, `lost_location`, `item_image`, `finder_image`, `status_id`) VALUES
(1, 'dd', 'dd', 'dd', 'กก', '2025-01-06 06:17:42', 'กก', 'เมาส์ปากกา.jpg', '001465902.jpg', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lost_items`
--
ALTER TABLE `lost_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `status_id` (`status_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lost_items`
--
ALTER TABLE `lost_items`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lost_items`
--
ALTER TABLE `lost_items`
  ADD CONSTRAINT `lost_items_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`status_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
