-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 24, 2024 at 02:21 PM
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
-- Truncate table before insert `adviser`
--

TRUNCATE TABLE `adviser`;
--
-- Dumping data for table `adviser`
--

INSERT INTO `adviser` (`AdviserId`, `FullName`, `CreatedDate`, `CreatedBy`) VALUES
(1, 'Adviser One', '2024-04-08 09:59:27', 'AdminUser'),
(2, 'Adviser Two', '2024-04-08 09:59:27', 'AdminUser'),
(3, 'Adviser Three', '2024-04-08 09:59:27', 'AdminUser'),
(4, 'Adviser Four', '2024-04-08 09:59:27', 'AdminUser'),
(5, 'Adviser Five', '2024-04-08 09:59:27', 'AdminUser');

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
-- Table structure for table `checklist`
--

CREATE TABLE `checklist` (
  `CheckListId` int(11) NOT NULL,
  `Part` int(11) NOT NULL,
  `StepNumber` int(11) NOT NULL,
  `TaskName` varchar(1000) NOT NULL,
  `TaskNameAlias` varchar(1000) NOT NULL,
  `Assignee` varchar(255) NOT NULL,
  `Action` enum('Manual','Upload','Approval','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `checklist`
--

TRUNCATE TABLE `checklist`;
--
-- Dumping data for table `checklist`
--

INSERT INTO `checklist` (`CheckListId`, `Part`, `StepNumber`, `TaskName`, `TaskNameAlias`, `Assignee`, `Action`) VALUES
(1, 1, 1, 'Class orientation and discussion', 'N/A', 'Instructor', 'Manual'),
(2, 1, 2, 'Titles preparation', 'N/A', 'Researchers', 'Manual'),
(3, 1, 3, 'Evaluation of titles / Title defense', 'N/A', 'Instructor', 'Manual'),
(4, 1, 4, 'Prepare Form 10, Form 11, Form 12', 'Forms_10_to_12', 'Researchers', 'Upload'),
(5, 1, 5, 'Writing of Chapter 1', 'Chapter_1', 'Researchers', 'Upload'),
(6, 1, 6, 'Checking of Chapter 1', 'N/A', 'Instructor, Adviser', 'Approval'),
(7, 1, 7, 'Writing of Chapter 2', 'Chapter_2', 'Researchers', 'Upload'),
(8, 1, 8, 'Checking of Chapter 2', 'N/A', 'Instructor', 'Approval'),
(9, 1, 9, 'Prepare Form 15', 'Form_15', 'Researchers', 'Upload'),
(10, 1, 10, 'Prepare Form 13', 'Form_13', 'Researchers', 'Upload'),
(11, 1, 11, 'Print evaluation copies (3 copies)', 'N/A', 'Researchers', 'Manual'),
(12, 1, 12, 'Distribution of Form 13 and evaluation copies', 'N/A', 'Researchers', 'Manual'),
(13, 1, 13, 'Retrieval of Form 13', 'N/A', 'Research Coordinator', 'Manual'),
(14, 1, 14, 'Payment of defense fees', 'Payment_Fee', 'Researchers', 'Upload'),
(15, 1, 15, 'Prepare Form 16', 'Form_16', 'Researchers', 'Upload'),
(16, 1, 16, 'Print Form 17', 'N/A', 'Researchers', 'Manual'),
(17, 1, 17, 'Proposal defense, Presentation, Noting of recommendations, Filling of Form 17, Retrieval of Form 17', 'N/A', 'Instructor, Research Coordinator', 'Approval'),
(18, 1, 18, 'Accomplish Minutes of defense', 'Minutes_of_Defense', 'Researchers', 'Upload'),
(19, 1, 19, 'Consolidation of Proposal documents', 'N/A', 'Research Coordinator', 'Approval'),
(20, 1, 20, 'Protocol Application for UREB', 'Protocol_Application_UREB', 'Researchers', 'Upload'),
(21, 1, 21, 'Endorsement to UREB', 'Endorsement_UREB', 'Researchers', 'Upload'),
(22, 1, 22, 'Submission to UREB, Payment of Review fees', 'Submission_UREB', 'Researchers', 'Upload'),
(23, 1, 23, 'Issuance of Certificate of Approval or Certificate of Exemption', 'Certificate_of_Approval', 'UREB', 'Upload'),
(24, 1, 24, 'END OF THESIS 1 / CAPSTONE 1', 'N/A', '0', 'Manual');

INSERT INTO `checklist` (`CheckListId`, `Part`, `StepNumber`, `TaskName`, `Assignee`, `TaskNameAlias`, `Action`) VALUES
(NULL, 2, 2, 'Checking the Chapter 3', 'Instructor', 'N/A', 'Approval'),
(NULL, 2, 3, 'Writing the Chapter 4', 'Researchers', 'Chapter_4', 'Upload'),
(NULL, 2, 4, 'Checking the Chapter 4', 'Instructor', 'N/A', 'Approval'),
(NULL, 2, 5, 'Prepare files for initial plagiarism', 'Researchers', 'N/A', 'Manual'),
(NULL, 2, 6, 'Run initial plagiarism check. Provide result', 'Research Coordinator', 'Initial_Plagiarism_Check_Result', 'Upload'),
(NULL, 2, 7, 'Endorsement for plagiarism certificate', 'Research Coordinator', 'Endorsement_for_Plagiarism_Certificate', 'Upload'),
(NULL, 2, 8, 'Submit endorsement to URC', 'Researchers', 'N/A', 'Manual'),
(NULL, 2, 9, 'Issue Plagiarism Certificate', 'Research Coordinator', 'Plagiarism_Certificate', 'Upload'),
(NULL, 2, 10, 'Prepare Form 15', 'Researchers', 'Thesis_2_Form_15', 'Upload'),
(NULL, 2, 11, 'Print Form 13', 'Researchers', 'Thesis_2_Form_13', 'Upload'),
(NULL, 2, 12, 'Print evaluation copies (3 copies)', 'Researchers', 'N/A', 'Manual'),
(NULL, 2, 13, 'Distribution of Form 13 and evaluation copies', 'Researchers', 'N/A', 'Manual'),
(NULL, 2, 14, 'Retrieval of Form 13', 'Research Coordinator', 'N/A', 'Manual'),
(NULL, 2, 15, 'Payment of defense fees', 'Researchers', 'Thesis_2_Defense_Fees', 'Upload'),
(NULL, 2, 16, 'Prepare Form 16', 'Researchers', 'Thesis_2_Form_16', 'Upload'),
(NULL, 2, 17, 'Print Form 18', 'Research Coordinator', 'N/A', 'Manual'),
(NULL, 2, 18, 'Proposal defense, Presentation, Noting of recommendations, Filling of Form 18, Retrieval of Form 18', 'Research Coordinator, Instructor', 'N/A', 'Approval'),
(NULL, 2, 19, 'Accomplish Minutes of defense', 'Researchers', 'Thesis_2_Minutes_of_Defense', 'Upload'),
(NULL, 2, 20, 'Prepare files for final plagiarism', 'Researchers', 'N/A', 'Manual'),
(NULL, 2, 21, 'Run final plagiarism check. Provide result', 'Research Coordinator', 'Final_Plagiarism_Result', 'Upload'),
(NULL, 2, 22, 'Endorsement for plagiarism clearance', 'Researchers', 'Endorsement_for_plagiarism_clearance', 'Upload'),
(NULL, 2, 23, 'Submit endorsement to URC', 'Researchers', 'N/A', 'Manual'),
(NULL, 2, 24, 'Issue Plagiarism Clearance', 'Researchers', 'Plagiarism_Clearance', 'Upload'),
(NULL, 2, 25, 'Select editor', 'Researchers', 'N/A', ''),
(NULL, 2, 26, 'Endorsement for editing', 'Researchers', 'Endorsement_for_editing', 'Upload'),
(NULL, 2, 27, 'Submit file for editing', 'Researchers', 'N/A', 'Manual'),
(NULL, 2, 28, 'Editing of file, Submit result, Sign certification', 'Researchers', 'Signed_Certification', 'Upload'),
(NULL, 2, 29, 'Submit certification to URC', 'Researchers', 'N/A', 'Manual'),
(NULL, 2, 30, 'Issue Editing Clearance', 'Researchers', 'Issue_Editing_Clearance', 'Upload'),
(NULL, 2, 31, 'Reformat manuscript to single spacing', 'Researchers', 'Formatted_Manuscript', 'Upload'),
(NULL, 2, 32, 'Submit final report to UREB', 'Researchers', 'Final_Report_to_UREB', 'Upload'),
(NULL, 2, 33, 'Issue Ethics Clearance', 'Researchers', 'Ethics_Clearance', 'Upload'),
(NULL, 2, 34, 'Prepare Declaration of Originality', 'Researchers', 'Declaration_of_Originality', 'Upload'),
(NULL, 2, 35, 'Notarization of Declaration of Originality', 'Researchers', 'Notarized_Declaration_of_Originality', 'Upload'),
(NULL, 2, 36, 'Upload final manuscript in single space', 'Researchers', 'Final_Manuscript', 'Upload'),
(NULL, 2, 37, 'Checking of final manuscript in single space', 'Instructor', 'N/A', 'Approval'),
(NULL, 2, 38, 'Printing of final manuscript sample copy', 'Researchers', 'N/A', 'Manual'),
(NULL, 2, 39, 'Verification of sample copy', 'Instructor', 'N/A', 'Approval'),
(NULL, 2, 40, 'Signing of Approval Sheet', 'Researchers', 'Approval_Sheet', 'Upload'),
(NULL, 2, 41, 'Printing of additional copies, Verification of print outs', 'Researchers', 'N/A', 'Manual'),
(NULL, 2, 42, 'Binding of copies', 'Researchers', 'N/A', 'Manual'),
(NULL, 2, 43, 'Signing of Approval Sheets of hardbound copies', 'Researchers', 'Approval_Sheet_for_hardbound_copies', 'Upload'),
(NULL, 2, 44, 'Prepare files for burning', 'Researchers', 'N/A', 'Manual'),
(NULL, 2, 45, 'Checking of files for burning', 'Research Coordinator', 'N/A', 'Approval'),
(NULL, 2, 46, 'Burning of CDs', 'Researchers', 'N/A', 'Manual'),
(NULL, 2, 47, 'Distribution of hardbounds', 'Researchers', 'N/A', 'Manual'),
(NULL, 2, 48, 'Submission of CDs, Submission of Evaluation forms (Form 31, Form 39, Form 41)', 'Researchers', 'Evaluation_Form', 'Upload'),
(NULL, 2, 49, 'Verification of submitted CDs, Updating of checklist', 'Research Coordinator', 'N/A', 'Manual'),
(NULL, 2, 50, 'Submission of working hardware prototypes (BSCpE, BSECE, BSEE)', 'Researchers', 'Working_Hardware_Prototype', 'Upload'),
(NULL, 2, 51, 'Submission of system installation package (BSIT)', 'Researchers', 'System_Installation_Package', 'Upload'),
(NULL, 2, 52, 'END OF THESIS 2 CAPSTONE 2', '', '', 'Manual')

INSERT INTO thesis_checklist_map
SELECT 1, CheckListId, 0, 'Not Started' FROM checklist WHERE Part = 2
INSERT INTO thesis_checklist_map
SELECT 2, CheckListId, 0, 'Not Started' FROM checklist WHERE Part = 2
INSERT INTO thesis_checklist_map
SELECT 3, CheckListId, 0, 'Not Started' FROM checklist WHERE Part = 2
-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE `file` (
  `file_id` int(11) NOT NULL,
  `filename` text NOT NULL,
  `location` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Truncate table before insert `file`
--

TRUNCATE TABLE `file`;
--
-- Dumping data for table `file`
--

INSERT INTO `file` (`file_id`, `filename`, `location`) VALUES
(1, '1570204997.jpg', 'upload/1570204997.jpg'),
(2, '1713779220.doc', 'upload/1713779220.doc');

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
-- Truncate table before insert `files`
--

TRUNCATE TABLE `files`;
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
-- Truncate table before insert `panelmember`
--

TRUNCATE TABLE `panelmember`;
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
-- Truncate table before insert `student`
--

TRUNCATE TABLE `student`;
--
-- Dumping data for table `student`
--

INSERT INTO `student` (`StudentId`, `FirstName`, `MiddleName`, `LastName`, `Course`, `Department`, `Year`, `CreatedDate`, `CreatedBy`) VALUES
(1, 'Student', '', 'One', 'Computer Engineering', 'Engineering', 'Fourth', '2024-04-08 10:02:23', 'AdminUser'),
(2, 'Student', '', 'Two', 'COE', 'Engineering', 'Fourth', '2024-04-08 10:02:23', 'AdminUser'),
(3, 'Student', '', 'Three', 'Computer Engineering', 'Engineering', 'Fourth', '2024-04-08 10:03:38', 'AdminUser'),
(4, 'Student', '', 'Four', 'COE', 'Engineering', 'Fourth', '2024-04-08 10:03:38', 'AdminUser'),
(5, 'Student', '', 'Five', 'Computer Engineering', 'Engineering', 'Fourth', '2024-04-08 10:04:15', 'AdminUser'),
(6, 'Student', '', 'Six', 'COE', 'Engineering', 'Fourth', '2024-04-08 10:04:15', 'AdminUser'),
(7, 'Student', '', 'Seven', 'COE', 'Engineering', 'Fourth', '2024-04-17 19:33:14', 'AdminUser'),
(8, 'Student', '', 'Eight', 'COE', 'Engineering', 'Fourth', '2024-04-17 19:33:14', 'AdminUser'),
(9, 'Student', '', 'Nine', 'COE', 'Engineering', 'Fourth', '2024-04-17 19:33:48', 'AdminUser');

-- --------------------------------------------------------

--
-- Table structure for table `thesis`
--

CREATE TABLE `thesis` (
  `ThesisId` int(11) NOT NULL,
  `Title` varchar(1000) NOT NULL,
  `AdviserId` int(11) DEFAULT NULL,
  `InstructorId` int(11) DEFAULT NULL,
  `School` varchar(255) NOT NULL DEFAULT 'SAINT MARY’S UNIVERSITY',
  `SchoolYear` int(11) NOT NULL,
  `DateOfFinalDefense` date DEFAULT NULL,
  `CreatedBy` varchar(255) NOT NULL DEFAULT 'System Admin',
  `CreatedDate` date NOT NULL DEFAULT current_timestamp(),
  `LastModifiedBy` varchar(255) NOT NULL DEFAULT 'System Admin',
  `LastModifiedDate` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `thesis`
--

TRUNCATE TABLE `thesis`;
--
-- Dumping data for table `thesis`
--

INSERT INTO `thesis` (`ThesisId`, `Title`, `AdviserId`, `InstructorId`, `School`, `SchoolYear`, `DateOfFinalDefense`, `CreatedBy`, `CreatedDate`, `LastModifiedBy`, `LastModifiedDate`) VALUES
(1, 'Thesis Management System', 7, 12, 'SAINT MARY’S UNIVERSITY', 2024, '2024-04-15', 'System Admin', '2024-04-22', 'System Admin', '2024-04-22'),
(2, 'My Test Thesis', 1, 13, 'SAINT MARY’S UNIVERSITY', 2024, '2024-04-16', 'System Admin', '2024-04-22', 'System Admin', '2024-04-22'),
(3, 'This thesis is for Instructor role', 8, 14, 'SAINT MARY’S UNIVERSITY', 2024, '2024-04-27', 'System Admin', '2024-04-22', 'System Admin', '2024-04-22');

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
-- Truncate table before insert `thesispanelmembermap`
--

TRUNCATE TABLE `thesispanelmembermap`;
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
-- Truncate table before insert `thesisstudentmap`
--

TRUNCATE TABLE `thesisstudentmap`;
--
-- Dumping data for table `thesisstudentmap`
--

INSERT INTO `thesisstudentmap` (`ThesisStudentMapId`, `ThesisId`, `StudentId`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 2, 6),
(5, 2, 4),
(6, 2, 5),
(7, 3, 7),
(8, 3, 8),
(9, 3, 9);

-- --------------------------------------------------------

--
-- Table structure for table `thesis_checklist_approval_map`
--

CREATE TABLE `thesis_checklist_approval_map` (
  `ThesisChecklistApprovalId` int(11) NOT NULL,
  `ThesisId` int(11) DEFAULT NULL,
  `CheckListId` int(11) DEFAULT NULL,
  `ApproverId` int(11) DEFAULT NULL,
  `Approved` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `thesis_checklist_approval_map`
--

TRUNCATE TABLE `thesis_checklist_approval_map`;
--
-- Dumping data for table `thesis_checklist_approval_map`
--

INSERT INTO `thesis_checklist_approval_map` (`ThesisChecklistApprovalId`, `ThesisId`, `CheckListId`, `ApproverId`, `Approved`) VALUES
(1, 1, 6, 12, 1),
(2, 1, 6, 7, 1),
(3, 1, 8, 12, 0);

-- --------------------------------------------------------

--
-- Table structure for table `thesis_checklist_file_map`
--

CREATE TABLE `thesis_checklist_file_map` (
  `ThesisChecklistFileId` int(11) NOT NULL,
  `ThesisId` int(11) DEFAULT NULL,
  `CheckListId` int(11) DEFAULT NULL,
  `FileName` varchar(1000) DEFAULT NULL,
  `FilePath` varchar(1000) DEFAULT NULL,
  `UploadedBy` varchar(255) DEFAULT NULL,
  `UploadedDate` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `thesis_checklist_file_map`
--

TRUNCATE TABLE `thesis_checklist_file_map`;
--
-- Dumping data for table `thesis_checklist_file_map`
--

INSERT INTO `thesis_checklist_file_map` (`ThesisChecklistFileId`, `ThesisId`, `CheckListId`, `FileName`, `FilePath`, `UploadedBy`, `UploadedDate`) VALUES
(1, 1, 4, 'demo_ms_word.doc', './../uploads/demo_ms_word.doc', 'RC', '2024-04-22 19:47:44'),
(2, 3, 4, 'PHP References.doc', './../uploads/PHP References.doc', 'RC', '2024-04-22 20:39:49'),
(3, 1, 5, 'Chapter_1_Thesis_Management_System', './../uploads/Chapter_1_Thesis_Management_Systemdoc', 'RC', '2024-04-22 21:27:30'),
(4, 2, 4, 'Forms_10_to_12_My_Test_Thesis', './../uploads/Forms_10_to_12_My_Test_Thesis.doc', 'RC', '2024-04-22 21:54:42'),
(5, 3, 4, 'Forms_10_to_12_This_thesis_is_for_Instructor_role', './../uploads/Forms_10_to_12_This_thesis_is_for_Instructor_role.docx', 'RC', '2024-04-22 22:32:52'),
(6, 1, 7, 'Chapter_2_Thesis_Management_System', './../uploads/Chapter_2_Thesis_Management_System.docx', 'Student One', '2024-04-24 18:59:22'),
(7, 1, 7, 'Chapter_2_Thesis_Management_System', './../uploads/Chapter_2_Thesis_Management_System.doc', 'Student Two', '2024-04-24 19:10:04'),
(8, 1, 7, 'Chapter_2_Thesis_Management_System', './../uploads/Chapter_2_Thesis_Management_System.docx', 'Student Two', '2024-04-24 19:11:01');

-- --------------------------------------------------------

--
-- Table structure for table `thesis_checklist_map`
--

CREATE TABLE `thesis_checklist_map` (
  `ThesisChecklistId` int(11) NOT NULL,
  `ThesisId` int(11) DEFAULT NULL,
  `CheckListId` int(11) DEFAULT NULL,
  `Completed` tinyint(1) NOT NULL DEFAULT 0,
  `Status` enum('Not Started','In Progress','Completed') NOT NULL DEFAULT 'Not Started'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `thesis_checklist_map`
--

TRUNCATE TABLE `thesis_checklist_map`;
--
-- Dumping data for table `thesis_checklist_map`
--

INSERT INTO `thesis_checklist_map` (`ThesisChecklistId`, `ThesisId`, `CheckListId`, `Completed`, `Status`) VALUES
(1, 1, 1, 1, 'Completed'),
(2, 1, 2, 0, 'Completed'),
(3, 1, 3, 0, 'Completed'),
(4, 1, 4, 0, 'Completed'),
(5, 1, 5, 0, 'Completed'),
(6, 1, 6, 0, 'Completed'),
(7, 1, 7, 0, 'Completed'),
(8, 1, 8, 0, 'Completed'),
(9, 1, 9, 0, 'Completed'),
(10, 1, 10, 0, 'Completed'),
(11, 1, 11, 0, 'Completed'),
(12, 1, 12, 0, 'Completed'),
(13, 1, 13, 0, 'Completed'),
(14, 1, 14, 0, 'Not Started'),
(15, 1, 15, 0, 'Not Started'),
(16, 1, 16, 0, 'Not Started'),
(17, 1, 17, 0, 'Not Started'),
(18, 1, 18, 0, 'Not Started'),
(19, 1, 19, 0, 'Not Started'),
(20, 1, 20, 0, 'Not Started'),
(21, 1, 21, 0, 'Not Started'),
(22, 1, 22, 0, 'Not Started'),
(23, 1, 23, 0, 'Not Started'),
(24, 1, 24, 0, 'Not Started'),
(32, 2, 1, 0, 'Completed'),
(33, 2, 2, 0, 'Completed'),
(34, 2, 3, 0, 'Completed'),
(35, 2, 4, 0, 'Completed'),
(36, 2, 5, 0, 'Not Started'),
(37, 2, 6, 0, 'Not Started'),
(38, 2, 7, 0, 'Not Started'),
(39, 2, 8, 0, 'Not Started'),
(40, 2, 9, 0, 'Not Started'),
(41, 2, 10, 0, 'Not Started'),
(42, 2, 11, 0, 'Not Started'),
(43, 2, 12, 0, 'Not Started'),
(44, 2, 13, 0, 'Not Started'),
(45, 2, 14, 0, 'Not Started'),
(46, 2, 15, 0, 'Not Started'),
(47, 2, 16, 0, 'Not Started'),
(48, 2, 17, 0, 'Not Started'),
(49, 2, 18, 0, 'Not Started'),
(50, 2, 19, 0, 'Not Started'),
(51, 2, 20, 0, 'Not Started'),
(52, 2, 21, 0, 'Not Started'),
(53, 2, 22, 0, 'Not Started'),
(54, 2, 23, 0, 'Not Started'),
(55, 2, 24, 0, 'Not Started'),
(63, 3, 1, 0, 'Completed'),
(64, 3, 2, 0, 'Not Started'),
(65, 3, 3, 0, 'Not Started'),
(66, 3, 4, 0, 'Not Started'),
(67, 3, 5, 0, 'Not Started'),
(68, 3, 6, 0, 'Not Started'),
(69, 3, 7, 0, 'Not Started'),
(70, 3, 8, 0, 'Not Started'),
(71, 3, 9, 0, 'Not Started'),
(72, 3, 10, 0, 'Not Started'),
(73, 3, 11, 0, 'Not Started'),
(74, 3, 12, 0, 'Not Started'),
(75, 3, 13, 0, 'Not Started'),
(76, 3, 14, 0, 'Not Started'),
(77, 3, 15, 0, 'Not Started'),
(78, 3, 16, 0, 'Not Started'),
(79, 3, 17, 0, 'Not Started'),
(80, 3, 18, 0, 'Not Started'),
(81, 3, 19, 0, 'Not Started'),
(82, 3, 20, 0, 'Not Started'),
(83, 3, 21, 0, 'Not Started'),
(84, 3, 22, 0, 'Not Started'),
(85, 3, 23, 0, 'Not Started'),
(86, 3, 24, 0, 'Not Started');

-- --------------------------------------------------------

--
-- Stand-in structure for view `thesis_checklist_vw`
-- (See below for the actual view)
--
CREATE TABLE `thesis_checklist_vw` (
`ThesisId` int(11)
,`Part` int(11)
,`Status` enum('Not Started','In Progress','Completed')
,`Title` varchar(1000)
,`Authors` mediumtext
,`Adviser` varchar(255)
,`Instructor` varchar(255)
,`StepNumber` int(11)
,`TaskName` varchar(1000)
,`UploadedFileName` text
,`Assignee` varchar(255)
,`Action` enum('Manual','Upload','Approval','')
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `thesis_groupedstudents_vw`
-- (See below for the actual view)
--
CREATE TABLE `thesis_groupedstudents_vw` (
`ThesisId` int(11)
,`Title` varchar(1000)
,`Authors` mediumtext
,`Adviser` varchar(255)
,`Instructor` varchar(255)
);

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
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserId` int(11) NOT NULL,
  `Role` enum('Adviser','Dean','Instructor','Student','Research Coordinator') NOT NULL,
  `Name` varchar(255) NOT NULL,
  `UserName` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `SecurityQuestion` varchar(255) NOT NULL,
  `SecurityAnswer` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `users`
--

TRUNCATE TABLE `users`;
--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserId`, `Role`, `Name`, `UserName`, `Password`, `Email`, `SecurityQuestion`, `SecurityAnswer`) VALUES
(1, 'Adviser', 'Adviser Two', 'adviser.two', 'c4ca4238a0b923820dcc509a6f75849b', 'nelbuen@test.com', 'What elementary school did you attend?', 'hilo'),
(3, 'Research Coordinator', 'RC', 'rc', 'c4ca4238a0b923820dcc509a6f75849b', 'rc@testing.com', 'What elementary school did you attend?', 'tes'),
(4, 'Dean', 'Dean', 'deanto', 'c4ca4238a0b923820dcc509a6f75849b', 'dean@testing.com', 'What elementary school did you attend?', 'mes'),
(5, 'Adviser', 'Adviser Four', 'adviser.4', 'c20ad4d76fe97759aa27a0c99bff6710', 'test.adviser@testing.com', 'What elementary school did you attend?', 'mes'),
(6, 'Instructor', 'Instructor Seven', 'instructor.seven', 'c4ca4238a0b923820dcc509a6f75849b', 'test.instructor@testing.com', 'What elementary school did you attend?', 'mes'),
(7, 'Adviser', 'Adviser One', 'adviser.one', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes'),
(8, 'Adviser', 'Adviser Three', 'adviser.three', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes'),
(9, 'Adviser', 'Adviser Five', 'adviser.five', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes'),
(10, 'Adviser', 'Adviser Six', 'adviser.six', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes'),
(11, 'Adviser', 'Adviser Seven', 'adviser.seven', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes'),
(12, 'Instructor', 'Instructor One', 'instructor.one', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes'),
(13, 'Instructor', 'Instructor Two', 'instructor.two', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes'),
(14, 'Instructor', 'Instructor Three', 'instructor.three', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes'),
(15, 'Instructor', 'Instructor Four', 'instructor.four', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes'),
(16, 'Instructor', 'Instructor Five', 'instructor.five', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes'),
(17, 'Instructor', 'Instructor Six', 'instructor.six', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes'),
(18, 'Student', 'Student One', 'student.1', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes'),
(19, 'Student', 'Student Two', 'student.2', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes'),
(20, 'Student', 'Student Three', 'student.3', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes'),
(21, 'Student', 'Student Four', 'student.4', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes'),
(22, 'Student', 'Student Five', 'student.5', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes'),
(23, 'Student', 'Student Six', 'student.6', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes'),
(24, 'Student', 'Student Seven', 'student.7', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes'),
(25, 'Student', 'Student Eight', 'student.8', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes'),
(26, 'Student', 'Student Nine', 'student.9', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes');

-- --------------------------------------------------------

--
-- Structure for view `adviser_student_vw`
--
DROP TABLE IF EXISTS `adviser_student_vw`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `adviser_student_vw`  AS SELECT `t`.`ThesisId` AS `ThesisId`, `t`.`School` AS `School`, `t`.`SchoolYear` AS `SchoolYear`, `a`.`FullName` AS `Adviser`, concat(`s`.`FirstName`,' ',`s`.`MiddleName`,' ',`s`.`LastName`) AS `StudentName`, `t`.`DateOfFinalDefense` AS `DateOfFinalDefense` FROM (((`thesis` `t` left join `adviser` `a` on(`t`.`AdviserId` = `a`.`AdviserId`)) left join `thesisstudentmap` `tsm` on(`t`.`ThesisId` = `tsm`.`ThesisId`)) left join `student` `s` on(`tsm`.`StudentId` = `s`.`StudentId`)) ;

-- --------------------------------------------------------

--
-- Structure for view `thesis_checklist_vw`
--
DROP TABLE IF EXISTS `thesis_checklist_vw`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `thesis_checklist_vw`  AS SELECT `t`.`ThesisId` AS `ThesisId`, `c`.`Part` AS `Part`, `tcm`.`Status` AS `Status`, `t`.`Title` AS `Title`, `tgs`.`Authors` AS `Authors`, `tgs`.`Adviser` AS `Adviser`, `tgs`.`Instructor` AS `Instructor`, `c`.`StepNumber` AS `StepNumber`, `c`.`TaskName` AS `TaskName`, concat(`c`.`TaskNameAlias`,'_',replace(`t`.`Title`,' ','_')) AS `UploadedFileName`, `c`.`Assignee` AS `Assignee`, `c`.`Action` AS `Action` FROM (((`thesis` `t` left join `thesis_checklist_map` `tcm` on(`t`.`ThesisId` = `tcm`.`ThesisId`)) left join `checklist` `c` on(`c`.`CheckListId` = `tcm`.`CheckListId`)) left join `thesis_groupedstudents_vw` `tgs` on(`t`.`ThesisId` = `tgs`.`ThesisId`)) ;

-- --------------------------------------------------------

--
-- Structure for view `thesis_groupedstudents_vw`
--
DROP TABLE IF EXISTS `thesis_groupedstudents_vw`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `thesis_groupedstudents_vw`  AS SELECT `t`.`ThesisId` AS `ThesisId`, `t`.`Title` AS `Title`, group_concat(distinct concat(`s`.`FirstName`,' ',`s`.`LastName`) separator ',') AS `Authors`, `adviser`.`Name` AS `Adviser`, `instructor`.`Name` AS `Instructor` FROM ((((`thesis` `t` left join `thesisstudentmap` `tsm` on(`tsm`.`ThesisId` = `t`.`ThesisId`)) left join `student` `s` on(`tsm`.`StudentId` = `s`.`StudentId`)) left join `users` `adviser` on(`t`.`AdviserId` = `adviser`.`UserId`)) left join `users` `instructor` on(`instructor`.`UserId` = `t`.`InstructorId`)) GROUP BY `t`.`Title`, `adviser`.`Name`, `instructor`.`Name` ;

-- --------------------------------------------------------

--
-- Structure for view `thesis_student_adviser_vw`
--
DROP TABLE IF EXISTS `thesis_student_adviser_vw`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `thesis_student_adviser_vw`  AS SELECT `t`.`Title` AS `Title`, concat(`s`.`FirstName`,' ',`s`.`MiddleName`,' ',`s`.`LastName`) AS `StudentName`, `s`.`Course` AS `Course`, `a`.`Name` AS `Adviser`, `t`.`DateOfFinalDefense` AS `DateOfFinalDefense` FROM (((`thesis` `t` left join `thesisstudentmap` `tsm` on(`t`.`ThesisId` = `tsm`.`ThesisId`)) left join `student` `s` on(`tsm`.`StudentId` = `s`.`StudentId`)) left join `users` `a` on(`t`.`AdviserId` = `a`.`UserId`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adviser`
--
ALTER TABLE `adviser`
  ADD PRIMARY KEY (`AdviserId`);

--
-- Indexes for table `checklist`
--
ALTER TABLE `checklist`
  ADD PRIMARY KEY (`CheckListId`);

--
-- Indexes for table `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`file_id`);

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
  ADD KEY `CHK_Adviser_User` (`AdviserId`),
  ADD KEY `CHK_Instructor_User` (`InstructorId`);

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
-- Indexes for table `thesis_checklist_approval_map`
--
ALTER TABLE `thesis_checklist_approval_map`
  ADD PRIMARY KEY (`ThesisChecklistApprovalId`),
  ADD KEY `CHK_ThesisId_Approval` (`ThesisId`),
  ADD KEY `CHK_checklistId_Approval` (`CheckListId`),
  ADD KEY `CHK_UserId_Approval` (`ApproverId`);

--
-- Indexes for table `thesis_checklist_file_map`
--
ALTER TABLE `thesis_checklist_file_map`
  ADD PRIMARY KEY (`ThesisChecklistFileId`),
  ADD KEY `CHK_Thesis_Id` (`ThesisId`),
  ADD KEY `CHK_checklist_Id` (`CheckListId`);

--
-- Indexes for table `thesis_checklist_map`
--
ALTER TABLE `thesis_checklist_map`
  ADD PRIMARY KEY (`ThesisChecklistId`),
  ADD KEY `CHK_ThesisId` (`ThesisId`),
  ADD KEY `CHK_checklistId` (`CheckListId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adviser`
--
ALTER TABLE `adviser`
  MODIFY `AdviserId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `checklist`
--
ALTER TABLE `checklist`
  MODIFY `CheckListId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `file`
--
ALTER TABLE `file`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `StudentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `thesis`
--
ALTER TABLE `thesis`
  MODIFY `ThesisId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `thesispanelmembermap`
--
ALTER TABLE `thesispanelmembermap`
  MODIFY `ThesisPanelMemberMap` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `thesisstudentmap`
--
ALTER TABLE `thesisstudentmap`
  MODIFY `ThesisStudentMapId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `thesis_checklist_approval_map`
--
ALTER TABLE `thesis_checklist_approval_map`
  MODIFY `ThesisChecklistApprovalId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `thesis_checklist_file_map`
--
ALTER TABLE `thesis_checklist_file_map`
  MODIFY `ThesisChecklistFileId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `thesis_checklist_map`
--
ALTER TABLE `thesis_checklist_map`
  MODIFY `ThesisChecklistId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `thesis`
--
ALTER TABLE `thesis`
  ADD CONSTRAINT `CHK_Adviser_User` FOREIGN KEY (`AdviserId`) REFERENCES `users` (`UserId`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `CHK_Instructor_User` FOREIGN KEY (`InstructorId`) REFERENCES `users` (`UserId`) ON DELETE SET NULL ON UPDATE SET NULL;

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

--
-- Constraints for table `thesis_checklist_approval_map`
--
ALTER TABLE `thesis_checklist_approval_map`
  ADD CONSTRAINT `CHK_ThesisId_Approval` FOREIGN KEY (`ThesisId`) REFERENCES `thesis` (`ThesisId`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `CHK_UserId_Approval` FOREIGN KEY (`ApproverId`) REFERENCES `users` (`UserId`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `CHK_checklistId_Approval` FOREIGN KEY (`CheckListId`) REFERENCES `checklist` (`CheckListId`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `thesis_checklist_file_map`
--
ALTER TABLE `thesis_checklist_file_map`
  ADD CONSTRAINT `CHK_Thesis_Id` FOREIGN KEY (`ThesisId`) REFERENCES `thesis` (`ThesisId`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `CHK_checklist_Id` FOREIGN KEY (`CheckListId`) REFERENCES `checklist` (`CheckListId`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `thesis_checklist_map`
--
ALTER TABLE `thesis_checklist_map`
  ADD CONSTRAINT `CHK_ThesisId` FOREIGN KEY (`ThesisId`) REFERENCES `thesis` (`ThesisId`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `CHK_checklistId` FOREIGN KEY (`CheckListId`) REFERENCES `checklist` (`CheckListId`) ON DELETE SET NULL ON UPDATE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


/***********************************************/
CREATE PROCEDURE SplitAndInsertArrayString()
BEGIN
    DECLARE pos INT DEFAULT 1;
    DECLARE len INT;
    DECLARE value VARCHAR(255);

    -- Create a temporary table to store the individual values
    DROP TEMPORARY TABLE IF EXISTS temp_students;
    CREATE TEMPORARY TABLE temp_students (StudentId VARCHAR(255));

    -- Find the number of values in the array string
    SET len = CHAR_LENGTH(array_string) - CHAR_LENGTH(REPLACE(array_string, ',', '')) + 1;

    -- Use a loop to extract each value and insert it into the temporary table
    WHILE pos <= len DO
        SET value = SUBSTRING_INDEX(SUBSTRING_INDEX(array_string, ',', pos), ',', -1);
        INSERT INTO temp_students (StudentId) VALUES (value);
        SET pos = pos + 1;
    END WHILE;
END


/***********************************************/
DROP PROCEDURE IF EXISTS CreateNewThesis;

DELIMITER //

CREATE PROCEDURE CreateNewThesis(
    IN title VARCHAR(1000),
    IN proponents VARCHAR(1000),
    IN adviser INT,
    IN instructor INT,
    IN school_year VARCHAR(100),
    IN dateofdefense DATE,
    IN createdby VARCHAR(255)
)
BEGIN
    -- Declare a variable to track errors
    DECLARE error_occurred BOOLEAN DEFAULT FALSE;
    DECLARE last_thesis_id INT;

    -- Start the transaction
    START TRANSACTION;

    /* Insert into thesis table */
    INSERT INTO `thesis` (`ThesisId`, `Title`, `AdviserId`, `InstructorId`, `School`, `SchoolYear`, `DateOfFinalDefense`, `CreatedBy`, `CreatedDate`, `LastModifiedBy`, `LastModifiedDate`) VALUES 
    (NULL, title, adviser, instructor, 'SAINT MARY’S UNIVERSITY', school_year, dateofdefense, createdby, current_timestamp(), createdby, current_timestamp());

    SELECT LAST_INSERT_ID() INTO last_thesis_id;
    
    /* Split and Save proponents to temp_students */
    CALL SplitAndInsertArrayString(proponents);
    
    /* Insert into thesisstudentmap table */
    INSERT INTO `thesisstudentmap` (`ThesisStudentMapId`, `ThesisId`, `StudentId`) 
    SELECT NULL, last_thesis_id, s.StudentId 
    FROM temp_students temp
    LEFT JOIN Student s
    ON temp.StudentId = s.UserId;

    /* Insert into thesis_checklist_map table */
    INSERT INTO `thesis_checklist_map`
    SELECT NULL, last_thesis_id, CheckListId, 0, 'Not Started' FROM checklist;

    /* Create a temporary table to store the individual values */
    DROP TEMPORARY TABLE IF EXISTS temp_approvers;
    CREATE TEMPORARY TABLE temp_approvers (CheckListId INT, Approver VARCHAR(255));

    INSERT INTO temp_approvers (CheckListId, Approver)
    SELECT CheckListId,
     TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(c.Assignee, ',', numbers.n), ',', -1)) AS Approver
    FROM checklist c
    JOIN (
    SELECT 
        (a.N + b.N * 10 + 1) AS n
    FROM 
        (SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
        CROSS JOIN (SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
) AS numbers
ON 
    CHAR_LENGTH(c.Assignee) - CHAR_LENGTH(REPLACE(c.Assignee, ',', '')) >= numbers.n - 1
    WHERE Action = 'Approval';

    /* Insert into thesis_checklist_approval_map */
    INSERT INTO `thesis_checklist_approval_map`
    SELECT NULL, 
            last_thesis_id AS ThesisId, 
            CheckListId,
            CASE
              WHEN Approver = 'Adviser' THEN adviser
              WHEN Approver = 'Instructor' THEN instructor
            END AS ApproverId,
            0 AS Approved
    FROM temp_approvers
    WHERE Approver IN ('Adviser', 'Instructor');

    INSERT INTO `thesis_checklist_approval_map`
    SELECT NULL, 
            last_thesis_id AS ThesisId, 
            a.CheckListId,
            u.UserId AS ApproverId,
            0 AS Approved
    FROM temp_approvers a
    LEFT JOIN users u
    ON a.Approver = u.Role
    AND u.Role = 'Research Coordinator'
    WHERE a.Approver = 'Research Coordinator';

    INSERT INTO `thesispanelmembermap` VALUES (NULL, last_thesis_id, '');
    
    -- Check for errors
    IF NOT error_occurred THEN
        -- If no errors, commit the transaction
        COMMIT;
    ELSE
        -- If an error occurred, rollback the transaction
        ROLLBACK;
    END IF;

    DROP TEMPORARY TABLE IF EXISTS temp_students;
    DROP TEMPORARY TABLE IF EXISTS temp_approvers;
END //

DELIMITER ;

/***********************************************/
DROP PROCEDURE IF EXISTS CreateNewUser;

DELIMITER //

CREATE PROCEDURE CreateNewUser(
    IN _role VARCHAR(255),
    IN _name VARCHAR(255),
    IN username VARCHAR(255),
    IN _password VARCHAR(255), 
    IN email VARCHAR(255),
    IN secquestion VARCHAR(255),
    IN securityanswer VARCHAR(255),
    IN course VARCHAR(255),
    IN department VARCHAR(255),
    IN _year VARCHAR(255)
)
BEGIN
    -- Declare a variable to track errors
    DECLARE error_occurred BOOLEAN DEFAULT FALSE;
    DECLARE last_thesis_id INT;

    -- Start the transaction
    START TRANSACTION;

    -- Insert into users table
    INSERT INTO `users` (UserId, Role, Name, UserName, Password, Email, SecurityQuestion, SecurityAnswer) 
    VALUES (NULL, _role, _name, username, _password, email, secquestion, securityanswer);

    -- Get the last insert id
    SELECT LAST_INSERT_ID() INTO last_thesis_id;

    -- If role is Student, insert into student table
    IF _role = 'Student' THEN
        INSERT INTO `student` 
        SELECT NULL, 
            UserId, 
            SUBSTRING_INDEX(Name, ' ', 1) AS FirstName, 
            NULL AS MiddleName, 
            TRIM(SUBSTRING_INDEX(Name, ' ', -1)) AS LastName,
            course,
            department,
            _year,
            current_timestamp,
            _name
        FROM users
        WHERE UserId = last_thesis_id;
    END IF;

    -- Check for errors
    IF NOT error_occurred THEN
        -- If no errors, commit the transaction
        COMMIT;
    ELSE
        -- If an error occurred, rollback the transaction
        ROLLBACK;
    END IF;
END //

DELIMITER ;

/***********************************************/

DROP PROCEDURE IF EXISTS CalculateThesisProgress;

DELIMITER //

CREATE PROCEDURE CalculateThesisProgress(IN thesisId INT)
BEGIN
    DECLARE total INT;
    DECLARE completed INT;
    DECLARE inProgress INT;
    DECLARE notStarted INT;
    DECLARE percentage DECIMAL(5, 0);
    
    SET @sql := CONCAT('CREATE TEMPORARY TABLE Temp_Table SELECT * FROM thesis_checklist_vw WHERE ThesisId = ', thesisId, ';');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    
    SELECT COUNT(*) INTO total FROM Temp_Table;
    SELECT COUNT(*) INTO completed FROM Temp_Table WHERE Status = 'Completed';
    SELECT COUNT(*) INTO inProgress FROM Temp_Table WHERE Status = 'In Progress';
    SELECT COUNT(*) INTO notStarted FROM Temp_Table WHERE Status = 'Not Started';
    SET percentage := (completed / total) * 100;
    
    -- -- Select results
    SELECT total AS Total, completed AS Completed, inProgress AS InProgress, notStarted AS NotStarted, ROUND(percentage, 0) AS Percentage;

    DROP TEMPORARY TABLE IF EXISTS Temp_Table;
END //

DELIMITER ;

/***********************************************/

CREATE OR REPLACE VIEW `adviser_student_vw` AS
SELECT `t`.`ThesisId` AS `ThesisId`,
`t`.`School` AS `School`,
`t`.`SchoolYear` AS `SchoolYear`,
`u`.`Name` AS `Adviser`,
concat(`s`.`FirstName`,' ',`s`.`MiddleName`,' ',`s`.`LastName`) AS `StudentName`,
`t`.`DateOfFinalDefense` AS `DateOfFinalDefense` 
FROM `thesis` `t` 
left join `users` `u` 
on `t`.`AdviserId` = `u`.`UserId`
left join `thesisstudentmap` `tsm` 
on `t`.`ThesisId` = `tsm`.`ThesisId` 
left join `student` `s` 
on `tsm`.`StudentId` = `s`.`StudentId`;