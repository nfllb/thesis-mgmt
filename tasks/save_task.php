<?php

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");

if (isset($_POST['update_step'])) {
    $thesisId = $_POST['thesis_Id'];
    $checklistId = $_POST['checklist_Id'];
    $new_step_status = $_POST['new_step_status'];
    $action = $_POST['action'];

    if ($action == 'Manual') {
        if ($new_step_status == 'Completed') {
            $update_query = "UPDATE thesis_checklist_map SET Status = '$new_step_status', CompletedDate = current_timestamp(), CompletedBy = '" . $_SESSION['name'] . "' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklistId;
        } else {
            $update_query = "UPDATE thesis_checklist_map SET Status = '$new_step_status' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklistId;
        }

        $query_run = mysqli_query($con, $update_query);

        $max_checklistId_query = "SELECT MAX(ChecklistId) AS Max_ChecklistId FROM `checklist`";
        $max_checklistId_result = mysqli_query($con, $max_checklistId_query);
        $max_checklistId = mysqli_fetch_assoc($max_checklistId_result);

        if ($max_checklistId['Max_ChecklistId'] == $checklistId && $new_step_status == 'Completed') {
            $update_thesis = "UPDATE thesis SET Status = 'Completed', LastModifiedDate = CURDATE(), LastModifiedBy = '" . $_SESSION['name'] . "' WHERE ThesisId = " . $thesisId;
            $update_thesis_result = mysqli_query($con, $update_thesis);
        } else {
            $update_thesis = "UPDATE thesis SET Status = 'In Progress', LastModifiedDate = CURDATE(), LastModifiedBy = '" . $_SESSION['name'] . "' WHERE ThesisId = " . $thesisId;
            $update_thesis_result = mysqli_query($con, $update_thesis);
        }

        return;
    }
}

if (isset($_POST['save_editor'])) {
    $thesisId = $_POST['thesis_Id'];
    $checklistId = $_POST['checklist_Id'];
    $new_step_status = $_POST['new_step_status'];
    $action = $_POST['action'];
    $editorId = $_POST['editor'];

    if ($action == 'Manual') {
        $insert_query = "INSERT INTO `thesis_checklist_editor_map` (`ThesisChecklistEditorId`, `ThesisId`, `CheckListId`, `EditorId`) VALUES (NULL,?,?,?)";
        $stmt = $con->prepare($insert_query);
        $stmt->bind_param("iii", $thesisId, $checklistId, $editorId);
        $insert = $stmt->execute();

        if ($new_step_status == 'Completed') {
            $update_query = "UPDATE thesis_checklist_map SET Status = '$new_step_status', CompletedDate = current_timestamp(), CompletedBy = '" . $_SESSION['name'] . "' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklistId;
        } else {
            $update_query = "UPDATE thesis_checklist_map SET Status = '$new_step_status' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklistId;
        }

        $query_run = mysqli_query($con, $update_query);

        $update_thesis = "UPDATE thesis SET Status = 'In Progress', LastModifiedDate = CURDATE(), LastModifiedBy = '" . $_SESSION['name'] . "' WHERE ThesisId = " . $thesisId;
        $update_thesis_result = mysqli_query($con, $update_thesis);

        return;
    }
}

