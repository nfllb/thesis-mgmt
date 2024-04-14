<?php
session_start();
include './../dbconnect.php';

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
$securityquestion = validate($_POST['securityquestion']);
$securityanswer = validate($_POST['securityanswer']);


if (empty($role))
{
  header("Location: ../signup.php?error=Academic Role is required");
  exit();
} else if (empty($name))
{
  header("Location: ../signup.php?error=Name is required");
  exit();
} else if (empty($username))
{
  header("Location: ../signup.php?error=User Name is required");
  exit();
} else if (empty($email))
{
  header("Location: ../signup.php?error=Email is required");
  exit();
} else if (empty($password))
{
  header("Location: ../signup.php?error=Password is required");
  exit();
} else
{
  // hashing the password
  $password = md5($password);

  $sql = "SELECT UserId FROM users WHERE UserName='$username' ";
  $result = mysqli_query($con, $sql);

  if (mysqli_num_rows($result) > 0)
  {
    header("Location: ../signup.php?error=The username is taken try another");
    exit();
  } else
  {
    $insertSQL = "INSERT INTO users(Role, Name, UserName, Password, Email, SecurityQuestion, SecurityAnswer) VALUES ('$role', '$name', '$username', '$password', '$email', '$securityquestion', '$securityanswer')";
    $result2 = mysqli_query($con, $insertSQL);
    if ($result2)
    {
      session_unset();
      session_destroy();
      header("Location: ../login.php");
      exit();
    } else
    {
      header("Location: signup.php?error=unknown error occurred");
      exit();
    }
  }
}