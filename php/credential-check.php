<?php
session_start();
include './../dbconnect.php';

if (isset($_POST['username']) && isset($_POST['password']))
{
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
        header("Location: ../login.php?error=Username is required");
    } else if (empty($password))
    {
        header("Location: ../login.php?error=Password is required");
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
            if ($row['Password'] === $password && $row['UserName'] == $username)
            {
                $_SESSION['name'] = $row['Name'];
                $_SESSION['userid'] = $row['UserId'];
                $_SESSION['role'] = $row['Role'];
                $_SESSION['username'] = $row['UserName'];
                header("Location: ./../index.php");

            } else
            {
                header("Location: ../login.php?error=Incorect User name or password");
            }
        } else
        {
            header("Location: ../login.php?error=Incorect User name or password");
        }
    }
} else
{
    header("Location: ../login.php");
}