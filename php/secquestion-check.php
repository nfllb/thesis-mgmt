<?php

session_start();
include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$secanswer = test_input($_POST['secanswer']);

if ($secanswer == $_SESSION['sec_answer'])
{
    header("Location: /thesis-mgmt/forgotpassword/resetpassword.php");
} else
{
    header("Location: /thesis-mgmt/forgotpassword/securityquestion.php?error=Incorrect security answer.");
}