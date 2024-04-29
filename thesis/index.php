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
        <title>Thesis</title>

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" href="/thesis-mgmt/css/styles.css">
    </head>

    <body class="content">
        <div>
            <h3 style="position:absolute;margin-top:20px;">Thesis</h3>
            <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/header.php"); ?>
            <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/sidebar.php"); ?>
            <hr>
        </div>

        <?php

        $sqlGetFiles = "SELECT * FROM thesis_groupedstudents_vw  ORDER BY ThesisId";
        $result = mysqli_query($con, $sqlGetFiles);

        if (mysqli_num_rows($result) > 0)
        {
            while ($thesis = mysqli_fetch_assoc($result))
            {
                $thesis_id = $thesis["ThesisId"];
                $thesis_title = $thesis["Title"];
                $thesis_lastModDate = $thesis["LastModifiedDate"];
                $formatted_date = date("F j, Y", strtotime($thesis_lastModDate));
                $thesis_authors = $thesis["Authors"];
                $authors_arr = explode(',', $thesis_authors);

                $progressQuery = "CALL CalculateThesisProgress($thesis_id);";
                $resultProgress = mysqli_query($con, $progressQuery);
                $progress = mysqli_fetch_assoc($resultProgress);

                $progressPercentage = $progress["Percentage"];
                $progressTotal = $progress["Total"];
                $progressCompleted = $progress["Completed"];
                $progressInProgress = $progress["InProgress"];
                $progressNotStarted = $progress["NotStarted"];

                echo "<div class='container'>
                        <div id='thesisContainer' class='card w-60 mb-3' style='float:left;'>
                            <div class='card-body'>
                                <h5 class='card-title'>$thesis_title</h5>
                                <h6 class='thesis-text-color'>Authors: ";
                foreach ($authors_arr as $author)
                {
                    echo "<span class='badge text-bg-secondary'>$author</span>";
                }
                echo "</h6>";

                if ($_SESSION['role'] == 'Research Coordinator')
                {
                    echo "<a href='/thesis-mgmt/php/download-thesis.php?thesisId=$thesis_id' class='btn btn-primary btn-sm'><i style='margin-right:3px;' class='fa-regular fa-circle-down'></i>Download</a><br>";
                } else
                {
                    echo "<div style='margin-top: 35px;'></div>";
                }
                echo "<span class='thesis-text-color'>Last Updated Date: $formatted_date </span>
                        </div>
                    </div>";
                ?>

                <div id='thesisProgressContainer_<?php echo $thesis_id ?>' style='float:left; margin-left: 20px;'
                    class='progress-container card-body'>
                    <svg class='progress-circle' viewBox='0 0 100 50'>
                        <circle class='progress-background' cx='50' cy='50' r='40'></circle>
                        <circle class='progress-bar' cx='50' cy='50' r='40' transform='rotate(90 50 50)'></circle>
                        <text class='progress-text' x='50' y='50'>0%</text>
                    </svg>
                    <div class="labels">
                        <!-- Label for total parts -->
                        <text class="label-total-parts" x="50" y="10" text-anchor="middle" fill="#333">Total Parts: <tspan
                                class="total-parts-value"><?php echo $progressTotal; ?></tspan></text>

                        <!-- Label for completed tasks -->
                        <text class="label-completed" x="25" y="30" fill="green">Completed: <tspan class="completed-value">
                                <?php echo $progressCompleted; ?>
                            </tspan>
                        </text>
                        <br>
                        <!-- Label for tasks in progress -->
                        <text class="label-in-progress" x="25" y="45" fill="yellow">On Going: <tspan class="in-progress-value">
                                <?php echo $progressInProgress; ?>
                            </tspan></text>

                        <!-- Label for tasks not started -->
                        <text class="label-not-started" x="25" y="60" fill="gray">Not Started: <tspan class="not-started-value">
                                <?php echo $progressNotStarted; ?>
                            </tspan></text>
                    </div>

                    <script>
                        var progressPercentage_<?php echo $thesis_id; ?> = <?php echo $progressPercentage; ?>;
                        console.log(progressPercentage_<?php echo $thesis_id; ?>);

                        function setProgress_<?php echo $thesis_id; ?>(progress) {
                            const circle = document.querySelector('#thesisProgressContainer_<?php echo $thesis_id; ?> .progress-bar');
                            const text = document.querySelector('#thesisProgressContainer_<?php echo $thesis_id; ?> .progress-text');

                            const radius = circle.getAttribute('r');
                            const circumference = Math.PI * 2 * radius; // Full circumference

                            const offset = circumference - (progress / 100) * circumference;

                            circle.style.strokeDasharray = `${circumference} ${circumference}`;
                            circle.style.strokeDashoffset = offset;

                            text.textContent = `${progress}%`;
                        }


                        setProgress_<?php echo $thesis_id; ?>(progressPercentage_<?php echo $thesis_id; ?>);
                    </script>
                </div>
                </div>
                </>
                <?php
                $con->next_result();
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
    </body>

    </html>

<?php } else
{
    header("Location: /thesis-mgmt/login.php");
} ?>