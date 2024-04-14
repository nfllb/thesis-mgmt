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

$username = $_SESSION['username'];
$newpassword = test_input($_POST['newpassword']);
$confirmpassword = test_input($_POST['confirmpassword']);


if ($newpassword == $confirmpassword)
{
    $confirmpassword = md5($confirmpassword);
    $updateSQL = "UPDATE users SET Password = '$confirmpassword' WHERE UserName='$username';";
    $result2 = mysqli_query($con, $updateSQL);
    if ($result2)
    {
        session_unset();
        session_destroy();
        header("Location: ../login.php");
        exit();
    } else
    {
        header("Location: ./../forgotpassword/resetpassword.php?error=unknown error occurred");
        exit();
    }
} else
{
    header("Location: ./../forgotpassword/resetpassword.php?error=Password does not match");
}