-- Additional Tables for Student Attendance System
-- 6 New Tables to Complete the 10 Tables Requirement

-- --------------------------------------------------------

--
-- Table structure for table `classes`
-- Stores class/section information
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  `section` varchar(10) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `max_students` int(11) DEFAULT 40,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `class_name`, `section`, `teacher_id`, `academic_year`, `max_students`) VALUES
(1, 'Grade 10', 'A', 8, '2024-2025', 35),
(2, 'Grade 10', 'B', 8, '2024-2025', 35),
(3, 'Grade 11', 'A', 8, '2024-2025', 30),
(4, 'Grade 9', 'A', 8, '2024-2025', 40),
(5, 'Grade 12', 'A', 8, '2024-2025', 25);

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
-- Stores assignment information and submissions
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `assigned_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `max_marks` int(11) DEFAULT 100,
  `status` enum('active','completed','expired') DEFAULT 'active',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `title`, `description`, `subject_id`, `teacher_id`, `class_id`, `assigned_date`, `due_date`, `max_marks`, `status`) VALUES
(1, 'Mathematics Homework Chapter 5', 'Complete all exercises from chapter 5 including word problems', 1, 8, 1, '2024-03-20', '2024-03-27', 50, 'active'),
(2, 'Science Project', 'Create a presentation on renewable energy sources', 2, 8, 1, '2024-03-18', '2024-04-01', 100, 'active'),
(3, 'English Essay', 'Write a 500-word essay on environmental conservation', 3, 8, 2, '2024-03-22', '2024-03-29', 75, 'active'),
(4, 'Computer Programming Task', 'Complete the Python programming exercises', 4, 8, 3, '2024-03-19', '2024-03-26', 80, 'completed'),
(5, 'Drama Performance', 'Prepare a 5-minute monologue', 5, 8, 1, '2024-03-21', '2024-03-28', 60, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submissions`
-- Stores student assignment submissions
--

CREATE TABLE `assignment_submissions` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `submission_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `file_path` varchar(255) DEFAULT NULL,
  `marks_obtained` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('submitted','graded','late') DEFAULT 'submitted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignment_submissions`
--

INSERT INTO `assignment_submissions` (`id`, `assignment_id`, `student_id`, `submission_date`, `file_path`, `marks_obtained`, `remarks`, `status`) VALUES
(1, 1, 1, '2024-03-25 10:30:00', 'uploads/math_ch5_adnan.pdf', 45, 'Excellent work! All problems solved correctly.', 'graded'),
(2, 1, 2, '2024-03-26 14:20:00', 'uploads/math_ch5_abhinav.pdf', 38, 'Good effort, some calculation errors in Q3.', 'graded'),
(3, 2, 1, '2024-03-28 16:45:00', 'uploads/science_project_adnan.pptx', 92, 'Outstanding presentation with great visuals!', 'graded'),
(4, 3, 3, '2024-03-27 11:15:00', 'uploads/english_essay_ishu.docx', 68, 'Well-written essay, needs better grammar check.', 'graded'),
(5, 4, 4, '2024-03-24 09:00:00', 'uploads/python_exercises_pankaj.zip', 75, 'All programs working correctly.', 'graded');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
-- Stores exam information
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `exam_name` varchar(100) NOT NULL,
  `exam_type` enum('midterm','final','quiz','assignment_test') DEFAULT 'midterm',
  `subject_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `exam_date` date DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT 60,
  `max_marks` int(11) DEFAULT 100,
  `status` enum('upcoming','ongoing','completed','cancelled') DEFAULT 'upcoming',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `exam_name`, `exam_type`, `subject_id`, `class_id`, `exam_date`, `duration_minutes`, `max_marks`, `status`) VALUES
(1, 'Midterm Mathematics', 'midterm', 1, 1, '2024-03-15', 90, 100, 'completed'),
(2, 'Final Science Exam', 'final', 2, 1, '2024-04-10', 120, 100, 'upcoming'),
(3, 'English Quiz', 'quiz', 3, 2, '2024-03-20', 30, 50, 'completed'),
(4, 'Computer Practical Test', 'assignment_test', 4, 3, '2024-03-25', 60, 80, 'completed'),
(5, 'Drama Performance Assessment', 'final', 5, 1, '2024-03-30', 45, 60, 'upcoming');

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
-- Stores class schedule/timetable information
--

CREATE TABLE `timetable` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `room_number` varchar(20) DEFAULT NULL,
  `semester` varchar(20) DEFAULT 'Fall 2024'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timetable`
