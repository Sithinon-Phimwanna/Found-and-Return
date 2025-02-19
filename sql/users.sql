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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UserAdminID` (`UserAdminID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` double NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
