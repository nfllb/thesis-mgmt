<?php
session_start();
include './../dbconnect.php';

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
    <link rel="stylesheet" href="./../css/index.css">
    <style>
        thead {
            font-weight: bold;
            background: #e5e5e5;
        }

        .btn {
            margin-bottom: 2.5rem;
        }

        tfoot {
            color: #969696;
        }
    </style>

    <title>Reports</title>
</head>

<body>
    <?php
    // include 'sidebar.php';
    ?>

    <?php
    $sqlGetFiles = 'SELECT * FROM adviser_student_vw';
    $result = mysqli_query($con, $sqlGetFiles);
    $data_for_copy = mysqli_fetch_all($result);
    $data_for_copy_json = json_encode($data_for_copy);
    echo "<div class='container' style='padding-top:50px;'>
                <caption>Summary List of Adviser - Promoters</caption>
                <br/>
                <br/>
                ";
    echo "<div style='float:right;margin-bottom:10px;'>
                    <button type='button' class='btn btn-primary btn-sm' id='export-btn' data-clipboard-target='#resultsTable'
                    data-toggle='tooltip' data-placement='top' title='Copy data to clipboard'>
                        <i class='fa-regular fa-copy' style='margin-right:3px;'></i>
                        Copy
                    </button>
                    </div>
                    ";


    if (mysqli_num_rows($result) > 0)
    {
        echo "<table id='resultsTable' class='table table-bordered table-hover table-sm w-auto'>
                        <thead>
                            <tr class='btn-primary'>
                                <th>ADVISER-PROMOTER</th>
                                <th>STUDENT RESEARCHERS</th>
                                <th>DATE OF FINAL DEFENSE</th>
                            </tr>
                        </thead>
                        <tbody>";
        foreach ($data_for_copy as $row)
        {
            echo "<tr>
                                    <td>$row[3]</td>
                                    <td>$row[4]</td>
                                    <td>$row[5]</td>
                                </tr>";
        }
        echo "</tbody>
                    </table>";
    } else
    {
        //Display when no record found;
    }

    echo "</div>";
    ?>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>
    <script type="text/javascript">
        // If you do not want to use jQuery you can use Pure JavaScript. See FAQ below
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    <script>
        var clipboard = new ClipboardJS('.btn');
    </script>

</body>

</html>