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

$username = test_input($_POST['username']);
$password = test_input($_POST['password']);

if (empty($username))
{
    header("Location: /thesis-mgmt/login.php?error=Username is required");
} else if (empty($password))
{
    header("Location: /thesis-mgmt/login.php?error=Password is required");
} else
{

    // Hashing the password
    $password = md5($password);

    $sql = "SELECT * FROM users WHERE UserName='$username' AND Password='$password'";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) === 1)
    {
        // the user name must be unique
        $row = mysqli_fetch_assoc($result);

        if ($row['Status'] == 'Active')
        {
            if ($row['Password'] === $password && $row['UserName'] == $username)
            {
                $_SESSION['name'] = $row['Name'];
                $_SESSION['userid'] = $row['UserId'];
                $_SESSION['role'] = $row['Role'];
                $_SESSION['username'] = $row['UserName'];
                header("Location: /thesis-mgmt/index.php");

            } else
            {
                header("Location: /thesis-mgmt/login.php?error=Incorrect username or password.");
            }
        } else
        {
            header("Location: /thesis-mgmt/login.php?error=The user is currently inactive. Please reach out to your Research Coordinator for assistance.");
        }

    } else
    {
        header("Location: /thesis-mgmt/login.php?error=Incorrect username or password.");
    }
}