if (isset($_POST['upload_file_step'])) {
    $thesisId = $_POST['thesis_Id'];
    $checklist_id = $_POST['checklist_id'];
    $new_step_status = $_POST['new_step_status'];
    $action = $_POST['action'];
    $step_file_name = $_POST['step_file_name'];

    if ($action == 'Upload') {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/thesis-mgmt/uploads/';

        // Allowed file types 
        $allowTypes = array('pdf', 'doc', 'docx', 'jpg', 'png', 'jpeg');

        // Upload file 
        $uploadedFile = '';
        if (!empty($_FILES["file"])) {
            // File path config 
            $fileName = basename($_FILES["file"]["name"]);
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
            $stepFileName = $step_file_name;
            $targetFilePath = $uploadDir . $stepFileName . '.' . $fileType;
            $fileTemp = $_FILES['file']['tmp_name'];
            $fileSize = $_FILES['file']['size'];

            // Allow certain file formats to upload 
            if (in_array($fileType, $allowTypes)) {
                // Upload file to the server 
                if (move_uploaded_file($fileTemp, $targetFilePath)) {
                    $delete_query = "DELETE FROM thesis_checklist_file_map WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id;
                    $run_query = mysqli_query($con, $delete_query);
                    // Insert form data in the database 
                    $sqlQ = "INSERT INTO thesis_checklist_file_map (`ThesisChecklistFileId`, `ThesisId`, `CheckListId`, `FileName`, `FilePath`, `UploadedBy`, `UploadedDate`) VALUES (NULL,?,?,?,?,?,NOW());";
                    $stmt = $con->prepare($sqlQ);
                    $stmt->bind_param("iisss", $thesisId, $checklist_id, $stepFileName, $targetFilePath, $_SESSION['name']);
                    $insert = $stmt->execute();

                    if ($insert) {
                        if ($new_step_status == 'Completed') {
                            $update_query = "UPDATE thesis_checklist_map SET Status = '$new_step_status', CompletedDate = current_timestamp(), CompletedBy = '" . $_SESSION['name'] . "' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id;
                        } else {
                            $update_query = "UPDATE thesis_checklist_map SET Status = '$new_step_status' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id;
                        }

                        $query_run = mysqli_query($con, $update_query);
                        $update_thesis = "UPDATE thesis SET Status = 'In Progress', LastModifiedDate = CURDATE(), LastModifiedBy = '" . $_SESSION['name'] . "' WHERE ThesisId = " . $thesisId;
                        $update_thesis_result = mysqli_query($con, $update_thesis);
                        echo 'success';
                    }
                } else {
                    echo 'Sorry, there was an error uploading your file.';
                }
            } else {
                echo 'Sorry, only ' . implode('/', $allowTypes) . ' files are allowed to upload.';
            }
        } else {
            echo 'Please upload file first!';
        }
    }
}

if (isset($_POST['approve_step'])) {
    $thesisId = $_POST['thesis_Id'];
    $checklist_id = $_POST['checklist_id'];
    $new_step_status = $_POST['new_step_status'];
    $action = $_POST['action'];

    if ($action == 'Approval') {
        $update_approval_map = "UPDATE thesis_checklist_approval_map SET Approved = 1 WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id . " AND ApproverId = " . $_SESSION['userid'];
        $update_approval_map_result = mysqli_query($con, $update_approval_map);

        $select_unapproved = "SELECT ThesisChecklistApprovalId FROM thesis_checklist_approval_map WHERE Approved = 0 AND ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id;
        $select_unapproved_result = mysqli_query($con, $select_unapproved);
        if ($select_unapproved_result && mysqli_num_rows($select_unapproved_result) > 0) {
            $update_query = "UPDATE thesis_checklist_map SET Status = 'In Progress' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id;
        } else {
            if ($new_step_status == 'Completed') {
                $update_query = "UPDATE thesis_checklist_map SET Status = '$new_step_status', CompletedDate = current_timestamp(), CompletedBy = '" . $_SESSION['name'] . "' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id;
            } else {
                $update_query = "UPDATE thesis_checklist_map SET Status = '$new_step_status' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id;
            }
        }

        $update_query_result = mysqli_query($con, $update_query);
        if ($update_query_result) {
            $update_thesis = "UPDATE thesis SET Status = 'In Progress', LastModifiedDate = CURDATE(), LastModifiedBy = '" . $_SESSION['name'] . "' WHERE ThesisId = " . $thesisId;
            $update_thesis_result = mysqli_query($con, $update_thesis);
            echo 'success';
        } else {
            echo 'Sorry, there was an error saving to database. Please contact your research coordinator.';;
        }
        return;
    }
}