--

INSERT INTO `timetable` (`id`, `class_id`, `subject_id`, `teacher_id`, `day_of_week`, `start_time`, `end_time`, `room_number`, `semester`) VALUES
(1, 1, 1, 8, 'Monday', '09:00:00', '10:00:00', 'Room 101', 'Fall 2024'),
(2, 1, 2, 8, 'Monday', '10:30:00', '11:30:00', 'Room 102', 'Fall 2024'),
(3, 1, 3, 8, 'Tuesday', '09:00:00', '10:00:00', 'Room 103', 'Fall 2024'),
(4, 1, 4, 8, 'Tuesday', '10:30:00', '11:30:00', 'Lab 1', 'Fall 2024'),
(5, 1, 5, 8, 'Wednesday', '14:00:00', '15:00:00', 'Room 105', 'Fall 2024'),
(6, 2, 1, 8, 'Monday', '11:00:00', '12:00:00', 'Room 201', 'Fall 2024'),
(7, 2, 2, 8, 'Tuesday', '11:00:00', '12:00:00', 'Room 202', 'Fall 2024'),
(8, 3, 3, 8, 'Wednesday', '09:00:00', '10:00:00', 'Room 301', 'Fall 2024'),
(9, 4, 4, 8, 'Thursday', '10:00:00', '11:00:00', 'Lab 2', 'Fall 2024'),
(10, 5, 5, 8, 'Friday', '09:00:00', '10:00:00', 'Room 401', 'Fall 2024');

-- --------------------------------------------------------

--
-- Table structure for table `notices`
-- Stores notices and announcements
--

CREATE TABLE `notices` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `notice_type` enum('general','exam','holiday','event','urgent') DEFAULT 'general',
  `target_audience` enum('all','students','teachers','parents','admin') DEFAULT 'all',
  `posted_by` int(11) NOT NULL,
  `posted_date` date DEFAULT CURRENT_DATE,
  `expiry_date` date DEFAULT NULL,
  `status` enum('active','expired','archived') DEFAULT 'active',
  `attachment_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notices`
--

INSERT INTO `notices` (`id`, `title`, `content`, `notice_type`, `target_audience`, `posted_by`, `posted_date`, `expiry_date`, `status`, `attachment_path`) VALUES
(1, 'Annual Sports Day Announcement', 'The annual sports day will be held on 15th April 2024. All students are requested to participate actively.', 'event', 'all', 9, '2024-03-20', '2024-04-15', 'active', NULL),
(2, 'Midterm Examination Schedule', 'Midterm examinations will start from 25th March 2024. Please check the detailed schedule on the notice board.', 'exam', 'students', 9, '2024-03-18', '2024-03-25', 'active', 'uploads/exam_schedule.pdf'),
(3, 'Holiday Notice - Spring Break', 'School will remain closed from 1st April to 7th April for spring break.', 'holiday', 'all', 9, '2024-03-15', '2024-04-07', 'active', NULL),
(4, 'Parent-Teacher Meeting', 'Parent-teacher meeting scheduled for 30th March 2024 at 10:00 AM. All parents are requested to attend.', 'general', 'parents', 9, '2024-03-22', '2024-03-30', 'active', NULL),
(5, 'Lab Safety Guidelines', 'Important safety guidelines for computer lab usage. All students must follow these rules strictly.', 'urgent', 'students', 8, '2024-03-10', '2024-06-10', 'active', 'uploads/lab_safety.pdf');

-- --------------------------------------------------------

--
-- Indexes for new tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posted_by` (`posted_by`);

--
-- AUTO_INCREMENT for new tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for new tables
--

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_ibfk_3` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD CONSTRAINT `assignment_submissions_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignment_submissions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exams_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `timetable`
--
ALTER TABLE `timetable`
  ADD CONSTRAINT `timetable_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `timetable_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `timetable_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notices`
--
ALTER TABLE `notices`
  ADD CONSTRAINT `notices_ibfk_1` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

COMMIT;
