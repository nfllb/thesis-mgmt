<?php

session_start();
include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");

$title = $_POST['title'];
$students = $_POST['studentIds'];
$select_adviser = $_POST['select_adviser'];
$select_instructor = $_POST['select_instructor'];
$year = $_POST['year'];
$dateofdefense = $_POST['dateofdefense'];

// Prepare the stored procedure call
$stmt = $con->prepare("CALL CreateNewThesis(?, ?, ?, ?, ?, ?, ?)");

// Bind the parameters
$title = $_POST['title'];
$students = $_POST['studentIds'];
$select_adviser = $_POST['select_adviser'];
$select_instructor = $_POST['select_instructor'];
$year = $_POST['year'];
$dateofdefense = $_POST['dateofdefense'];

$stmt->bind_param("ssiisss", $title, $students, $select_adviser, $select_instructor, $year, $dateofdefense, $_SESSION['name']);

// Execute the stored procedure
$stmt->execute();

// Check for errors
if ($stmt->error)
{
    echo "Thesis creation encountered an error. Please reach out to your system administrator and provide the error message.\n " . $stmt->error;
} else
{
    echo "success";
}

// Close statement
$stmt->close();