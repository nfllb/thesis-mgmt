<?php
session_start();
include './../dbconnect.php';

if (
  isset($_POST['role']) && isset($_POST['name']) && isset($_POST['username'])
  && isset($_POST['email']) && isset($_POST['password'])
)
{

  function validate($data)
  {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  $role = validate($_POST['role']);
  $name = validate($_POST['name']);
  $username = validate($_POST['username']);
  $email = validate($_POST['email']);
  $password = validate($_POST['password']);


  $user_data = 'username=' . $username . '&name=' . $name;

  if (empty($role))
  {
    header("Location: ../signup.php?error=Role is required&$user_data");
    exit();
  } else if (empty($name))
  {
    header("Location: ../signup.php?error=Name is required&$user_data");
    exit();
  } else if (empty($username))
  {
    header("Location: ../signup.php?error=User Name is required&$user_data");
    exit();
  } else if (empty($email))
  {
    header("Location: ../signup.php?error=Email is required&$user_data");
    exit();
  } else if (empty($password))
  {
    header("Location: ../signup.php?error=Password is required&$user_data");
    exit();
  } else
  {
    // hashing the password
    $password = md5($password);

    $sql = "SELECT UserId FROM users WHERE UserName='$username' ";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) > 0)
    {
      header("Location: ../signup.php?error=The username is taken try another&$user_data");
      exit();
    } else
    {
      $insertSQL = "INSERT INTO users(Role, Name, UserName, Password, Email) VALUES ('$role', '$name', '$username', '$password', '$email')";
      $result2 = mysqli_query($con, $insertSQL);
      if ($result2)
      {
        header("Location: ../login.php");
        exit();
      } else
      {
        header("Location: signup.php?error=unknown error occurred&$user_data");
        exit();
      }
    }
  }

} else
{
  header("Location: ../signup.php");
  exit();
}