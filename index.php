<?php
session_start();
if (isset($_SESSION['username']) && isset($_SESSION['userid']))
{ ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
            integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
        <link rel="stylesheet" href="./css/index.css">

        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Thesis Management</title>
    </head>

    <body>
        <div class="card-body text-center">
            <h5 class="card-title">
                <?= $_SESSION['username'] ?>
            </h5>
            <a href="logout.php" class="btn btn-dark">Logout</a>
        </div>
        <div class="wrapper">
            <div class="section">
                <div class="top_navbar">
                    <div class="hamburger">
                        <a href="#">
                            <i class="fas fa-bars"></i>
                        </a>
                    </div>
                </div>

            </div>
            <div class="sidebar">
                <ul>
                    <div>
                        <a class="newdoc">
                            <span class="icon"><i class="fa-solid fa-circle-plus"></i></span>
                            <span class="item">Add new document</span>
                        </a>
                    </div>
                    <br>
                    <br>
                    <li>
                        <a href="#">
                            <span class="icon"><i class="fas fa-desktop"></i></span>
                            <span class="item">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <span class="icon"><i class="fas fa-user-friends"></i></span>
                            <span class="item">Thesis</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <span class="icon"><i class="fa-solid fa-clock"></i></span>
                            <span class="item">Itinerary</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <span class="icon"><i class="fa-solid fa-users"></i></span>
                            <span class="item">Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <span class="icon"><i class="fas fa-chart-line"></i></span>
                            <span class="item">Forms</span>
                        </a>
                    </li>
                    <li>
                        <a href="./reports.php">
                            <span class="icon"><i class="fas fa-user-shield"></i></span>
                            <span class="item">Reports</span>
                        </a>
                    </li>
                </ul>
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
    </body>

    </html>
<?php } else
{
    header("Location: login.php");
} ?>