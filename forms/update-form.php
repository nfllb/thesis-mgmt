<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");

// Include PHPWord library
require_once ("./../plugins/vendor/autoload.php");

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Element\TextRun;

// Get the file name and selected data from the AJAX request
$fileName = $_POST['fileName'];
$selectedThesisId = $_POST['selectedThesisId'];
$selectedThesisName = $_POST['thesisTitle'];

$sql_Select = "SELECT * FROM thesis_groupedstudents_vw WHERE ThesisId = " . $selectedThesisId;
$result = mysqli_query($con, $sql_Select);
$thesis_details = mysqli_fetch_assoc($result);

$thesis_students = $thesis_details['Authors'];
// Remove the last comma
if (substr($thesis_students, -1) === ',')
{
    $thesis_students = substr($thesis_students, 0, -1);
}

$thesis_course = $thesis_details['Course'];
$thesis_dept = $thesis_details['Department'];
$thesis_year = $thesis_details['Year'];
$thesis_school_year = $thesis_details['SchoolYear'];

$thesis_instructor = $thesis_details['Instructor'];
$thesis_adviser = $thesis_details['Adviser'];
$thesis_date = $thesis_details['DateOfFinalDefense'];

$documentPath = './../files/forms/' . $fileName;
$phpWord = IOFactory::load($documentPath);

$templateProcessor = new TemplateProcessor($documentPath);
$variables = $templateProcessor->getVariables();

$templateProcessor->setValue('Title', $selectedThesisName);

$thesis_students_one_line = str_replace(',', ', ', $thesis_students);
$templateProcessor->setValue('Students', $thesis_students_one_line);

$thesis_students_multi_line = str_replace(',', '<w:br/>', $thesis_students);
$templateProcessor->setValue('Student_MultiLine', $thesis_students_multi_line);

if (in_array('Students_MultiLine_Underlined', $variables))
{
    // Create a table
    $table = new \PhpOffice\PhpWord\Element\Table();

    // Split the string into an array
    $valuesArray = explode(',', $thesis_students);
    // Add cells to the table for each value in the array
    foreach ($valuesArray as $value)
    {
        // Add a row to the table
        $table->addRow()->addCell(4000, array('borderBottomColor' => '000000', 'borderBottomSize' => 5, 'borderBottomStyle' => 'single'))->addText($value, array('name' => 'Cambria', 'size' => 12));

        // Add an extra cell to the row for spacing
        $table->addRow()->addCell(4000)->setHeight(0);
    }

    // Replace the variable with the table
    $templateProcessor->setComplexBlock('Students_MultiLine_Underlined', $table);
}

if (in_array('Students_MultiLine_Underlined_Numbered', $variables))
{
    // Create a table
    $table = new \PhpOffice\PhpWord\Element\Table();

    // Split the string into an array
    $valuesArray = explode(',', $thesis_students);

    $counter = 1;
    foreach ($valuesArray as $value)
    {
        // Add a row to the table
        $tableRow = $table->addRow();

        $tableRow->addCell(500)->addText($counter . '.', array('name' => 'Cambria', 'size' => 12));
        $tableRow->addCell(4000, array('borderBottomColor' => '000000', 'borderBottomSize' => 5, 'borderBottomStyle' => 'single'))->addText($value, array('name' => 'Cambria', 'size' => 12));

        $counter++;
    }

    // Replace the variable with the table
    $templateProcessor->setComplexBlock('Students_MultiLine_Underlined_Numbered', $table);
}

if (in_array('Students_Table_Signature', $variables))
{
    // Create a table
    $table = new \PhpOffice\PhpWord\Element\Table();
    $tableHeader = $table->addRow();

    $tableHeader->addCell(4000)->addText('Proponents (Name)', array('name' => 'Cambria', 'size' => 12, 'bold' => true));
    $tableHeader->addCell(4000);
    $tableHeader->addCell(4000)->addText('Signature', array('name' => 'Cambria', 'size' => 12, 'bold' => true));

    // Split the string into an array
    $valuesArray = explode(',', $thesis_students);

    $counter = 1;
    foreach ($valuesArray as $value)
    {
        // Add a row to the table
        $tableRow = $table->addRow();

        $tableRow->addCell(4000, array('borderBottomColor' => '000000', 'borderBottomSize' => 5, 'borderBottomStyle' => 'single'))->addText($value, array('name' => 'Cambria', 'size' => 12));
        $tableRow->addCell(4000);
        $tableRow->addCell(4000, array('borderBottomColor' => '000000', 'borderBottomSize' => 5, 'borderBottomStyle' => 'single'))->addText('', array('name' => 'Cambria', 'size' => 12));

        $counter++;
    }

    // Replace the variable with the table
    $templateProcessor->setComplexBlock('Students_Table_Signature', $table);
}


$templateProcessor->setValue('Course', $thesis_course);
$templateProcessor->setValue('Year', $thesis_year);
$templateProcessor->setValue('Adviser', $thesis_adviser);
$templateProcessor->setValue('Instructor', $thesis_instructor);
$templateProcessor->setValue('SchoolYear', $thesis_school_year);
$templateProcessor->setValue('DateOfFinalDefense', $thesis_date);
$templateProcessor->setValue('Date', date('m-d-Y'));

//$updatedDocumentPath = './../files/forms/form_results/' . $selectedThesisName . '_' . $fileName;

$updatedDocumentPath = './../files/forms/form_results/' . $fileName;

$templateProcessor->saveAs($updatedDocumentPath);

// Return the updated document path
echo $updatedDocumentPath;
