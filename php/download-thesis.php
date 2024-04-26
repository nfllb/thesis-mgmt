<?php
include './../dbconnect.php';

$thesisId = $_GET["thesisId"];
$sql_Select = "SELECT * FROM thesis_checklist_file_map WHERE ThesisId = " . $thesisId;
$result = mysqli_query($con, $sql_Select);

if ($result && mysqli_num_rows($result) > 0)
{


    $files = array();
    while ($thesis = mysqli_fetch_assoc($result))
    {
        array_push($files, $thesis["FilePath"]);
    }

    $zip = new ZipArchive();
    $zip_name = time() . ".zip"; // Zip name
    $zip->open($zip_name, ZipArchive::CREATE);
    foreach ($files as $file)
    {
        echo $path = $file;
        if (file_exists($path))
        {
            $zip->addFromString(basename($path), file_get_contents($path));
        } else
        {
            echo "file does not exist";
        }
    }
    $zip->close();
    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename=' . $zip_name);
    readfile($zip_name);
} else
{
    header("Location: ../tasks/index.php?error=There are no uploaded files for this thesis.");
}