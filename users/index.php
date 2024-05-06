<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/thesis-mgmt/css/styles.css">
    <style>
        h1 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .add-user-btn {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 8px 12px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }

        .add-user-btn:hover {
            background-color: #0056b3;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            position: relative;
        }

        .btn-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .btn-container button {
            margin-right: 10px;
        }

        .filter-container {
            margin-top: 10px;
        }

        .filter-container input[type="text"] {
            width: 200px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .table-container {
            margin-top: 20px;
        }

        .status {
            width: 100px;
        }

        .status select {
            width: 100%;
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ccc;
            background-color: #fff;
        }

        .status option {
            padding: 6px;
        }

        .edit-btn,
        .save-btn {
            /* background-color: #4CAF50; */
            border: none;
            color: white;
            padding: 8px 12px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }

        .edit-btn:hover,
        .save-btn:hover {
            /* background-color: #45a049; */
        }

        .edit-btn {
            /* background-color: #007bff; */
        }

        .edit-btn:hover {
            /* background-color: #0056b3; */
        }
    </style>
</head>

<body class="content">
    <header>
        <h3 style="position:absolute;margin-top:20px;">User Management</h3>
        <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/header.php"); ?>
        <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/sidebar.php"); ?>
        <hr>
    </header>

    <!-- Modal for editing user details -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm" method="post">
                        <input type="hidden" id="editUserId" name="user_id">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editName" name="name">
                        </div>
                        <div class="mb-3">
                            <label for="editUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="editUsername" name="username">
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="editEmail" name="email">
                        </div>
                        <div class="mb-3">
                            <label for="editRole" class="form-label">Role</label>
                            <input type="text" class="form-control" id="editRole" name="role">
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <button style="float: right;" type="submit" class="btn btn-sm btn-primary" name="save">Save
                            changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="container">
        <div class="btn-container">
            <button class="add-user-btn" onclick="window.location.href = '/thesis-mgmt/signup.php';">Add User</button>
            <div class="filter-container">
                <input type="text" id="filterInput" placeholder="Search for usernames...">
            </div>
        </div>
        <?php
        // If form is submitted, update user data in database
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save']))
        {
            $user_id = $_POST['user_id'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $role = $_POST['role'];
            $status = $_POST['status'];
            $name = $_POST['name'];

            $sql = "UPDATE users SET `Name`='$name', `UserName`='$username', `Email`='$email', `Role`='$role', `Status`='$status' WHERE UserId = $user_id";

            if ($con->query($sql) === FALSE)
            {
                echo "Error updating record: " . $con->error;
            }
        }

        // Fetch users from database
        $sql = "SELECT * FROM users";
        $result = $con->query($sql);

        if ($result->num_rows > 0)
        {
            echo "<div class='table-container'>";
            echo "<table>";
            echo "<tr><th style='display: none;'>User ID</th><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Edit</th></tr>";

            while ($row = $result->fetch_assoc())
            {
                echo "<tr>";
                echo "<td style='display: none;'>" . $row["UserId"] . "</td>";
                echo "<td>" . $row["Name"] . "</td>";
                echo "<td>" . $row["UserName"] . "</td>";
                echo "<td>" . $row["Email"] . "</td>";
                echo "<td>" . $row["Role"] . "</td>";
                echo "<td>" . $row["Status"] . "</td>";
                echo "<td>";
                echo "<button class='edit-btn btn btn-sm btn-primary' type='button' data-bs-toggle='modal' data-bs-target='#editUserModal' data-status='" . $row["Status"] . "' data-role='" . $row["Role"] . "' data-name='" . $row["Name"] . "' data-user-id='" . $row["UserId"] . "' data-username='" . $row["UserName"] . "' data-email='" . $row["Email"] . "'>Edit</button>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
        } else
        {
            echo "0 results";
        }

        $con->close();
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>

    <script>
        // Function to populate modal fields with user data
        function populateModal(userId, name, username, email, role, status) {
            document.getElementById('editUserId').value = userId;
            document.getElementById('editName').value = name;
            document.getElementById('editUsername').value = username;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRole').value = role;
            document.getElementById('editStatus').value = status;
            // Populate more fields if needed
        }

        // Event listener for Edit button click
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                const userId = this.getAttribute('data-user-id');
                const name = this.getAttribute('data-name');
                const username = this.getAttribute('data-username');
                const email = this.getAttribute('data-email');
                const role = this.getAttribute('data-role');
                const status = this.getAttribute('data-status');
                populateModal(userId, name, username, email, role, status);
            });
        });

        // Filter table as you type
        document.getElementById("filterInput").addEventListener("keyup", function () {
            var filterValue = this.value.toUpperCase();
            var table = document.querySelector("table");
            var rows = table.getElementsByTagName("tr");
            for (var i = 1; i < rows.length; i++) { // Start from 1 to skip header row
                var cells = rows[i].getElementsByTagName("td")[1]; // Filter based on the second column (Username)
                if (cells) {
                    var username = cells.textContent || cells.innerText;
                    if (username.toUpperCase().indexOf(filterValue) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        });
    </script>

</body>

</html>