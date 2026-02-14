-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 14, 2026 at 11:43 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lulus2`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`) VALUES
(1, '12-F1'),
(2, '12-F2'),
(3, '12-F3'),
(4, '12-F4'),
(5, '12-F5'),
(6, '12-F6'),
(7, '12-F7'),
(8, '12-F8');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `score` decimal(5,2) DEFAULT 0.00,
  `sem1` decimal(5,2) DEFAULT 0.00,
  `sem2` decimal(5,2) DEFAULT 0.00,
  `sem3` decimal(5,2) DEFAULT 0.00,
  `sem4` decimal(5,2) DEFAULT 0.00,
  `sem5` decimal(5,2) DEFAULT 0.00,
  `sem6` decimal(5,2) DEFAULT 0.00,
  `school_exam` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `app_name` varchar(255) NOT NULL DEFAULT 'SKL App',
  `school_name` varchar(255) NOT NULL,
  `headmaster_name` varchar(255) NOT NULL,
  `headmaster_nip` varchar(50) NOT NULL,
  `npsn` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `website` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `graduation_date` date DEFAULT NULL,
  `letter_number` varchar(100) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `app_name`, `school_name`, `headmaster_name`, `headmaster_nip`, `npsn`, `address`, `website`, `logo`, `graduation_date`, `letter_number`, `favicon`) VALUES
(1, 'LULUS SMA', 'LULUS SMA ', 'KEPALA SEKOLAH', '0123456789', '20102026', 'jakarta', 'lulussekolah.web.id', NULL, '2025-05-05', '010 Tahun 2025', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `nisn` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `class` varchar(20) NOT NULL,
  `gender` enum('L','P') DEFAULT NULL,
  `pob` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `status` enum('LULUS','TIDAK LULUS') DEFAULT 'LULUS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('General','Elective','Local') NOT NULL DEFAULT 'General',
  `class_name` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `code`, `name`, `type`, `class_name`) VALUES
(1, 'PApvBj', 'Pendidikan Agama dan Budi Pekerti', 'General', 'Semua'),
(2, 'PPGnCs', 'Pendidikan Pancasila', 'General', 'Semua'),
(3, 'BInDO', 'Bahasa Indonesia', 'General', 'Semua'),
(4, 'MTKo', 'Matematika', 'General', 'Semua'),
(5, 'IPA', 'Ilmu Pengetahuan Alam', 'General', 'Semua'),
(6, 'IPS', 'Ilmu Pengetahuan Sosial', 'General', 'Semua'),
(7, 'BING', 'Bahasa Inggris', 'General', 'Semua'),
(8, 'PJOK', 'Pendidikan Jasmani Olahraga dan Kesehatan', 'General', 'Semua'),
(9, 'INF', 'Informatika', 'General', 'Semua'),
(10, 'SEJ', 'Sejarah', 'General', 'Semua'),
(11, 'SBK', 'Seni, Budaya, dan Prakarya', 'General', 'Semua'),
(12, 'SB', 'Seni dan Budaya', 'General', 'Semua'),
(13, 'BJEP', 'Bahasa Jepang', 'Elective', 'Semua'),
(14, 'BILmj', 'Bahasa Inggris Lanjut', 'Elective', 'Semua'),
(15, 'SOS', 'Sosiologi', 'Elective', 'Semua'),
(16, 'ANT', 'Antropologi', 'Elective', 'Semua'),
(17, 'PKW', 'Prakarya dan Kewirausahaan', 'Elective', 'Semua'),
(18, 'MULOK', 'Muatan Lokal', 'Local', 'Semua');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nisn` (`nisn`);

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
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
