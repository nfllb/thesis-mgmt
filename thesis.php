<?php
session_start();
include 'dbconnect.php';
// if (isset($_SESSION['username']) && isset($_SESSION['userid']))
// { 

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thesis</title>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/styles.css">
</head>

<body>
    <div>
        <h3>Thesis</h3>
        <!-- ?php echo $_SESSION['username']; ?> -->
    </div>
    <hr>

    <?php

    $sqlGetFiles = "SELECT * FROM thesis_groupedstudents_vw";
    $result = mysqli_query($con, $sqlGetFiles);

    if (mysqli_num_rows($result) > 0)
    {
        while ($thesis = mysqli_fetch_assoc($result))
        {
            $thesis_title = $thesis["Title"];
            $thesis_authors = $thesis["Authors"];
            $authors_arr = explode(',', $thesis_authors);
            echo "<div class='container'>
                    <div id='thesisContainer' class='card w-75 mb-3'>
                        <div class='card-body'>
                            <h5 class='card-title'>$thesis_title</h5>
                            <h6 class='thesis-text-color'>Authors: ";
            foreach ($authors_arr as $author)
            {
                echo "<span class='badge text-bg-secondary'>$author</span>";
            }
            echo "</h6>
                            <a href='./php/download-thesis.php' class='btn btn-primary btn-sm'><i style='margin-right:3px;' class='fa-regular fa-circle-down'></i>Download</a>
                            <br><span class='thesis-text-color'>Last Updated Date: March 10, 2024 </span>
                        </div>
                    </div>
                    </div>";
        }
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
<!-- ?php } else
{
    header("Location: login.php");
} ?> -->