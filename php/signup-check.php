<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");

function validate($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

$role = validate($_POST['role']);
$firstname = validate($_POST['firstname']);
$middlename = validate($_POST['middlename']);
$lastname = validate($_POST['lastname']);
$username = validate($_POST['username']);
$email = validate($_POST['email']);
$password = validate($_POST['password']);
$securityquestion = validate($_POST['securityquestion']);
$securityanswer = validate($_POST['securityanswer']);
$department = '';
$year = '';
$course = '';
$idnumber = '';


if (isset($_POST['department'])) {
  $department = validate($_POST['department']);
}
if (isset($_POST['year'])) {
  $year = validate($_POST['year']);
}
if (isset($_POST['course'])) {
  $course = validate($_POST['course']);
}
if (isset($_POST['idnumber'])) {
  $idnumber = validate($_POST['idnumber']);
}

// hashing the password
$password = md5($password);

$select_existing_user = "SELECT UserId FROM users WHERE UserName='$username' ";
$result_select_existing_user = mysqli_query($con, $select_existing_user);

$select_idnumber_inuse = "SELECT IDNumber FROM student WHERE IDNumber='$idnumber' ";
$result_select_idnumber_inuse = mysqli_query($con, $select_idnumber_inuse);

if (mysqli_num_rows($result_select_existing_user) > 0) {
  header("Location: /thesis-mgmt/signup.php?error=The User Name is already in use. Please try a different one.");
  exit();
} else if ($role == 'Student' && mysqli_num_rows($result_select_idnumber_inuse) > 0) {
  header("Location: /thesis-mgmt/signup.php?error=The ID Number is already in use. Please try a different one.");
  exit();
} else {
  // Prepare the stored procedure call
  $stmt = $con->prepare("CALL CreateNewUser(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  $stmt->bind_param("sssssssssssss", $role, $firstname, $middlename, $lastname, $username, $password, $email, $securityquestion, $securityanswer, $course, $department, $year, $idnumber);

  // Execute the stored procedure
  $stmt->execute();

  // Check for errors
  if ($stmt->error) {
    header("Location: /thesis-mgmt/signup.php?error=User creation encountered an error. Please reach out to your system administrator and provide the error message.");
    exit();
  } else {
    session_unset();
    session_destroy();
    header("Location: /thesis-mgmt/login.php");
  }

  // Close statement
  $stmt->close();
}
