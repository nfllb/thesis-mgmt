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
        $update_query = "UPDATE thesis_checklist_map SET Status = '$new_step_status' WHERE ThesisId = " . $thesisId . " AND CheckListId = " . $stepId . ";";
        $query_run = mysqli_query($con, $update_query);
        echo $update_query;
        return;
    }

}

if (isset($_POST['upload_file_step']))
{

    $thesisId = $_POST['thesis_Id'];
    $stepId = $_POST['step_number'];
    $new_step_status = $_POST['new_step_status'];
    $action = $_POST['action'];

    if ($action == 'Upload')
    {

        $uploadDir = './../uploads/';

        // Allowed file types 
        $allowTypes = array('pdf', 'doc', 'docx', 'jpg', 'png', 'jpeg');

        // Default response 
        $response = array(
            'status' => 0,
            'message' => 'Form submission failed, please try again.'
        );

        $uploadStatus = 1;

        // Upload file 
        $uploadedFile = '';
        if (!empty($_FILES["file"]))
        {
            // File path config 
            $fileName = basename($_FILES["file"]["name"]);
            $targetFilePath = $uploadDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            $fileTemp = $_FILES['file']['tmp_name'];
            $fileSize = $_FILES['file']['size'];

            // Allow certain file formats to upload 
            if (in_array($fileType, $allowTypes))
            {
                // Upload file to the server 
                if (move_uploaded_file($fileTemp, $targetFilePath))
                {
                    // Insert form data in the database 
                    $sqlQ = "INSERT INTO thesis_checklist_file_map (`ThesisChecklistFileId`, `ThesisId`, `CheckListId`, `FileName`, `FilePath`, `UploadedBy`, `UploadedDate`) VALUES (NULL,?,?,?,?,?,NOW());
                    UPDATE `thesis_checklist_map` SET `Status` = ? WHERE ThesisId = ? AND CheckListId = ? ;";
                    $stmt = $con->prepare($sqlQ);
                    $stmt->bind_param("iissssii", $thesisId, $stepId, $fileName, $targetFilePath, $_SESSION['name'], $new_step_status, $thesisId, $stepId);
                    $insert = $stmt->execute();

                    if ($insert)
                    {
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