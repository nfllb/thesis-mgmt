<?php
session_start();
if (isset($_SESSION['username']) && isset($_SESSION['userid']))
{ ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Forms</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
        <link rel="stylesheet" href="/thesis-mgmt/css/styles.css">

        <style>
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th,
            td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #f2f2f2;
            }

            tr:hover {
                background-color: #f5f5f5;
            }

            .file-link {
                color: blue;
                text-decoration: underline;
            }

            /* Custom styles for the input filter */
            .filter-input-container {
                position: relative;
                width: 250px;
                float: right;
                margin-bottom: 10px;
            }

            .filter-input {
                width: 100%;
                padding: 3px;
                /* Adjust right padding for icon */
                border: 1px solid #ccc;
                border-radius: 5px;
                font-size: 13px;
                outline: none;
                transition: border-color 0.3s ease-in-out;
            }

            .filter-icon {
                position: absolute;
                top: 50%;
                right: 10px;
                transform: translateY(-50%);
                font-size: 20px;
                color: #999;
                pointer-events: none;
                /* Prevent icon from being clickable */
            }

            .filter-input:focus {
                border-color: #007bff;
            }

            .modal-backdrop.show {
                opacity: 0.5;
                /* Adjust opacity as needed */
            }
        </style>
    </head>

    <body class="content">
        <div>
            <h3 style="position:absolute;margin-top:20px;">Forms</h3>
            <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/header.php"); ?>
            <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/sidebar.php"); ?>
            <hr>
        </div>

        <!-- Input filter -->
        <div class="filter-input-container">
            <input type="text" id="filterInput" class="filter-input" onkeyup="filterFiles()"
                placeholder="Filter by file name...">
            <span class="filter-icon"><i class="fas fa-search"></i></span>
        </div>
        <table id="fileTable">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>File Type</th>
                    <th>File Size (Bytes)</th>
                    <th>Generate</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Specify the directory path
                $directory = "./../files/forms/";

                // Get all files in the directory
                $files = array_diff(scandir($directory), array('.', '..'));

                // Output the list of files (excluding directories)
                foreach ($files as $file)
                {
                    $filePath = $directory . $file;
                    if (is_file($filePath))
                    {
                        $fileSize = filesize($filePath);
                        $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
                        echo "<tr>";
                        echo "<td>$file</td>";
                        echo "<td>$fileType</td>";
                        echo "<td>$fileSize</td>";
                        echo "<td><button class='btn btn-sm btn-primary generate-button' data-toggle='modal' data-target='#exampleModal' data-file='$file'>Generate</button></td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel" data-filename="">Choose Thesis</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Dynamic options will be inserted here -->
                        <div id="databaseOptions"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-sm btn-primary" onclick="generateDocument()">Generate
                            Document</button>
                    </div>

                    <!-- Loading Icon -->
                    <div id="loadingIcon" class="text-center"
                        style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div>Loading...</div>
                    </div>
                </div>
            </div>
        </div>





        <!-- Include Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
            crossorigin="anonymous"></script>

        <script>
            function filterFiles() {
                var input, filter, table, tr, td, i, txtValue;
                input = document.getElementById("filterInput");
                filter = input.value.toUpperCase();
                table = document.getElementById("fileTable");
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

            function generateDocument() {
                // Show loading icon
                $('#loadingIcon').show();
                // Get the selected data from the form

                var selectElement = document.getElementById("selectedThesis");
                var selectedIndex = selectElement.selectedIndex;
                var selectedOption = selectElement.options[selectedIndex];
                var selectedThesisTitle = selectedOption.text;
                var selectedThesisId = selectedOption.value;

                // Get the file name
                var fileName = $('.modal-title').data('filename');

                // Send AJAX request to PHP script
                $.ajax({
                    type: 'POST',
                    url: 'update-form.php',
                    data: { fileName: fileName, selectedThesisId: selectedThesisId, thesisTitle: selectedThesisTitle },
                    success: function (data) {
                        //alert(data);
                        // Trigger download of the updated document
                        window.location.href = 'download-form.php?file=' + data;

                        // Hide loading icon once download is initiated
                        $('#loadingIcon').hide();

                        // Close the modal after a short delay (adjust as needed)
                        setTimeout(function () {
                            $('#exampleModal').modal('hide');
                        }, 2000); // Close after 2 seconds


                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        // Hide loading icon once download is initiated
                        $('#loadingIcon').hide();
                    }
                });
            }
            // Fetch options from the database and populate the modal form
            $(document).ready(function () {
                $('.generate-button').click(function () {
                    var fileName = $(this).data('file');
                    $('.modal-title').data('filename', fileName); // Set the file name in modal title
                    $('#exampleModal').modal('show');

                    // Fetch data from the database and populate options in the modal form
                    $.ajax({
                        type: 'GET',
                        url: 'fetch-thesis-options.php',
                        success: function (data) {
                            $('#databaseOptions').html(data); // Populate options in modal form
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                });
            });

        </script>
    </body>

    </html>
<?php } else
{
    header("Location: /thesis-mgmt/login.php");
} ?>