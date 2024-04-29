<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");

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
$department = '';
$year = '';
$course = '';

if (isset($_POST['department']))
{
  $department = validate($_POST['department']);
}
if (isset($_POST['year']))
{
  $year = validate($_POST['year']);
}
if (isset($_POST['course']))
{
  $course = validate($_POST['course']);
}

if (empty($role))
{
  header("Location: /thesis-mgmt/signup.php?error=Academic Role is required.");
  exit();
} else if (empty($name))
{
  header("Location: /thesis-mgmt/signup.php?error=Name is required.");
  exit();
} else if (empty($username))
{
  header("Location: /thesis-mgmt/signup.php?error=User Name is required.");
  exit();
} else if (empty($email))
{
  header("Location: /thesis-mgmt/signup.php?error=An email address is necessary.");
  exit();
} else if (empty($password))
{
  header("Location: /thesis-mgmt/signup.php?error=Password is required.");
  exit();
} else
{
  // hashing the password
  $password = md5($password);

  $sql = "SELECT UserId FROM users WHERE UserName='$username' ";
  $result = mysqli_query($con, $sql);

  if (mysqli_num_rows($result) > 0)
  {
    header("Location: /thesis-mgmt/signup.php?error=The username is already in use. Please try another.");
    exit();
  } else
  {
    // Prepare the stored procedure call
    $stmt = $con->prepare("CALL CreateNewUser(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssssssssss", $role, $name, $username, $password, $email, $securityquestion, $securityanswer, $course, $department, $year);

    // Execute the stored procedure
    $stmt->execute();

    // Check for errors
    if ($stmt->error)
    {
      header("Location: /thesis-mgmt/signup.php?error=User creation encountered an error. Please reach out to your system administrator and provide the error message.");
      exit();
    } else
    {
      session_unset();
      session_destroy();
      header("Location: /thesis-mgmt/login.php");
    }

    // Close statement
    $stmt->close();
  }
}