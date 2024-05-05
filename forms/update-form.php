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

if (in_array('Panel_Table_Two_Col', $variables))
{
    $no_panelists = 'No panelists have been selected.';
    // Create a table with 2 columns
    $table = new \PhpOffice\PhpWord\Element\Table();

    // Retrieve data from your database (assuming you only need one field)
    $get_Panels = "SELECT PanelMembers FROM thesispanelmembermap WHERE ThesisId = $selectedThesisId";
    $get_Panels_result = $con->query($get_Panels);
    if (mysqli_num_rows($get_Panels_result) > 0)
    {
        $panelists_arr = $get_Panels_result->fetch_assoc();
        $panel = $panelists_arr["PanelMembers"];
        if (!empty($panel))
        {
            // Split the string into an array
            $valuesArray = explode(';', $panel);

            // Determine number of rows needed to display all values
            $numRows = ceil(count($valuesArray) / 2);

            $fontStyle = ['name' => 'Cambria', 'size' => 12, 'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0)];
            $cellHCentered = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER];
            $cellVCentered = ['valign' => 'bottom', 'borderBottomColor' => '000000', 'borderBottomSize' => 3, 'borderBottomStyle' => 'single'];

            // Populate the table with data from the database in a 2-column layout
            for ($i = 0; $i < $numRows; $i++)
            {
                $table->addRow();
                // Add data to the first column
                $index = $i * 2;
                $table->addCell(4000, $cellVCentered)->addText(isset($valuesArray[$index]) ? $valuesArray[$index] : '', $fontStyle, $cellHCentered);
                // Add blank cell for the middle column
                $table->addCell(2500);
                // Add data to the third column
                $index++;
                if ($index <= (count($valuesArray) - 1))
                {
                    $table->addCell(4000, $cellVCentered)->addText(isset($valuesArray[$index]) ? $valuesArray[$index] : '', $fontStyle, $cellHCentered);
                } else
                {
                    $table->addCell(4000);
                }

                $table->addRow();
                $table->addCell(4000, array('valign' => 'bottom'))->addText('Panelist ' . $index, array('name' => 'Cambria', 'size' => 12, 'italic' => true), $cellHCentered);
                $table->addCell(2500);
                if (($index + 1) <= count($valuesArray))
                {
                    $table->addCell(4000, array('valign' => 'bottom'))->addText('Panelist ' . $index + 1, array('name' => 'Cambria', 'size' => 12, 'italic' => true), $cellHCentered);
                }

                if ($i < ($numRows - 1))
                {
                    $table->addRow();
                    $table->addCell(4000);
                    $table->addCell(2500);
                    $table->addCell(4000);
                }

            }

            // Replace the variable with the table
            $templateProcessor->setComplexBlock('Panel_Table_Two_Col', $table);
        } else
        {
            // Blank PanelMembers, show "No panelists have been selected."
            $templateProcessor->setValue('Panel_Table_Two_Col', $no_panelists);
        }
    } else
    {
        // Blank PanelMembers, show "No panelists have been selected."
        $templateProcessor->setValue('Panel_Table_Two_Col', $no_panelists);
    }
}


if (in_array('Panelists', $variables))
{
    $no_panelists = 'No panelists have been selected.';
    $panelists = '';

    $get_Panels = "SELECT PanelMembers FROM thesispanelmembermap WHERE ThesisId = $selectedThesisId";
    $get_Panels_result = $con->query($get_Panels);
    if (mysqli_num_rows($get_Panels_result) > 0)
    {
        $panelists_arr = $get_Panels_result->fetch_assoc();
        $panel = $panelists_arr["PanelMembers"];
        if (!empty($panel))
        {
            $panelists = str_replace(';', ', ', $panel);
        } else
        {
            $panelists = $no_panelists;
        }
    } else
    {
        $panelists = $no_panelists;
    }
    $templateProcessor->setValue('Panelists', $panelists);
}

