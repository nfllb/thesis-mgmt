<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Files in Folder</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

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

        .filter-input {
            margin-bottom: 10px;
        }

        .modal-backdrop.show {
            opacity: 0.5;
            /* Adjust opacity as needed */
        }
    </style>
</head>

<body>
    <h1>Files in Folder</h1>
    <input type="text" id="filterInput" class="filter-input" onkeyup="filterFiles()"
        placeholder="Filter by file name...">
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
                    echo "<td><a class='file-link' href='$filePath' target='_blank'>$file</a></td>";
                    echo "<td>$fileType</td>";
                    echo "<td>$fileSize</td>";
                    echo "<td><button class='btn btn-primary generate-button' data-toggle='modal' data-target='#exampleModal' data-file='$file'>Generate</button></td>";
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Dynamic options will be inserted here -->
                    <div id="databaseOptions"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="generateDocument()">Generate
                        Document</button>
                </div>

                <!-- Loading Icon -->
                <div id="loadingIcon" class="text-center"
                    style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1050;">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div>Loading...</div>
                </div>
            </div>
        </div>
    </div>


    <!-- Include Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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