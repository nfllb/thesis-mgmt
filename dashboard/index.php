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
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Thesis Dashboard</title>

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/thesis-mgmt/css/styles.css">

        <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
        <style>
            h1 {
                margin-bottom: 20px;
                color: #333;
            }

            .cards-container {
                display: flex;
                justify-content: space-between;
            }

            .card {
                background-color: #f5eeea;
                border: 1px solid #d3d3d3;
                border-radius: 10px;
                padding: 20px;
                margin: 20px;
                display: inline-block;
                width: calc(20% - 20px);
                box-sizing: border-box;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                font-size: 14px;
                position: relative;
            }

            .card-icon-container {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 20px;
                margin-bottom: 10px;

            }

            .card-icon {
                color: #FFF;
                font-size: 21px;
            }

            .card-value {
                color: #000;
                font-size: 24px;
                font-weight: bold;
                margin: 5px 0;
                text-align: left;
            }

            .card-value-label {
                color: #585858;
                font-size: 12px;
                margin-bottom: 5px;
                text-align: left;
            }

            .large-card {
                width: calc(60% - 20px);
                margin-top: 20px;
                margin-left: 0;
                float: left;
                position: relative;
                background-color: #f5eeea;
                padding: 20px;
                border-radius: 10px;
            }

            .gauge-card-container {
                width: 370px;
                margin-top: 20px;
                margin-left: 0;
                float: right;
                position: relative;
                background-color: #f5eeea;
                padding: 20px;
                border-radius: 10px;
                display: flex;
                flex-direction: column;
            }

            .label-and-filter {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .progress-label {
                flex: 1;
            }

            .filter-container {
                margin-left: auto;
                margin-left: 20px;
            }

            .gauge-chart-container {
                margin-top: -20px;
            }

            #gauge-chart {
                width: 100%;
                height: 310px;
                margin: auto;
            }

            select {
                padding: 5px;
                //font-size: 16px;
                margin: 10px 0;
                border: 1px solid #ddd;
                border-radius: 5px;
            }

            .loading {
                display: none;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }

            .loading-card {
                display: none;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
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

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }

            @media screen and (max-width: 768px) {
                .card {
                    width: calc(50% - 20px);
                }
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
        </style>
    </head>

    <body class="content">
        <div class="container">
            <div>
                <h3 style="position:absolute;margin-top:20px;">Dashboard</h3>
                <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/header.php"); ?>
                <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/sidebar.php"); ?>
                <hr>
            </div>
            <div class="overview-selects">
                <div><span style="font-size: 24px; font-weight: bold">Overview</span></div>
                <div>
                    <label style="font-size: 14px;" for="department">Select Department:</label>
                    <select id="department">
                        <option value="Engineering">SEAIT</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 14px;" for="course">Select Course:</label>
                    <select id="course">
                        <option value="All">All</option>
                        <option value="Architecture">Architecture</option>
                        <option value="Civil Engineering">Civil Engineering</option>
                        <option value="Computer Engineering">Computer Engineering</option>
                        <option value="Electrical Engineering">Electrical Engineering</option>
                        <option value="Electronics Engineering">Electronics Engineering</option>
                        <option value="Information Technology">Information Technology</option>
                        <option value="Computer Science">Computer Science</option>
                        <option value="Library and Information Science">Library and Information Science</option>
                    </select>
                </div>
            </div>

            <div class="cards-container">
                <div class="card">
                    <div style="background-color: blueviolet;" class="card-icon-container">
                        <i class="fa-solid fa-chart-simple card-icon"></i>
                    </div>
                    <div>
                        <p class="card-value-label" id="total-thesis-label">Total Department Thesis</p>
                        <p class="card-value" id="total-theses">0</p>
                        <div class="loading-card">
                            <div class="loader"></div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div style="background-color: orangered;" class="card-icon-container">
                        <i class="fa-solid fa-suitcase card-icon"></i>
                    </div>
                    <div>
                        <p class="card-value-label" id="total-course-label">Total Course Thesis</p>
                        <p class="card-value" id="theses-in-progress">0</p>
                        <div class="loading-card">
                            <div class="loader"></div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div style="background-color: dodgerblue;" class="card-icon-container">
                        <i class="fa-solid fa-clock card-icon"></i>
                    </div>
                    <div>
                        <p class="card-value-label" id="total-pending-label">Pending Defense</p>
                        <p class="card-value" id="completed-theses">0</p>
                        <div class="loading-card">
                            <div class="loader"></div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div style="background-color: orange;" class="card-icon-container">
                        <i class="fa-solid fa-user card-icon"></i>
                    </div>
                    <div>
                        <p class="card-value-label" id="total-users-label">Online Users</p>
                        <p class="card-value" id="theses-pending-review">0</p>
                        <div class="loading-card">
                            <div class="loader"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="large-card">
                <div style="display: flex; align-items: center;">
                    <h6 style="font-weight: bold; margin-right: 80px;">Thesis Progress Summary</h6>

                    <label for="date-filter">Date Filter:</label>
                    <input style="margin-right: 10px;" type="date" id="date-filter">

                    <label for="status-filter">Status Filter:</label>
                    <select id="status-filter">
                        <option value="all">All Statuses</option>
                        <option value="Not Started">Not Started</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <table id="all-thesis-records">
                    <tr>
                        <th>Title</th>
                        <th>Instructor</th>
                        <th>Last Updated</th>
                        <th>Status</th>
                        <th>Progress</th>
                    </tr>
                    <?php

                    $dashboardTable_query = "SELECT * FROM getdashboardtable";
                    $dashboardTable_result = mysqli_query($con, $dashboardTable_query);

                    if ($dashboardTable_result && mysqli_num_rows($dashboardTable_result) > 0)
                    {
                        while ($thesis = mysqli_fetch_assoc($dashboardTable_result))
                        {
                            $Title = $thesis['Title'];
                            $Instructor = $thesis['Name'];
                            $LastMod = $thesis['LastModifiedDate'];
                            $Status = $thesis['Status'];
                            $Percent = $thesis['Percent'];

                            echo "<tr>
                                    <td>$Title</td>
                                    <td>$Instructor</td>
                                    <td>$LastMod</td>
                                    <td>$Status</td>
                                    <td>$Percent " . " % </td>
                                </tr>";
                        }
                    } else
                    {
                        echo "<div>No Data Available</div>";
                    }

                    ?>
                </table>
                <!-- <div class="loading">
                    <div class="loader"></div>
                </div> -->
            </div>

            <div class="gauge-card-container">
                <div class="label-and-filter">
                    <div style="font-weight: bold; font-size: 15px;" class="progress-label">Overall Progress</div>
                    <div class="filter-container">
                        <!-- Your filter elements here -->
                        <label for="part-filter">Filter:</label>
                        <select id="part-filter">
                            <option value="All">All</option>
                            <option value="1">Part 1</option>
                            <option value="2">Part 2</option>
                        </select>
                    </div>
                </div>
                <div class="gauge-chart-container">
                    <div id="gauge-chart"></div>
                    <div class="loading-gauge">
                        <div class="loader"></div>
                    </div>
                </div>
            </div>
        </div>


        </div>

        <script>
            // Function to fetch data from the database using AJAX
            function fetchDataFromDatabase() {
                // Show loading icon for each card
                var loadingIcons = document.querySelectorAll('.loading-card');
                loadingIcons.forEach(function (loadingIcon) {
                    loadingIcon.style.display = 'block';
                });

                setTimeout(function () {
                    // Get selected department and course values
                    var department = document.getElementById('department').value;
                    var course = document.getElementById('course').value;

                    // Make an AJAX request to a PHP script that will fetch data from the database
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === XMLHttpRequest.DONE) {
                            if (xhr.status === 200) {
                                // Parse the JSON response and update the displayed data
                                var responseData = JSON.parse(xhr.responseText);
                                //alert(JSON.stringify(responseData));
                                document.getElementById('total-theses').textContent = responseData.totalDepartment;
                                document.getElementById('theses-in-progress').textContent = responseData.totalCourse;
                                document.getElementById('completed-theses').textContent = responseData.pendingDefense;
                                document.getElementById('theses-pending-review').textContent = responseData.activeUser;
                            } else {
                                // Handle error
                                console.error('Error fetching data from the server:', xhr.status);
                            }
                        }
                        // Hide loading icon for each card
                        loadingIcons.forEach(function (loadingIcon) {
                            loadingIcon.style.display = 'none';
                        });
                    };
                    xhr.open('GET', '/thesis-mgmt/dashboard/dashboard-details.php?department=' + encodeURIComponent(department) + '&course=' + encodeURIComponent(course));
                    xhr.send();
                }, 1000);
            }

            // Event listeners for filter changes
            document.getElementById('department').addEventListener('change', fetchDataFromDatabase);
            document.getElementById('course').addEventListener('change', fetchDataFromDatabase);

            // Call fetchDataFromDatabase on initial load
            fetchDataFromDatabase();


            // Filter function for the table
            function filterTable() {
                var selectedDate = document.getElementById('date-filter').value;
                var statusFilter = document.getElementById('status-filter').value;
                var rows = document.getElementById('all-thesis-records').getElementsByTagName('tr');

                for (var i = 1; i < rows.length; i++) {
                    var row = rows[i];
                    var dateCell = row.getElementsByTagName('td')[2].textContent;
                    var statusCell = row.getElementsByTagName('td')[3].textContent;

                    // Check if date matches the filter
                    if (selectedDate) {
                        var rowDate = new Date(dateCell);
                        var selectedDateObj = new Date(selectedDate);
                        if (rowDate.toDateString() !== selectedDateObj.toDateString()) {
                            row.style.display = 'none';
                            continue;
                        }
                    }

                    // Check if status matches the filter
                    if (statusFilter !== 'all' && statusCell.toLowerCase() !== statusFilter.toLowerCase()) {
                        row.style.display = 'none';
                        continue;
                    }

                    row.style.display = '';
                }
            }

            // Event listeners for filter changes
            document.getElementById('date-filter').addEventListener('change', filterTable);
            document.getElementById('status-filter').addEventListener('change', filterTable);

        </script>

        <script>

            function getGaugeData() {
                // Show loading icon for each card
                var loadingIcons = document.querySelectorAll('.loading-gauge');
                loadingIcons.forEach(function (loadingIcon) {
                    loadingIcon.style.display = 'block';
                });

                setTimeout(function () {
                    var partFilter = document.getElementById('part-filter').value;

                    // Make an AJAX request to a PHP script that will fetch data from the database
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === XMLHttpRequest.DONE) {
                            if (xhr.status === 200) {
                                // Parse the JSON response and update the displayed data
                                var responseData = JSON.parse(xhr.responseText);
                                var gaugeContainer = document.getElementById('gauge-chart');

                                var myChart = echarts.init(gaugeContainer);
                                var option;

                                const gaugeData = [
                                    {
                                        value: 0,
                                        name: 'Completed',
                                        title: {
                                            offsetCenter: ['-80%', '50%']
                                        },
                                        detail: {
                                            offsetCenter: ['-80%', '68%'],
                                            width: 40,
                                            height: 12,
                                            fontSize: 14,
                                            color: '#fff',
                                            backgroundColor: '#91cc75',
                                            borderRadius: 3,
                                            formatter: '{value}%'
                                        }
                                    },
                                    {
                                        value: 0,
                                        name: 'In Progress',
                                        title: {
                                            offsetCenter: ['0%', '50%']
                                        },
                                        detail: {
                                            offsetCenter: ['0%', '68%'],
                                            width: 40,
                                            height: 12,
                                            fontSize: 14,
                                            color: '#fff',
                                            backgroundColor: '#fac858',
                                            borderRadius: 3,
                                            formatter: '{value}%'
                                        }
                                    },
                                    {
                                        value: 0,
                                        name: 'Not Started',
                                        title: {
                                            offsetCenter: ['80%', '50%']
                                        },
                                        detail: {
                                            offsetCenter: ['80%', '68%'],
                                            width: 40,
                                            height: 12,
                                            fontSize: 14,
                                            color: '#fff',
                                            backgroundColor: '#ee6666',
                                            borderRadius: 3,
                                            formatter: '{value}%'
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
                                                show: false,
                                                showAbove: false,
                                                size: 12,
                                                itemStyle: {
                                                    color: '#FAC858'
                                                }
                                            },
                                            startAngle: 180,
                                            endAngle: 0,
                                            min: 0,
                                            max: 100,
                                            splitNumber: 5,
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
                                                fontSize: 14
                                            },
                                            detail: {
                                                width: 25,
                                                height: 12,
                                                fontSize: 14,
                                                color: '#fff',
                                                backgroundColor: 'inherit',
                                                borderRadius: 3,
                                                formatter: '{value}%'
                                            },
                                            data: gaugeData
                                        }
                                    ]
                                };

                                gaugeData[0].value = responseData.Completed;
                                gaugeData[1].value = responseData.InProgress;
                                gaugeData[2].value = responseData.NotStarted;
                                // Set options to chart instance
                                myChart.setOption(option);
                            } else {
                                // Handle error
                                console.error('Error fetching data from the server:', xhr.status);
                            }
                        }
                        // Hide loading icon for each card
                        loadingIcons.forEach(function (loadingIcon) {
                            loadingIcon.style.display = 'none';
                        });
                    };
                    xhr.open('GET', '/thesis-mgmt/dashboard/dashboard-gauge.php?part=' + encodeURIComponent(partFilter));
                    xhr.send();
                }, 1000);

            }

            document.getElementById('part-filter').addEventListener('change', getGaugeData);

            // Call fetchDataFromDatabase on initial load
            getGaugeData();
        </script>
    </body>

    </html>

<?php } else
{
    header("Location: /thesis-mgmt/login.php");
} ?>