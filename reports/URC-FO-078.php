<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link rel="stylesheet" href="/thesis-mgmt/css/styles.css">
    <style>
        .table {
            background-color: #ecdfd7;
            border-radius: 8px;
            border-collapse: collapse;
            width: 80%;
        }

        .table th,
        .table td {
            border: 1px solid #d3d3d3;
            padding: 10px;
        }

        .table th {
            background-color: #ffffff;
            font-weight: bold;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f7f7f7;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .filter-input-container {
            position: relative;
            width: 250px;
            float: left;
            margin-bottom: 10px;
        }

        .filter-input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 13px;
            outline: none;
            transition: border-color 0.3s ease-in-out;
        }

        .filter-icon {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            font-size: 20px;
            color: #999;
            pointer-events: none;
        }

        .filter-input:focus {
            border-color: #007bff;
        }

        .last-column {
            width: 120px;
        }
    </style>
</head>

<body class="content">
    <div>
        <h3 style="position:absolute;margin-top:20px;">
            <caption>Summary List of Adviser - Promoters</caption>
        </h3>
        <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/header.php"); ?>
        <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/sidebar.php"); ?>
        <hr>
    </div>

    <?php
    $sqlGetFiles = 'SELECT * FROM panel_student_vw ORDER BY PanelMember ASC';
    $result = mysqli_query($con, $sqlGetFiles);
    $data_for_copy = mysqli_fetch_all($result);
    $data_for_copy_json = json_encode($data_for_copy);
    ?>
    <div class="container" style="margin: 0 auto; display: flex; flex-direction: column; align-items: center;">
        <div class="clearfix" style="width: 80%; display: flex; justify-content: start; align-items: center;">
            <div class="filter-input-container">
                <input type="text" id="filterInput" class="filter-input" onkeyup="filterList()"
                    placeholder="Filter by school year...">
                <span class="filter-icon"><i class="fas fa-search"></i></span>
            </div>
            <div class="btn-container" style="padding-left: 10px;">
                <button type="button" class="btn btn-primary btn-sm mb-2" id="export-btn"
                    data-clipboard-target="#resultsTable" data-toggle="tooltip" data-placement="top"
                    title="Copy data to clipboard">
                    <i class="fa-regular fa-copy" style="margin-right: 3px;"></i>
                    Copy
                </button>
            </div>
        </div>
        <div class="table-responsive" style="width: 80%">
            <?php
            if (mysqli_num_rows($result) > 0)
            {
                echo "<table id='resultsTable' class='table'>
    <thead>
        <tr style='text-align: center;'>
            <th style='display: none;'>Year</th>
            <th style='width: auto;'>Panel Members</th>
            <th style='width: auto;'>Student Researchers</th>
            <th style='width: auto;'>Date of Final Defense</th>
        </tr>
    </thead>
    <tbody>";
                foreach ($data_for_copy as $row)
                {
                    echo "<tr>
                <td style='display: none;'>$row[2]</td>
                <td>$row[3]</td>
                <td>$row[4]</td>
                <td>$row[5]</td>
            </tr>";
                }
                echo "</tbody>
</table>";
            } else
            {
                echo "<p>No data available</p>";
            }
            ?>
        </div>
    </div>

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

    <script>
        function filterList() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("filterInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("resultsTable");
            tr = table.getElementsByTagName("tr");

            // Loop through all table rows, and hide those that don't match the filter
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0]; // Index 0 is the File Name column
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>

</body>

</html>