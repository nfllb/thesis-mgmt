-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2024 at 08:48 AM
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

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `CalculateThesisProgress`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `CalculateThesisProgress` (IN `thesisId` INT)   BEGIN
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
END$$

DROP PROCEDURE IF EXISTS `CreateNewThesis`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateNewThesis` (IN `title` VARCHAR(1000), IN `proponents` VARCHAR(1000), IN `adviser` INT, IN `instructor` INT, IN `school_year` VARCHAR(100), IN `dateofdefense` DATE, IN `createdby` VARCHAR(255))   BEGIN
    -- Declare a variable to track errors
    DECLARE error_occurred BOOLEAN DEFAULT FALSE;
    DECLARE last_thesis_id INT;

    -- Start the transaction
    START TRANSACTION;

    /* Insert into thesis table */
    INSERT INTO `thesis` (`ThesisId`, `Title`, `AdviserId`, `InstructorId`, `School`, `SchoolYear`, `DateOfFinalDefense`, `Status`, `CreatedBy`, `CreatedDate`, `LastModifiedBy`, `LastModifiedDate`) VALUES 
    (NULL, title, adviser, instructor, 'SAINT MARY’S UNIVERSITY', school_year, dateofdefense, 'Not Started', createdby, current_timestamp(), createdby, current_timestamp());

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
END$$

DROP PROCEDURE IF EXISTS `CreateNewUser`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateNewUser` (IN `_role` VARCHAR(255), IN `_firstname` VARCHAR(255), IN `_middlename` VARCHAR(255), IN `_lastname` VARCHAR(255), IN `username` VARCHAR(255), IN `_password` VARCHAR(255), IN `email` VARCHAR(255), IN `secquestion` VARCHAR(255), IN `securityanswer` VARCHAR(255), IN `course` VARCHAR(255), IN `department` VARCHAR(255), IN `_year` VARCHAR(255), IN `_idnumber` VARCHAR(255))   BEGIN
    -- Declare a variable to track errors
    DECLARE error_occurred BOOLEAN DEFAULT FALSE;
    DECLARE last_thesis_id INT;
    DECLARE fullName VARCHAR(255);

    -- Start the transaction
    START TRANSACTION;

    SET fullName = CONCAT(_firstname, ' ', _middlename, ' ', _lastname);
    -- Insert into users table
    INSERT INTO `users` (UserId, Role, Name, UserName, Password, Email, SecurityQuestion, SecurityAnswer, Status) 
    VALUES (NULL, _role, fullName, username, _password, email, secquestion, securityanswer, 'Active');

    -- Get the last insert id
    SELECT LAST_INSERT_ID() INTO last_thesis_id;

    -- If role is Student, insert into student table
    IF _role = 'Student' THEN
        INSERT INTO `student` 
        SELECT NULL, 
            UserId,
            _idnumber AS IDNumber,
            _firstname AS FirstName, 
            _middlename AS MiddleName, 
            _lastname AS LastName,
            course,
            department,
            _year,
            current_timestamp,
            fullName
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
END$$

DROP PROCEDURE IF EXISTS `getDashboardDetails`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getDashboardDetails` (IN `course` VARCHAR(255), IN `department` VARCHAR(255))   BEGIN
  select (select count( distinct `t`.`ThesisId`) AS `totalDepartment` 
          from ((`thesis_mgmt`.`thesis` `t` 
          left join `thesis_mgmt`.`thesisstudentmap` `tsm` 
          on(`t`.`ThesisId` = `tsm`.`ThesisId`)) 
          left join `thesis_mgmt`.`student` `s` 
          on(`tsm`.`StudentId` = `s`.`StudentId`)) 
          where `s`.`Department` = department) AS `totalDepartment`,

          IF(course = 'All', 
            (select count( distinct `t`.`ThesisId`) AS `totalCourse` 
            from ((`thesis_mgmt`.`thesis` `t` 
            left join `thesis_mgmt`.`thesisstudentmap` `tsm` 
            on(`t`.`ThesisId` = `tsm`.`ThesisId`)) 
            left join `thesis_mgmt`.`student` `s` 
            on(`tsm`.`StudentId` = `s`.`StudentId`)) 
            where `s`.`Department` = department),

            (select count( distinct `t`.`ThesisId`) AS `totalCourse` 
            from ((`thesis_mgmt`.`thesis` `t` 
            left join `thesis_mgmt`.`thesisstudentmap` `tsm` 
            on(`t`.`ThesisId` = `tsm`.`ThesisId`)) 
            left join `thesis_mgmt`.`student` `s` 
            on(`tsm`.`StudentId` = `s`.`StudentId`)) 
            where `s`.`Department` = department 
            and `s`.`Course` = course) ) AS `totalCourse`,
          
          IF(course = 'All', 
          (SELECT COUNT(DISTINCT ThesisId)
          FROM thesis_checklist_map
          WHERE Status != 'Not Started'
          AND ThesisId IN ( select t.ThesisId
                            from ((`thesis_mgmt`.`thesis` `t` 
                                  left join `thesis_mgmt`.`thesisstudentmap` `tsm` 
                                  on(`t`.`ThesisId` = `tsm`.`ThesisId`)) 
                                  left join `thesis_mgmt`.`student` `s` 
                                  on(`tsm`.`StudentId` = `s`.`StudentId`)) 
                            where `s`.`Department` = department)
          
          ),
          
          (SELECT COUNT(DISTINCT ThesisId)
          FROM thesis_checklist_map
          WHERE Status != 'Not Started'
          AND ThesisId IN ( select t.ThesisId
                            from ((`thesis_mgmt`.`thesis` `t` 
                                  left join `thesis_mgmt`.`thesisstudentmap` `tsm` 
                                  on(`t`.`ThesisId` = `tsm`.`ThesisId`)) 
                                  left join `thesis_mgmt`.`student` `s` 
                                  on(`tsm`.`StudentId` = `s`.`StudentId`)) 
                            where `s`.`Department` = department 
                            and `s`.`Course` = course)
          
          )) AS `pendingDefense`,
          
          (select count( distinct `thesis_mgmt`.`users`.`UserId`) AS `activeUser` 
          from `thesis_mgmt`.`users` 
          where `thesis_mgmt`.`users`.`Status` = 'Active') AS `activeUser`;
END$$

DROP PROCEDURE IF EXISTS `getUploadedFileCount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUploadedFileCount` (IN `thesisId` INT)   BEGIN
  DECLARE FileCount INT;

   SET @sql := CONCAT('CREATE TEMPORARY TABLE Temp_Table SELECT * FROM thesis_checklist_file_map WHERE ThesisId = ', thesisId, ';');
   PREPARE stmt FROM @sql;
   EXECUTE stmt;
   DEALLOCATE PREPARE stmt;
   
   SELECT COUNT(*) INTO FileCount FROM Temp_Table;

   SELECT FileCount;

   DROP TEMPORARY TABLE IF EXISTS Temp_Table;
END$$

DROP PROCEDURE IF EXISTS `SplitAndInsertArrayString`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SplitAndInsertArrayString` (IN `array_string` VARCHAR(255))   BEGIN
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
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `adviser`
--

DROP TABLE IF EXISTS `adviser`;
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
DROP VIEW IF EXISTS `adviser_student_vw`;
CREATE TABLE `adviser_student_vw` (
`ThesisId` int(11)
,`School` varchar(255)
,`SchoolYear` varchar(255)
,`Adviser` varchar(255)
,`StudentName` text
,`DateOfFinalDefense` date
);

-- --------------------------------------------------------

--
-- Table structure for table `checklist`
--

