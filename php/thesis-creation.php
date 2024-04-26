<?php

session_start();
include './../dbconnect.php';

if (isset($_POST['create_new_thesis']))
{
    // Call stored procedure
    $sql = "CALL CreateNewThesis()";
    $result = $mysqli->query($sql);

    if ($result === FALSE)
    {
        echo "An error occured during thesis creation: " . $mysqli->error;
    } else
    {
        echo "success";
    }
}