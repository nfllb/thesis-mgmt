<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");
if (isset($_SESSION['username']) && isset($_SESSION['userid']))
{
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Task</title>

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" href="/thesis-mgmt/css/styles.css">
    </head>

    <body class="content">
        <div>
            <h3 style="position:absolute;margin-top:20px;">Tasks</h3>
            <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/header.php"); ?>
            <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/sidebar.php"); ?>
            <hr>
        </div>

        <?php
        $sql_WhereClause = ' WHERE ';
        if ($_SESSION['role'] == 'Research Coordinator')
        {
            $sql_WhereClause = $sql_WhereClause . '1';
        } else if ($_SESSION['role'] == 'Adviser')
        {
            $sql_WhereClause = $sql_WhereClause . 'Adviser = \'' . $_SESSION['name'] . '\'';
        } else if ($_SESSION['role'] == 'Instructor')
        {
            $sql_WhereClause = $sql_WhereClause . 'Instructor = \'' . $_SESSION['name'] . '\'';
        } else if ($_SESSION['role'] == 'Student')
        {
            $sql_WhereClause = $sql_WhereClause . 'Authors LIKE \'%' . $_SESSION['name'] . '%\'';
        }

        $sql_Select = "SELECT * FROM thesis_groupedstudents_vw" . $sql_WhereClause . ' ORDER BY ThesisId';
        $result = mysqli_query($con, $sql_Select);

        if ($result && mysqli_num_rows($result) > 0)
        {
            while ($thesis = mysqli_fetch_assoc($result))
            {
                $thesis_id = $thesis["ThesisId"];
                $thesis_title = $thesis["Title"];
                $thesis_lastModDate = $thesis["LastModifiedDate"];
                $formatted_date = date("F j, Y", strtotime($thesis_lastModDate));
                $thesis_authors = $thesis["Authors"];
                $authors_arr = explode(',', $thesis_authors);
                echo "<div class='container'>
                    <div id='thesisContainer' class='card w-75 mb-3'>
                        <div class='card-body'>
                            <a id='thesisName' href='/thesis-mgmt/tasks/task_details.php?thesisId=$thesis_id'><h5 class='card-title black_text_hover_pointer'>$thesis_title</h5></a>
                            <h6 class='thesis-text-color'>Authors: ";
                foreach ($authors_arr as $author)
                {
                    echo "<span class='badge text-bg-secondary'>$author</span>";
                }
                echo "</h6>
                            <a href='/thesis-mgmt/php/download-thesis.php?thesisId=$thesis_id' class='btn btn-primary btn-sm'><i style='margin-right:3px;' class='fa-regular fa-circle-down'></i>Download</a>
                            <br><span class='thesis-text-color'>Last Updated Date: $formatted_date </span>
                        </div>
                    </div>
                    </div>";
            }
        } else
        {
            echo "<div class='content container'>
            <div id='thesisContainer' class='card w-100 mb-3'>
                <div class='card-body'>
                    <div style='font-size:16px;'class='alert alert-danger' role='alert>
                        <span class='icon'><i style='font-size:18px;' class='fa-regular fa-circle-xmark'></i></span>
                        You don't have access to any thesis record. Contact your research coordinator for help.
                    </div>
                </div>
            </div>
            </div>";
        }
        ?>

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
            integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
            crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
            integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
            crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>
        <script type="text/javascript">
            // If you do not want to use jQuery you can use Pure JavaScript. See FAQ below
            $(document).ready(function () {
                $('[data-toggle="tooltip"]').tooltip();
            });
        </script>
    </body>

    </html>
<?php } else
{
    header("Location: /thesis-mgmt/login.php");
} ?>