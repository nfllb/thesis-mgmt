<?php

session_start();
include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");

if (isset($_POST['update_step']))
{
    $thesisId = $_POST['thesis_Id'];
    $checklistId = $_POST['checklist_Id'];
    $new_step_status = $_POST['new_step_status'];
    $action = $_POST['action'];

    if ($action == 'Manual')
    {
        $update_query = "UPDATE thesis_checklist_map SET Status = '$new_step_status' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklistId;
        $query_run = mysqli_query($con, $update_query);
        return;
    }

}

if (isset($_POST['save_editor']))
{
    $thesisId = $_POST['thesis_Id'];
    $checklistId = $_POST['checklist_Id'];
    $new_step_status = $_POST['new_step_status'];
    $action = $_POST['action'];
    $editorId = $_POST['editor'];

    if ($action == 'Manual')
    {
        $insert_query = "INSERT INTO `thesis_checklist_editor_map` (`ThesisChecklistEditorId`, `ThesisId`, `CheckListId`, `EditorId`) VALUES (NULL,?,?,?)";
        $stmt = $con->prepare($insert_query);
        $stmt->bind_param("iii", $thesisId, $checklistId, $editorId);
        $insert = $stmt->execute();

        $update_query = "UPDATE thesis_checklist_map SET Status = '$new_step_status' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklistId;
        $query_run = mysqli_query($con, $update_query);

        return;
    }

}

if (isset($_POST['upload_file_step']))
{
    $thesisId = $_POST['thesis_Id'];
    $checklist_id = $_POST['checklist_id'];
    $new_step_status = $_POST['new_step_status'];
    $action = $_POST['action'];
    $step_file_name = $_POST['step_file_name'];

    if ($action == 'Upload')
    {
        $uploadDir = '/thesis-mgmt/uploads/';

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
                    $delete_query = "DELETE FROM thesis_checklist_file_map WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id;
                    $run_query = mysqli_query($con, $delete_query);
                    // Insert form data in the database 
                    $sqlQ = "INSERT INTO thesis_checklist_file_map (`ThesisChecklistFileId`, `ThesisId`, `CheckListId`, `FileName`, `FilePath`, `UploadedBy`, `UploadedDate`) VALUES (NULL,?,?,?,?,?,NOW());";
                    $stmt = $con->prepare($sqlQ);
                    $stmt->bind_param("iisss", $thesisId, $checklist_id, $stepFileName, $targetFilePath, $_SESSION['name']);
                    $insert = $stmt->execute();

                    if ($insert)
                    {
                        $update_query = "UPDATE thesis_checklist_map SET Status = '$new_step_status' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id;
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
    $checklist_id = $_POST['checklist_id'];
    $new_step_status = $_POST['new_step_status'];
    $action = $_POST['action'];

    if ($action == 'Approval')
    {
        $update_approval_map = "UPDATE thesis_checklist_approval_map SET Approved = 1 WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id . " AND ApproverId = " . $_SESSION['userid'];
        $update_approval_map_result = mysqli_query($con, $update_approval_map);

        $select_unapproved = "SELECT ThesisChecklistApprovalId FROM thesis_checklist_approval_map WHERE Approved = 0 AND ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id;
        $select_unapproved_result = mysqli_query($con, $select_unapproved);
        if ($select_unapproved_result && mysqli_num_rows($select_unapproved_result) > 0)
        {
            $update_query = "UPDATE thesis_checklist_map SET Status = 'In Progress' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id;
        } else
        {
            $update_query = "UPDATE thesis_checklist_map SET Status = '$new_step_status' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id;
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
    $checklist_id = $_POST['checklist_id'];
    $new_step_status = $_POST['new_step_status'];
    $action = $_POST['action'];

    if ($action == 'Approval')
    {
        $update_approval_map = "UPDATE thesis_checklist_approval_map SET Approved = 0 WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id;
        $update_approval_map_result = mysqli_query($con, $update_approval_map);

        $checklist_id = $checklist_id - 1;
        $update_query = "UPDATE thesis_checklist_map SET Status = 'In Progress' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id;
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