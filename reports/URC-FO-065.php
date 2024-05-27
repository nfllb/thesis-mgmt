<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");

if (isset($_SESSION['username']) && isset($_SESSION['userid'])) {
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reports</title>

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" href="/thesis-mgmt/css/styles.css">

        <style>
            .table {
                background-color: #ecdfd7;
                border-radius: 8px;
                border-collapse: collapse;
                width: 100%;
            }

            .table th,
            .table td {
                border: 1px solid lightslategray;
                padding: 10px;
                font-family: Cambria, serif !important;
            }

            .table th {
                background-color: #ffffff;
                font-weight: bold;
                font-size: 14px !important;
            }

            .table tbody tr (even) {
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
                <caption> Reports / URC-FO-065
                </caption>
            </h3>
            <?php include($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/header.php"); ?>
            <?php include($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/sidebar.php"); ?>
            <hr>
        </div>
        <?php
        if (isset($_SESSION['role']) && $_SESSION['role'] == 'Research Coordinator') { ?>
            <div class="container" style="margin: 0 auto; display: flex; flex-direction: column; align-items: center;">
                <div class="clearfix" style="width: 80%; display: flex; justify-content: end; align-items: center;">
                    <!-- <div class="filter-input-container">
                <input type="text" id="filterInput" class="filter-input" onkeyup="filterList()"
                    placeholder="Filter by school year...">
                <span class="filter-icon"><i class="fas fa-search"></i></span>
            </div> -->
                    <div class="btn-container" style="margin-right: -100px;">
                        <button type="button" class="btn btn-primary btn-sm mb-2" id="export-btn" data-clipboard-target="#resultsTable" data-toggle="tooltip" data-placement="top" title="Copy data to clipboard">
                            <i class="fa-regular fa-copy" style="margin-right: 3px;"></i>
                            Copy
                        </button>
                    </div>
                </div>
                <div class="table-responsive" style="width: 100%">
                    <table id="resultsTable" class="table">
                        <thead>
                            <tr style="text-align: center;">
                                <th style='display: none;'>Year</th>
                                <th style="width: auto;">Title</th>
                                <th style="width: auto;">ID Number</th>
                                <th style="width: auto;">Proponents</th>
                                <th style="width: auto;">OR Number</th>
                                <th style="width: auto;">Adviser</th>
                                <th style="width: auto;">Panelists</th>
                                <th style="width: auto;">Data Analyst</th>
                                <th style="width: auto;">Language Editor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Connect to your database
                            $mysqli = mysqli_connect("localhost", "root", "", "thesis_mgmt");

                            // Check connection
                            if ($mysqli->connect_error) {
                                die("Connection failed: " . $mysqli->connect_error);
                            }

                            // Fetch data from the database
                            $sql = "SELECT * FROM thesis_student_panel_editor_vw";
                            $result = $mysqli->query($sql);

                            if ($result->num_rows > 0) {
                                // Initialize variables to keep track of category changes
                                $currentTitle = null;
                                $currentAdviser = null;
                                $currentPanels = null;
                                $currentEditor = null;
                                $rowCount = 0;

                                while ($row = $result->fetch_assoc()) {
                                    $thesisTitle = $row["Title"];
                                    $thesisProponent_ID = $row["UserId"];
                                    $thesisProponent = $row["StudentName"];
                                    $thesisAdviser = $row["Adviser"];
                                    $thesisPanels = empty($row["PanelMembers"]) ? 'No panelists selected.' : str_replace(";", "<br>", $row["PanelMembers"]);
                                    $thesis_rowSpan = $row["count"];
                                    $thesis_school_year = $row["SchoolYear"];
                                    $thesis_editor = $row["Editor"];
                                    $thesisProponent_IDNumber = $row["IDNumber"];

                                    // Check if the category has changed
                                    if ($currentTitle !== $thesisTitle) {
                                        // If it's not the first category, close the previous row
                                        if ($currentTitle !== null) {
                                            echo "</tr>";
                                        }
                                        // Start a new row for the category
                                        echo "<tr scope=row>";
                                        echo "<td style='display: none;'>$thesis_school_year</td>";
                                        // Output the title with rowspan
                                        echo "<td rowspan='$thesis_rowSpan'>$thesisTitle</td>";
                                        // Output the proponent without rowspan
                                        echo "<td>$thesisProponent_IDNumber</td>";
                                        // Output the proponent without rowspan
                                        echo "<td>$thesisProponent</td>";
                                        // Output the adviser with rowspan
                                        echo "<td rowspan='$thesis_rowSpan'></td>";
                                        // Output the adviser with rowspan
                                        echo "<td rowspan='$thesis_rowSpan'>$thesisAdviser</td>";
                                        // Output the adviser with rowspan
                                        echo "<td rowspan='$thesis_rowSpan'>$thesisPanels</td>";
                                        // Output the adviser with rowspan
                                        echo "<td rowspan='$thesis_rowSpan'></td>";
                                        // Output the adviser with rowspan
                                        echo "<td rowspan='$thesis_rowSpan'>$thesis_editor</td>";

                                        // Reset the row count for the new category
                                        $rowCount = 0;
                                        // Update the current category
                                        $currentTitle = $thesisTitle;
                                        $currentAdviser = $thesisAdviser;
                                        $currentPanels = $thesisPanels;
                                        $currentEditor = $thesis_editor;
                                    } else {
                                        // Output subsequent proponents in separate rows
                                        echo "<tr>";
                                        echo "<td>$thesisProponent_IDNumber</td>";
                                        echo "<td>$thesisProponent</td>";
                                        echo "</tr>";
                                    }
                                }
                            } else {
                                echo "<tr><td colspan='5'>No data found</td></tr>";
                            }
                            $mysqli->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php
        } else {
            echo "<div class='container'>
        <div id='thesisContainer' class='card w-100 mb-3'>
            <div class='card-body'>
                <div style='font-size:16px;'class='alert alert-danger' role='alert>
                    <span class='icon'><i style='font-size:18px;' class='fa-regular fa-circle-xmark'></i></span>
                    You don't have access to this page. Contact your research coordinator for help.
                </div>
            </div>
        </div>
        </div>";
        }
        ?>

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>
        <script type="text/javascript">
            // If you do not want to use jQuery you can use Pure JavaScript. See FAQ below
            $(document).ready(function() {
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
<?php
} else {
    header("Location: /thesis-mgmt/login.php");
} ?>