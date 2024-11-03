-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2024 at 04:27 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mhac_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `name`, `email`, `password`, `role_id`, `created_at`) VALUES
(1, 'Admin', 'admin@must.ac.mw', '$2y$10$wJMRi0WXvVa0wkcC.ZS50uby/r2IpkNofo9wVQV2x7s.gyzsDWP8S', 3, '2024-10-26 22:32:33');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` datetime NOT NULL,
  `status` enum('Pending','Confirmed','Completed','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `student_id`, `doctor_id`, `appointment_date`, `status`, `created_at`) VALUES
(12, 1, 2, '2024-10-28 03:57:00', 'Confirmed', '2024-10-28 01:56:11');

-- --------------------------------------------------------

--
-- Table structure for table `conditions`
--

CREATE TABLE `conditions` (
  `condition_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `health_tips` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `conditions`
--

INSERT INTO `conditions` (`condition_id`, `name`, `description`, `health_tips`) VALUES
(1, 'Common Cold', 'Viral infection causing cough, sore throat, and runny nose', 'Seek immediate medical attention. Vaccines are available for prevention.'),
(2, 'Influenza', 'A contagious respiratory illness with fever, cough, and body aches', 'Drink plenty of fluids and avoid strenuous activities. Get the annual flu vaccine for prevention.'),
(3, 'Gastroenteritis', 'Inflammation of the stomach and intestines, causing vomiting and diarrhea', 'Stay hydrated; consider an electrolyte solution. Avoid solid foods until symptoms improve.'),
(4, 'COVID-19', 'Respiratory illness with fever, cough, and shortness of breath', 'Isolate yourself, monitor oxygen levels, and seek medical help if symptoms worsen.'),
(5, 'Hypertension', 'High blood pressure, often asymptomatic but may cause headaches', 'Regular exercise, a balanced diet, and stress management can help control blood pressure.'),
(6, 'Migraine', 'Severe headache often accompanied by nausea and sensitivity to light', 'Avoid known triggers and practice relaxation techniques. Consider speaking to a doctor about preventive medication.'),
(7, 'Pneumonia', 'Lung infection causing cough, fever, and chest pain', 'Rest and drink plenty of fluids. Seek medical advice to determine if antibiotics are needed.'),
(8, 'Asthma', 'Chronic condition causing breathing difficulty due to narrowed airways', 'Avoid triggers, carry an inhaler, and follow a doctor’s asthma action plan.'),
(9, 'Heart Disease', 'Conditions affecting the heart, causing chest pain and shortness of breath', 'Follow a heart-healthy diet, exercise regularly, and manage stress levels.'),
(10, 'Anemia', 'Low red blood cell count, causing fatigue and dizziness', 'Consume iron-rich foods and take prescribed supplements if necessary.'),
(11, 'Diabetes', 'A metabolic disease with high blood sugar levels and increased thirst', 'Monitor blood glucose levels, maintain a balanced diet, and stay physically active.'),
(12, 'Allergic Rhinitis', 'Allergic reaction causing runny nose and itchy eyes', 'Avoid allergens, consider antihistamines, and keep your living space clean.'),
(13, 'Rheumatoid Arthritis', 'Autoimmune disorder causing joint pain and swelling', 'Stay active with low-impact exercises. Anti-inflammatory medications may help.'),
(14, 'Liver Disease', 'Conditions affecting liver function, can cause jaundice and fatigue', 'Limit alcohol intake, maintain a balanced diet, and avoid unnecessary medications.'),
(15, 'Meningitis', 'Inflammation of brain membranes causing headache, fever, and neck stiffness', 'Seek immediate medical attention. Vaccines are available for prevention.');

-- --------------------------------------------------------

--
-- Table structure for table `conditionsymptoms`
--