if (in_array('Panelists_With_Role', $variables))
{
    $no_panelists = 'No panelists have been selected.';
    $panelists = '';

    $get_Panels = "SELECT PanelMembers FROM thesispanelmembermap WHERE ThesisId = $selectedThesisId";
    $get_Panels_result = $con->query($get_Panels);
    if (mysqli_num_rows($get_Panels_result) > 0)
    {
        $panelists_arr = $get_Panels_result->fetch_assoc();
        $panel = $panelists_arr["PanelMembers"];
        if (!empty($panel))
        {
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection();

            // Create a table
            //$table = new \PhpOffice\PhpWord\Element\Table();
            $table = $section->addTable(['width' => 50 * 50, 'unit' => 'pct', 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER]);
            $fontStyle = ['name' => 'Cambria', 'size' => 12, 'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0)];
            $cellHCentered = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER];
            $cellVCentered = ['valign' => 'bottom', 'borderBottomColor' => '000000', 'borderBottomSize' => 3, 'borderBottomStyle' => 'single'];

            // Split the string into an array
            $valuesArray = explode(';', $panel);
            $numRows = count($valuesArray);
            for ($i = 0; $i < $numRows; $i++)
            {
                $cell1 = $table->addRow()->addCell(4000)->addText($valuesArray[$i], $fontStyle, $cellHCentered);
                $paragraphStyle = $cell1->getParagraphStyle();
                $paragraphStyle->setSpaceAfter(0);

                $panelRole = ($i == 0) ? 'Chairman' : 'Member';
                $table->addRow();
                $table->addCell(4000)->addText($panelRole, array('name' => 'Cambria', 'size' => 12, 'italic' => true), $cellHCentered);

                if ($i < $numRows - 1)
                {
                    $cell2 = $table->addRow()->addCell(4000)->addText('');
                    $paragraphStyle2 = $cell2->getParagraphStyle();
                    $paragraphStyle2->setSpaceAfter(0);
                }

            }

            // Replace the variable with the table
            $templateProcessor->setComplexBlock('Panelists_With_Role', $table);
        } else
        {
            $templateProcessor->setValue('Panelists_With_Role', $no_panelists);
        }
    } else
    {
        $templateProcessor->setValue('Panelists_With_Role', $no_panelists);
    }
}

if (in_array('Panel_Table_Two_Col_Signature', $variables))
{
    $no_panelists = 'No panelists have been selected.';
    // Create a table with 2 columns
    $table = new \PhpOffice\PhpWord\Element\Table();

    // Retrieve data from your database (assuming you only need one field)
    $get_Panels = "SELECT PanelMembers FROM thesispanelmembermap WHERE ThesisId = $selectedThesisId";
    $get_Panels_result = $con->query($get_Panels);
    if (mysqli_num_rows($get_Panels_result) > 0)
    {
        $panelists_arr = $get_Panels_result->fetch_assoc();
        $panel = $panelists_arr["PanelMembers"];
        if (!empty($panel))
        {
            // Split the string into an array
            $valuesArray = explode(';', $panel);

            // Determine number of rows needed to display all values
            $numRows = ceil(count($valuesArray) / 2);

            $fontStyle = ['name' => 'Cambria', 'size' => 12, 'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0)];
            $cellHCentered = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER];
            $cellVCentered = ['valign' => 'bottom', 'borderBottomColor' => '000000', 'borderBottomSize' => 3, 'borderBottomStyle' => 'single'];

            // Populate the table with data from the database in a 2-column layout
            for ($i = 0; $i < $numRows; $i++)
            {
                $table->addRow();
                $index = $i * 2;

                // Add data to the first column
                $cell1 = $table->addCell(4000, $cellVCentered)->addText(isset($valuesArray[$index]) ? $valuesArray[$index] : '', $fontStyle, $cellHCentered);
                $paragraphStyle = $cell1->getParagraphStyle();
                $paragraphStyle->setSpaceAfter(0);

                // Add blank cell for the middle column
                $table->addCell(2500);

                // Add data to the third column
                $index++;
                if ($index <= (count($valuesArray) - 1))
                {
                    $cell2 = $table->addCell(4000, $cellVCentered)->addText(isset($valuesArray[$index]) ? $valuesArray[$index] : '', $fontStyle, $cellHCentered);
                    $paragraphStyle2 = $cell2->getParagraphStyle();
                    $paragraphStyle2->setSpaceAfter(0);
                } else
                {
                    $table->addCell(4000);
                }

                $table->addRow();
                $table->addCell(4000, array('valign' => 'bottom'))->addText('(Printed Name and Signature)', array('name' => 'Cambria', 'size' => 12, 'italic' => true), $cellHCentered);
                $table->addCell(2500);
                if (($index + 1) <= count($valuesArray))
                {
                    $table->addCell(4000, array('valign' => 'bottom'))->addText('(Printed Name and Signature)', array('name' => 'Cambria', 'size' => 12, 'italic' => true), $cellHCentered);
                }

                if ($i < ($numRows - 1))
                {
                    $table->addRow();
                    $table->addCell(4000);
                    $table->addCell(2500);
                    $table->addCell(4000);
                }

            }

            // Replace the variable with the table
            $templateProcessor->setComplexBlock('Panel_Table_Two_Col_Signature', $table);
        } else
        {
            // Blank PanelMembers, show "No panelists have been selected."
            $templateProcessor->setValue('Panel_Table_Two_Col_Signature', $no_panelists);
        }
    } else
    {
        // Blank PanelMembers, show "No panelists have been selected."
        $templateProcessor->setValue('Panel_Table_Two_Col_Signature', $no_panelists);
    }
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
