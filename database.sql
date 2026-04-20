-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 31, 2026 at 05:06 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `attendance_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` enum('present','absent') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `subject_id`, `date`, `status`) VALUES
(1, 1, 1, '2026-03-25', 'present'),
(2, 1, 2, '2026-03-26', 'absent'),
(3, 1, 3, '2026-03-27', 'absent'),
(4, 1, 4, '2026-03-27', 'absent'),
(5, 2, 3, '2026-03-29', 'present'),
(6, 2, 1, '2026-03-25', 'present'),
(7, 2, 2, '2026-03-25', 'present'),
(8, 3, 1, '2026-03-28', 'present'),
(9, 3, 3, '2026-03-27', 'present'),
(10, 3, 5, '2026-03-27', 'present'),
(11, 3, 2, '2026-03-27', 'present'),
(12, 4, 1, '2026-03-29', 'absent'),
(13, 4, 4, '2026-03-25', 'present'),
(14, 4, 3, '2026-03-25', 'absent'),
(15, 5, 4, '2026-03-26', 'absent'),
(16, 5, 1, '2026-03-26', 'present'),
(17, 6, 3, '2026-03-26', 'absent'),
(18, 6, 4, '2026-03-25', 'present'),
(19, 6, 1, '2026-03-26', 'present'),
(20, 7, 5, '2026-03-26', 'present'),
(21, 7, 4, '2026-03-25', 'present'),
(22, 7, 1, '2026-03-25', 'present'),
(23, 1, 1, '2026-03-28', 'present'),
(24, 1, 2, '2026-03-29', 'present'),
(25, 2, 3, '2026-03-28', 'absent'),
(26, 2, 4, '2026-03-29', 'present'),
(27, 3, 2, '2026-03-29', 'present'),
(28, 3, 4, '2026-03-28', 'present'),
(29, 4, 5, '2026-03-28', 'present'),
(30, 4, 2, '2026-03-29', 'absent'),
(31, 5, 3, '2026-03-28', 'present'),
(32, 5, 1, '2026-03-29', 'present'),
(33, 6, 4, '2026-03-28', 'absent'),
(34, 6, 5, '2026-03-29', 'present'),
(35, 7, 2, '2026-03-28', 'present'),
(36, 7, 3, '2026-03-29', 'present');

-- --------------------------------------------------------

--
-- Table structure for table `marks`
--

CREATE TABLE `marks` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `marks` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `marks`
--

INSERT INTO `marks` (`id`, `student_id`, `subject_id`, `marks`) VALUES
(1, 1, 1, 88),
(2, 1, 2, 75),
(3, 1, 3, 92),
(4, 1, 4, 100),
(5, 1, 5, 40),
(6, 2, 1, 35),
(7, 2, 2, 78),
(8, 2, 3, 77),
(9, 2, 5, 75),
(10, 3, 1, 69),
(11, 3, 3, 100),
(12, 3, 5, 100),
(13, 4, 2, 70),
(14, 4, 4, 80),
(15, 5, 4, 90),
(16, 5, 1, 82),
(17, 6, 3, 100),
(18, 6, 1, 85),
(19, 7, 4, 69),
(20, 7, 5, 72),
(21, 1, 1, 91),
(22, 1, 2, 82),
(23, 2, 2, 74),
(24, 2, 4, 68),
(25, 3, 4, 95),
(26, 3, 2, 88),
(27, 4, 1, 60),
(28, 4, 3, 72),
(29, 5, 2, 79),
(30, 5, 3, 85),
(31, 6, 5, 91),
(32, 6, 2, 76),
(33, 7, 1, 84),
(34, 7, 3, 80);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`) VALUES
(1, 'Math'),
(2, 'Science'),
(3, 'English'),
(4, 'Computer'),
(5, 'Drama');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role` enum('admin','teacher','student') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Adnan', 'adnan@email.com', '1234', 'student'),
(2, 'Abhinav', 'abhinav@email.com', '1234', 'student'),
(3, 'Ishu', 'ishu@email.com', '1234', 'student'),
(4, 'Pankaj', 'pankaj@email.com', '1234', 'student'),
(5, 'Tushar', 'tushar@email.com', '1234', 'student'),
(6, 'Zaidan', 'zaidan@email.com', '1234', 'student'),
(7, 'Utkarsh', 'utkarsh@email.com', '1234', 'student'),
(8, 'Teacher User', 'teacher@email.com', '1234', 'teacher'),
(9, 'Admin User', 'admin@email.com', '1234', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `marks`
--
ALTER TABLE `marks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `marks`
--
ALTER TABLE `marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marks`
--
ALTER TABLE `marks`
  ADD CONSTRAINT `marks_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marks_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