CREATE TABLE `conditionsymptoms` (
  `condition_id` int(11) NOT NULL,
  `symptom_id` int(11) NOT NULL,
  `severity_weight` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `conditionsymptoms`
--

INSERT INTO `conditionsymptoms` (`condition_id`, `symptom_id`, `severity_weight`) VALUES
(1, 3, 2),
(1, 4, 1),
(1, 8, 1),
(2, 1, 3),
(2, 3, 2),
(2, 8, 1),
(3, 7, 2),
(3, 9, 3),
(4, 1, 3),
(4, 3, 2),
(4, 5, 3),
(4, 8, 1),
(5, 2, 1),
(5, 6, 2),
(6, 2, 3),
(6, 7, 2),
(7, 1, 2),
(7, 3, 2),
(7, 5, 3),
(8, 5, 3),
(8, 6, 2),
(9, 5, 3),
(9, 6, 3),
(10, 8, 2),
(10, 10, 1),
(11, 1, 2),
(11, 8, 1),
(11, 20, 3),
(12, 13, 2),
(12, 15, 2),
(12, 18, 1),
(13, 12, 3),
(13, 16, 2),
(14, 8, 1),
(14, 9, 2),
(14, 19, 3),
(15, 1, 3),
(15, 2, 2),
(15, 4, 3),
(15, 19, 3);

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `bio` text DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `years_of_experience` int(11) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `name`, `specialization`, `bio`, `phone_number`, `profile_picture`, `years_of_experience`, `email`) VALUES
(2, 'Dickson Dzinjalamala', 'General Practitioner', '', '0881044687', NULL, 3, 'doc1@must.ac.mw');

-- --------------------------------------------------------

--
-- Table structure for table `medicalrecords`
--