if (isset($_POST['reject_step'])) {
    $thesisId = $_POST['thesis_Id'];
    $checklist_id = $_POST['checklist_id'];
    $new_step_status = $_POST['new_step_status'];
    $action = $_POST['action'];

    if ($action == 'Approval') {
        $update_approval_map = "UPDATE thesis_checklist_approval_map SET Approved = 0 WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id;
        $update_approval_map_result = mysqli_query($con, $update_approval_map);

        $checklist_id = $checklist_id - 1;
        $update_query = "UPDATE thesis_checklist_map SET Status = 'In Progress' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id;
        $update_query_result = mysqli_query($con, $update_query);
        if ($update_query_result) {
            $update_thesis = "UPDATE thesis SET Status = 'In Progress', LastModifiedDate = CURDATE(), LastModifiedBy = '" . $_SESSION['name'] . "' WHERE ThesisId = " . $thesisId;
            $update_thesis_result = mysqli_query($con, $update_thesis);
            echo 'success';
        } else {
            echo 'Sorry, there was an error saving to database. Please contact your research coordinator.';;
        }
        return;
    }
}

if (isset($_POST['update_panel'])) {
    $thesisId = $_POST['thesis_Id'];
    $panelists = $_POST['panelists'];
    $panelists = str_replace('; ', ';', $panelists);

    $get_Panels = "SELECT ThesisId FROM thesispanelmembermap WHERE ThesisId = $thesisId";
    $get_Panels_result = mysqli_query($con, $get_Panels);
    if ($get_Panels_result && mysqli_num_rows($get_Panels_result) > 0) {
        $update_thesisPanelMap = "UPDATE thesispanelmembermap SET PanelMembers = '$panelists' WHERE ThesisId = $thesisId";
    } else {
        $update_thesisPanelMap = "INSERT INTO `thesispanelmembermap` (`ThesisPanelMemberMap`, `ThesisId`, `PanelMembers`) VALUES (NULL, $thesisId, '$panelists')";
    }

    $update_thesisPanelMap_result = mysqli_query($con, $update_thesisPanelMap);

    $update_thesis = "UPDATE thesis SET Status = 'In Progress', LastModifiedDate = CURDATE(), LastModifiedBy = '" . $_SESSION['name'] . "' WHERE ThesisId = " . $thesisId;
    $update_thesis_result = mysqli_query($con, $update_thesis);
    if ($update_thesis_result) {
        echo 'success';
    } else {
        echo 'Sorry, there was an error saving to database. Please contact your research coordinator.';;
    }
    return;
}

if (isset($_POST['delete_file'])) {
    $thesisId = $_POST['thesis_Id'];
    $checklist_id = $_POST['checklist_id'];
    $new_step_status = $_POST['new_step_status'];
    $action = $_POST['action'];
    $step_file_name = $_POST['step_file_name'];

    if ($action == 'DeleteFile') {
        $fileDir = $_SERVER['DOCUMENT_ROOT'] . '/thesis-mgmt/uploads/';

        $allowTypes = array('pdf', 'doc', 'docx', 'jpg', 'png', 'jpeg');
        foreach ($allowTypes as $fileType) {
            $targetFilePath = $fileDir . $step_file_name . '.' . $fileType;
            if (file_exists($targetFilePath)) {
                if (unlink($targetFilePath)) {
                    $delete_query = "DELETE FROM thesis_checklist_file_map WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id;
                    $run_query = mysqli_query($con, $delete_query);

                    $update_query = "UPDATE thesis_checklist_map SET Status = '$new_step_status', CompletedDate = null, CompletedBy = null WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $checklist_id;
                    $query_run = mysqli_query($con, $update_query);
                    $update_thesis = "UPDATE thesis SET Status = 'In Progress', LastModifiedDate = CURDATE(), LastModifiedBy = '" . $_SESSION['name'] . "' WHERE ThesisId = " . $thesisId;
                    $update_thesis_result = mysqli_query($con, $update_thesis);

                    echo "The file was deleted successfully.";
                    break;
                } else {
                    echo "There was an error deleting the file. Please try again.";
                }
            }
        }
    }
}
