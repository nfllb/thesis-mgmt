<?php
session_start();
if (isset($_SESSION['username']) && isset($_SESSION['userid']))
{ ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Thesis Management</title>
    </head>

    <body>
        <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/header.php"); ?>
        <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/sidebar.php"); ?>

        <?php
        if ($_SESSION['role'] != 'Student')
        {
            header("Location: /thesis-mgmt/dashboard/index.php");
        } else
        {
            $sql_Select = "SELECT * FROM thesis_groupedstudents_vw" . " WHERE Authors LIKE '%" . $_SESSION['name'] . "%'";
            echo $sql_Select;
            $result = mysqli_query($con, $sql_Select);
            if ($result && mysqli_num_rows($result) == 0)
            {
                header("Location: /thesis-mgmt/create_new_thesis.php");
            } else
            {
                header("Location: /thesis-mgmt/tasks/index.php");
            }
        }
        ?>
    </body>

    </html>
<?php } else
{
    header("Location: /thesis-mgmt/login.php");
} ?>