CREATE TABLE `medicalrecords` (
  `record_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `blood_group` varchar(5) DEFAULT NULL,
  `chronic_conditions` text DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `medicalrecords`
--

INSERT INTO `medicalrecords` (`record_id`, `student_id`, `weight`, `height`, `blood_group`, `chronic_conditions`, `allergies`, `created_at`) VALUES
(1, 2, '60.00', '200.00', 'O', 'Asthma', 'peanuts', '2024-10-30 04:07:15');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `type` enum('Appointment','Prescription','Message','System') NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `message`, `type`, `sent_at`, `is_read`) VALUES
(16, 6, 'New appointment request from Student ID: 1.', 'Appointment', '2024-10-28 01:56:11', 0),
(17, 4, 'Your appointment has been confirmed for 2024-10-28 03:57.', 'Appointment', '2024-10-28 01:56:41', 0);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` enum('Student','Doctor','Admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Student'),
(2, 'Doctor'),
(3, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `reg_number` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `program_of_study` varchar(50) NOT NULL,
  `year_of_admission` int(11) NOT NULL,
  `expected_graduation_year` int(11) NOT NULL,
  `status` enum('Active','Graduated','Withdrawn') NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `reg_number`, `email`, `name`, `program_of_study`, `year_of_admission`, `expected_graduation_year`, `status`, `date_of_birth`, `gender`, `phone_number`, `profile_picture`) VALUES
(1, '20240001', '20240001@must.ac.mw', 'John Doe', 'Computer Science', 2020, 2024, 'Active', '2000-02-15', 'Male', '0999888777', 'path/to/profile1.jpg'),
(2, '20240002', '20240002@must.ac.mw', 'Jane Smith', 'Business Administration', 2019, 2023, 'Graduated', '1999-04-22', 'Female', '0999666555', 'path/to/profile2.jpg'),
(3, '20240003', '20240003@must.ac.mw', 'Michael Johnson', 'Accounting', 2020, 2024, 'Active', '2000-06-13', 'Male', '0999777666', 'path/to/profile3.jpg'),
(4, '20240004', '20240004@must.ac.mw', 'Emily Davis', 'Nursing', 2018, 2022, 'Graduated', '1998-12-31', 'Female', '0999555444', 'path/to/profile4.jpg'),
(5, '20240005', '20240005@must.ac.mw', 'Chris Brown', 'Computer Science', 2022, 2026, 'Active', '2002-01-18', 'Male', '0999444333', 'path/to/profile5.jpg'),
(6, '20240006', '20240006@must.ac.mw', 'Jessica Miller', 'Law', 2017, 2021, 'Graduated', '1997-08-07', 'Female', '0999333222', 'path/to/profile6.jpg'),
(7, '20240007', '20240007@must.ac.mw', 'David Wilson', 'Education', 2018, 2022, 'Withdrawn', '1999-09-12', 'Male', '0999222111', 'path/to/profile7.jpg'),
(8, '20240008', '20240008@must.ac.mw', 'Anna Moore', 'Computer Science', 2021, 2025, 'Active', '2000-03-10', 'Female', '0999111100', 'path/to/profile8.jpg'),
(9, '20240009', '20240009@must.ac.mw', 'James Anderson', 'Engineering', 2019, 2023, 'Active', '1999-11-23', 'Male', '0999000099', 'path/to/profile9.jpg'),
(10, '20240010', '20240010@must.ac.mw', 'Laura Taylor', 'Medicine', 2020, 2024, 'Active', '2001-05-05', 'Female', '0998998899', 'path/to/profile10.jpg'),
(11, '20240011', '20240011@must.ac.mw', 'Daniel Thomas', 'Architecture', 2017, 2021, 'Graduated', '1997-07-17', 'Male', '0998777665', 'path/to/profile11.jpg'),
(12, '20240012', '20240012@must.ac.mw', 'Sophia Martinez', 'Pharmacy', 2020, 2024, 'Active', '2000-10-26', 'Female', '0998666554', 'path/to/profile12.jpg'),
(13, '20240013', '20240013@must.ac.mw', 'Matthew Harris', 'Engineering', 2019, 2023, 'Withdrawn', '2000-12-01', 'Male', '0998555443', 'path/to/profile13.jpg'),
(14, '20240014', '20240014@must.ac.mw', 'Olivia Lewis', 'Psychology', 2020, 2024, 'Active', '2000-09-20', 'Female', '0998444332', 'path/to/profile14.jpg'),
(15, '20240015', '20240015@must.ac.mw', 'Henry Walker', 'Economics', 2021, 2025, 'Active', '2002-06-06', 'Male', '0998333221', 'path/to/profile15.jpg'),
(16, '20240016', '20240016@must.ac.mw', 'Lily Young', 'Law', 2017, 2021, 'Graduated', '1997-04-08', 'Female', '0998222110', 'path/to/profile16.jpg'),
(17, '20240017', '20240017@must.ac.mw', 'Benjamin Hall', 'Medicine', 2020, 2024, 'Active', '1999-05-15', 'Male', '0998111099', 'path/to/profile17.jpg'),
(18, '20240018', '20240018@must.ac.mw', 'Grace King', 'Education', 2019, 2023, 'Withdrawn', '2000-11-19', 'Female', '0998000088', 'path/to/profile18.jpg'),
(19, '20240019', '20240019@must.ac.mw', 'Samuel Scott', 'Pharmacy', 2020, 2024, 'Active', '2001-09-04', 'Male', '0997999977', 'path/to/profile19.jpg'),
(20, '20240020', '20240020@must.ac.mw', 'Isabella Allen', 'Business Administration', 2022, 2026, 'Active', '2002-02-12', 'Female', '0997888866', 'path/to/profile20.jpg'),
(21, '20240021', '20240021@must.ac.mw', 'Tyler Evans', 'Engineering', 2018, 2022, 'Graduated', '1998-03-03', 'Male', '0997777755', 'path/to/profile21.jpg'),
(22, '20240022', '20240022@must.ac.mw', 'Samantha Turner', 'Nursing', 2017, 2021, 'Graduated', '1997-07-14', 'Female', '0997666644', 'path/to/profile22.jpg'),
(23, '20240023', '20240023@must.ac.mw', 'Ethan Ward', 'Computer Science', 2020, 2024, 'Active', '2001-06-28', 'Male', '0997555533', 'path/to/profile23.jpg'),
(24, '20240024', '20240024@must.ac.mw', 'Ava Wright', 'Law', 2021, 2025, 'Active', '2002-08-19', 'Female', '0997444422', 'path/to/profile24.jpg'),
(25, '20240025', '20240025@must.ac.mw', 'Logan Carter', 'Medicine', 2020, 2024, 'Active', '1999-12-12', 'Male', '0997333311', 'path/to/profile25.jpg'),
(26, '20240026', '20240026@must.ac.mw', 'Natalie Baker', 'Economics', 2019, 2023, 'Active', '2001-01-24', 'Female', '0997222200', 'path/to/profile26.jpg'),
(27, '20240027', '20240027@must.ac.mw', 'Brandon Green', 'Psychology', 2020, 2024, 'Withdrawn', '2000-10-01', 'Male', '0997111199', 'path/to/profile27.jpg'),
(28, '20240028', '20240028@must.ac.mw', 'Mia Adams', 'Business Administration', 2018, 2022, 'Graduated', '1998-05-20', 'Female', '0997000088', 'path/to/profile28.jpg'),
(29, '20240029', '20240029@must.ac.mw', 'Lucas Nelson', 'Accounting', 2021, 2025, 'Active', '2002-04-17', 'Male', '0996999977', 'path/to/profile29.jpg'),
(30, '20240030', '20240030@must.ac.mw', 'Zoe Rivera', 'Pharmacy', 2022, 2026, 'Active', '2002-03-03', 'Female', '0996888866', 'path/to/profile30.jpg'),
(31, '20240031', '20240031@must.ac.mw', 'Jason Perez', 'Engineering', 2019, 2023, 'Active', '2000-12-10', 'Male', '0996777755', 'path/to/profile31.jpg'),
(32, '20240032', '20240032@must.ac.mw', 'Lily White', 'Education', 2017, 2021, 'Graduated', '1997-09-25', 'Female', '0996666644', 'path/to/profile32.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `symptoms`
--

CREATE TABLE `symptoms` (
  `symptom_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `symptoms`
--

INSERT INTO `symptoms` (`symptom_id`, `name`, `description`) VALUES
(1, 'Fever', 'Elevated body temperature'),
(2, 'Headache', 'Pain in the head region'),
(3, 'Cough', 'Expulsion of air from the lungs'),
(4, 'Sore throat', 'Pain or irritation in the throat'),
(5, 'Shortness of breath', 'Difficulty in breathing'),
(6, 'Chest pain', 'Pain or discomfort in the chest area'),
(7, 'Nausea', 'Feeling of wanting to vomit'),
(8, 'Fatigue', 'Feeling of tiredness or exhaustion'),
(9, 'Abdominal pain', 'Pain in the stomach region'),
(10, 'Dizziness', 'Sensation of spinning or lightheadedness'),
(11, 'Loss of Appetite', 'Reduced desire to eat'),
(12, 'Joint Pain', 'Pain in joints of the body'),
(13, 'Runny Nose', 'Discharge of mucus from the nose'),
(14, 'Muscle Pain', 'Pain in muscles across the body'),
(15, 'Itching', 'Sensation that causes a desire to scratch'),
(16, 'Swelling', 'Enlarged or puffed-up area on the body'),
(17, 'Vomiting', 'Expelling contents from the stomach through the mouth'),
(18, 'Rash', 'Red, inflamed area of the skin'),
(19, 'Confusion', 'Difficulty thinking clearly or making decisions'),
(20, 'Excessive Thirst', 'Unusually high need to drink fluids');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `student_id`, `doctor_id`, `email`, `password`, `role_id`, `created_at`) VALUES
(4, 1, NULL, '20240001@must.ac.mw', '$2y$10$qtN1bBB95IKhKMz11Phg2.R2imQIfogTM2mipXGaaj6V1lRqw.dby', 1, '2024-10-27 02:01:56'),
(5, 2, NULL, '20240002@must.ac.mw', '$2y$10$k5KhUrLQ6mfa4bUUI4FU3uBh9Tkvfd70qNpTUTIqQnS8e9Ifvi/8G', 1, '2024-10-27 02:34:09'),
(6, NULL, 2, 'doc1@must.ac.mw', '$2y$10$ziGQu3Ox199Q8VjNd88MfueqfUE52U0k08eKWScIRcVMAPZwUNT2q', 2, '2024-10-27 06:38:21'),
(7, 6, NULL, '20240006@must.ac.mw', '$2y$10$TgVOErz.Fa8BO5sindo21O5cLmWyVrtUMKXokzlgv4tn56aTFI8ym', 1, '2024-10-31 03:45:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `conditions`
--
ALTER TABLE `conditions`
  ADD PRIMARY KEY (`condition_id`);

--
-- Indexes for table `conditionsymptoms`
--
ALTER TABLE `conditionsymptoms`
  ADD PRIMARY KEY (`condition_id`,`symptom_id`),
  ADD KEY `symptom_id` (`symptom_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `medicalrecords`
--
ALTER TABLE `medicalrecords`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `reg_number` (`reg_number`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `symptoms`
--
ALTER TABLE `symptoms`
  ADD PRIMARY KEY (`symptom_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `doctor_id` (`doctor_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `conditions`
--
ALTER TABLE `conditions`
  MODIFY `condition_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `medicalrecords`
--
ALTER TABLE `medicalrecords`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `symptoms`
--
ALTER TABLE `symptoms`
  MODIFY `symptom_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`);

--
-- Constraints for table `conditionsymptoms`
--
ALTER TABLE `conditionsymptoms`
  ADD CONSTRAINT `conditionsymptoms_ibfk_1` FOREIGN KEY (`condition_id`) REFERENCES `conditions` (`condition_id`),
  ADD CONSTRAINT `conditionsymptoms_ibfk_2` FOREIGN KEY (`symptom_id`) REFERENCES `symptoms` (`symptom_id`);

--
-- Constraints for table `medicalrecords`
--
ALTER TABLE `medicalrecords`
  ADD CONSTRAINT `medicalrecords_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`),
  ADD CONSTRAINT `users_ibfk_3` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
