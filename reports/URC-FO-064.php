<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");
if (isset($_SESSION['username']) && isset($_SESSION['userid']))
{ ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reports</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
        <link rel="stylesheet" href="/thesis-mgmt/css/styles.css">

    </head>

    <body class="content">
        <div>
            <h3 style="position:absolute;margin-top:20px;">Report / URC-FO-064</h3>
            <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/header.php"); ?>
            <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/sidebar.php"); ?>
            <hr>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>ID Number</th>
                    <th>Proponents</th>
                    <th>OR Number</th>
                    <th>Adviser</th>
                    <th>Panelists</th>
                </tr>
            </thead>
            <tbody>
                <!-- Add your data rows here -->
                <tr>
                    <!-- Merged cell for Title -->
                    <td rowspan="4">Title 1</td>
                    <td>ID001</td>
                    <td>Proponent 1, Proponent 2</td>
                    <!-- Merged cell for OR Number -->
                    <td rowspan="4">OR001</td>
                    <!-- Merged cell for Adviser -->
                    <td rowspan="4">Adviser 1</td>
                    <td>Panelist 1, Panelist 2</td>
                </tr>
                <tr>
                    <td>ID002</td>
                    <td>Proponent 3, Proponent 4</td>
                    <td>Panelist 3, Panelist 4</td>
                </tr>
                <tr>
                    <td>ID003</td>
                    <td>Proponent 5, Proponent 6</td>
                    <td>Panelist 5, Panelist 6</td>
                </tr>
                <tr>
                    <td>ID004</td>
                    <td>Proponent 7, Proponent 8</td>
                    <td>Panelist 7, Panelist 8</td>
                </tr>
                <!-- Add more rows as needed -->
            </tbody>
        </table>

        <!-- Include Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
            crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>

        <script>
            var clipboard = new ClipboardJS('.btn');
        </script>

        <script type="text/javascript">
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