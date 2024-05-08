<?php
include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");

// Check if the department and course parameters are set
if (isset($_GET['part']))
{
    $part = $_GET['part'];

    $dashboardQuery = '';
    if ($part == 'All')
    {
        $dashboardQuery = "SELECT ROUND(AVG(Completed),2) AS Completed, ROUND(AVG(InProgress),2) AS InProgress, ROUND(AVG(NotStarted),2) AS NotStarted FROM getDashboardGauge";
    } else
    {
        $dashboardQuery = "SELECT * FROM getDashboardGauge WHERE Part = $part";
    }

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
    echo json_encode(array('error' => 'Part parameter is required'));
}
?>