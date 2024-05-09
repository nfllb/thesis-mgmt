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
                background-color: #fff;
                border-radius: 8px;
                overflow: hidden;
            }

            th,
            td {
                border: 1px solid var(--primary-bg-color);
                padding: 12px;
                text-align: left;
            }

            th {
                background-color: #F4ECE3;
                color: var(--primary-text-color);
                font-weight: bold;
                font-size: 14px !important;
                text-align: center;
            }

            tr:nth-child(even) {
                background-color: #F9F7F4;
            }

            tr:hover {
                background-color: #FCEBD1;
            }

            .file-link {
                color: blue;
                text-decoration: underline;
            }

            .filter-input-container {
                position: relative;
                width: 250px;
                float: right;
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

            .modal-backdrop.show {
                opacity: 0.5;
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
                    <th></th>
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
                        <button id="closeModal" type="button" class="btn btn-sm btn-secondary"
                            data-bs-dismiss="modal">Close</button>
                        <button id="generateDoc" type="button" class="btn btn-sm btn-primary"
                            onclick="generateDocument()">Generate
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
                $('#generateDoc').addClass('disabled');
                $('#closeModal').addClass('disabled');

                // Get the selected data from the form

                var selectElement = document.getElementById("selectedThesis");
                var selectedIndex = selectElement.selectedIndex;
                var selectedOption = selectElement.options[selectedIndex];
                var selectedThesisTitle = selectedOption.text;
                var selectedThesisId = selectedOption.value;
                if (selectedThesisId == 'none') {
                    alert('Please choose a thesis.');
                    $('#loadingIcon').hide();
                    $('#generateDoc').removeClass('disabled');
                    $('#closeModal').removeClass('disabled');
                } else {
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

                            // Close the modal after a short delay (adjust as needed)
                            setTimeout(function () {
                                showToast(
                                    "Download successful",
                                    "success"
                                );
                            }, 3000);

                            $('#exampleModal').modal('hide');

                            // Hide loading icon once download is initiated
                            $('#loadingIcon').hide();
                            $('#generateDoc').removeClass('disabled');
                            $('#closeModal').removeClass('disabled');

                        },
                        error: function (xhr, status, error) {
                            showToast(
                                "Error downloading file",
                                "error"
                            );
                            console.error(xhr.responseText);
                            // Hide loading icon once download is initiated
                            $('#loadingIcon').hide();
                            $('#generateDoc').removeClass('disabled');
                            $('#closeModal').removeClass('disabled');
                        }
                    });
                }
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

            function showToast(message, type) {
                // Create toaster element
                var toaster = document.createElement("div");
                toaster.className = "toaster " + type;
                toaster.textContent = message;

                // Set position and size
                toaster.style.position = "fixed";
                toaster.style.bottom = "20px";
                toaster.style.right = "20px";
                toaster.style.width = "300px";
                toaster.style.padding = "15px";
                toaster.style.borderRadius = "10px";
                toaster.style.zIndex = "9999";

                // Append toaster to the body
                document.body.appendChild(toaster);

                // Display the toaster
                toaster.style.display = "block";

                // Fade out and remove the toaster after 3 seconds
                setTimeout(function () {
                    toaster.style.opacity = "0";
                    setTimeout(function () {
                        document.body.removeChild(toaster);
                    }, 3000);
                }, 3000);
            }
        </script>
    </body>

    </html>
<?php } else
{
    header("Location: /thesis-mgmt/login.php");
} ?>