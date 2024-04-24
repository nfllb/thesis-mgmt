<?php

session_start();
include './../dbconnect.php';

if (isset($_POST['update_step']))
{
    $thesisId = $_POST['thesis_Id'];
    $stepId = $_POST['step_number'];
    $new_step_status = $_POST['new_step_status'];
    $action = $_POST['action'];

    if ($action == 'Manual')
    {
        $update_query = "UPDATE thesis_checklist_map SET Status = '$new_step_status' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $stepId;
        $query_run = mysqli_query($con, $update_query);
        return;
    }

}

if (isset($_POST['upload_file_step']))
{
    $thesisId = $_POST['thesis_Id'];
    $stepId = $_POST['step_number'];
    $new_step_status = $_POST['new_step_status'];
    $action = $_POST['action'];
    $step_file_name = $_POST['step_file_name'];

    if ($action == 'Upload')
    {
        $uploadDir = './../uploads/';

        // Allowed file types 
        $allowTypes = array('pdf', 'doc', 'docx', 'jpg', 'png', 'jpeg');

        // Upload file 
        $uploadedFile = '';
        if (!empty($_FILES["file"]))
        {
            // File path config 
            $fileName = basename($_FILES["file"]["name"]);
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
            $stepFileName = $step_file_name;
            $targetFilePath = $uploadDir . $stepFileName . '.' . $fileType;
            $fileTemp = $_FILES['file']['tmp_name'];
            $fileSize = $_FILES['file']['size'];

            // Allow certain file formats to upload 
            if (in_array($fileType, $allowTypes))
            {
                // Upload file to the server 
                if (move_uploaded_file($fileTemp, $targetFilePath))
                {
                    // Insert form data in the database 
                    $sqlQ = "INSERT INTO thesis_checklist_file_map (`ThesisChecklistFileId`, `ThesisId`, `CheckListId`, `FileName`, `FilePath`, `UploadedBy`, `UploadedDate`) VALUES (NULL,?,?,?,?,?,NOW());";
                    $stmt = $con->prepare($sqlQ);
                    $stmt->bind_param("iisss", $thesisId, $stepId, $stepFileName, $targetFilePath, $_SESSION['name']);
                    $insert = $stmt->execute();

                    if ($insert)
                    {
                        $update_query = "UPDATE thesis_checklist_map SET Status = '$new_step_status' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $stepId;
                        $query_run = mysqli_query($con, $update_query);
                        echo 'success';
                    }
                } else
                {
                    echo 'Sorry, there was an error uploading your file.';
                }
            } else
            {
                echo 'Sorry, only ' . implode('/', $allowTypes) . ' files are allowed to upload.';
            }
        } else
        {
            echo 'Please upload file first!';
        }
    }
}

if (isset($_POST['approve_step']))
{
    $thesisId = $_POST['thesis_Id'];
    $stepId = $_POST['step_number'];
    $new_step_status = $_POST['new_step_status'];
    $action = $_POST['action'];

    if ($action == 'Approval')
    {
        $update_approval_map = "UPDATE thesis_checklist_approval_map SET Approved = 1 WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $stepId . " AND ApproverId = " . $_SESSION['userid'];
        $update_approval_map_result = mysqli_query($con, $update_approval_map);

        $select_unapproved = "SELECT ThesisChecklistApprovalId FROM thesis_checklist_approval_map WHERE Approved = 0 AND ThesisId = " . $thesisId . " AND CheckListId = " . $stepId;
        $select_unapproved_result = mysqli_query($con, $select_unapproved);
        if ($select_unapproved_result && mysqli_num_rows($select_unapproved_result) > 0)
        {
            $update_query = "UPDATE thesis_checklist_map SET Status = 'In Progress' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $stepId;
        } else
        {
            $update_query = "UPDATE thesis_checklist_map SET Status = '$new_step_status' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $stepId;
        }

        $update_query_result = mysqli_query($con, $update_query);
        if ($update_query_result)
        {
            echo 'success';
        } else
        {
            echo 'Sorry, there was an error saving to database. Please contact your research coordinator.';
            ;
        }
        return;
    }

}

if (isset($_POST['reject_step']))
{
    $thesisId = $_POST['thesis_Id'];
    $stepId = $_POST['step_number'];
    $new_step_status = $_POST['new_step_status'];
    $action = $_POST['action'];

    if ($action == 'Approval')
    {
        $update_approval_map = "UPDATE thesis_checklist_approval_map SET Approved = 0 WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $stepId;
        $update_approval_map_result = mysqli_query($con, $update_approval_map);

        $stepId = $stepId - 1;
        $update_query = "UPDATE thesis_checklist_map SET Status = 'In Progress' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $stepId;
        $update_query_result = mysqli_query($con, $update_query);
        if ($update_query_result)
        {
            echo 'success';
        } else
        {
            echo 'Sorry, there was an error saving to database. Please contact your research coordinator.';
            ;
        }
        return;
    }

}