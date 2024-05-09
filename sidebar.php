<head>
    <link rel="stylesheet" href="/thesis-mgmt/css/styles.css">
</head>

<div class="wrapper">
    <div class="sidebar">
        <ul class="list-unstyled">
            <!-- New Document -->
            <div>
                <a class="newdoc" style="cursor: pointer" onclick="uploadFile()">
                    <span class="icon"><i class="fa-solid fa-circle-plus"></i></span>
                    <span class="item">Add new document</span>
                </a>
            </div>
            <br>
            <br>
            <!-- New Thesis -->
            <li>
                <a href="/thesis-mgmt/create_new_thesis.php">
                    <span class="icon"><i class="fas fa-book"></i></span>
                    <span class="item">Create New Thesis</span>
                </a>
            </li>
            <!-- Dashboard -->
            <li>
                <a href="/thesis-mgmt/dashboard/index.php">
                    <span class="icon"><i class="fas fa-desktop"></i></span>
                    <span class="item">Dashboard</span>
                </a>
            </li>
            <!-- Thesis -->
            <li>
                <a href="/thesis-mgmt/thesis/index.php">
                    <span class="icon"><i class="fas fa-user-friends"></i></span>
                    <span class="item">Thesis</span>
                </a>
            </li>
            <!-- Users -->
            <?php
            if (isset($_SESSION['role']) && $_SESSION['role'] == 'Research Coordinator')
            { ?>
                <li>
                    <a href="/thesis-mgmt/users/index.php">
                        <span class="icon"><i class="fa-solid fa-users"></i></span>
                        <span class="item">Users</span>
                    </a>
                </li>
            <?php }
            ?>
            <!-- Forms -->
            <?php
            if (isset($_SESSION['role']) && $_SESSION['role'] != 'Dean')
            { ?>
                <li>
                    <a href="/thesis-mgmt/forms/index.php">
                        <span class="icon"><i class="fas fa-chart-line"></i></span>
                        <span class="item">Forms</span>
                    </a>
                </li>
            <?php }
            ?>
            <!-- Reports -->
            <?php
            if (isset($_SESSION['role']) && $_SESSION['role'] == 'Research Coordinator')
            { ?>
                <li>
                    <a href="/thesis-mgmt/reports/index.php">
                        <span class="icon"><i class="fas fa-user-shield"></i></span>
                        <span class="item">Reports</span>
                    </a>
                </li>
            <?php }
            ?>
            <!-- Tasks -->
            <li>
                <a href="/thesis-mgmt/tasks/index.php">
                    <span class="icon"><i class="fas fa-tasks"></i></span>
                    <span class="item">Tasks</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Circle shape -->
<div style="width: 40px;
    height: 40px;
    background-color: #f07900;
    border-radius: 50%;
    margin-top: 20px; 
    position: fixed;
    bottom: 20px;
    left: 4%;
    bottom: 5%;
    display: flex;
    justify-content: center;
    align-items: center;
    transform: translateX(-50%);" class="circle">
    <a href="/thesis-mgmt/user-manual.php">
        <i style="color: #fff; font-size:30px;" class="fa-regular fa-circle-question"></i>
    </a>
</div>


<!-- Link to the external JavaScript file -->
<script src="/thesis-mgmt/js/upload.js"></script>