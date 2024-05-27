<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");
if (isset($_SESSION['username']) && isset($_SESSION['userid']))
{
    $selectedCourse = isset($_GET['course']) ? $_GET['course'] : 'All';
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Progress Chart</title>

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" href="/thesis-mgmt/css/styles.css">

        <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
        <style>
            .gauge-chart-container {
                margin-top: -20px;
            }

            .gauge-chart {
                width: 100%;
                height: 230px;
            }

            .loading-gauge {
                display: none;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }

            .loader {
                border: 7px solid #f3f3f3;
                border-top: 7px solid #3498db;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                animation: spin 1s linear infinite;
            }

            .overview-selects {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
                font-size: 12px;
            }

            .overview-selects>div:nth-child(2),
            .overview-selects>div:nth-child(3) {
                flex: 0 0 auto;
                margin-left: 10px;
                text-align: right;
            }

            .overview-selects>div:first-child {
                flex: 1;
                text-align: left;
            }

            select {
                padding: 5px;
                margin: 10px 0;
                border: 1px solid #ddd;
                border-radius: 5px;
            }

            .no-records-message {
                margin: 20px 0;
                padding: 20px;
                background-color: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
                border-radius: 5px;
                text-align: center;
            }

            .card-title {
                display: inline-block;
                vertical-align: top;
                width: 100%;
                white-space: normal;
                word-break: break-word;
            }

            .card-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                background-color: inherit;
            }

            .progress-container {
                display: inline-block;
                align-items: center;
                vertical-align: top;
                flex: 1;
                height: 75%;
                width: 75%;
            }

            .container {
                display: flex;
                align-items: flex-start;
            }

            .card {
                flex: 1;
                margin-right: 20px;
            }

            * {
                box-sizing: border-box;
            }

            .row-container {
                margin: 0;
                padding: 0;
            }
        </style>
    </head>

    <body class="content">
        <div>
            <h3 style="position:absolute;margin-top:20px;">Progress Chart</h3>
            <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/header.php"); ?>
            <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/sidebar.php"); ?>
            <hr>
        </div>
        <div class="overview-selects">
            <div>
                <label style="font-size: 14px;" for="course">Select Course:</label>
                <select id="course">
                    <option value="All" <?= $selectedCourse == 'All' ? 'selected' : '' ?>>All</option>
                    <option value="Architecture" <?= $selectedCourse == 'Architecture' ? 'selected' : '' ?>>Architecture
                    </option>
                    <option value="Civil Engineering" <?= $selectedCourse == 'Civil Engineering' ? 'selected' : '' ?>>Civil
                        Engineering</option>
                    <option value="Computer Engineering" <?= $selectedCourse == 'Computer Engineering' ? 'selected' : '' ?>>
                        Computer Engineering</option>
                    <option value="Electrical Engineering" <?= $selectedCourse == 'Electrical Engineering' ? 'selected' : '' ?>>Electrical Engineering</option>
                    <option value="Electronics Engineering" <?= $selectedCourse == 'Electronics Engineering' ? 'selected' : '' ?>>Electronics Engineering</option>
                    <option value="Information Technology" <?= $selectedCourse == 'Information Technology' ? 'selected' : '' ?>>Information Technology</option>
                    <option value="Computer Science" <?= $selectedCourse == 'Computer Science' ? 'selected' : '' ?>>Computer
                        Science</option>
                    <option value="Library and Information Science" <?= $selectedCourse == 'Library and Information Science' ? 'selected' : '' ?>>Library and Information Science</option>
                </select>
            </div>
        </div>

        <?php

        $sqlGetFiles = "SELECT * FROM thesis_groupedstudents_vw";
        if ($selectedCourse != 'All')
        {
            $sqlGetFiles .= " WHERE Course = '$selectedCourse'";
        }
        $sqlGetFiles .= " ORDER BY ThesisId";
        $result = mysqli_query($con, $sqlGetFiles);

        if (mysqli_num_rows($result) > 0)
        {
            while ($thesis = mysqli_fetch_assoc($result))
            {
                $thesis_id = $thesis["ThesisId"];
                $thesis_title = $thesis["Title"];
                $thesis_status = $thesis["Status"];
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
                $progressNotStarted = $progress["NotStarted"]; ?>
                <div class="container mb-3">
                    <div class="container" style="display: flex;">
                        <div class="row-container" style="display: flex; flex-wrap: wrap; align-items: stretch;"></div>
                        <div id="thesisContainer" class="card mb-2" style="flex: 0 0 65%;">
                            <div class="card-body">
                                <div class="card-header mb-3">
                                    <h5 class="card-title"><?php echo $thesis_title; ?>
                                        <?php if ($thesis_status == 'Completed')
                                        { ?>
                                            <span class="badge badge-success"
                                                style="margin-left: 15px; background-color: green; font-style: oblique; font-family: 'Segoe UI'; font-size: 20px;">Completed</span>
                                        <?php } ?>
                                    </h5>
                                </div>
                                <h6 class="thesis-text-color mb-3">Authors:
                                    <?php foreach ($authors_arr as $author)
                                    { ?>
                                        <span class="badge text-bg-secondary"><?php echo $author; ?></span>
                                    <?php } ?>
                                </h6>

                                <?php if ($_SESSION['role'] == 'Research Coordinator')
                                {
                                    $con->next_result();
                                    $getFiles_Select = "CALL getUploadedFileCount($thesis_id);";
                                    $getFiles_Result = mysqli_query($con, $getFiles_Select);
                                    $files = mysqli_fetch_assoc($getFiles_Result);
                                    $fileCount = $files["FileCount"];
                                    ?>
                                    <?php if ($fileCount > 0)
                                    { ?>
                                        <a href='/thesis-mgmt/php/download-thesis.php?page=thesis&thesisId=<?php echo $thesis_id; ?>'
                                            class='btn btn-primary btn-sm mb-3'><i style='margin-right:3px;'
                                                class='fa-regular fa-circle-down'></i>Download</a><br>
                                    <?php } else
                                    { ?>
                                        <a href='/thesis-mgmt/php/download-thesis.php?page=thesis&thesisId=<?php echo $thesis_id; ?>'
                                            class='btn btn-primary disabledDownload btn-sm mb-3'><i style='margin-right:3px;'
                                                class='fa-regular fa-circle-down'></i>Download</a><br>
                                    <?php } ?>
                                <?php } else
                                { ?>
                                    <div style='margin-top: 35px;'></div>
                                <?php } ?>

                                <span class='thesis-text-color'>Last Updated Date: <?php echo $formatted_date; ?> </span>
                            </div>
                        </div>

                        <div class="" style="flex: 1;">
                            <div class="card-body">
                                <div class='progress-container'>
                                    <div class="gauge-chart-container">
                                        <div class="gauge-chart" id="gauge-chart_<?php echo $thesis_id ?>"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        function setProgress_<?php echo $thesis_id; ?>() {
                            var gaugeContainer = document.getElementById('gauge-chart_<?php echo $thesis_id; ?>');

                            var myChart = echarts.init(gaugeContainer);
                            var option;

                            const gaugeData = [
                                {
                                    value: <?php echo $progressCompleted; ?>,
                                    name: 'Completed',
                                    title: {
                                        offsetCenter: ['-83%', '30%'],
                                        fontSize: 12
                                    },
                                    detail: {
                                        offsetCenter: ['-83%', '48%'],
                                        width: 20,
                                        height: 10,
                                        fontSize: 11,
                                        color: '#fff',
                                        backgroundColor: '#91cc75',
                                        borderRadius: 3,
                                        formatter: '{value}'
                                    }
                                },
                                {
                                    value: <?php echo $progressInProgress; ?>,
                                    name: 'In Progress',
                                    title: {
                                        offsetCenter: ['0%', '30%'],
                                        fontSize: 12
                                    },
                                    detail: {
                                        offsetCenter: ['0%', '48%'],
                                        width: 20,
                                        height: 12,
                                        fontSize: 11,
                                        color: '#fff',
                                        backgroundColor: '#fac858',
                                        borderRadius: 3,
                                        formatter: '{value}'
                                    }
                                },
                                {
                                    value: <?php echo $progressNotStarted; ?>,
                                    name: 'Not Started',
                                    title: {
                                        offsetCenter: ['83%', '30%'],
                                        fontSize: 12
                                    },
                                    detail: {
                                        offsetCenter: ['83%', '48%'],
                                        width: 20,
                                        height: 12,
                                        fontSize: 11,
                                        color: '#fff',
                                        backgroundColor: '#ee6666',
                                        borderRadius: 3,
                                        formatter: '{value}'
                                    }
                                }
                            ];

                            // Set options for gauge chart
                            option = {
                                color: ['#91cc75', '#fac858', '#ee6666'],
                                series: [
                                    {
                                        type: 'gauge',
                                        anchor: {
                                            show: true,
                                            showAbove: false,
                                            size: 12,
                                            itemStyle: {
                                                color: '#FAC858'
                                            }
                                        },
                                        startAngle: 180,
                                        endAngle: 0,
                                        min: 0,
                                        max: <?php echo $progressTotal ?>,
                                        splitNumber: 4,
                                        progress: {
                                            show: true,
                                            roundCap: true,
                                            width: 18
                                        },
                                        pointer: {
                                            icon: 'path://M2090.36389,615.30999 L2090.36389,615.30999 C2091.48372,615.30999 2092.40383,616.194028 2092.44859,617.312956 L2096.90698,728.755929 C2097.05155,732.369577 2094.2393,735.416212 2090.62566,735.56078 C2090.53845,735.564269 2090.45117,735.566014 2090.36389,735.566014 L2090.36389,735.566014 C2086.74736,735.566014 2083.81557,732.63423 2083.81557,729.017692 C2083.81557,728.930412 2083.81732,728.84314 2083.82081,728.755929 L2088.2792,617.312956 C2088.32396,616.194028 2089.24407,615.30999 2090.36389,615.30999 Z',
                                            length: '65%',
                                            width: 5,
                                            offsetCenter: [0, '5%']
                                        },
                                        axisLine: {
                                            roundCap: true,
                                            lineStyle: {
                                                width: 18
                                            }
                                        },
                                        axisTick: {
                                            splitNumber: 2,
                                            lineStyle: {
                                                width: 2,
                                                color: '#999'
                                            }
                                        },
                                        splitLine: {
                                            length: 12,
                                            lineStyle: {
                                                width: 3,
                                                color: '#999'
                                            }
                                        },
                                        axisLabel: {
                                            distance: 20,
                                            color: '#999',
                                            fontSize: 12
                                        },
                                        title: {
                                            fontSize: 12
                                        },
                                        detail: {
                                            width: 25,
                                            height: 12,
                                            fontSize: 14,
                                            color: '#fff',
                                            backgroundColor: 'inherit',
                                            borderRadius: 3,
                                            formatter: '{value}'
                                        },
                                        data: gaugeData
                                    }
                                ]
                            };

                            // Set options to chart instance
                            myChart.setOption(option);
                        }

                        setProgress_<?php echo $thesis_id; ?>();
                    </script>
                </div>
                </div>
                <?php
                $con->next_result();
            }
        } else
        {
            echo "<div class='no-records-message'>No records found for the selected course.</div>";
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

        <script>
            document.getElementById('course').addEventListener('change', function () {
                var selectedCourse = this.value;
                window.location.href = '?course=' + selectedCourse;
            });
        </script>

        <script>
            window.addEventListener('DOMContentLoaded', (event) => {
                // Get all rows of cards
                const rows = document.querySelectorAll('.row-container');

                console.log("Number of rows found:", rows.length);

                // Loop through each row
                rows.forEach((row, index) => {
                    console.log("Processing row:", index + 1);
                    // Get the first card in the row
                    const firstCard = row.querySelector('.card:first-of-type');

                    // If a first card is found in the row
                    if (firstCard) {
                        console.log("First card found in row:", index + 1);
                        // Get the height of the first card
                        const firstCardHeight = firstCard.offsetHeight;

                        console.log("Height of first card in row", index + 1, ":", firstCardHeight);

                        // Set the height of the progress container in the row to match the height of the first card
                        const progressContainers = row.querySelectorAll('.progress-container');
                        progressContainers.forEach(container => {
                            container.style.height = `${firstCardHeight}px`;
                            console.log("Height of progress container in row", index + 1, "adjusted to:", firstCardHeight);
                        });
                    } else {
                        console.log("No card found in row:", index + 1);
                    }
                });
            });
        </script>



    </body>

    </html>

<?php } else
{
    header("Location: /thesis-mgmt/login.php");
} ?>