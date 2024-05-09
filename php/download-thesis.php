<?php
// Include database connection
include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");

// Check if thesisId is provided
if (!isset($_GET["thesisId"]) || empty($_GET["thesisId"]))
{
    header("Location: /thesis-mgmt/tasks/index.php?error=Thesis ID is missing.");
    exit;
}

$thesisId = $_GET["thesisId"];
$page = $_GET["page"];

// Prepare and execute SQL query
$sql_Select = "SELECT FilePath FROM thesis_checklist_file_map WHERE ThesisId = ?";
$stmt = mysqli_prepare($con, $sql_Select);
mysqli_stmt_bind_param($stmt, "i", $thesisId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if result is valid
if ($result && mysqli_num_rows($result) > 0)
{
    $files = array();
    while ($row = mysqli_fetch_assoc($result))
    {
        $files[] = $row["FilePath"];
    }

    // Create ZIP archive
    $zip = new ZipArchive();
    $zip_name = time() . ".zip"; // Zip name
    if ($zip->open($zip_name, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE)
    {
        foreach ($files as $file)
        {
            if (file_exists($file))
            {
                $zip->addFile($file, basename($file));
            } else
            {
                echo "File does not exist: $file";
            }
        }
        $zip->close();

        // Set headers and send zip file to user
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=' . $zip_name);
        readfile($zip_name);
        unlink($zip_name); // Remove the temporary ZIP file after sending
    } else
    {
        echo "Failed to create ZIP archive.";
    }
} else
{
    header("Location: /thesis-mgmt/" . $page . "/index.php?error=There are no uploaded files for this thesis.");
}

// Close database connection
mysqli_stmt_close($stmt);
mysqli_close($con);
