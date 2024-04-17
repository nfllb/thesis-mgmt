<?php

// $fileurl = './../files/forms/form_results/Language-Editor-Certification_My Test Thesis.docx';
// header("Content-type:application/docx");
// header('Content-Disposition: attachment; filename=' . $fileurl);
// readfile($fileurl);

$files = array('./../files/forms/form_results/Language-Editor-Certification_My Test Thesis.docx', './../files/forms/Language-Editor-Certification.docx');

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