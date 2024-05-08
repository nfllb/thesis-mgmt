<?php
include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");

// Check if the department and course parameters are set
if (isset($_GET['department']) && isset($_GET['course']))
{
    // Retrieve the department and course values from the GET parameters
    $department = $_GET['department'];
    $course = $_GET['course'];

    $dashboardQuery = "CALL getDashboardDetails('$course', '$department');";
    $resultDashboard = mysqli_query($con, $dashboardQuery);
    if ($resultDashboard)
    {
        $dashboard = mysqli_fetch_assoc($resultDashboard);
        header('Content-Type: application/json');
        echo json_encode($dashboard);
    } else
    {
        // If the query failed, return an error response
        http_response_code(500); // Internal Server Error
        echo json_encode(array('error' => 'Error executing query'));
    }
} else
{
    // If the department and course parameters are not set, return a bad request response
    http_response_code(400); // Bad Request
    echo json_encode(array('error' => 'Department and course parameters are required'));
}
?>