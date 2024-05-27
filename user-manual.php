<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Manual - Thesis Management System</title>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">

    <style>
        ol,
        ul {
            padding: 0;
            margin: 0;
        }

        ol {
            counter-reset: item;
            list-style: none;
            padding-left: 30px;
        }

        ol li {
            counter-increment: item;
            margin-bottom: 10px;
            padding-left: 20px;
            position: relative;
        }


        ol li:before {
            content: counter(item) ". ";
            font-weight: bold;
            position: absolute;
            left: 0;
            top: 0;
        }
    </style>
</head>

<body class="content">
    <div>
        <h3 style="position:absolute;margin-top:20px;">User Manual</h3>
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/header.php"); ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/sidebar.php"); ?>
        <hr>
    </div>

    <h1>User Manual for the Thesis Management System</h1>
    <br>
    <h2>Using the System</h2>
    <ol>
        <li><strong>Run XAMPP:</strong> Start XAMPP to run the system.</li>
        <li><strong>Access the System:</strong> Open your web browser and navigate to localhost. Click on the designated
            folder for the Thesis Management System.</li>
        <li><strong>Login:</strong> Enter your credentials to log in. If you forgot your password, use the "Forgot
            Password" option to reset it. If you don't have an account, click "Sign Up" to register.</li>
    </ol>
    <br>
    <h2>Manual for Admin</h2>
    <ol>
        <li><strong>Add Documents:</strong> Click on the "Add Document" button to upload files such as forms.</li>
        <li><strong>Dashboard:</strong> Click "Dashboard" to view an overview of the system.</li>
        <li><strong>Thesis:</strong> View all thesis projects by clicking on "Thesis".</li>
        <li><strong>Users:</strong> View system users by clicking "Users".</li>
        <li><strong>Forms:</strong> Access forms by clicking the "Forms" button.</li>
        <li><strong>Reports:</strong> Generate reports for the system by clicking "Reports".</li>
        <li><strong>User Manual:</strong> Access the user manual by clicking the question mark icon.</li>
    </ol>

    <!-- Repeat the same structure for Manual for Dean, Instructor, Adviser, and Student -->
    <br>
    <!-- Manual for Dean -->
    <h2>Manual for Dean</h2>
    <ol>
        <li><strong>Dashboard:</strong> View system overview by clicking "Dashboard".</li>
        <li><strong>Thesis:</strong> Access thesis projects by clicking "Thesis".</li>
        <li><strong>Forms:</strong> Access forms by clicking the "Forms" button.</li>
        <li><strong>User Manual:</strong> Access the user manual by clicking the question mark icon.</li>
    </ol>
    <br>
    <!-- Manual for Instructor -->
    <h2>Manual for Instructor</h2>
    <ol>
        <li><strong>Dashboard:</strong> View system overview by clicking "Dashboard".</li>
        <li><strong>Tasks:</strong> Manage submissions, approvals, and revisions of thesis papers by clicking "Tasks".
        </li>
        <li><strong>Thesis:</strong> Access thesis projects by clicking "Thesis".</li>
        <li><strong>Forms:</strong> Access forms by clicking the "Forms" button.</li>
        <li><strong>User Manual:</strong> Access the user manual by clicking the question mark icon.</li>
    </ol>
    <br>
    <!-- Manual for Adviser -->
    <h2>Manual for Adviser</h2>
    <ol>
        <li><strong>Dashboard:</strong> View system overview by clicking "Dashboard".</li>
        <li><strong>Tasks:</strong> Check submissions of papers under your advisory by clicking "Tasks".</li>
        <li><strong>Thesis:</strong> Access thesis projects by clicking "Thesis".</li>
        <li><strong>Forms:</strong> Access forms by clicking the "Forms" button.</li>
        <li><strong>User Manual:</strong> Access the user manual by clicking the question mark icon.</li>
    </ol>
    <br>
    <!-- Manual for Student -->
    <h2>Manual for Student</h2>
    <ol>
        <li><strong>Dashboard:</strong> View system overview by clicking "Dashboard".</li>
        <li><strong>Tasks:</strong> Check your own thesis project and submissions by clicking "Tasks".</li>
        <li><strong>Thesis:</strong> Access thesis projects by clicking "Thesis".</li>
        <li><strong>Forms:</strong> Access forms by clicking the "Forms" button. Generate forms if needed.</li>
        <li><strong>User Manual:</strong> Access the user manual by clicking the question mark icon.</li>
    </ol>

</body>

</html>