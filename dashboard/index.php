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
                margin-top: 20px;
            }

            #gauge-chart {
                width: 100%;
                height: 350px;
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

            .loader {
                border: 4px solid #f3f3f3;
                border-top: 4px solid #3498db;
                border-radius: 50%;
                width: 20px;
                height: 20px;
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
                    <label style="font-size: 16px;" for="department">Select Department:</label>
                    <select id="department">
                        <option value="all">All Departments</option>
                        <option value="computer_science">Computer Science</option>
                        <option value="engineering">Engineering</option>
                        <option value="biology">Biology</option>
                        <option value="chemistry">Chemistry</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 16px;" for="course">Select Course:</label>
                    <select id="course">
                        <option value="all">All Courses</option>
                        <option value="course1">Course 1</option>
                        <option value="course2">Course 2</option>
                        <option value="course3">Course 3</option>
                        <option value="course4">Course 4</option>
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
                        <p class="card-value" id="total-theses">50</p>
                        <div class="loading">
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
                        <p class="card-value" id="theses-in-progress">20</p>
                        <div class="loading">
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
                        <p class="card-value" id="completed-theses">30</p>
                        <div class="loading">
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
                        <p class="card-value" id="theses-pending-review">10</p>
                        <div class="loading">
                            <div class="loader"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="large-card">
                <div style="display: flex; align-items: center;">
                    <h6 style="font-weight: bold; margin-right: 120px;">Thesis Progress Summary</h6>
                    <label for="date-filter">Date Filter:</label>
                    <select id="date-filter">
                        <option value="all">All Dates</option>
                        <option value="last-week">Last Week</option>
                        <option value="last-month">Last Month</option>
                        <option value="last-year">Last Year</option>
                    </select>
                    <label for="status-filter">Status Filter:</label>
                    <select id="status-filter">
                        <option value="all">All Statuses</option>
                        <option value="Completed">Completed</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Pending Review">Pending Review</option>
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
                    <tr>
                        <td>Thesis 1</td>
                        <td>Ins 1</td>
                        <td>2024-05-01</td>
                        <td>Completed</td>
                        <td>10%</td>
                    </tr>
                    <tr>
                        <td>Thesis 2</td>
                        <td>Ins 2</td>
                        <td>2024-05-01</td>
                        <td>In Progress</td>
                        <td>10%</td>
                    </tr>
                    <tr>
                        <td>Thesis 3</td>
                        <td>Ins 3</td>
                        <td>2024-05-01</td>
                        <td>Completed</td>
                        <td>10%</td>
                    </tr>
                    <tr>
                        <td>Thesis 4</td>
                        <td>Ins 4</td>
                        <td>2023-05-01</td>
                        <td>Pending Review</td>
                        <td>10%</td>
                    </tr>
                    <!-- Add more rows as needed -->
                </table>
                <div class="loading">
                    <div class="loader"></div>
                </div>
            </div>

            <div class="gauge-card-container">
                <div class="label-and-filter">
                    <div style="font-weight: bold; font-size: 15px;" class="progress-label">Overall Progress</div>
                    <div class="filter-container">
                        <!-- Your filter elements here -->
                        <label for="filter">Filter:</label>
                        <select id="filter">
                            <option value="option1">Option 1</option>
                            <option value="option2">Option 2</option>
                            <option value="option3">Option 3</option>
                        </select>
                    </div>
                </div>
                <div class="gauge-chart-container">
                    <div id="gauge-chart"></div>
                </div>
            </div>


        </div>

        <script>
            // Placeholder data (replace with actual data from backend)
            var totalTheses = 50;
            var thesesInProgress = 20;
            var completedTheses = 30;
            var thesesPendingReview = 10;

            // Display placeholder data
            document.getElementById('total-theses').textContent = totalTheses;
            document.getElementById('theses-in-progress').textContent = thesesInProgress;
            document.getElementById('completed-theses').textContent = completedTheses;
            document.getElementById('theses-pending-review').textContent = thesesPendingReview;

            // Function to update data based on filters
            function updateData() {
                // Show loading icon for each card
                var loadingIcons = document.querySelectorAll('.loading');
                loadingIcons.forEach(function (loadingIcon) {
                    loadingIcon.style.display = 'block';
                });

                // Placeholder function, replace with actual logic to fetch data from backend
                setTimeout(function () {
                    // Update total theses count
                    totalTheses = Math.floor(Math.random() * 100) + 1;
                    document.getElementById('total-theses').textContent = totalTheses;

                    // Update other counts accordingly
                    thesesInProgress = Math.floor(Math.random() * totalTheses);
                    completedTheses = Math.floor(Math.random() * (totalTheses - thesesInProgress));
                    thesesPendingReview = totalTheses - thesesInProgress - completedTheses;

                    // Update displayed data
                    document.getElementById('theses-in-progress').textContent = thesesInProgress;
                    document.getElementById('completed-theses').textContent = completedTheses;
                    document.getElementById('theses-pending-review').textContent = thesesPendingReview;

                    // Hide loading icon for each card
                    loadingIcons.forEach(function (loadingIcon) {
                        loadingIcon.style.display = 'none';
                    });
                }, 1000); // Simulate delay for fetching data
            }

            // Event listeners for filter changes
            document.getElementById('department').addEventListener('change', updateData);
            document.getElementById('course').addEventListener('change', updateData);

            // Filter function for the table
            function filterTable() {
                var dateFilter = document.getElementById('date-filter').value;
                var statusFilter = document.getElementById('status-filter').value;
                var rows = document.getElementById('all-thesis-records').getElementsByTagName('tr');

                for (var i = 1; i < rows.length; i++) {
                    var row = rows[i];
                    var dateCell = row.getElementsByTagName('td')[2].textContent;
                    var statusCell = row.getElementsByTagName('td')[3].textContent;

                    // Check if date matches the filter
                    if (dateFilter !== 'all') {
                        var currentDate = new Date();
                        var rowDate = new Date(dateCell);
                        var daysDifference = Math.ceil((currentDate - rowDate) / (1000 * 60 * 60 * 24));

                        if (dateFilter === 'last-week' && daysDifference > 7) {
                            row.style.display = 'none';
                            continue;
                        } else if (dateFilter === 'last-month' && daysDifference > 30) {
                            row.style.display = 'none';
                            continue;
                        } else if (dateFilter === 'last-year' && daysDifference > 365) {
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
            // Placeholder data
            var totalTheses = 50;
            var completedTheses = 30;

            // Calculate completion percentage
            var gaugeValue = (completedTheses / totalTheses) * 100;

            // Get the gauge chart container element
            var gaugeContainer = document.getElementById('gauge-chart');

            // Set the width of the gauge chart container
            gaugeContainer.style.width = '300'; // Adjust width as needed

            // Reinitialize the ECharts instance with the updated container width
            var myChart = echarts.init(gaugeContainer);


            // Set options for gauge chart
            var option = {
                series: [
                    {
                        type: 'gauge',
                        startAngle: 180,
                        endAngle: 0,
                        min: 0,
                        max: 100,
                        splitNumber: 5,
                        itemStyle: {
                            color: '#58D9F9',
                            shadowColor: 'rgba(0,138,255,0.45)',
                            shadowBlur: 10,
                            shadowOffsetX: 2,
                            shadowOffsetY: 2
                        },
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
                            show: false
                        },
                        detail: {
                            width: '80%',
                            lineHeight: 85,
                            height: 80,
                            borderRadius: 8,
                            offsetCenter: [0, '15%'],
                            valueAnimation: true,
                            formatter: function (value) {
                                return '{value|' + value.toFixed(0) + '}{unit|%}';
                            },
                            rich: {
                                value: {
                                    fontSize: 20,
                                    fontWeight: 'bolder',
                                    color: '#777'
                                },
                                unit: {
                                    fontSize: 20,
                                    color: '#999',
                                    padding: [0, 0, 0, 10]
                                }
                            }
                        },
                        data: [
                            {
                                value: 90
                            }
                        ]
                    }
                ]
            };

            // Set options to chart instance
            myChart.setOption(option);
        </script>
    </body>

    </html>

<?php } else
{
    header("Location: /thesis-mgmt/login.php");
} ?>