DROP TABLE IF EXISTS `checklist`;
CREATE TABLE `checklist` (
  `CheckListId` int(11) NOT NULL,
  `Part` int(11) NOT NULL,
  `StepNumber` int(11) NOT NULL,
  `TaskName` varchar(1000) NOT NULL,
  `TaskNameAlias` varchar(1000) NOT NULL,
  `Assignee` varchar(255) NOT NULL,
  `Action` enum('Manual','Upload','Approval','Select') NOT NULL,
  `FormShortDesc` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `checklist`
--

TRUNCATE TABLE `checklist`;
--
-- Dumping data for table `checklist`
--

INSERT INTO `checklist` (`CheckListId`, `Part`, `StepNumber`, `TaskName`, `TaskNameAlias`, `Assignee`, `Action`, `FormShortDesc`) VALUES
(1, 1, 1, 'Class orientation and discussion', 'N/A', 'Instructor', 'Manual', NULL),
(2, 1, 2, 'Titles preparation', 'N/A', 'Researchers', 'Manual', NULL),
(3, 1, 3, 'Evaluation of titles / Title defense', 'N/A', 'Instructor', 'Manual', NULL),
(4, 1, 4, 'Prepare Form 10, Form 11, Form 12', 'Forms_10_to_12', 'Researchers', 'Upload', '*Form 10: Letter of Intent for Prospective Adviser-Promoter\n*Form 11: Adviser-Promoter Acceptance Form\n*Form 12: Agreement on Promotership'),
(5, 1, 5, 'Writing of Chapter 1', 'Chapter_1', 'Researchers', 'Upload', NULL),
(6, 1, 6, 'Checking of Chapter 1', 'N/A', 'Instructor, Adviser', 'Approval', NULL),
(7, 1, 7, 'Writing of Chapter 2', 'Chapter_2', 'Researchers', 'Upload', NULL),
(8, 1, 8, 'Checking of Chapter 2', 'N/A', 'Instructor, Adviser', 'Approval', NULL),
(9, 1, 9, 'Prepare Form 15', 'Form_15', 'Researchers', 'Upload', '*Form 15: Oral Defense Recommendation Sheet'),
(10, 1, 10, 'Prepare Form 13', 'Form_13', 'Researchers', 'Upload', '*Form 13: Checklist for Technical Review'),
(11, 1, 11, 'Print evaluation copies (3 copies)', 'N/A', 'Researchers', 'Manual', NULL),
(12, 1, 12, 'Distribution of Form 13 and evaluation copies', 'N/A', 'Researchers', 'Manual', NULL),
(13, 1, 13, 'Retrieval of Form 13', 'N/A', 'Research Coordinator', 'Manual', NULL),
(14, 1, 14, 'Payment of defense fees', 'Payment_Fee', 'Researchers', 'Upload', NULL),
(15, 1, 15, 'Prepare Form 16', 'Form_16', 'Researchers', 'Upload', '*Form 16: Consent of the Technical Panel'),
(16, 1, 16, 'Print Form 17', 'N/A', 'Researchers', 'Manual', '*Form 17: Research Evaluation Form for Proposal Defense (Basic Pure and Applied Research)'),
(17, 1, 17, 'Proposal defense, Presentation, Noting of recommendations, Filling of Form 17, Retrieval of Form 17', 'N/A', 'Instructor, Research Coordinator', 'Approval', NULL),
(18, 1, 18, 'Accomplish Minutes of defense', 'Minutes_of_Defense', 'Researchers', 'Upload', NULL),
(19, 1, 19, 'Consolidation of Proposal documents', 'N/A', 'Research Coordinator', 'Approval', NULL),
(20, 1, 20, 'Protocol Application for UREB', 'Protocol_Application_UREB', 'Researchers', 'Upload', NULL),
(21, 1, 21, 'Endorsement to UREB', 'Endorsement_UREB', 'Researchers', 'Upload', NULL),
(22, 1, 22, 'Submission to UREB, Payment of Review fees', 'Submission_UREB', 'Researchers', 'Upload', NULL),
(23, 1, 23, 'Issuance of Certificate of Approval or Certificate of Exemption', 'Certificate_of_Approval', 'UREB', 'Upload', NULL),
(24, 1, 24, 'END OF THESIS 1 / CAPSTONE 1', 'N/A', 'Research Coordinator', 'Manual', NULL),
(25, 2, 1, 'Writing the Chapter 3', 'Chapter_3', 'Researchers', 'Upload', NULL),
(26, 2, 2, 'Checking the Chapter 3', 'N/A', 'Instructor', 'Approval', NULL),
(27, 2, 3, 'Writing the Chapter 4', 'Chapter_4', 'Researchers', 'Upload', NULL),
(28, 2, 4, 'Checking the Chapter 4', 'N/A', 'Instructor', 'Approval', NULL),
(29, 2, 5, 'Prepare files for initial plagiarism', 'N/A', 'Researchers', 'Manual', NULL),
(30, 2, 6, 'Run initial plagiarism check. Provide result', 'Initial_Plagiarism_Check_Result', 'Research Coordinator', 'Upload', NULL),
(31, 2, 7, 'Endorsement for plagiarism certificate', 'Endorsement_for_Plagiarism_Certificate', 'Research Coordinator', 'Upload', NULL),
(32, 2, 8, 'Submit endorsement to URC', 'N/A', 'Researchers', 'Manual', NULL),
(33, 2, 9, 'Issue Plagiarism Certificate', 'Plagiarism_Certificate', 'Research Coordinator', 'Upload', NULL),
(34, 2, 10, 'Prepare Form 15', 'Thesis_2_Form_15', 'Researchers', 'Upload', '*Form 15: Oral Defense Recommendation Sheet'),
(35, 2, 11, 'Print Form 13', 'Thesis_2_Form_13', 'Researchers', 'Upload', '*Form 13: Checklist for Technical Review'),
(36, 2, 12, 'Print evaluation copies (3 copies)', 'N/A', 'Researchers', 'Manual', NULL),
(37, 2, 13, 'Distribution of Form 13 and evaluation copies', 'N/A', 'Researchers', 'Manual', NULL),
(38, 2, 14, 'Retrieval of Form 13', 'N/A', 'Research Coordinator', 'Manual', NULL),
(39, 2, 15, 'Payment of defense fees', 'Thesis_2_Defense_Fees', 'Researchers', 'Upload', NULL),
(40, 2, 16, 'Prepare Form 16', 'Thesis_2_Form_16', 'Researchers', 'Upload', '*Form 16: Consent of the Technical Panel'),
(41, 2, 17, 'Print Form 18', 'N/A', 'Research Coordinator', 'Manual', '*Form 18: Research Evaluation Form for Oral Defense'),
(42, 2, 18, 'Proposal defense, Presentation, Noting of recommendations, Filling of Form 18, Retrieval of Form 18', 'N/A', 'Research Coordinator, Instructor', 'Approval', NULL),
(43, 2, 19, 'Accomplish Minutes of defense', 'Thesis_2_Minutes_of_Defense', 'Researchers', 'Upload', NULL),
(44, 2, 20, 'Prepare files for final plagiarism', 'N/A', 'Researchers', 'Manual', NULL),
(45, 2, 21, 'Run final plagiarism check. Provide result', 'Final_Plagiarism_Result', 'Research Coordinator', 'Upload', NULL),
(46, 2, 22, 'Endorsement for plagiarism clearance', 'Endorsement_for_plagiarism_clearance', 'Researchers', 'Upload', NULL),
(47, 2, 23, 'Submit endorsement to URC', 'N/A', 'Researchers', 'Manual', NULL),
(48, 2, 24, 'Issue Plagiarism Clearance', 'Plagiarism_Clearance', 'Researchers', 'Upload', NULL),
(49, 2, 25, 'Select editor', 'N/A', 'Researchers', 'Select', NULL),
(50, 2, 26, 'Endorsement for editing', 'Endorsement_for_editing', 'Researchers', 'Upload', NULL),
(51, 2, 27, 'Submit file for editing', 'N/A', 'Researchers', 'Manual', NULL),
(52, 2, 28, 'Editing of file, Submit result, Sign certification', 'Signed_Certification', 'Researchers', 'Upload', NULL),
(53, 2, 29, 'Submit certification to URC', 'N/A', 'Researchers', 'Manual', NULL),
(54, 2, 30, 'Issue Editing Clearance', 'Issue_Editing_Clearance', 'Researchers', 'Upload', NULL),
(55, 2, 31, 'Reformat manuscript to single spacing', 'Formatted_Manuscript', 'Researchers', 'Upload', NULL),
(56, 2, 32, 'Submit final report to UREB', 'Final_Report_to_UREB', 'Researchers', 'Upload', NULL),
(57, 2, 33, 'Issue Ethics Clearance', 'Ethics_Clearance', 'Researchers', 'Upload', NULL),
(58, 2, 34, 'Prepare Declaration of Originality', 'Declaration_of_Originality', 'Researchers', 'Upload', NULL),
(59, 2, 35, 'Notarization of Declaration of Originality', 'Notarized_Declaration_of_Originality', 'Researchers', 'Upload', NULL),
(60, 2, 36, 'Upload final manuscript in single space', 'Final_Manuscript', 'Researchers', 'Upload', NULL),
(61, 2, 37, 'Checking of final manuscript in single space', 'N/A', 'Instructor', 'Approval', NULL),
(62, 2, 38, 'Printing of final manuscript sample copy', 'N/A', 'Researchers', 'Manual', NULL),
(63, 2, 39, 'Verification of sample copy', 'N/A', 'Instructor', 'Approval', NULL),
(64, 2, 40, 'Signing of Approval Sheet', 'Approval_Sheet', 'Researchers', 'Upload', NULL),
(65, 2, 41, 'Printing of additional copies, Verification of print outs', 'N/A', 'Researchers', 'Manual', NULL),
(66, 2, 42, 'Binding of copies', 'N/A', 'Researchers', 'Manual', NULL),
(67, 2, 43, 'Signing of Approval Sheets of hardbound copies', 'Approval_Sheet_for_hardbound_copies', 'Researchers', 'Upload', NULL),
(68, 2, 44, 'Prepare files for burning', 'N/A', 'Researchers', 'Manual', NULL),
(69, 2, 45, 'Checking of files for burning', 'N/A', 'Research Coordinator', 'Approval', NULL),
(70, 2, 46, 'Burning of CDs', 'N/A', 'Researchers', 'Manual', NULL),
(71, 2, 47, 'Distribution of hardbounds', 'N/A', 'Researchers', 'Manual', NULL),
(72, 2, 48, 'Submission of CDs, Submission of Evaluation forms (Form 31, Form 39, Form 41)', 'Evaluation_Form', 'Researchers', 'Upload', NULL),
(73, 2, 49, 'Verification of submitted CDs, Updating of checklist', 'N/A', 'Research Coordinator', 'Manual', NULL),
(74, 2, 50, 'Submission of working hardware prototypes (BSCpE, BSECE, BSEE)', 'Working_Hardware_Prototype', 'Researchers', 'Upload', NULL),
(75, 2, 51, 'Submission of system installation package (BSIT)', 'System_Installation_Package', 'Researchers', 'Upload', NULL),
(76, 2, 52, 'END OF THESIS 2 CAPSTONE 2', '', 'Research Coordinator', 'Manual', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `editor`
--

DROP TABLE IF EXISTS `editor`;
CREATE TABLE `editor` (
  `EditorId` int(11) NOT NULL,
  `EditorName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `editor`
--

TRUNCATE TABLE `editor`;
--
-- Dumping data for table `editor`
--

INSERT INTO `editor` (`EditorId`, `EditorName`) VALUES
(3, 'DR. ZAYDA S. ASUNCION'),
(4, 'MISS MARITES B. QUEROL'),
(5, 'MRS. MABEL D. MAMAOAG'),
(6, 'MRS. MA. INES R. MINIA'),
(7, 'MRS. ZEMAIA SEN M. PAULINO'),
(8, 'DR. CLARA M. GONZALES'),
(9, 'MRS. HAYDEE D. JAMES'),
(10, 'MRS. LYCEL I. HALOC'),
(11, 'MS. EUXINE PAULINE R. BAUTISTA'),
(12, 'MR. SHERWEEN JERRY PAUL V. SAQUING'),
(13, 'DR. MOISES ALEXANDER T. ASUNCION'),
(14, 'DR. CHRISTOPHER ALLEN S. MARQUEZ'),
(15, 'MR. GEROME H. BAUTISTA'),
(16, 'DR. GLENDA SALEM'),
(17, 'DR. FE YOLANDA G. DEL ROSARIO');

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

DROP TABLE IF EXISTS `file`;
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

DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `fileid` int(11) NOT NULL,
  `DocumentCode` varchar(100) DEFAULT NULL,
  `filename` varchar(500) NOT NULL,
  `filepath` varchar(1000) NOT NULL,
  `Type` enum('Form','Report','','') DEFAULT NULL,
  `OrderNumber` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `files`
--

TRUNCATE TABLE `files`;
--
-- Dumping data for table `files`
--

INSERT INTO `files` (`fileid`, `DocumentCode`, `filename`, `filepath`, `Type`, `OrderNumber`) VALUES
(2, 'URC-FO-064', 'Faculty Remuneration of Student Researches Requested by the Research Chair to the URC Proposal Defense.docx', 'files/reports/URC-FO-064_Faculty-Remuneration-of-Student-Researches-Requested-by-the-Research-Chair-to-the-URC-Proposal-Defense.docx', 'Report', NULL),
(3, 'URC-FO-065', 'URC-FO-065_Faculty-Remuneration-of-Student-Researches-Requested-by-the-Research-Chair-to-the-URC-Oral-Defense.docx', 'files/reports/URC-FO-065_Faculty-Remuneration-of-Student-Researches-Requested-by-the-Research-Chair-to-the-URC-Oral-Defense.docx', 'Report', NULL),
(4, 'URC-FO-077', 'URC-FO-077_Summary-List-of-Adviser-Promoter.docx', 'files/reports/URC-FO-077_Summary-List-of-Adviser-Promoter.docx', 'Report', NULL),
(5, 'URC-FO-078', 'URC-FO-078_Summary-List-of-Panel-Members.docx', 'files/reports/URC-FO-078_Summary-List-of-Panel-Members.docx', 'Report', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `getdashboardgauge`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `getdashboardgauge`;
CREATE TABLE `getdashboardgauge` (
`Part` int(11)
,`Completed` decimal(26,2)
,`InProgress` decimal(26,2)
,`NotStarted` decimal(26,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `getdashboardtable`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `getdashboardtable`;
CREATE TABLE `getdashboardtable` (
`Title` varchar(1000)
,`Name` varchar(255)
,`LastModifiedDate` date
,`Status` enum('Not Started','In Progress','Completed','')
,`Percent` decimal(24,0)
);

-- --------------------------------------------------------

--
-- Table structure for table `panelmember`
--

DROP TABLE IF EXISTS `panelmember`;
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
-- Stand-in structure for view `panel_student_vw`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `panel_student_vw`;
CREATE TABLE `panel_student_vw` (
`ThesisId` int(11)
,`School` varchar(255)
,`SchoolYear` varchar(255)
,`PanelMember` varchar(500)
,`StudentName` text
,`DateOfFinalDefense` date
);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE `student` (
  `StudentId` int(11) NOT NULL,
  `UserId` int(11) DEFAULT NULL,
  `IDNumber` varchar(255) DEFAULT NULL,
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

INSERT INTO `student` (`StudentId`, `UserId`, `IDNumber`, `FirstName`, `MiddleName`, `LastName`, `Course`, `Department`, `Year`, `CreatedDate`, `CreatedBy`) VALUES
(1, 18, 'SMU-0001', 'Student Testing', 'T.', 'One', 'Computer Engineering', 'Engineering', 'Fourth', '2024-04-08 10:02:23', 'AdminUser'),
(2, 19, 'SMU-0002', 'Student', '', 'Two', 'Computer Engineering', 'Engineering', 'Fourth', '2024-04-08 10:02:23', 'AdminUser'),
(3, 20, 'SMU-0003', 'Student', '', 'Three', 'Computer Engineering', 'Engineering', 'Fourth', '2024-04-08 10:03:38', 'AdminUser'),
(4, 21, 'SMU-0004', 'Student', '', 'Four', 'Architecture', 'Engineering', 'Fourth', '2024-04-08 10:03:38', 'AdminUser'),
(5, 22, 'SMU-0005', 'Student', '', 'Five', 'Architecture', 'Engineering', 'Fourth', '2024-04-08 10:04:15', 'AdminUser'),
(6, 23, 'SMU-0006', 'Student', '', 'Six', 'Architecture', 'Engineering', 'Fourth', '2024-04-08 10:04:15', 'AdminUser'),
(7, 24, 'SMU-0007', 'Student', '', 'Seven', 'Civil Engineering', 'Engineering', 'Fourth', '2024-04-17 19:33:14', 'AdminUser'),
(8, 25, 'SMU-0008', 'Student', '', 'Eight', 'Civil Engineering', 'Engineering', 'Fourth', '2024-04-17 19:33:14', 'AdminUser'),
(9, 26, 'SMU-0009', 'Student', '', 'Nine', 'Civil Engineering', 'Engineering', 'Fourth', '2024-04-17 19:33:48', 'AdminUser'),
(10, 33, 'SMU-0010', 'Student', '', 'Ten', 'Computer Engineering', 'Engineering', 'Fourth', '2024-04-17 19:33:48', 'AdminUser'),
(11, 35, 'SMU-0011', 'Student', '', 'Eleven', 'Computer Engineering', 'Engineering', 'Fourth', '2024-04-17 19:33:48', 'AdminUser'),
(12, 36, 'SMU-0012', 'Student', '', 'Twelve', 'Computer Engineering', 'Engineering', 'Fourth', '2024-04-17 19:33:48', 'AdminUser'),
(13, 52, 'SMU-0013', 'Student', '', 'Thirteen', 'Electronics Engineering', 'engineering', 'Fifth', '2024-04-28 23:04:29', 'Student Thirteen'),
(14, 56, 'SMU-0014', 'Student', 'T', '001', 'Electronics Engineering', 'engineering', 'Fifth', '2024-05-19 21:51:59', 'Student T 001');

-- --------------------------------------------------------

--
-- Table structure for table `thesis`
--

DROP TABLE IF EXISTS `thesis`;
CREATE TABLE `thesis` (
  `ThesisId` int(11) NOT NULL,
  `Title` varchar(1000) NOT NULL,
  `AdviserId` int(11) DEFAULT NULL,
  `InstructorId` int(11) DEFAULT NULL,
  `School` varchar(255) NOT NULL DEFAULT 'SAINT MARY’S UNIVERSITY',
  `SchoolYear` varchar(255) NOT NULL,
  `DateOfFinalDefense` date DEFAULT NULL,
  `Status` enum('Not Started','In Progress','Completed','') DEFAULT 'Not Started',
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

INSERT INTO `thesis` (`ThesisId`, `Title`, `AdviserId`, `InstructorId`, `School`, `SchoolYear`, `DateOfFinalDefense`, `Status`, `CreatedBy`, `CreatedDate`, `LastModifiedBy`, `LastModifiedDate`) VALUES
(1, 'Thesis Management System Design and Implementation of a Thesis Management System at Saint Mary\'s University Research Coordinator Office', 7, 12, 'SAINT MARY’S UNIVERSITY', '2023-2024 Sem 2', '2024-04-15', 'In Progress', 'System Admin', '2024-04-22', 'Adviser Testing T. One', '2024-05-24'),
(2, 'Design and Implementation of a Thesis Management System at Saint Mary\'s University Research Coordinator Office', 1, 13, 'SAINT MARY’S UNIVERSITY', '2023-2024 Sem 2', '2024-04-16', 'In Progress', 'System Admin', '2024-04-22', 'Student Five', '2024-05-09'),
(3, 'This thesis is for Instructor role', 8, 14, 'SAINT MARY’S UNIVERSITY', '2023-2024 Sem 1', '2024-04-27', 'In Progress', 'System Admin', '2024-04-22', 'RC', '2024-05-05'),
(6, 'Thesis Created By mySQL', 5, 15, 'SAINT MARY’S UNIVERSITY', '2023-2024 Sem 1', '2024-04-28', 'In Progress', 'System Admin', '2024-04-28', 'Instructor Four', '2024-05-20'),
(7, 'Created via tool', 54, 16, 'SAINT MARY’S UNIVERSITY', '2022-2023 Sem 2', '2024-05-11', 'Completed', 'Student Ten', '2024-04-28', 'RC', '2024-05-08'),
(8, 'Testing Only', 7, 6, 'SAINT MARY’S UNIVERSITY', '2022-2023 Sem 1', '2024-05-15', 'In Progress', 'RC', '2024-05-05', 'RC', '2024-05-19');

-- --------------------------------------------------------

--
-- Table structure for table `thesispanelmembermap`
--

DROP TABLE IF EXISTS `thesispanelmembermap`;
CREATE TABLE `thesispanelmembermap` (
  `ThesisPanelMemberMap` int(11) NOT NULL,
  `ThesisId` int(11) DEFAULT NULL,
  `PanelMembers` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `thesispanelmembermap`
--

TRUNCATE TABLE `thesispanelmembermap`;
--
-- Dumping data for table `thesispanelmembermap`
--

INSERT INTO `thesispanelmembermap` (`ThesisPanelMemberMap`, `ThesisId`, `PanelMembers`) VALUES
(1, 1, 'Panel One;Panel Two;Four;One'),
(3, 2, 'Panel 4;Panel 7'),
(4, 3, ''),
(5, 8, 'test panel;');

-- --------------------------------------------------------

--
-- Table structure for table `thesisstudentmap`
--

DROP TABLE IF EXISTS `thesisstudentmap`;
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
(9, 3, 9),
(11, 6, 8),
(12, 6, 4),
(13, 6, 5),
(14, 7, 10),
(15, 7, 11),
(16, 7, 12),
(17, 8, 9),
(18, 8, 5),
(19, 8, 7);

-- --------------------------------------------------------

--
-- Table structure for table `thesis_checklist_approval_map`
--

DROP TABLE IF EXISTS `thesis_checklist_approval_map`;
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
(4, 3, 26, 14, 1),
(5, 6, 6, 15, 1),
(6, 6, 6, 5, 1),
(7, 6, 8, 15, 1),
(8, 6, 17, 15, 0),
(9, 6, 26, 15, 0),
(10, 6, 28, 15, 0),
(11, 6, 42, 15, 0),
(12, 6, 61, 15, 0),
(13, 6, 63, 15, 0),
(20, 6, 17, 3, 0),
(21, 6, 19, 3, 0),
(22, 6, 42, 3, 0),
(23, 6, 69, 3, 0),
(24, 6, 17, 34, 0),
(25, 6, 19, 34, 0),
(26, 6, 42, 34, 0),
(27, 6, 69, 34, 0),
(35, 7, 6, 16, 0),
(36, 7, 6, 9, 0),
(37, 7, 8, 16, 0),
(38, 7, 17, 16, 0),
(39, 7, 26, 16, 0),
(40, 7, 28, 16, 0),
(41, 7, 42, 16, 0),
(42, 7, 61, 16, 0),
(43, 7, 63, 16, 0),
(50, 7, 17, 3, 0),
(51, 7, 19, 3, 0),
(52, 7, 42, 3, 0),
(53, 7, 69, 3, 0),
(54, 7, 17, 34, 0),
(55, 7, 19, 34, 0),
(56, 7, 42, 34, 0),
(57, 7, 69, 34, 0),
(58, 8, 6, 6, 0),
(59, 8, 6, 11, 0),
(60, 8, 8, 6, 0),
(61, 8, 17, 6, 0),
(62, 8, 26, 6, 0),
(63, 8, 28, 6, 0),
(64, 8, 42, 6, 0),
(65, 8, 61, 6, 0),
(66, 8, 63, 6, 0),
(73, 8, 17, 3, 0),
(74, 8, 19, 3, 0),
(75, 8, 42, 3, 0),
(76, 8, 69, 3, 0),
(77, 8, 17, 34, 0),
(78, 8, 19, 34, 0),
(79, 8, 42, 34, 0),
(80, 8, 69, 34, 0),
(81, 6, 8, 5, 0),
(82, 1, 6, 12, 1),
(83, 1, 6, 7, 1),
(84, 1, 8, 12, 1),
(85, 1, 8, 7, 1),
(86, 1, 17, 12, 0),
(87, 1, 26, 12, 0),
(88, 1, 28, 12, 1),
(89, 1, 42, 12, 0),
(90, 1, 61, 12, 0),
(91, 1, 63, 12, 0),
(97, 1, 17, 3, 0),
(98, 1, 19, 3, 0),
(99, 1, 42, 3, 0),
(100, 1, 69, 3, 0),
(101, 1, 17, 34, 0),
(102, 1, 19, 34, 0),
(103, 1, 42, 34, 0),
(104, 1, 69, 34, 0);

-- --------------------------------------------------------

--
-- Table structure for table `thesis_checklist_editor_map`
--

DROP TABLE IF EXISTS `thesis_checklist_editor_map`;
CREATE TABLE `thesis_checklist_editor_map` (
  `ThesisChecklistEditorId` int(11) NOT NULL,
  `ThesisId` int(11) DEFAULT NULL,
  `CheckListId` int(11) DEFAULT NULL,
  `EditorId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `thesis_checklist_editor_map`
--

TRUNCATE TABLE `thesis_checklist_editor_map`;
--
-- Dumping data for table `thesis_checklist_editor_map`
--

INSERT INTO `thesis_checklist_editor_map` (`ThesisChecklistEditorId`, `ThesisId`, `CheckListId`, `EditorId`) VALUES
(3, 1, 49, 9),
(4, 6, 49, 15);

-- --------------------------------------------------------

--
-- Table structure for table `thesis_checklist_file_map`
--

DROP TABLE IF EXISTS `thesis_checklist_file_map`;
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
(2, 3, 4, 'PHP References.doc', './../uploads/PHP References.doc', 'RC', '2024-04-22 20:39:49'),
(5, 3, 4, 'Forms_10_to_12_This_thesis_is_for_Instructor_role', './../uploads/Forms_10_to_12_This_thesis_is_for_Instructor_role.docx', 'RC', '2024-04-22 22:32:52'),
(10, 3, 25, 'Chapter_3_This_thesis_is_for_Instructor_role', './../uploads/Chapter_3_This_thesis_is_for_Instructor_role.docx', 'Student Nine', '2024-04-25 17:44:49'),
(11, 3, 25, 'Chapter_3_This_thesis_is_for_Instructor_role', './../uploads/Chapter_3_This_thesis_is_for_Instructor_role.docx', 'Student Seven', '2024-04-25 17:57:23'),
(12, 3, 4, 'Forms_10_to_12_This_thesis_is_for_Instructor_role', './../uploads/Forms_10_to_12_This_thesis_is_for_Instructor_role.doc', 'Student Nine', '2024-04-25 19:09:46'),
(13, 3, 5, 'Chapter_1_This_thesis_is_for_Instructor_role', './../uploads/Chapter_1_This_thesis_is_for_Instructor_role.docx', 'Student Nine', '2024-04-25 19:09:54'),
(16, 6, 4, 'Chapter_3_Thesis_Created_By_mySQL', './../uploads/Chapter_3_Thesis_Created_By_mySQL.docx', 'Student Four', '2024-04-28 16:11:23'),
(17, 6, 5, 'Chapter_3_Thesis_Created_By_mySQL', './../uploads/Chapter_3_Thesis_Created_By_mySQL.docx', 'Student Four', '2024-04-28 16:11:36'),
(18, 1, 7, 'Chapter_2_Thesis_Management_System', 'C:/xampp/htdocs/thesis-mgmt/uploads/Chapter_2_Thesis_Management_System.docx', 'Student One', '2024-05-04 20:37:20'),
(19, 8, 4, 'Chapter_3_Testing_Only', 'C:/xampp/htdocs/thesis-mgmt/uploads/Chapter_3_Testing_Only.docx', 'Student Five', '2024-05-09 18:59:57'),
(23, 1, 10, 'Form_13_Thesis_Management_System', 'C:/xampp/htdocs/thesis-mgmt/uploads/Form_13_Thesis_Management_System.docx', 'Student Testing T. One', '2024-05-20 15:32:37'),
(24, 6, 7, 'Chapter_3_Thesis_Created_By_mySQL', 'C:/xampp/htdocs/thesis-mgmt/uploads/Chapter_3_Thesis_Created_By_mySQL.docx', 'Student Four', '2024-05-20 17:04:40'),
(26, 1, 4, 'Chapter_4_Thesis_Management_System', 'C:/xampp/htdocs/thesis-mgmt/uploads/Chapter_4_Thesis_Management_System.pdf', 'Student Testing T. One', '2024-05-24 18:08:00'),
(27, 1, 27, 'Chapter_4_Thesis_Management_System', 'C:/xampp/htdocs/thesis-mgmt/uploads/Chapter_4_Thesis_Management_System.docx', 'Student Testing T. One', '2024-05-24 18:28:21'),
(29, 1, 25, 'Chapter_3_Thesis_Management_System', 'C:/xampp/htdocs/thesis-mgmt/uploads/Chapter_3_Thesis_Management_System.docx', 'Student Testing T. One', '2024-05-24 18:49:31'),
(31, 1, 31, 'Endorsement_for_Plagiarism_Certificate_Thesis_Management_System', 'C:/xampp/htdocs/thesis-mgmt/uploads/Endorsement_for_Plagiarism_Certificate_Thesis_Management_System.docx', 'Ri Coordinator', '2024-05-24 18:52:36'),
(32, 1, 30, 'Initial_Plagiarism_Check_Result_Thesis_Management_System', 'C:/xampp/htdocs/thesis-mgmt/uploads/Initial_Plagiarism_Check_Result_Thesis_Management_System.docx', 'Ri Coordinator', '2024-05-24 18:52:57');

-- --------------------------------------------------------

--
-- Table structure for table `thesis_checklist_map`
--

DROP TABLE IF EXISTS `thesis_checklist_map`;
CREATE TABLE `thesis_checklist_map` (
  `ThesisChecklistId` int(11) NOT NULL,
  `ThesisId` int(11) DEFAULT NULL,
  `CheckListId` int(11) DEFAULT NULL,
  `Completed` tinyint(1) NOT NULL DEFAULT 0,
  `Status` enum('Not Started','In Progress','Completed') NOT NULL DEFAULT 'Not Started',
  `CompletedDate` datetime DEFAULT NULL,
  `CompletedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `thesis_checklist_map`
--

TRUNCATE TABLE `thesis_checklist_map`;
--
-- Dumping data for table `thesis_checklist_map`
--

INSERT INTO `thesis_checklist_map` (`ThesisChecklistId`, `ThesisId`, `CheckListId`, `Completed`, `Status`, `CompletedDate`, `CompletedBy`) VALUES
(1, 1, 1, 1, 'Completed', '2024-05-01 15:21:07', NULL),
(2, 1, 2, 0, 'Completed', '2024-05-02 15:21:07', NULL),
(3, 1, 3, 0, 'Completed', '2024-05-03 15:21:07', NULL),
(4, 1, 4, 0, 'Completed', '2024-05-24 18:08:00', 'Student Testing T. One'),
(5, 1, 5, 0, 'Completed', '2024-05-08 15:21:07', NULL),
(6, 1, 6, 0, 'Completed', '2024-05-10 15:21:07', NULL),
(7, 1, 7, 0, 'Completed', '2024-05-12 15:21:07', NULL),
(8, 1, 8, 0, 'Completed', '2024-05-24 19:04:11', 'Adviser Testing T. One'),
(9, 1, 9, 0, 'Not Started', NULL, NULL),
(10, 1, 10, 0, 'Completed', '2024-05-20 15:32:37', 'Student Testing T. One'),
(11, 1, 11, 0, 'Completed', '2024-05-20 15:35:19', 'Student Testing T. One'),
(12, 1, 12, 0, 'Not Started', NULL, NULL),
(13, 1, 13, 0, 'Not Started', NULL, NULL),
(14, 1, 14, 0, 'Not Started', NULL, NULL),
(15, 1, 15, 0, 'Not Started', NULL, NULL),
(16, 1, 16, 0, 'Not Started', NULL, NULL),
(17, 1, 17, 0, 'Not Started', NULL, NULL),
(18, 1, 18, 0, 'Not Started', NULL, NULL),
(19, 1, 19, 0, 'Not Started', NULL, NULL),
(20, 1, 20, 0, 'Not Started', NULL, NULL),
(21, 1, 21, 0, 'Not Started', NULL, NULL),
(22, 1, 22, 0, 'Not Started', NULL, NULL),
(23, 1, 23, 0, 'Not Started', NULL, NULL),
(24, 1, 24, 0, 'Not Started', NULL, NULL),
(32, 2, 1, 0, 'Not Started', NULL, NULL),
(33, 2, 2, 0, 'Not Started', NULL, NULL),
(34, 2, 3, 0, 'Not Started', NULL, NULL),
(35, 2, 4, 0, 'Not Started', NULL, NULL),
(36, 2, 5, 0, 'Not Started', NULL, NULL),
(37, 2, 6, 0, 'Not Started', NULL, NULL),
(38, 2, 7, 0, 'Not Started', NULL, NULL),
(39, 2, 8, 0, 'Not Started', NULL, NULL),
(40, 2, 9, 0, 'Not Started', NULL, NULL),
(41, 2, 10, 0, 'Not Started', NULL, NULL),
(42, 2, 11, 0, 'Not Started', NULL, NULL),
(43, 2, 12, 0, 'Not Started', NULL, NULL),
(44, 2, 13, 0, 'Not Started', NULL, NULL),
(45, 2, 14, 0, 'Not Started', NULL, NULL),
(46, 2, 15, 0, 'Not Started', NULL, NULL),
(47, 2, 16, 0, 'Not Started', NULL, NULL),
(48, 2, 17, 0, 'Not Started', NULL, NULL),
(49, 2, 18, 0, 'Not Started', NULL, NULL),
(50, 2, 19, 0, 'Not Started', NULL, NULL),
(51, 2, 20, 0, 'Not Started', NULL, NULL),
(52, 2, 21, 0, 'Not Started', NULL, NULL),
(53, 2, 22, 0, 'Not Started', NULL, NULL),
(54, 2, 23, 0, 'Not Started', NULL, NULL),
(55, 2, 24, 0, 'Not Started', NULL, NULL),
(63, 3, 1, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(64, 3, 2, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(65, 3, 3, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(66, 3, 4, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(67, 3, 5, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(68, 3, 6, 0, 'Not Started', NULL, NULL),
(69, 3, 7, 0, 'Not Started', NULL, NULL),
(70, 3, 8, 0, 'Not Started', NULL, NULL),
(71, 3, 9, 0, 'Not Started', NULL, NULL),
(72, 3, 10, 0, 'Not Started', NULL, NULL),
(73, 3, 11, 0, 'Not Started', NULL, NULL),
(74, 3, 12, 0, 'Not Started', NULL, NULL),
(75, 3, 13, 0, 'Not Started', NULL, NULL),
(76, 3, 14, 0, 'Not Started', NULL, NULL),
(77, 3, 15, 0, 'Not Started', NULL, NULL),
(78, 3, 16, 0, 'Not Started', NULL, NULL),
(79, 3, 17, 0, 'Not Started', NULL, NULL),
(80, 3, 18, 0, 'Not Started', NULL, NULL),
(81, 3, 19, 0, 'Not Started', NULL, NULL),
(82, 3, 20, 0, 'Not Started', NULL, NULL),
(83, 3, 21, 0, 'Not Started', NULL, NULL),
(84, 3, 22, 0, 'Not Started', NULL, NULL),
(85, 3, 23, 0, 'Not Started', NULL, NULL),
(86, 3, 24, 0, 'Not Started', NULL, NULL),
(94, 1, 25, 0, 'Completed', '2024-05-24 18:49:31', 'Student Testing T. One'),
(95, 1, 26, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(96, 1, 27, 0, 'Completed', '2024-05-24 18:28:22', 'Student Testing T. One'),
(97, 1, 28, 0, 'Completed', '2024-05-24 18:36:46', 'Instructor One'),
(98, 1, 29, 0, 'Completed', '2024-05-24 18:37:13', 'Student Testing T. One'),
(99, 1, 30, 0, 'Completed', '2024-05-24 18:52:57', 'Ri Coordinator'),
(100, 1, 31, 0, 'Completed', '2024-05-24 18:52:36', 'Ri Coordinator'),
(101, 1, 32, 0, 'Not Started', NULL, NULL),
(102, 1, 33, 0, 'Not Started', NULL, NULL),
(103, 1, 34, 0, 'Not Started', NULL, NULL),
(104, 1, 35, 0, 'Not Started', NULL, NULL),
(105, 1, 36, 0, 'Not Started', NULL, NULL),
(106, 1, 37, 0, 'Not Started', NULL, NULL),
(107, 1, 38, 0, 'Not Started', NULL, NULL),
(108, 1, 39, 0, 'Not Started', NULL, NULL),
(109, 1, 40, 0, 'Not Started', NULL, NULL),
(110, 1, 41, 0, 'Not Started', NULL, NULL),
(111, 1, 42, 0, 'Not Started', NULL, NULL),
(112, 1, 43, 0, 'Not Started', NULL, NULL),
(113, 1, 44, 0, 'Not Started', NULL, NULL),
(114, 1, 45, 0, 'Not Started', NULL, NULL),
(115, 1, 46, 0, 'Not Started', NULL, NULL),
(116, 1, 47, 0, 'Not Started', NULL, NULL),
(117, 1, 48, 0, 'Not Started', NULL, NULL),
(118, 1, 49, 0, 'Not Started', NULL, NULL),
(119, 1, 50, 0, 'Not Started', NULL, NULL),
(120, 1, 51, 0, 'Not Started', NULL, NULL),
(121, 1, 52, 0, 'Not Started', NULL, NULL),
(122, 1, 53, 0, 'Not Started', NULL, NULL),
(123, 1, 54, 0, 'Not Started', NULL, NULL),
(124, 1, 55, 0, 'Not Started', NULL, NULL),
(125, 1, 56, 0, 'Not Started', NULL, NULL),
(126, 1, 57, 0, 'Not Started', NULL, NULL),
(127, 1, 58, 0, 'Not Started', NULL, NULL),
(128, 1, 59, 0, 'Not Started', NULL, NULL),
(129, 1, 60, 0, 'Not Started', NULL, NULL),
(130, 1, 61, 0, 'Not Started', NULL, NULL),
(131, 1, 62, 0, 'Not Started', NULL, NULL),
(132, 1, 63, 0, 'Not Started', NULL, NULL),
(133, 1, 64, 0, 'Not Started', NULL, NULL),
(134, 1, 65, 0, 'Not Started', NULL, NULL),
(135, 1, 66, 0, 'Not Started', NULL, NULL),
(136, 1, 67, 0, 'Not Started', NULL, NULL),
(137, 1, 68, 0, 'Not Started', NULL, NULL),
(138, 1, 69, 0, 'Not Started', NULL, NULL),
(139, 1, 70, 0, 'Not Started', NULL, NULL),
(140, 1, 71, 0, 'Not Started', NULL, NULL),
(141, 1, 72, 0, 'Not Started', NULL, NULL),
(142, 1, 73, 0, 'Not Started', NULL, NULL),
(143, 1, 74, 0, 'Not Started', NULL, NULL),
(144, 1, 75, 0, 'Not Started', NULL, NULL),
(145, 1, 76, 0, 'Not Started', NULL, NULL),
(157, 2, 25, 0, 'Not Started', NULL, NULL),
(158, 2, 26, 0, 'Not Started', NULL, NULL),
(159, 2, 27, 0, 'Not Started', NULL, NULL),
(160, 2, 28, 0, 'Not Started', NULL, NULL),
(161, 2, 29, 0, 'Not Started', NULL, NULL),
(162, 2, 30, 0, 'Not Started', NULL, NULL),
(163, 2, 31, 0, 'Not Started', NULL, NULL),
(164, 2, 32, 0, 'Not Started', NULL, NULL),
(165, 2, 33, 0, 'Not Started', NULL, NULL),
(166, 2, 34, 0, 'Not Started', NULL, NULL),
(167, 2, 35, 0, 'Not Started', NULL, NULL),
(168, 2, 36, 0, 'Not Started', NULL, NULL),
(169, 2, 37, 0, 'Not Started', NULL, NULL),
(170, 2, 38, 0, 'Not Started', NULL, NULL),
(171, 2, 39, 0, 'Not Started', NULL, NULL),
(172, 2, 40, 0, 'Not Started', NULL, NULL),
(173, 2, 41, 0, 'Not Started', NULL, NULL),
(174, 2, 42, 0, 'Not Started', NULL, NULL),
(175, 2, 43, 0, 'Not Started', NULL, NULL),
(176, 2, 44, 0, 'Not Started', NULL, NULL),
(177, 2, 45, 0, 'Not Started', NULL, NULL),
(178, 2, 46, 0, 'Not Started', NULL, NULL),
(179, 2, 47, 0, 'Not Started', NULL, NULL),
(180, 2, 48, 0, 'Not Started', NULL, NULL),
(181, 2, 49, 0, 'Not Started', NULL, NULL),
(182, 2, 50, 0, 'Not Started', NULL, NULL),
(183, 2, 51, 0, 'Not Started', NULL, NULL),
(184, 2, 52, 0, 'Not Started', NULL, NULL),
(185, 2, 53, 0, 'Not Started', NULL, NULL),
(186, 2, 54, 0, 'Not Started', NULL, NULL),
(187, 2, 55, 0, 'Not Started', NULL, NULL),
(188, 2, 56, 0, 'Not Started', NULL, NULL),
(189, 2, 57, 0, 'Not Started', NULL, NULL),
(190, 2, 58, 0, 'Not Started', NULL, NULL),
(191, 2, 59, 0, 'Not Started', NULL, NULL),
(192, 2, 60, 0, 'Not Started', NULL, NULL),
(193, 2, 61, 0, 'Not Started', NULL, NULL),
(194, 2, 62, 0, 'Not Started', NULL, NULL),
(195, 2, 63, 0, 'Not Started', NULL, NULL),
(196, 2, 64, 0, 'Not Started', NULL, NULL),
(197, 2, 65, 0, 'Not Started', NULL, NULL),
(198, 2, 66, 0, 'Not Started', NULL, NULL),
(199, 2, 67, 0, 'Not Started', NULL, NULL),
(200, 2, 68, 0, 'Not Started', NULL, NULL),
(201, 2, 69, 0, 'Not Started', NULL, NULL),
(202, 2, 70, 0, 'Not Started', NULL, NULL),
(203, 2, 71, 0, 'Not Started', NULL, NULL),
(204, 2, 72, 0, 'Not Started', NULL, NULL),
(205, 2, 73, 0, 'Not Started', NULL, NULL),
(206, 2, 74, 0, 'Not Started', NULL, NULL),
(207, 2, 75, 0, 'Not Started', NULL, NULL),
(208, 2, 76, 0, 'Not Started', NULL, NULL),
(220, 3, 25, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(221, 3, 26, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(222, 3, 27, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(223, 3, 28, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(224, 3, 29, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(225, 3, 30, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(226, 3, 31, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(227, 3, 32, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(228, 3, 33, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(229, 3, 34, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(230, 3, 35, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(231, 3, 36, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(232, 3, 37, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(233, 3, 38, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(234, 3, 39, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(235, 3, 40, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(236, 3, 41, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(237, 3, 42, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(238, 3, 43, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(239, 3, 44, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(240, 3, 45, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(241, 3, 46, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(242, 3, 47, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(243, 3, 48, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(244, 3, 49, 0, 'Not Started', NULL, NULL),
(245, 3, 50, 0, 'Not Started', NULL, NULL),
(246, 3, 51, 0, 'Not Started', NULL, NULL),
(247, 3, 52, 0, 'Not Started', NULL, NULL),
(248, 3, 53, 0, 'Not Started', NULL, NULL),
(249, 3, 54, 0, 'Not Started', NULL, NULL),
(250, 3, 55, 0, 'Not Started', NULL, NULL),
(251, 3, 56, 0, 'Not Started', NULL, NULL),
(252, 3, 57, 0, 'Not Started', NULL, NULL),
(253, 3, 58, 0, 'Not Started', NULL, NULL),
(254, 3, 59, 0, 'Not Started', NULL, NULL),
(255, 3, 60, 0, 'Not Started', NULL, NULL),
(256, 3, 61, 0, 'Not Started', NULL, NULL),
(257, 3, 62, 0, 'Not Started', NULL, NULL),
(258, 3, 63, 0, 'Not Started', NULL, NULL),
(259, 3, 64, 0, 'Not Started', NULL, NULL),
(260, 3, 65, 0, 'Not Started', NULL, NULL),
(261, 3, 66, 0, 'Not Started', NULL, NULL),
(262, 3, 67, 0, 'Not Started', NULL, NULL),
(263, 3, 68, 0, 'Not Started', NULL, NULL),
(264, 3, 69, 0, 'Not Started', NULL, NULL),
(265, 3, 70, 0, 'Not Started', NULL, NULL),
(266, 3, 71, 0, 'Not Started', NULL, NULL),
(267, 3, 72, 0, 'Not Started', NULL, NULL),
(268, 3, 73, 0, 'Not Started', NULL, NULL),
(269, 3, 74, 0, 'Not Started', NULL, NULL),
(270, 3, 75, 0, 'Not Started', NULL, NULL),
(271, 3, 76, 0, 'Not Started', NULL, NULL),
(272, 6, 1, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(273, 6, 2, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(274, 6, 3, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(275, 6, 4, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(276, 6, 5, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(277, 6, 6, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(278, 6, 7, 0, 'Completed', '2024-05-20 17:04:40', 'Student Four'),
(279, 6, 8, 0, 'In Progress', NULL, NULL),
(280, 6, 9, 0, 'Not Started', NULL, NULL),
(281, 6, 10, 0, 'Not Started', NULL, NULL),
(282, 6, 11, 0, 'Not Started', NULL, NULL),
(283, 6, 12, 0, 'Not Started', NULL, NULL),
(284, 6, 13, 0, 'Not Started', NULL, NULL),
(285, 6, 14, 0, 'Not Started', NULL, NULL),
(286, 6, 15, 0, 'Not Started', NULL, NULL),
(287, 6, 16, 0, 'Not Started', NULL, NULL),
(288, 6, 17, 0, 'Not Started', NULL, NULL),
(289, 6, 18, 0, 'Not Started', NULL, NULL),
(290, 6, 19, 0, 'Not Started', NULL, NULL),
(291, 6, 20, 0, 'Not Started', NULL, NULL),
(292, 6, 21, 0, 'Not Started', NULL, NULL),
(293, 6, 22, 0, 'Not Started', NULL, NULL),
(294, 6, 23, 0, 'Not Started', NULL, NULL),
(295, 6, 24, 0, 'Not Started', NULL, NULL),
(296, 6, 25, 0, 'Not Started', NULL, NULL),
(297, 6, 26, 0, 'Not Started', NULL, NULL),
(298, 6, 27, 0, 'Not Started', NULL, NULL),
(299, 6, 28, 0, 'Not Started', NULL, NULL),
(300, 6, 29, 0, 'Not Started', NULL, NULL),
(301, 6, 30, 0, 'Not Started', NULL, NULL),
(302, 6, 31, 0, 'Not Started', NULL, NULL),
(303, 6, 32, 0, 'Not Started', NULL, NULL),
(304, 6, 33, 0, 'Not Started', NULL, NULL),
(305, 6, 34, 0, 'Not Started', NULL, NULL),
(306, 6, 35, 0, 'Not Started', NULL, NULL),
(307, 6, 36, 0, 'Not Started', NULL, NULL),
(308, 6, 37, 0, 'Not Started', NULL, NULL),
(309, 6, 38, 0, 'Not Started', NULL, NULL),
(310, 6, 39, 0, 'Not Started', NULL, NULL),
(311, 6, 40, 0, 'Not Started', NULL, NULL),
(312, 6, 41, 0, 'Not Started', NULL, NULL),
(313, 6, 42, 0, 'Not Started', NULL, NULL),
(314, 6, 43, 0, 'Not Started', NULL, NULL),
(315, 6, 44, 0, 'Not Started', NULL, NULL),
(316, 6, 45, 0, 'Not Started', NULL, NULL),
(317, 6, 46, 0, 'Not Started', NULL, NULL),
(318, 6, 47, 0, 'Not Started', NULL, NULL),
(319, 6, 48, 0, 'Not Started', NULL, NULL),
(320, 6, 49, 0, 'Not Started', NULL, NULL),
(321, 6, 50, 0, 'Not Started', NULL, NULL),
(322, 6, 51, 0, 'Not Started', NULL, NULL),
(323, 6, 52, 0, 'Not Started', NULL, NULL),
(324, 6, 53, 0, 'Not Started', NULL, NULL),
(325, 6, 54, 0, 'Not Started', NULL, NULL),
(326, 6, 55, 0, 'Not Started', NULL, NULL),
(327, 6, 56, 0, 'Not Started', NULL, NULL),
(328, 6, 57, 0, 'Not Started', NULL, NULL),
(329, 6, 58, 0, 'Not Started', NULL, NULL),
(330, 6, 59, 0, 'Not Started', NULL, NULL),
(331, 6, 60, 0, 'Not Started', NULL, NULL),
(332, 6, 61, 0, 'Not Started', NULL, NULL),
(333, 6, 62, 0, 'Not Started', NULL, NULL),
(334, 6, 63, 0, 'Not Started', NULL, NULL),
(335, 6, 64, 0, 'Not Started', NULL, NULL),
(336, 6, 65, 0, 'Not Started', NULL, NULL),
(337, 6, 66, 0, 'Not Started', NULL, NULL),
(338, 6, 67, 0, 'Not Started', NULL, NULL),
(339, 6, 68, 0, 'Not Started', NULL, NULL),
(340, 6, 69, 0, 'Not Started', NULL, NULL),
(341, 6, 70, 0, 'Not Started', NULL, NULL),
(342, 6, 71, 0, 'Not Started', NULL, NULL),
(343, 6, 72, 0, 'Not Started', NULL, NULL),
(344, 6, 73, 0, 'Not Started', NULL, NULL),
(345, 6, 74, 0, 'Not Started', NULL, NULL),
(346, 6, 75, 0, 'Not Started', NULL, NULL),
(347, 6, 76, 0, 'Not Started', NULL, NULL),
(399, 7, 1, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(400, 7, 2, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(401, 7, 3, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(402, 7, 4, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(403, 7, 5, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(404, 7, 6, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(405, 7, 7, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(406, 7, 8, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(407, 7, 9, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(408, 7, 10, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(409, 7, 11, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(410, 7, 12, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(411, 7, 13, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(412, 7, 14, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(413, 7, 15, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(414, 7, 16, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(415, 7, 17, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(416, 7, 18, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(417, 7, 19, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(418, 7, 20, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(419, 7, 21, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(420, 7, 22, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(421, 7, 23, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(422, 7, 24, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(423, 7, 25, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(424, 7, 26, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(425, 7, 27, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(426, 7, 28, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(427, 7, 29, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(428, 7, 30, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(429, 7, 31, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(430, 7, 32, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(431, 7, 33, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(432, 7, 34, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(433, 7, 35, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(434, 7, 36, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(435, 7, 37, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(436, 7, 38, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(437, 7, 39, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(438, 7, 40, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(439, 7, 41, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(440, 7, 42, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(441, 7, 43, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(442, 7, 44, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(443, 7, 45, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(444, 7, 46, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(445, 7, 47, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(446, 7, 48, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(447, 7, 49, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(448, 7, 50, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(449, 7, 51, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(450, 7, 52, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(451, 7, 53, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(452, 7, 54, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(453, 7, 55, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(454, 7, 56, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(455, 7, 57, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(456, 7, 58, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(457, 7, 59, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(458, 7, 60, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(459, 7, 61, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(460, 7, 62, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(461, 7, 63, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(462, 7, 64, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(463, 7, 65, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(464, 7, 66, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(465, 7, 67, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(466, 7, 68, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(467, 7, 69, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(468, 7, 70, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(469, 7, 71, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(470, 7, 72, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(471, 7, 73, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(472, 7, 74, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(473, 7, 75, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(474, 7, 76, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(475, 8, 1, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(476, 8, 2, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(477, 8, 3, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(478, 8, 4, 0, 'Completed', '2024-05-20 15:21:07', NULL),
(479, 8, 5, 0, 'Not Started', NULL, NULL),
(480, 8, 6, 0, 'Not Started', NULL, NULL),
(481, 8, 7, 0, 'Not Started', NULL, NULL),
(482, 8, 8, 0, 'Not Started', NULL, NULL),
(483, 8, 9, 0, 'Not Started', NULL, NULL),
(484, 8, 10, 0, 'Not Started', NULL, NULL),
(485, 8, 11, 0, 'Not Started', NULL, NULL),
(486, 8, 12, 0, 'Not Started', NULL, NULL),
(487, 8, 13, 0, 'Not Started', NULL, NULL),
(488, 8, 14, 0, 'Not Started', NULL, NULL),
(489, 8, 15, 0, 'Not Started', NULL, NULL),
(490, 8, 16, 0, 'Not Started', NULL, NULL),
(491, 8, 17, 0, 'Not Started', NULL, NULL),
(492, 8, 18, 0, 'Not Started', NULL, NULL),
(493, 8, 19, 0, 'Not Started', NULL, NULL),
(494, 8, 20, 0, 'Not Started', NULL, NULL),
(495, 8, 21, 0, 'Not Started', NULL, NULL),
(496, 8, 22, 0, 'Not Started', NULL, NULL),
(497, 8, 23, 0, 'Not Started', NULL, NULL),
(498, 8, 24, 0, 'Not Started', NULL, NULL),
(499, 8, 25, 0, 'Not Started', NULL, NULL),
(500, 8, 26, 0, 'Not Started', NULL, NULL),
(501, 8, 27, 0, 'Not Started', NULL, NULL),
(502, 8, 28, 0, 'Not Started', NULL, NULL),
(503, 8, 29, 0, 'Not Started', NULL, NULL),
(504, 8, 30, 0, 'Not Started', NULL, NULL),
(505, 8, 31, 0, 'Not Started', NULL, NULL),
(506, 8, 32, 0, 'Not Started', NULL, NULL),
(507, 8, 33, 0, 'Not Started', NULL, NULL),
(508, 8, 34, 0, 'Not Started', NULL, NULL),
(509, 8, 35, 0, 'Not Started', NULL, NULL),
(510, 8, 36, 0, 'Not Started', NULL, NULL),
(511, 8, 37, 0, 'Not Started', NULL, NULL),
(512, 8, 38, 0, 'Not Started', NULL, NULL),
(513, 8, 39, 0, 'Not Started', NULL, NULL),
(514, 8, 40, 0, 'Not Started', NULL, NULL),
(515, 8, 41, 0, 'Not Started', NULL, NULL),
(516, 8, 42, 0, 'Not Started', NULL, NULL),
(517, 8, 43, 0, 'Not Started', NULL, NULL),
(518, 8, 44, 0, 'Not Started', NULL, NULL),
(519, 8, 45, 0, 'Not Started', NULL, NULL),
(520, 8, 46, 0, 'Not Started', NULL, NULL),
(521, 8, 47, 0, 'Not Started', NULL, NULL),
(522, 8, 48, 0, 'Not Started', NULL, NULL),
(523, 8, 49, 0, 'Not Started', NULL, NULL),
(524, 8, 50, 0, 'Not Started', NULL, NULL),
(525, 8, 51, 0, 'Not Started', NULL, NULL),
(526, 8, 52, 0, 'Not Started', NULL, NULL),
(527, 8, 53, 0, 'Not Started', NULL, NULL),
(528, 8, 54, 0, 'Not Started', NULL, NULL),
(529, 8, 55, 0, 'Not Started', NULL, NULL),
(530, 8, 56, 0, 'Not Started', NULL, NULL),
(531, 8, 57, 0, 'Not Started', NULL, NULL),
(532, 8, 58, 0, 'Not Started', NULL, NULL),
(533, 8, 59, 0, 'Not Started', NULL, NULL),
(534, 8, 60, 0, 'Not Started', NULL, NULL),
(535, 8, 61, 0, 'Not Started', NULL, NULL),
(536, 8, 62, 0, 'Not Started', NULL, NULL),
(537, 8, 63, 0, 'Not Started', NULL, NULL),
(538, 8, 64, 0, 'Not Started', NULL, NULL),
(539, 8, 65, 0, 'Not Started', NULL, NULL),
(540, 8, 66, 0, 'Not Started', NULL, NULL),
(541, 8, 67, 0, 'Not Started', NULL, NULL),
(542, 8, 68, 0, 'Not Started', NULL, NULL),
(543, 8, 69, 0, 'Not Started', NULL, NULL),
(544, 8, 70, 0, 'Not Started', NULL, NULL),
(545, 8, 71, 0, 'Not Started', NULL, NULL),
(546, 8, 72, 0, 'Not Started', NULL, NULL),
(547, 8, 73, 0, 'Not Started', NULL, NULL),
(548, 8, 74, 0, 'Not Started', NULL, NULL),
(549, 8, 75, 0, 'Not Started', NULL, NULL),
(550, 8, 76, 0, 'Not Started', NULL, NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `thesis_checklist_vw`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `thesis_checklist_vw`;
CREATE TABLE `thesis_checklist_vw` (
`ThesisId` int(11)
,`CheckListId` int(11)
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
,`Action` enum('Manual','Upload','Approval','Select')
,`CompletedDate` varchar(19)
,`FormShortDesc` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `thesis_dashboard_details`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `thesis_dashboard_details`;
CREATE TABLE `thesis_dashboard_details` (
`totalDepartment` bigint(21)
,`totalCourse` bigint(21)
,`pendingDefense` int(2)
,`activeUser` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `thesis_groupedstudents_vw`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `thesis_groupedstudents_vw`;
CREATE TABLE `thesis_groupedstudents_vw` (
`ThesisId` int(11)
,`Title` varchar(1000)
,`Authors` mediumtext
,`Adviser` varchar(255)
,`Instructor` varchar(255)
,`LastModifiedDate` date
,`DateOfFinalDefense` date
,`Course` varchar(255)
,`Department` varchar(255)
,`Year` varchar(15)
,`SchoolYear` varchar(255)
,`Status` enum('Not Started','In Progress','Completed','')
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `thesis_student_adviser_vw`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `thesis_student_adviser_vw`;
CREATE TABLE `thesis_student_adviser_vw` (
`Title` varchar(1000)
,`StudentName` text
,`Course` varchar(255)
,`Adviser` varchar(255)
,`DateOfFinalDefense` date
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `thesis_student_panel_editor_vw`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `thesis_student_panel_editor_vw`;
CREATE TABLE `thesis_student_panel_editor_vw` (
`ThesisId` int(11)
,`Title` varchar(1000)
,`UserId` int(11)
,`StudentName` text
,`Adviser` varchar(255)
,`PanelMembers` varchar(500)
,`count` bigint(21)
,`SchoolYear` varchar(255)
,`Editor` varchar(255)
,`IDNumber` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `thesis_student_panel_vw`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `thesis_student_panel_vw`;
CREATE TABLE `thesis_student_panel_vw` (
`ThesisId` int(11)
,`Title` varchar(1000)
,`UserId` int(11)
,`StudentName` text
,`Adviser` varchar(255)
,`PanelMembers` varchar(500)
,`count` bigint(21)
,`SchoolYear` varchar(255)
,`IDNumber` varchar(255)
);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `UserId` int(11) NOT NULL,
  `Role` enum('Adviser','Dean','Instructor','Student','Research Coordinator') NOT NULL,
  `Name` varchar(255) NOT NULL,
  `UserName` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `SecurityQuestion` varchar(255) NOT NULL,
  `SecurityAnswer` varchar(255) NOT NULL,
  `Status` enum('Active','Inactive','','') DEFAULT NULL,
  `LastLoginDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `users`
--

TRUNCATE TABLE `users`;
--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserId`, `Role`, `Name`, `UserName`, `Password`, `Email`, `SecurityQuestion`, `SecurityAnswer`, `Status`, `LastLoginDate`) VALUES
(1, 'Adviser', 'Adviser Two', 'adviser.two', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@test.com', 'What elementary school did you attend?', 'hilo', 'Inactive', '2024-05-20 00:20:35'),
(3, 'Research Coordinator', 'RC', 'rc', 'c4ca4238a0b923820dcc509a6f75849b', 'rc@testing.com', 'What elementary school did you attend?', 'tes', 'Active', '2024-05-27 14:24:42'),
(4, 'Dean', 'Dean', 'deanto', 'c4ca4238a0b923820dcc509a6f75849b', 'dean@testing.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(5, 'Adviser', 'Adviser Testing Y. Four', 'adviser.4', 'c4ca4238a0b923820dcc509a6f75849b', 'test.adviser@testing.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(6, 'Instructor', 'Instructor Seven', 'instructor.seven', 'c4ca4238a0b923820dcc509a6f75849b', 'test.instructor@testing.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(7, 'Adviser', 'Adviser Testing T. One', 'adviser.one', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-24 19:02:10'),
(8, 'Adviser', 'Adviser Three', 'adviser.three', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(9, 'Adviser', 'Adviser Five', 'adviser.five', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes', 'Inactive', '2024-05-20 00:20:35'),
(10, 'Adviser', 'Adviser Six', 'adviser.six', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(11, 'Adviser', 'Adviser Seven', 'adviser.seven', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(12, 'Instructor', 'Instructor One', 'instructor.one', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-24 18:29:11'),
(13, 'Instructor', 'Instructor Two', 'instructor.two', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(14, 'Instructor', 'Instructor Three', 'instructor.three', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(15, 'Instructor', 'Instructor Four', 'instructor.four', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 18:28:13'),
(16, 'Instructor', 'Instructor Five', 'instructor.five', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 18:27:46'),
(17, 'Instructor', 'Instructor Six', 'instructor.six', 'c4ca4238a0b923820dcc509a6f75849b', 'test@test.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(18, 'Student', 'Student Testing T. One', 'student.1', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-24 18:53:57'),
(19, 'Student', 'Student Two', 'student.2', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(20, 'Student', 'Student Three', 'student.3', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(21, 'Student', 'Student Four', 'student.4', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 16:58:09'),
(22, 'Student', 'Student Five', 'student.5', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 18:27:17'),
(23, 'Student', 'Student Six', 'student.6', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(24, 'Student', 'Student Seven', 'student.7', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(25, 'Student', 'Student Eight', 'student.8', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(26, 'Student', 'Student Nine', 'student.9', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(33, 'Student', 'Student Ten', 'student.10', 'c4ca4238a0b923820dcc509a6f75849b', 'test@testing.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(34, 'Research Coordinator', 'Ri Coordinator', 'ri.coor', 'c4ca4238a0b923820dcc509a6f75849b', 'rc@testing.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-24 18:50:28'),
(35, 'Student', 'Student Eleven', 'student.11', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(36, 'Student', 'Student Twelve', 'student.12', 'c4ca4238a0b923820dcc509a6f75849b', 'testing@testing.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(37, 'Dean', 'Dean One', 'dean.1', 'c4ca4238a0b923820dcc509a6f75849b', 'test@testing.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(39, 'Dean', 'Dean Two', 'dean.2', 'c4ca4238a0b923820dcc509a6f75849b', 'test@testing.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(52, 'Student', 'Student Thirteen', 'student.13', 'c4ca4238a0b923820dcc509a6f75849b', 'test@testing.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(53, 'Instructor', 'Test Instructor H. Testing', 'test.instructor.testing', '25f9e794323b453885f5181f1b624d0b', 'test.instructor.testing@test.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35'),
(54, 'Adviser', 'Test Adviser J. Testing', 'test.adviser.testing', '25d55ad283aa400af464c76d713c07ad', 'test.adviser.testing@test.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:38:31'),
(56, 'Student', 'Student T 001', 'student.001', '6ebe76c9fb411be97b3b0d48b791a7c9', 'student.001@test.com', 'What elementary school did you attend?', 'mes', 'Active', '2024-05-20 00:20:35');

-- --------------------------------------------------------

--
-- Stand-in structure for view `users_vw`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `users_vw`;
CREATE TABLE `users_vw` (
`UserId` int(11)
,`Role` enum('Adviser','Dean','Instructor','Student','Research Coordinator')
,`Name` varchar(255)
,`UserName` varchar(255)
,`Password` varchar(255)
,`Email` varchar(255)
,`SecurityQuestion` varchar(255)
,`SecurityAnswer` varchar(255)
,`Status` enum('Active','Inactive','','')
,`LastLoginDate` datetime
,`SchoolYear` varchar(255)
);

-- --------------------------------------------------------

--
-- Structure for view `adviser_student_vw`
--
DROP TABLE IF EXISTS `adviser_student_vw`;

DROP VIEW IF EXISTS `adviser_student_vw`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `adviser_student_vw`  AS SELECT `t`.`ThesisId` AS `ThesisId`, `t`.`School` AS `School`, `t`.`SchoolYear` AS `SchoolYear`, `u`.`Name` AS `Adviser`, concat(`s`.`FirstName`,' ',`s`.`MiddleName`,' ',`s`.`LastName`) AS `StudentName`, `t`.`DateOfFinalDefense` AS `DateOfFinalDefense` FROM (((`thesis` `t` left join `users` `u` on(`t`.`AdviserId` = `u`.`UserId`)) left join `thesisstudentmap` `tsm` on(`t`.`ThesisId` = `tsm`.`ThesisId`)) left join `student` `s` on(`tsm`.`StudentId` = `s`.`StudentId`)) ;

-- --------------------------------------------------------

--
-- Structure for view `getdashboardgauge`
--
DROP TABLE IF EXISTS `getdashboardgauge`;

DROP VIEW IF EXISTS `getdashboardgauge`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `getdashboardgauge`  AS SELECT `c`.`Part` AS `Part`, round(count(distinct `comp`.`ThesisChecklistId`) / count(distinct `total`.`ThesisChecklistId`) * 100,2) AS `Completed`, round(count(distinct `inprog`.`ThesisChecklistId`) / count(distinct `total`.`ThesisChecklistId`) * 100,2) AS `InProgress`, round(count(distinct `notstarted`.`ThesisChecklistId`) / count(distinct `total`.`ThesisChecklistId`) * 100,2) AS `NotStarted` FROM ((((`checklist` `c` left join `thesis_checklist_map` `total` on(`c`.`CheckListId` = `total`.`CheckListId`)) left join `thesis_checklist_map` `comp` on(`c`.`CheckListId` = `comp`.`CheckListId` and `comp`.`Status` = 'Completed')) left join `thesis_checklist_map` `inprog` on(`c`.`CheckListId` = `inprog`.`CheckListId` and `inprog`.`Status` = 'In Progress')) left join `thesis_checklist_map` `notstarted` on(`c`.`CheckListId` = `notstarted`.`CheckListId` and `notstarted`.`Status` = 'Not Started')) GROUP BY `c`.`Part` ;

-- --------------------------------------------------------

--
-- Structure for view `getdashboardtable`
--
DROP TABLE IF EXISTS `getdashboardtable`;

DROP VIEW IF EXISTS `getdashboardtable`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `getdashboardtable`  AS SELECT `t`.`Title` AS `Title`, `u`.`Name` AS `Name`, `t`.`LastModifiedDate` AS `LastModifiedDate`, `t`.`Status` AS `Status`, ifnull(round(`comp`.`Count` / `total`.`Count` * 100,0),0) AS `Percent` FROM (((`thesis` `t` left join `users` `u` on(`t`.`InstructorId` = `u`.`UserId`)) left join (select `thesis_checklist_map`.`ThesisId` AS `ThesisId`,count(`thesis_checklist_map`.`ThesisChecklistId`) AS `Count` from `thesis_checklist_map` where `thesis_checklist_map`.`Status` = 'Completed' group by `thesis_checklist_map`.`ThesisId`) `comp` on(`t`.`ThesisId` = `comp`.`ThesisId`)) left join (select `thesis_checklist_map`.`ThesisId` AS `ThesisId`,count(`thesis_checklist_map`.`ThesisChecklistId`) AS `Count` from `thesis_checklist_map` group by `thesis_checklist_map`.`ThesisId`) `total` on(`t`.`ThesisId` = `total`.`ThesisId`)) ;

-- --------------------------------------------------------

--
-- Structure for view `panel_student_vw`
--
DROP TABLE IF EXISTS `panel_student_vw`;

DROP VIEW IF EXISTS `panel_student_vw`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `panel_student_vw`  AS SELECT `panel`.`ThesisId` AS `ThesisId`, `t`.`School` AS `School`, `t`.`SchoolYear` AS `SchoolYear`, `panel`.`PanelMember` AS `PanelMember`, concat(`s`.`FirstName`,' ',`s`.`MiddleName`,' ',`s`.`LastName`) AS `StudentName`, `t`.`DateOfFinalDefense` AS `DateOfFinalDefense` FROM ((((`thesispanelmembermap` `tpm` join (select `tpmm`.`ThesisId` AS `ThesisId`,substring_index(substring_index(`tpmm`.`PanelMembers`,';',`numbers`.`N`),';',-1) AS `PanelMember` from (`thesispanelmembermap` `tpmm` join (select 0 AS `N` union all select 1 AS `1` union all select 2 AS `2` union all select 3 AS `3` union all select 4 AS `4` union all select 5 AS `5` union all select 6 AS `6` union all select 7 AS `7` union all select 8 AS `8` union all select 9 AS `9`) `numbers` on(char_length(`tpmm`.`PanelMembers`) - char_length(replace(`tpmm`.`PanelMembers`,';','')) >= `numbers`.`N` - 1)) where `tpmm`.`PanelMembers` <> '') `panel` on(`tpm`.`ThesisId` = `panel`.`ThesisId` and `panel`.`PanelMember` <> '')) left join `thesis` `t` on(`panel`.`ThesisId` = `t`.`ThesisId`)) left join `thesisstudentmap` `tsm` on(`tsm`.`ThesisId` = `t`.`ThesisId`)) left join `student` `s` on(`s`.`StudentId` = `tsm`.`StudentId`)) ;

-- --------------------------------------------------------

--
-- Structure for view `thesis_checklist_vw`
--
DROP TABLE IF EXISTS `thesis_checklist_vw`;

DROP VIEW IF EXISTS `thesis_checklist_vw`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `thesis_checklist_vw`  AS SELECT `t`.`ThesisId` AS `ThesisId`, `c`.`CheckListId` AS `CheckListId`, `c`.`Part` AS `Part`, `tcm`.`Status` AS `Status`, `t`.`Title` AS `Title`, `tgs`.`Authors` AS `Authors`, `tgs`.`Adviser` AS `Adviser`, `tgs`.`Instructor` AS `Instructor`, `c`.`StepNumber` AS `StepNumber`, `c`.`TaskName` AS `TaskName`, concat(`c`.`TaskNameAlias`,'_',replace(`t`.`Title`,' ','_')) AS `UploadedFileName`, `c`.`Assignee` AS `Assignee`, `c`.`Action` AS `Action`, ifnull(`tcm`.`CompletedDate`,'') AS `CompletedDate`, `c`.`FormShortDesc` AS `FormShortDesc` FROM (((`thesis` `t` left join `thesis_checklist_map` `tcm` on(`t`.`ThesisId` = `tcm`.`ThesisId`)) left join `checklist` `c` on(`c`.`CheckListId` = `tcm`.`CheckListId`)) left join `thesis_groupedstudents_vw` `tgs` on(`t`.`ThesisId` = `tgs`.`ThesisId`)) ;

-- --------------------------------------------------------

--
-- Structure for view `thesis_dashboard_details`
--
DROP TABLE IF EXISTS `thesis_dashboard_details`;

DROP VIEW IF EXISTS `thesis_dashboard_details`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `thesis_dashboard_details`  AS SELECT (select distinct count(`t`.`ThesisId`) AS `totalDepartment` from ((`thesis` `t` left join `thesisstudentmap` `tsm` on(`t`.`ThesisId` = `tsm`.`ThesisId`)) left join `student` `s` on(`tsm`.`StudentId` = `s`.`StudentId`)) where `s`.`Department` = 'Engineering') AS `totalDepartment`, (select distinct count(`t`.`ThesisId`) AS `totalCourse` from ((`thesis` `t` left join `thesisstudentmap` `tsm` on(`t`.`ThesisId` = `tsm`.`ThesisId`)) left join `student` `s` on(`tsm`.`StudentId` = `s`.`StudentId`)) where `s`.`Department` = 'Engineering' and `s`.`Course` = 'Computer Engineering') AS `totalCourse`, (select 40 AS `pendingDefense`) AS `pendingDefense`, (select distinct count(`users`.`UserId`) AS `activeUser` from `users` where `users`.`Status` = 'Active') AS `activeUser` ;

-- --------------------------------------------------------

--
-- Structure for view `thesis_groupedstudents_vw`
--
DROP TABLE IF EXISTS `thesis_groupedstudents_vw`;

DROP VIEW IF EXISTS `thesis_groupedstudents_vw`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `thesis_groupedstudents_vw`  AS SELECT `t`.`ThesisId` AS `ThesisId`, `t`.`Title` AS `Title`, group_concat(distinct concat(`stu`.`Name`) separator ',') AS `Authors`, `adviser`.`Name` AS `Adviser`, `instructor`.`Name` AS `Instructor`, `t`.`LastModifiedDate` AS `LastModifiedDate`, `t`.`DateOfFinalDefense` AS `DateOfFinalDefense`, `s`.`Course` AS `Course`, `s`.`Department` AS `Department`, `s`.`Year` AS `Year`, `t`.`SchoolYear` AS `SchoolYear`, `t`.`Status` AS `Status` FROM (((((`thesis` `t` left join `thesisstudentmap` `tsm` on(`tsm`.`ThesisId` = `t`.`ThesisId`)) left join `student` `s` on(`tsm`.`StudentId` = `s`.`StudentId`)) left join `users` `stu` on(`stu`.`UserId` = `s`.`UserId`)) left join `users` `adviser` on(`t`.`AdviserId` = `adviser`.`UserId`)) left join `users` `instructor` on(`instructor`.`UserId` = `t`.`InstructorId`)) GROUP BY `t`.`Title`, `adviser`.`Name`, `instructor`.`Name`, `t`.`LastModifiedDate`, `t`.`DateOfFinalDefense`, `t`.`Status` ;

-- --------------------------------------------------------

--
-- Structure for view `thesis_student_adviser_vw`
--
DROP TABLE IF EXISTS `thesis_student_adviser_vw`;

DROP VIEW IF EXISTS `thesis_student_adviser_vw`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `thesis_student_adviser_vw`  AS SELECT `t`.`Title` AS `Title`, concat(`s`.`FirstName`,' ',`s`.`MiddleName`,' ',`s`.`LastName`) AS `StudentName`, `s`.`Course` AS `Course`, `a`.`Name` AS `Adviser`, `t`.`DateOfFinalDefense` AS `DateOfFinalDefense` FROM (((`thesis` `t` left join `thesisstudentmap` `tsm` on(`t`.`ThesisId` = `tsm`.`ThesisId`)) left join `student` `s` on(`tsm`.`StudentId` = `s`.`StudentId`)) left join `users` `a` on(`t`.`AdviserId` = `a`.`UserId`)) ;

-- --------------------------------------------------------

--
-- Structure for view `thesis_student_panel_editor_vw`
--
DROP TABLE IF EXISTS `thesis_student_panel_editor_vw`;

DROP VIEW IF EXISTS `thesis_student_panel_editor_vw`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `thesis_student_panel_editor_vw`  AS SELECT `tsp`.`ThesisId` AS `ThesisId`, `tsp`.`Title` AS `Title`, `tsp`.`UserId` AS `UserId`, `tsp`.`StudentName` AS `StudentName`, `tsp`.`Adviser` AS `Adviser`, `tsp`.`PanelMembers` AS `PanelMembers`, `tsp`.`count` AS `count`, `tsp`.`SchoolYear` AS `SchoolYear`, ifnull(`e`.`EditorName`,'') AS `Editor`, `tsp`.`IDNumber` AS `IDNumber` FROM ((`thesis_student_panel_vw` `tsp` left join `thesis_checklist_editor_map` `tcem` on(`tsp`.`ThesisId` = `tcem`.`ThesisId`)) left join `editor` `e` on(`e`.`EditorId` = `tcem`.`EditorId`)) ;

-- --------------------------------------------------------

--
-- Structure for view `thesis_student_panel_vw`
--
DROP TABLE IF EXISTS `thesis_student_panel_vw`;

DROP VIEW IF EXISTS `thesis_student_panel_vw`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `thesis_student_panel_vw`  AS SELECT `t`.`ThesisId` AS `ThesisId`, `t`.`Title` AS `Title`, `s`.`UserId` AS `UserId`, concat(`s`.`FirstName`,' ',`s`.`MiddleName`,' ',`s`.`LastName`) AS `StudentName`, `u`.`Name` AS `Adviser`, `tpmm`.`PanelMembers` AS `PanelMembers`, `studentcount`.`count` AS `count`, `t`.`SchoolYear` AS `SchoolYear`, `s`.`IDNumber` AS `IDNumber` FROM (((((`thesis` `t` left join `thesisstudentmap` `tsm` on(`t`.`ThesisId` = `tsm`.`ThesisId`)) left join `student` `s` on(`tsm`.`StudentId` = `s`.`StudentId`)) left join `users` `u` on(`u`.`UserId` = `t`.`AdviserId`)) left join `thesispanelmembermap` `tpmm` on(`tpmm`.`ThesisId` = `t`.`ThesisId`)) left join (select `t`.`ThesisId` AS `ThesisId`,count(`u`.`UserId`) AS `count` from ((((`thesis` `t` left join `thesisstudentmap` `tsm` on(`t`.`ThesisId` = `tsm`.`ThesisId`)) left join `student` `s` on(`tsm`.`StudentId` = `s`.`StudentId`)) left join `users` `u` on(`u`.`UserId` = `t`.`AdviserId`)) left join `thesispanelmembermap` `tpmm` on(`tpmm`.`ThesisId` = `t`.`ThesisId`)) group by `t`.`ThesisId`) `studentcount` on(`t`.`ThesisId` = `studentcount`.`ThesisId`)) ;

-- --------------------------------------------------------

--
-- Structure for view `users_vw`
--
DROP TABLE IF EXISTS `users_vw`;

DROP VIEW IF EXISTS `users_vw`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `users_vw`  AS SELECT `u`.`UserId` AS `UserId`, `u`.`Role` AS `Role`, `u`.`Name` AS `Name`, `u`.`UserName` AS `UserName`, `u`.`Password` AS `Password`, `u`.`Email` AS `Email`, `u`.`SecurityQuestion` AS `SecurityQuestion`, `u`.`SecurityAnswer` AS `SecurityAnswer`, `u`.`Status` AS `Status`, `u`.`LastLoginDate` AS `LastLoginDate`, ifnull(`t`.`SchoolYear`,'') AS `SchoolYear` FROM (((`users` `u` left join `student` `s` on(`u`.`UserId` = `s`.`UserId`)) left join `thesisstudentmap` `tsm` on(`s`.`StudentId` = `tsm`.`StudentId`)) left join `thesis` `t` on(`t`.`ThesisId` = `tsm`.`ThesisId`)) ;

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
-- Indexes for table `editor`
--
ALTER TABLE `editor`
  ADD PRIMARY KEY (`EditorId`);

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
  ADD PRIMARY KEY (`StudentId`),
  ADD KEY `CHK_StudentId_UserId` (`UserId`);

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
  ADD KEY `PanelMemberId` (`PanelMembers`);

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
-- Indexes for table `thesis_checklist_editor_map`
--
ALTER TABLE `thesis_checklist_editor_map`
  ADD PRIMARY KEY (`ThesisChecklistEditorId`),
  ADD KEY `CHK_ThesisId_Editor` (`ThesisId`),
  ADD KEY `CHK_Checklist_Editor` (`CheckListId`),
  ADD KEY `CHK_Editor` (`EditorId`);

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
  MODIFY `CheckListId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `editor`
--
ALTER TABLE `editor`
  MODIFY `EditorId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
  MODIFY `StudentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `thesis`
--
ALTER TABLE `thesis`
  MODIFY `ThesisId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `thesispanelmembermap`
--
ALTER TABLE `thesispanelmembermap`
  MODIFY `ThesisPanelMemberMap` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `thesisstudentmap`
--
ALTER TABLE `thesisstudentmap`
  MODIFY `ThesisStudentMapId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `thesis_checklist_approval_map`
--
ALTER TABLE `thesis_checklist_approval_map`
  MODIFY `ThesisChecklistApprovalId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `thesis_checklist_editor_map`
--
ALTER TABLE `thesis_checklist_editor_map`
  MODIFY `ThesisChecklistEditorId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `thesis_checklist_file_map`
--
ALTER TABLE `thesis_checklist_file_map`
  MODIFY `ThesisChecklistFileId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `thesis_checklist_map`
--
ALTER TABLE `thesis_checklist_map`
  MODIFY `ThesisChecklistId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=551;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `CHK_StudentId_UserId` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`) ON DELETE SET NULL ON UPDATE SET NULL;

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
-- Constraints for table `thesis_checklist_editor_map`
--
ALTER TABLE `thesis_checklist_editor_map`
  ADD CONSTRAINT `CHK_Checklist_Editor` FOREIGN KEY (`CheckListId`) REFERENCES `checklist` (`CheckListId`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `CHK_Editor` FOREIGN KEY (`EditorId`) REFERENCES `editor` (`EditorId`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `CHK_ThesisId_Editor` FOREIGN KEY (`ThesisId`) REFERENCES `thesis` (`ThesisId`) ON DELETE SET NULL ON UPDATE SET NULL;

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
