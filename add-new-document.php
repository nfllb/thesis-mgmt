<?php
if (isset($_FILES['file']))
{
    // Allowed file types 
    $allowTypes = array('pdf', 'doc', 'docx', 'jpg', 'png', 'jpeg');

    $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/files/forms/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file already exists
    if (file_exists($target_file))
    {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    if (in_array($fileType, $allowTypes))
    {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file))
        {
            echo "The file " . htmlspecialchars(basename($_FILES["file"]["name"])) . " has been uploaded.";
        } else
        {
            echo "Sorry, there was an error uploading your file.";
        }
    } else
    {
        echo 'Sorry, only ' . implode('/', $allowTypes) . ' files are allowed to upload.';
    }
} else
{
    echo 'Please upload file first!';
}
