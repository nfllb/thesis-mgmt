<?php

session_start();
include './../dbconnect.php';

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
    header("Location: ./../forgotpassword/resetpassword.php");
} else
{
    header("Location: ./../forgotpassword/securityquestion.php?error=Incorrect security answer.");
}