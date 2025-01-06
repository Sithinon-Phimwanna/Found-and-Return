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
-- โครงสร้างตาราง `found_items`
--

CREATE TABLE `found_items` (
  `found_id` int NOT NULL,
  `finder_name` varchar(255) DEFAULT NULL,
  `finder_contact` varchar(255) DEFAULT NULL,
  `found_type` varchar(255) DEFAULT NULL,
  `found_description` text,
  `found_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `found_location` varchar(255) DEFAULT NULL,
  `found_image` varchar(255) DEFAULT NULL,
  `status_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- dump ตาราง `found_items`
--

INSERT INTO `found_items` (`found_id`, `finder_name`, `finder_contact`, `found_type`, `found_description`, `found_date`, `found_location`, `found_image`, `status_id`) VALUES
(5, 'กก', '123-456-7890', 'bag', 'sss', '2025-01-06 05:17:08', '2', 'large.jpg', 2),
(6, 'jhone', '123-456-7890', 'bag', '00', '2025-01-06 05:59:47', '3', '3mb_001.jpg', 2),
(7, 'gg', 'gg', 'bag', 'กกก', '2025-01-06 06:02:08', '3', 'GSMN-OPP-RENO12256BR_3_241029_090827.jpeg', 2),
(8, 'jhone', 'wqq', 'bag', '22', '2025-01-06 06:05:17', '24', 'BWM-B1-107A_1.jpg', 2),
(9, 'jhone', 'wqq', 'bag', 'vv', '2025-01-06 06:12:32', '3', 'BWM-B1-107A_1 (1).jpg', 2);

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
  MODIFY `found_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
