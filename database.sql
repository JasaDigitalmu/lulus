-- database.sql

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

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
  `favicon` varchar(255) DEFAULT NULL,
  `graduation_date` date DEFAULT NULL,
  `letter_number` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `app_name`, `school_name`, `headmaster_name`, `headmaster_nip`, `npsn`, `address`, `website`, `logo`, `graduation_date`, `letter_number`) VALUES
(1, 'Aplikasi SKL', 'SMA Negeri 33 Jakarta', 'Saryanti,S.Pd., M.Si', '196808131992012002', '20101620', 'Jalan Kamal Raya nomor 54 Cengkareng, Jakarta 11730', 'www.sman33jkt.sch.id', 'logo.png', '2025-05-05', '010 Tahun 2025');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator'); -- password: password

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('General','Elective','Local') NOT NULL DEFAULT 'General'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `code`, `name`, `type`) VALUES
(1, 'PApvBj', 'Pendidikan Agama dan Budi Pekerti', 'General'),
(2, 'PPGnCs', 'Pendidikan Pancasila', 'General'),
(3, 'BInDO', 'Bahasa Indonesia', 'General'),
(4, 'MTKo', 'Matematika', 'General'),
(5, 'IPA', 'Ilmu Pengetahuan Alam', 'General'),
(6, 'IPS', 'Ilmu Pengetahuan Sosial', 'General'),
(7, 'BING', 'Bahasa Inggris', 'General'),
(8, 'PJOK', 'Pendidikan Jasmani Olahraga dan Kesehatan', 'General'),
(9, 'INF', 'Informatika', 'General'),
(10, 'SEJ', 'Sejarah', 'General'),
(11, 'SBK', 'Seni, Budaya, dan Prakarya', 'General'),
(12, 'SB', 'Seni dan Budaya', 'General'),
(13, 'BJEP', 'Bahasa Jepang', 'Elective'),
(14, 'BILmj', 'Bahasa Inggris Lanjut', 'Elective'),
(15, 'SOS', 'Sosiologi', 'Elective'),
(16, 'ANT', 'Antropologi', 'Elective'),
(17, 'PKW', 'Prakarya dan Kewirausahaan', 'Elective'),
(18, 'MULOK', 'Muatan Lokal', 'Local');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `nisn`, `name`, `class`, `gender`, `pob`, `dob`, `status`) VALUES
(1, '0067221748', 'AL FAJRI', 'XII IPS 1', 'L', 'JAKARTA', '2006-01-19', 'LULUS');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `score` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `student_id`, `subject_id`, `score`) VALUES
(1, 1, 1, '86.33'),
(2, 1, 2, '77.00'),
(3, 1, 3, '80.67'),
(4, 1, 4, '75.17'),
(5, 1, 5, '74.00'),
(6, 1, 6, '72.50'),
(7, 1, 7, '74.17'),
(8, 1, 8, '85.17'),
(9, 1, 9, '70.00'),
(10, 1, 10, '84.63'),
(11, 1, 11, '78.00'),
(12, 1, 12, '80.00'),
(13, 1, 13, '80.50'),
(14, 1, 14, '76.50'),
(15, 1, 15, '81.75'),
(16, 1, 16, '78.23'),
(17, 1, 17, '75.50'),
(18, 1, 18, '00.00');

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nisn` (`nisn`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;
COMMIT;
