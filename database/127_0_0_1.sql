-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2024 at 02:34 PM
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
-- Database: `thesis_mgmt`
--
CREATE DATABASE IF NOT EXISTS `thesis_mgmt` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `thesis_mgmt`;

-- --------------------------------------------------------

--
-- Table structure for table `adviser`
--

CREATE TABLE `adviser` (
  `AdviserId` int(11) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `CreatedDate` datetime NOT NULL DEFAULT current_timestamp(),
  `CreatedBy` varchar(255) NOT NULL DEFAULT 'AdminUser'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adviser`
--

INSERT INTO `adviser` (`AdviserId`, `FullName`, `CreatedDate`, `CreatedBy`) VALUES
(1, 'Mr. Adviser One', '2024-04-08 09:59:27', 'AdminUser'),
(2, 'Mr. Adviser Two', '2024-04-08 09:59:27', 'AdminUser'),
(3, 'Mr. Adviser Three', '2024-04-08 09:59:27', 'AdminUser'),
(4, 'Mr. Adviser Four', '2024-04-08 09:59:27', 'AdminUser'),
(5, 'Mr. Adviser Five', '2024-04-08 09:59:27', 'AdminUser');

-- --------------------------------------------------------

--
-- Stand-in structure for view `adviser_student_vw`
-- (See below for the actual view)
--
CREATE TABLE `adviser_student_vw` (
`ThesisId` int(11)
,`School` varchar(255)
,`SchoolYear` int(11)
,`Adviser` varchar(255)
,`StudentName` text
,`DateOfFinalDefense` date
);

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `fileid` int(11) NOT NULL,
  `DocumentCode` varchar(100) DEFAULT NULL,
  `filename` varchar(500) NOT NULL,
  `filepath` varchar(1000) NOT NULL,
  `createddate` datetime NOT NULL DEFAULT current_timestamp(),
  `createdby` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`fileid`, `DocumentCode`, `filename`, `filepath`, `createddate`, `createdby`) VALUES
(2, 'URC-FO-064', 'Faculty Remuneration of Student Researches Requested by the Research Chair to the URC Proposal Defense.docx', 'files/reports/URC-FO-064_Faculty-Remuneration-of-Student-Researches-Requested-by-the-Research-Chair-to-the-URC-Proposal-Defense.docx', '2024-04-07 14:18:32', 'AdminUser'),
(3, 'URC-FO-065', 'URC-FO-065_Faculty-Remuneration-of-Student-Researches-Requested-by-the-Research-Chair-to-the-URC-Oral-Defense.docx', 'files/reports/URC-FO-065_Faculty-Remuneration-of-Student-Researches-Requested-by-the-Research-Chair-to-the-URC-Oral-Defense.docx', '2024-04-08 08:43:34', 'AdminUser'),
(4, 'URC-FO-077', 'URC-FO-077_Summary-List-of-Adviser-Promoter.docx', 'files/reports/URC-FO-077_Summary-List-of-Adviser-Promoter.docx', '2024-04-08 08:44:05', 'AdminUser'),
(5, 'URC-FO-078', 'URC-FO-078_Summary-List-of-Panel-Members.docx', 'files/reports/URC-FO-078_Summary-List-of-Panel-Members.docx', '2024-04-08 08:45:11', 'AdminUser');

-- --------------------------------------------------------

--
-- Table structure for table `panelmember`
--

CREATE TABLE `panelmember` (
  `PanelMemberId` int(11) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `CreatedDate` datetime NOT NULL DEFAULT current_timestamp(),
  `CreatedBy` varchar(255) NOT NULL DEFAULT 'AdminUser'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `panelmember`
--

INSERT INTO `panelmember` (`PanelMemberId`, `FullName`, `CreatedDate`, `CreatedBy`) VALUES
(1, 'Mr. Panel One', '2024-04-08 09:58:06', 'AdminUser'),
(2, 'Mr. Panel Two', '2024-04-08 09:58:06', 'AdminUser'),
(3, 'Mr. Panel Three', '2024-04-08 09:58:06', 'AdminUser'),
(4, 'Mr. Panel Four', '2024-04-08 09:58:06', 'AdminUser'),
(5, 'Mr. Panel Five', '2024-04-08 09:58:06', 'AdminUser');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `StudentId` int(11) NOT NULL,
  `FirstName` varchar(255) NOT NULL,
  `MiddleName` varchar(255) NOT NULL,
  `LastName` varchar(255) NOT NULL,
  `Course` varchar(255) NOT NULL,
  `Department` varchar(255) NOT NULL,
  `Year` varchar(15) NOT NULL,
  `CreatedDate` datetime NOT NULL DEFAULT current_timestamp(),
  `CreatedBy` varchar(255) NOT NULL DEFAULT 'AdminUser'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`StudentId`, `FirstName`, `MiddleName`, `LastName`, `Course`, `Department`, `Year`, `CreatedDate`, `CreatedBy`) VALUES
(1, 'Student', '', 'One', 'Computer Engineering', 'Engineering', 'Fourth', '2024-04-08 10:02:23', 'AdminUser'),
(2, 'Student', '', 'Two', 'COE', 'Engineering', 'Fourth', '2024-04-08 10:02:23', 'AdminUser'),
(3, 'Student', '', 'Three', 'Computer Engineering', 'Engineering', 'Fourth', '2024-04-08 10:03:38', 'AdminUser'),
(4, 'Student', '', 'Four', 'COE', 'Engineering', 'Fourth', '2024-04-08 10:03:38', 'AdminUser'),
(5, 'Student', '', 'Five', 'Computer Engineering', 'Engineering', 'Fourth', '2024-04-08 10:04:15', 'AdminUser'),
(6, 'Student', '', 'Six', 'COE', 'Engineering', 'Fourth', '2024-04-08 10:04:15', 'AdminUser');

-- --------------------------------------------------------

--
-- Table structure for table `thesis`
--

CREATE TABLE `thesis` (
  `ThesisId` int(11) NOT NULL,
  `Title` varchar(1000) NOT NULL,
  `AdviserId` int(11) DEFAULT NULL,
  `Instructor` varchar(255) DEFAULT NULL,
  `School` varchar(255) NOT NULL DEFAULT 'SAINT MARY’S UNIVERSITY',
  `SchoolYear` int(11) NOT NULL,
  `DateOfFinalDefense` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thesis`
--

INSERT INTO `thesis` (`ThesisId`, `Title`, `AdviserId`, `Instructor`, `School`, `SchoolYear`, `DateOfFinalDefense`) VALUES
(1, 'Thesis Management System', 1, 'Instructor One', 'SAINT MARY’S UNIVERSITY', 2024, '2024-04-15'),
(2, 'My Test Thesis', 4, 'Mr. Instructor', 'SAINT MARY’S UNIVERSITY', 2024, '2024-04-16');

-- --------------------------------------------------------

--
-- Table structure for table `thesispanelmembermap`
--

CREATE TABLE `thesispanelmembermap` (
  `ThesisPanelMemberMap` int(11) NOT NULL,
  `ThesisId` int(11) DEFAULT NULL,
  `PanelMemberId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thesispanelmembermap`
--

INSERT INTO `thesispanelmembermap` (`ThesisPanelMemberMap`, `ThesisId`, `PanelMemberId`) VALUES
(1, 1, 1),
(2, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `thesisstudentmap`
--

CREATE TABLE `thesisstudentmap` (
  `ThesisStudentMapId` int(11) NOT NULL,
  `ThesisId` int(11) DEFAULT NULL,
  `StudentId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thesisstudentmap`
--

INSERT INTO `thesisstudentmap` (`ThesisStudentMapId`, `ThesisId`, `StudentId`) VALUES
(1, 1, 6),
(2, 1, 4),
(3, 1, 2),
(4, 2, 3),
(5, 2, 4),
(6, 2, 5);

-- --------------------------------------------------------

--
-- Stand-in structure for view `thesis_student_adviser_vw`
-- (See below for the actual view)
--
CREATE TABLE `thesis_student_adviser_vw` (
`Title` varchar(1000)
,`StudentName` text
,`Course` varchar(255)
,`Adviser` varchar(255)
,`DateOfFinalDefense` date
);

-- --------------------------------------------------------

--
-- Structure for view `adviser_student_vw`
--
DROP TABLE IF EXISTS `adviser_student_vw`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `adviser_student_vw`  AS SELECT `t`.`ThesisId` AS `ThesisId`, `t`.`School` AS `School`, `t`.`SchoolYear` AS `SchoolYear`, `a`.`FullName` AS `Adviser`, concat(`s`.`FirstName`,' ',`s`.`MiddleName`,' ',`s`.`LastName`) AS `StudentName`, `t`.`DateOfFinalDefense` AS `DateOfFinalDefense` FROM (((`thesis` `t` left join `adviser` `a` on(`t`.`AdviserId` = `a`.`AdviserId`)) left join `thesisstudentmap` `tsm` on(`t`.`ThesisId` = `tsm`.`ThesisId`)) left join `student` `s` on(`tsm`.`StudentId` = `s`.`StudentId`)) ;

-- --------------------------------------------------------

--
-- Structure for view `thesis_student_adviser_vw`
--
DROP TABLE IF EXISTS `thesis_student_adviser_vw`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `thesis_student_adviser_vw`  AS SELECT `t`.`Title` AS `Title`, concat(`s`.`FirstName`,' ',`s`.`MiddleName`,' ',`s`.`LastName`) AS `StudentName`, `s`.`Course` AS `Course`, `a`.`FullName` AS `Adviser`, `t`.`DateOfFinalDefense` AS `DateOfFinalDefense` FROM (((`thesis` `t` left join `thesisstudentmap` `tsm` on(`t`.`ThesisId` = `tsm`.`ThesisId`)) left join `student` `s` on(`tsm`.`StudentId` = `s`.`StudentId`)) left join `adviser` `a` on(`t`.`AdviserId` = `a`.`AdviserId`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adviser`
--
ALTER TABLE `adviser`
  ADD PRIMARY KEY (`AdviserId`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`fileid`);

--
-- Indexes for table `panelmember`
--
ALTER TABLE `panelmember`
  ADD PRIMARY KEY (`PanelMemberId`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`StudentId`);

--
-- Indexes for table `thesis`
--
ALTER TABLE `thesis`
  ADD PRIMARY KEY (`ThesisId`),
  ADD KEY `AdviserId` (`AdviserId`);

--
-- Indexes for table `thesispanelmembermap`
--
ALTER TABLE `thesispanelmembermap`
  ADD PRIMARY KEY (`ThesisPanelMemberMap`),
  ADD KEY `ThesisId` (`ThesisId`),
  ADD KEY `PanelMemberId` (`PanelMemberId`);

--
-- Indexes for table `thesisstudentmap`
--
ALTER TABLE `thesisstudentmap`
  ADD PRIMARY KEY (`ThesisStudentMapId`),
  ADD KEY `StudentId` (`StudentId`),
  ADD KEY `ThesisId_` (`ThesisId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adviser`
--
ALTER TABLE `adviser`
  MODIFY `AdviserId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `fileid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `panelmember`
--
ALTER TABLE `panelmember`
  MODIFY `PanelMemberId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `StudentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `thesis`
--
ALTER TABLE `thesis`
  MODIFY `ThesisId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `thesispanelmembermap`
--
ALTER TABLE `thesispanelmembermap`
  MODIFY `ThesisPanelMemberMap` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `thesisstudentmap`
--
ALTER TABLE `thesisstudentmap`
  MODIFY `ThesisStudentMapId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `thesis`
--
ALTER TABLE `thesis`
  ADD CONSTRAINT `AdviserId` FOREIGN KEY (`AdviserId`) REFERENCES `adviser` (`AdviserId`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `thesispanelmembermap`
--
ALTER TABLE `thesispanelmembermap`
  ADD CONSTRAINT `PanelMemberId` FOREIGN KEY (`PanelMemberId`) REFERENCES `panelmember` (`PanelMemberId`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `ThesisId` FOREIGN KEY (`ThesisId`) REFERENCES `thesis` (`ThesisId`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `thesisstudentmap`
--
ALTER TABLE `thesisstudentmap`
  ADD CONSTRAINT `StudentId` FOREIGN KEY (`StudentId`) REFERENCES `student` (`StudentId`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `ThesisId_` FOREIGN KEY (`ThesisId`) REFERENCES `thesis` (`ThesisId`) ON DELETE SET NULL ON UPDATE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
