<?php
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Thesis Management</title>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/styles.css">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <form class="border shadow p-3 rounded login-form" action="php/signup-check.php" method="post">
            <h3 class="text-center p-3">Signup</h3>
            <?php if (isset($_GET['error']))
            { ?>
                <div class="alert alert-danger" role="alert">
                    <span class="icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                    <?= $_GET['error'] ?>
                </div>
            <?php } ?>
            <div class="mb-3">
                <label for="role" class="form-label">Academic Role</label>
                <select class="form-select mb-2 shadow" name="role" id="role" onchange="toggleDiv(this.value)">
                    <option selected disabled value="selectRole">--Select Role--</option>
                    <option value="student">Student</option>
                    <option value="researchcoordinator">Research Coordinator</option>
                    <option value="dean">Dean</option>
                    <option value="instructor">Instructor</option>
                    <option value="adviser">Adviser</option>
                </select>
                <div id="student_details" style="display:none;">
                    <div class="mb-1">
                        <label for="department" class="form-label">Department</label>
                    </div>
                    <select class="form-select mb-2 shadow" name="department" id=department>
                        <option selected value="engineering">School of Engineering, Architecture, and Information
                            Technology
                        </option>
                    </select>
                    <div class="mb-1">
                        <label for="course" class="form-label">Course</label>
                    </div>
                    <select class="form-select mb-2 shadow" name="course" id=course>
                        <option selected disabled value="course">--Select Course--</option>
                        <option value="Architecture">Architecture</option>
                        <option value="Civil Engineering">Civil Engineering</option>
                        <option value="Computer Engineering">Computer Engineering</option>
                        <option value="Electrical Engineering">Electrical Engineering</option>
                        <option value="Electronics Engineering">Electronics Engineering</option>
                        <option value="Information Technology">Information Technology</option>
                        <option value="Computer Science">Computer Science</option>
                        <option value="Library and Information Science">Library and Information Science</option>
                    </select>
                    <div class="mb-1">
                        <label for="year" class="form-label">Year</label>
                    </div>
                    <select class="form-select mb-2 shadow" name="year" id=year>
                        <option selected disabled value="years">--Select Year--</option>
                        <option value="Third">Third</option>
                        <option value="Fourth">Fourth</option>
                        <option value="Fifth">Fifth</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control shadow" name="name" id="name">
                </div>
                <div class="form-group">
                    <div class="mb-1">
                        <label for="username" class="form-label">User Name</label>
                        <input type="text" class="form-control shadow" name="username" id="username">
                    </div>
                </div>
                <div class="form-group">
                    <div class="mb-1">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control shadow" id="email">
                    </div>
                </div>
                <div class="form-group">
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control shadow" id="password">
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <button type="submit" class="btn btn-primary form-control">Create my account</button>
                        <span class="ca">Already have an account? <a href="./login.php"
                                class="signup-link">Login</a></span>
                    </div>
                </div>
            </div>
    </div>

    </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script>
        function toggleDiv(value) {
            console.log(value);
            const details = document.getElementById('student_details');
            details.style.display = value == 'student' ? 'block' : 'none';
        }
    </script>
</body>

</html>