-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 07, 2026 at 12:01 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `student_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(2, 'admin', 'kibet@gmail.com', '1234', 'admin', '2026-03-12 14:39:13');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `status` enum('Present','Absent') DEFAULT NULL,
  `date` date DEFAULT curdate(),
  `date_marked` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `unit_id`, `status`, `date`, `date_marked`) VALUES
(1, 14, 19, 'Absent', '2026-03-12', '2026-03-12'),
(2, 12, 41, 'Present', '2026-03-14', '2026-03-14');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_name` varchar(100) DEFAULT NULL,
  `course_code` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_name`, `course_code`) VALUES
(1, 'Information Technology', 'DIT01'),
(2, 'Software Engineering', 'DSE02'),
(3, 'Cyber Security', 'DCS03'),
(4, 'Business Information Systems', 'BIS04'),
(5, 'Web Development & Design', 'WDD05'),
(6, 'Data Science & Analytics', 'DSA06'),
(7, 'Computer Science', 'DCS07'),
(8, 'Network Administration', 'DNA08'),
(9, 'Mobile Application Development', 'MAD09'),
(10, 'Project Management', 'DPM10');

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `initial_fee` decimal(10,2) NOT NULL DEFAULT 50000.00,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(10,2) NOT NULL DEFAULT 50000.00,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`id`, `student_id`, `initial_fee`, `paid_amount`, `balance`, `last_updated`) VALUES
(1, 12, 50000.00, 4000.00, 46000.00, '2026-03-17 17:57:47'),
(2, 16, 50000.00, 0.00, 50000.00, '2026-03-27 07:17:18');

-- --------------------------------------------------------

--
-- Table structure for table `marks`
--

CREATE TABLE `marks` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `cat` int(3) DEFAULT NULL,
  `exam` int(3) DEFAULT NULL,
  `total` int(3) DEFAULT NULL,
  `grade` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `marks`
--

INSERT INTO `marks` (`id`, `student_id`, `unit_id`, `cat`, `exam`, `total`, `grade`) VALUES
(1, 12, 34, 25, 65, 90, 'A'),
(2, 12, 41, 26, 59, 85, 'A');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `reg_number` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `year` int(1) DEFAULT 1,
  `semester` int(1) DEFAULT 1,
  `course_id` int(11) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `full_name`, `reg_number`, `password`, `email`, `course`, `created_at`, `year`, `semester`, `course_id`, `last_login`) VALUES
(12, 'Tevin Kimani', 'DIT/001/2026', '2345', 'kimani@gmail.com', '	Information Technology', '2026-03-09 20:45:49', 2, 2, 1, '2026-03-12 16:42:14'),
(14, 'Emmanuel Kibet', 'DIT/002/1025', '1234', 'emmanuel@gamil.com', NULL, '2026-03-10 09:15:26', 1, 2, 1, NULL),
(15, 'Jane Kamau', 'DIT/003/2026', '8900', 'kamau@gmail.com', NULL, '2026-03-17 16:11:36', 1, 2, 1, NULL),
(16, 'Kimani James', 'DIT/007/2026', '$2y$10$mB0htnPIO5U7k5ZQLOakyuNcwIb3TV7JkdeldCcJghzHmUIYTPIT6', NULL, NULL, '2026-03-27 07:17:18', 1, 1, 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `unit_name` varchar(100) NOT NULL,
  `unit_code` varchar(20) NOT NULL,
  `year` int(1) NOT NULL,
  `semester` int(1) NOT NULL,
  `course_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `unit_name`, `unit_code`, `year`, `semester`, `course_id`) VALUES
(20, 'introduction to programming', 'sit01', 1, 1, 1),
(23, 'Introduction to Computing', 'ICT101', 1, 1, 1),
(24, 'Communication Skills', 'CCS101', 1, 1, 1),
(25, 'Mathematics for ICT', 'MAT101', 1, 1, 1),
(26, 'Structured Programming', 'ICT102', 1, 1, 1),
(27, 'Operating Systems', 'ICT103', 1, 1, 1),
(28, 'Health Education', 'HED101', 1, 1, 1),
(29, 'Object Oriented Programming', 'ICT104', 1, 2, 1),
(30, 'Database Management Systems', 'ICT105', 1, 2, 1),
(31, 'System Analysis and Design', 'ICT106', 1, 2, 1),
(32, 'Web Development I', 'ICT107', 1, 2, 1),
(33, 'Network Essentials', 'ICT108', 1, 2, 1),
(34, 'Quantitative Skills', 'MAT102', 1, 2, 1),
(35, 'Web Development II (PHP)', 'ICT201', 2, 1, 1),
(36, 'Advanced Database Systems', 'ICT202', 2, 1, 1),
(37, 'Data Structures & Algorithms', 'ICT203', 2, 1, 1),
(38, 'Visual Programming', 'ICT204', 2, 1, 1),
(39, 'Management Information Systems', 'ICT205', 2, 1, 1),
(40, 'Research Methods', 'RES201', 2, 1, 1),
(41, 'Mobile Application Dev', 'ICT206', 2, 2, 1),
(42, 'Information Systems Security', 'ICT207', 2, 2, 1),
(43, 'Cloud Computing', 'ICT208', 2, 2, 1),
(44, 'Entrepreneurship Skills', 'ENT201', 2, 2, 1),
(45, 'Industrial Attachment', 'ATT201', 2, 2, 1),
(46, 'Final Year Project', 'PROJ201', 2, 2, 1),
(47, 'Software Engineering Fundamentals', 'SE101', 1, 1, 2),
(48, 'Discrete Mathematics', 'MAT105', 1, 1, 2),
(49, 'Introduction to C Programming', 'SE102', 1, 1, 2),
(50, 'Digital Logic Design', 'SE103', 1, 1, 2),
(51, 'Requirements Engineering', 'SE104', 1, 1, 2),
(52, 'Ethics in Engineering', 'ETH101', 1, 1, 2),
(53, 'Introduction to Cyber Security', 'CS101', 1, 1, 3),
(54, 'Linux Administration', 'CS102', 1, 1, 3),
(55, 'Network Security Fundamentals', 'CS103', 1, 1, 3),
(56, 'Ethical Hacking I', 'CS104', 1, 1, 3),
(57, 'Computer Forensics', 'CS105', 1, 1, 3),
(58, 'Security Governance', 'CS106', 1, 1, 3),
(59, 'Business Computing', 'BIS101', 1, 1, 4),
(60, 'Principles of Accounting', 'BIS102', 1, 1, 4),
(61, 'Management Info Systems', 'BIS103', 1, 1, 4),
(62, 'Financial Mathematics', 'BIS104', 1, 1, 4),
(63, 'Organizational Behavior', 'BIS105', 1, 1, 4),
(64, 'Business Communication', 'BIS106', 1, 1, 4),
(65, 'web', 'sit006', 1, 1, 2),
(67, 'cyber', 'DEG', 1, 1, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_code` (`course_code`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `marks`
--
ALTER TABLE `marks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reg_number` (`reg_number`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unit_code` (`unit_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `marks`
--
ALTER TABLE `marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fees`
--
ALTER TABLE `fees`
  ADD CONSTRAINT `fees_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
