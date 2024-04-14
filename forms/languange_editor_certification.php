<?php
session_start();
include './../dbconnect.php';
require_once ("./../plugins/vendor/autoload.php");

use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Element\TextRun;

$loggedInUser = 'Student Five';
$sqlGetFiles = "SELECT * FROM thesis_student_adviser_vw WHERE Title = 'My Test Thesis'";
// echo $sqlGetFiles;
$result = mysqli_query($con, $sqlGetFiles);
$thesis_details = mysqli_fetch_all($result);

$thesis_title = $thesis_details[0][0];
$thesis_course = $thesis_details[0][2];
$thesis_adviser = $thesis_details[0][3];
$thesis_date = $thesis_details[0][4];
$thesis_students = '';

foreach ($thesis_details as $row)
{
    $thesis_students = $thesis_students . $row[1] . "\n";
}

$filename = 'Language-Editor-Certification.docx';
$templateProcessor = new TemplateProcessor('./../files/forms' . "/{$filename}");

$Title = new TextRun();
$Title->addText($thesis_title);
$templateProcessor->setComplexValue('Title', $Title);

$Students = new TextRun();
$Students->addText($thesis_students);
$templateProcessor->setComplexValue('Students', $Students);

$Course = new TextRun();
$Course->addText($thesis_course);
$templateProcessor->setComplexValue('Course', $Course);

$Adviser = new TextRun();
$Adviser->addText($thesis_adviser);
$templateProcessor->setComplexValue('Adviser', $Adviser);

$DateOfFinalDefense = new TextRun();
$DateOfFinalDefense->addText($thesis_date);
$templateProcessor->setComplexValue('DateOfFinalDefense', $DateOfFinalDefense);

$filename_New = 'Language-Editor-Certification_' . $thesis_title . '.docx';
// $templateProcessor->saveAs("/form_results/{$filename_New}");

$templateProcessor->saveAs('./../files/forms/form_results/' . $filename_New);

echo "Form successfully generated";
