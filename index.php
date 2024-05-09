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

        <?php
        if ($_SESSION['role'] != 'Student')
        {
            header("Location: /thesis-mgmt/dashboard/index.php");
        } else
        {
            // Connect to your database
            $mysqli = mysqli_connect("localhost", "root", "", "thesis_mgmt");

            // Check connection
            if ($mysqli->connect_error)
            {
                die("Connection failed: " . $mysqli->connect_error);
            }

            $sql_Select = "SELECT * FROM thesis_groupedstudents_vw" . " WHERE Authors LIKE '%" . $_SESSION['name'] . "%'";
            $result = $mysqli->query($sql_Select);
            if ($result->num_rows == 0)
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