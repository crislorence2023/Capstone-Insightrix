-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2024 at 01:01 PM
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
-- Database: `evaluation_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_list`
--

CREATE TABLE `academic_list` (
  `id` int(30) NOT NULL,
  `year` text NOT NULL,
  `semester` int(30) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `status` int(1) NOT NULL DEFAULT 0 COMMENT '0=Pending,1=Start,2=Closed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_list`
--

INSERT INTO `academic_list` (`id`, `year`, `semester`, `is_default`, `status`) VALUES
(1, '2019-2020', 1, 0, 1),
(2, '2019-2020', 2, 0, 2),
(3, '2020-2021', 1, 0, 2),
(4, '2022-2023', 1, 0, 2),
(5, '2023-2024', 1, 0, 2),
(6, '2024-2025', 1, 0, 1),
(7, '2026-2027', 1, 0, 2),
(8, '2027-2028', 1, 0, 1),
(9, '2027-2028', 2, 1, 1),
(10, '2029-2030', 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `class_list`
--

CREATE TABLE `class_list` (
  `id` int(30) NOT NULL,
  `curriculum` text NOT NULL,
  `level` text NOT NULL,
  `section` text NOT NULL,
  `department` varchar(200) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_list`
--

INSERT INTO `class_list` (`id`, `curriculum`, `level`, `section`, `department`) VALUES
(3, 'BSIT', '1', '1', 'COT'),
(6, '', '2024', '2A', 'COE'),
(7, 'BSIT', '1', '1A', 'COT'),
(8, '', '4A', 'BSIT', 'COT'),
(9, '', '1-4', '', 'COT(Irregular)');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(30) NOT NULL,
  `academic_id` int(30) NOT NULL,
  `user_id` int(30) NOT NULL,
  `comment` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `criteria_list`
--

CREATE TABLE `criteria_list` (
  `id` int(30) NOT NULL,
  `criteria` text NOT NULL,
  `order_by` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `criteria_list`
--

INSERT INTO `criteria_list` (`id`, `criteria`, `order_by`) VALUES
(2, ' Knowledge and Expertise', 1),
(4, 'Teaching Methods Communication', 2),
(5, 'Classroom Management', 0),
(7, 'Assessment and Feedback', 3),
(8, 'Support and Accessibility', 5),
(9, 'Professionalism and Attitude', 6),
(10, ' Overall Satisfaction', 4);

-- --------------------------------------------------------

--
-- Table structure for table `department_list`
--

CREATE TABLE `department_list` (
  `id` int(30) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_list`
--

INSERT INTO `department_list` (`id`, `name`, `description`, `date_created`) VALUES
(2, 'COT', '', '2024-11-06 12:33:15'),
(3, 'Test', '', '2024-11-06 14:08:39'),
(5, 'asdsas', 'asdasd', '2024-11-06 14:27:48');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_answers`
--

CREATE TABLE `evaluation_answers` (
  `evaluation_id` int(30) NOT NULL,
  `question_id` int(30) NOT NULL,
  `rate` int(20) NOT NULL,
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation_answers`
--

INSERT INTO `evaluation_answers` (`evaluation_id`, `question_id`, `rate`, `comment`) VALUES
(140, 50, 5, NULL),
(140, 51, 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_list`
--

CREATE TABLE `evaluation_list` (
  `evaluation_id` int(30) NOT NULL,
  `academic_id` int(30) NOT NULL,
  `class_id` int(30) NOT NULL,
  `student_id` int(30) NOT NULL,
  `subject_id` int(30) NOT NULL,
  `faculty_id` int(30) NOT NULL,
  `restriction_id` int(30) NOT NULL,
  `date_taken` datetime NOT NULL DEFAULT current_timestamp(),
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty_list`
--

CREATE TABLE `faculty_list` (
  `id` int(30) NOT NULL,
  `school_id` varchar(100) NOT NULL,
  `firstname` varchar(200) NOT NULL,
  `lastname` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `department` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `avatar` text NOT NULL DEFAULT 'no-image-available.png',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `verification_code` varchar(6) DEFAULT NULL,
  `verification_code_expiry` datetime DEFAULT NULL,
  `is_password_changed` tinyint(1) NOT NULL DEFAULT 0,
  `email_verified_at` datetime DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_list`
--

INSERT INTO `faculty_list` (`id`, `school_id`, `firstname`, `lastname`, `email`, `department`, `password`, `avatar`, `date_created`, `verification_code`, `verification_code_expiry`, `is_password_changed`, `email_verified_at`, `reset_token`, `reset_token_expiry`) VALUES
(4, 'cris', 'Fatima', 'pasilang', 'fatima.pasilang@gmail.com', 'COT', '$2y$10$YQWCmkPbTYWJFaUvoiDM9.4WbpKtYO9LjEj/JNPr.FCNt1BAITquC', 'no-image-available.png', '2024-10-11 12:20:59', NULL, NULL, 1, NULL, NULL, NULL),
(5, '3210828', 'crislyn', 'pasilang', 'crislyn.pasilang@gmail.com', 'COT', '$2y$10$de9nYJrGFPNK/Q35uRIJreEWg0DDtTwNNOQxxGbhX8CPFeMuGvSWq', '1728874920_193900704_4747561108604440_1498018246974954191_n.jpg', '2024-10-14 11:02:14', NULL, NULL, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `email` varchar(200) NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `attempt_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(30) NOT NULL,
  `faculty_id` int(30) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question_list`
--

CREATE TABLE `question_list` (
  `id` int(30) NOT NULL,
  `academic_id` int(30) NOT NULL,
  `question` text NOT NULL,
  `order_by` int(30) NOT NULL,
  `criteria_id` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question_list`
--

INSERT INTO `question_list` (`id`, `academic_id`, `question`, `order_by`, `criteria_id`) VALUES
(1, 3, 'Sample Question', 0, 1),
(3, 3, 'Test', 3, 2),
(5, 0, 'Question 101', 0, 1),
(6, 3, 'Sample 101', 1, 1),
(7, 3, 'did he do well in teaching?', 2, 1),
(8, 3, 'werwerwerwererwerwe', 4, 1),
(9, 3, 'teaching', 5, 1),
(10, 4, 'sdfsdf', 0, 1),
(11, 4, 'asfqwew', 1, 0),
(12, 4, 'wer23wer', 2, 0),
(13, 4, 'sdfds', 3, 1),
(14, 4, 'sdfwerwe', 4, 1),
(15, 4, '3423rwerwe', 5, 2),
(16, 5, 'eqweqwe', 0, 1),
(17, 5, 'qweqwe', 1, 2),
(18, 6, 'qweqweqweasAS', 0, 1),
(19, 6, 'qweqweqwe', 3, 1),
(21, 6, 'werwer', 2, 1),
(24, 6, 'test 1', 1, 1),
(25, 6, 'The teacher demonstrates a thorough understanding of the subject.', 0, 2),
(26, 6, 'The teacher explains complex concepts in a way that is easy to understand.', 1, 2),
(27, 6, 'The teacher stays updated with recent developments in the subject matter.', 2, 2),
(29, 6, 'The teacher uses a variety of teaching methods to aid learning.', 0, 4),
(30, 6, 'The teacher communicates clearly and effectively.', 1, 4),
(31, 6, 'The teacher maintains a respectful and positive classroom environment.', 1, 5),
(32, 6, 'The teacher manages classroom activities efficiently.', 2, 5),
(34, 6, 'The teacher makes the class interesting and engaging.', 11, 6),
(35, 6, 'The teacher encourages student participation in class discussions.', 0, 5),
(36, 6, 'The teacher provides timely feedback on assignments and exams.', 0, 7),
(37, 6, 'The feedback given by the teacher helps improve my understanding of the subject.', 1, 7),
(38, 6, 'The teacher is approachable when I need help or clarification.', 0, 8),
(39, 6, 'The teacher shows concern for students’ learning and progress.', 1, 8),
(40, 6, 'The teacher is respectful towards all students.', 0, 9),
(41, 6, 'The teacher is enthusiastic and passionate about teaching.', 1, 9),
(42, 6, 'Overall, I am satisfied with the teaching and guidance provided by this teacher.', 0, 10),
(43, 6, 'I would recommend this teacher to other students.', 1, 10),
(44, 7, 'test 1', 0, 5),
(45, 7, 'test 2', 1, 0),
(46, 7, 'test 2', 2, 5),
(47, 7, 'test 3', 3, 2),
(48, 8, 'TEST', 0, 2),
(49, 8, 'Test', 1, 5),
(50, 9, 'test', 0, 5),
(51, 9, 'test2', 1, 2),
(52, 10, 'sdfsdf', 0, 5),
(53, 10, 'qweqweqw', 1, 2),
(54, 10, 'qweqwe', 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `restriction_list`
--

CREATE TABLE `restriction_list` (
  `id` int(30) NOT NULL,
  `academic_id` int(30) NOT NULL,
  `faculty_id` int(30) NOT NULL,
  `class_id` int(30) NOT NULL,
  `subject_id` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(30) NOT NULL,
  `firstname` varchar(200) NOT NULL,
  `lastname` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `avatar` text NOT NULL DEFAULT 'no-image-available.png',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `is_password_changed` tinyint(1) NOT NULL DEFAULT 0,
  `verification_code` varchar(6) DEFAULT NULL,
  `verification_code_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `firstname`, `lastname`, `email`, `password`, `avatar`, `date_created`, `is_password_changed`, `verification_code`, `verification_code_expiry`) VALUES
(10, 'asdasdas', 'pasilangqweqwe', 'crislorence2020@gmail.com', '$2y$10$rBEDPbvdSiQgg0RGiJtVFOaBNZI1qIhV9KV/1RKrLyS10GkKHusC.', 'no-image-available.png', '2024-10-22 22:52:10', 1, NULL, NULL),
(11, 'sdfkdsjk', 'sdkfkds', 'lusette.pasilang82@gmail.com', '$2y$10$p3ijyst7rKnxhhloAfVkZeA4NygyIF./YA3Bj/HlObhEd3fmXET12', 'no-image-available.png', '2024-10-28 14:55:09', 0, NULL, NULL),
(12, 'crislorence', 'pasilang', 'Renrenpasilang@gmail.com', '$2y$10$Qnyw.bQg5WzibGOMdT7Aj.OCTK6OA8xu8/2hPRZuBUnnxuwDJbYgO', 'no-image-available.png', '2024-11-02 09:39:23', 0, NULL, NULL),
(13, 'crislorence', 'reee', 'admin@admin.com', '$2y$10$cw2qOV7ZxXQzznsbbZ76N.QntOF.WOPSlyj8MzK9SZncslrNzbSBO', 'no-image-available.png', '2024-11-02 09:45:15', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_list`
--

CREATE TABLE `student_list` (
  `id` int(30) NOT NULL,
  `school_id` varchar(100) NOT NULL,
  `firstname` varchar(200) NOT NULL,
  `lastname` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `class_id` int(30) NOT NULL,
  `avatar` text NOT NULL DEFAULT 'no-image-available.png',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `verification_code` varchar(6) DEFAULT NULL,
  `verification_code_expiry` datetime DEFAULT NULL,
  `is_password_changed` tinyint(1) NOT NULL DEFAULT 0,
  `email_verified_at` datetime DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_list`
--

INSERT INTO `student_list` (`id`, `school_id`, `firstname`, `lastname`, `email`, `password`, `class_id`, `avatar`, `date_created`, `verification_code`, `verification_code_expiry`, `is_password_changed`, `email_verified_at`, `reset_token`, `reset_token_expiry`) VALUES
(1, '6231415', 'John', 'Smith', 'jsmith@sample.com', '41b05d8cf142ae773a67920ec1facced', 3, '1608012360_avatar.jpg', '2020-12-15 14:06:14', NULL, NULL, 1, NULL, NULL, NULL),
(2, '101497', 'Claire', 'Blake', 'cblake@sample.com', '4744ddea876b11dcb1d169fadf494418', 2, '1608012720_47446233-clean-noir-et-gradient-sombre-image-de-fond-abstrait-.jpg', '2020-12-15 14:12:03', NULL, NULL, 1, NULL, NULL, NULL),
(3, '123', 'Mike', 'Williams', 'mwilliams@sample.com', '3cc93e9a6741d8b40460457139cf8ced', 1, '1608034680_1605601740_download.jpg', '2020-12-15 20:18:22', NULL, NULL, 1, NULL, NULL, NULL),
(5, '3210829', 'lusette', 'test20', 'crislorence2020@gmail.com', '$2y$10$7v5SSz4T2gz0b2gSRdhdDu17T/v6pbqKe8DZ1UtST/j26xn0Q3N2e', 7, '1729929240_time3.png', '2024-10-06 13:07:31', NULL, NULL, 1, NULL, '984e83ea153aec9b98192098489978a721e21fffd08b68b0a995d8e6772ceb38', '2024-10-27 18:08:47'),
(6, '3210828', 'crislorence', 'pasilang', 'crislorence.pasilang@ctu.edu.ph', '$argon2id$v=19$m=65536,t=4,p=2$N1daSnV3WGJBcXNtemg4eg$fJSGASoRehDDotzhLwxrOrHWomgBfv6XqxW1NkG18ss', 7, '1728533880_UML final 2.drawio.png', '2024-10-10 12:18:55', NULL, NULL, 1, '2024-10-27 14:57:22', NULL, NULL),
(9, '12345', 'test', 'test', '', '$2y$10$MBz3V/9iPbwZRA/.3iH/Ye4dNMIL2sOKikBjuoY5QltIDCvqAkMVK', 3, 'no-image-available.png', '2024-10-18 09:35:59', 'c3a66a', '2024-10-18 11:37:02', 0, '2024-10-18 10:24:01', NULL, NULL),
(10, '1234', 'this is a test', 'a test', 'peachyzoril@gmail.com', '$2y$10$MqXKSNHiV2ZmM5yDA9C2ye0BqFzXT0GZ6aEDgP50A5wnnKuAw5yhO', 7, 'no-image-available.png', '2024-10-27 15:05:00', NULL, NULL, 1, '2024-10-27 17:29:41', NULL, NULL),
(11, '3210828', 'Jackielyn Rose', 'Verzosa', 'none@gmail.com', '$2y$10$EUJrguxtV.5J7UIhZ.JQLu/bHCsP9quC9qV5aEih61BXH8rn4smYq', 8, 'no-image-available.png', '2024-10-28 12:12:35', NULL, NULL, 0, NULL, NULL, NULL),
(12, '3210822', 'Belinda', 'Bayking', '', '$2y$10$9WdLe3jszID2/ijDcVjvV.GkJ7zG.O5a8eks5g5BTOUtJ93zKXZOK', 8, 'no-image-available.png', '2024-10-28 12:28:21', NULL, NULL, 0, NULL, NULL, NULL),
(13, '3210825', 'Hannah Rose', 'Asenjo', '', '$2y$10$xFU./Stm5JFq5ixd.PJPh.WYJ8j1Yq5mdY.luhVAWgGdZALByFdVu', 8, 'no-image-available.png', '2024-10-28 12:30:43', NULL, NULL, 0, NULL, NULL, NULL),
(14, '3210845', 'Ruthchelle', 'Ponce', '', '$2y$10$Gte1rd6H88Bl2htOZFuli.lGItmtsNndjWlHcB8w0zRT8MiKU1JkK', 8, 'no-image-available.png', '2024-10-28 12:31:57', NULL, NULL, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subject_list`
--

CREATE TABLE `subject_list` (
  `id` int(30) NOT NULL,
  `code` varchar(50) NOT NULL,
  `subject` text NOT NULL,
  `description` text NOT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject_list`
--

INSERT INTO `subject_list` (`id`, `code`, `subject`, `description`, `department`) VALUES
(1, '101', 'Sample Subject', 'Test 101', 'COE'),
(5, 'ENG-01', 'ENGLISH', '', 'COEED'),
(6, '970219', 'P elec 2', '', 'COE'),
(7, '1234', 'science', '', 'COT'),
(8, 'werqwe', 'testsubject', 'asdasd', 'COT'),
(9, '12345', 'Testsubject', 'no one', 'COT');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `cover_img` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `name`, `email`, `contact`, `address`, `cover_img`) VALUES
(1, 'Insightrix', 'info@sample.comm', '+6948 8542 623', '2102  Caldwell Road, Rochester, New York, 14608', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(30) NOT NULL,
  `firstname` varchar(200) NOT NULL,
  `lastname` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `avatar` text NOT NULL DEFAULT 'no-image-available.png',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `is_password_changed` tinyint(1) NOT NULL DEFAULT 0,
  `verification_code` varchar(6) DEFAULT NULL,
  `verification_code_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `password`, `avatar`, `date_created`, `is_password_changed`, `verification_code`, `verification_code_expiry`) VALUES
(1, 'Administrator', '', 'admin@admin.com', '$2y$10$24W./JOfaz/iEQTp7rYxA.nn2HUWFDtUvvTdUhn26GSu.3xqiRiOS', '1607135820_avatar.jpg', '2020-11-26 10:57:04', 0, NULL, NULL),
(2, 'Cris', 'Pasilang', 'crislorence2020@gmail.com', '227dc1a7dd7f0d22e2e69073dc26a197', '1726989600_459591987_429166080184045_9047589825170809984_n.jpg', '2024-09-22 15:20:21', 0, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_list`
--
ALTER TABLE `academic_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_list`
--
ALTER TABLE `class_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `academic_id` (`academic_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `criteria_list`
--
ALTER TABLE `criteria_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department_list`
--
ALTER TABLE `department_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `department_name` (`name`);

--
-- Indexes for table `evaluation_list`
--
ALTER TABLE `evaluation_list`
  ADD PRIMARY KEY (`evaluation_id`);

--
-- Indexes for table `faculty_list`
--
ALTER TABLE `faculty_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `idx_faculty_created` (`faculty_id`,`created_at`);

--
-- Indexes for table `question_list`
--
ALTER TABLE `question_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restriction_list`
--
ALTER TABLE `restriction_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `student_list`
--
ALTER TABLE `student_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subject_list`
--
ALTER TABLE `subject_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_list`
--
ALTER TABLE `academic_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `class_list`
--
ALTER TABLE `class_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `criteria_list`
--
ALTER TABLE `criteria_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `department_list`
--
ALTER TABLE `department_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `evaluation_list`
--
ALTER TABLE `evaluation_list`
  MODIFY `evaluation_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT for table `faculty_list`
--
ALTER TABLE `faculty_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `question_list`
--
ALTER TABLE `question_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `restriction_list`
--
ALTER TABLE `restriction_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=306;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `student_list`
--
ALTER TABLE `student_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `subject_list`
--
ALTER TABLE `subject_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`academic_id`) REFERENCES `academic_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty_list` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
