<div?php ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>Thesis Management</title>

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" href="/thesis-mgmt/css/styles.css">

        <style>
            .login-form input,
            .login-form select,
            .login-form button {
                margin-bottom: 10px;
            }

            .login-form h3 {
                margin-bottom: 20px;
                color: #d2691e;
            }

            #student_details {
                transition: max-height 0.3s ease;
            }

            #student_details.show {
                max-height: 1000px;
            }
        </style>
    </head>

    <body>
        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <form id="signupForm" class="border shadow p-3 rounded login-form" action="/thesis-mgmt/php/signup-check.php" method="post">
                <h3 class="text-center">Signup</h3>
                <?php if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <span class="icon"><i class="fa-solid fa-triangle-exclamation"></i> </span>
                        <?= '       ' . $_GET['error'] ?>
                    </div>
                <?php } ?>
                <div class="mb-3">
                    <label for="role" class="form-label">Academic Role</label>
                    <select required class="form-select mb-2 shadow" name="role" id="role" onchange="toggleDiv(this.value)">
                        <option selected disabled value="">--Select Role--</option>
                        <option value="student">Student</option>
                        <option value="researchcoordinator">Research Coordinator</option>
                        <option value="dean">Dean</option>
                        <option value="instructor">Instructor</option>
                        <option value="adviser">Adviser</option>
                    </select>
                    <div id="student_details" style="display:none;">
                        <div class="mb-2">
                            <label for="department" class="form-label">Department</label>
                        </div>
                        <select required class="form-select mb-2 shadow" name="department" id=department>
                            <option selected value="engineering">School of Engineering, Architecture, and Information
                                Technology
                            </option>
                        </select>
                        <div class="mb-2">
                            <label for="course" class="form-label">Course</label>
                        </div>
                        <select required class="form-select mb-2 shadow" name="course" id=course>
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
                        <div class="mb-2">
                            <label for="year" class="form-label">Year</label>
                        </div>
                        <select required class="form-select mb-2 shadow" name="year" id=year>
                            <option selected disabled value="years">--Select Year--</option>
                            <option value="Third">Third</option>
                            <option value="Fourth">Fourth</option>
                            <option value="Fifth">Fifth</option>
                        </select>
                        <div class="form-group">
                            <div class="mb-1">
                                <label for="idnumber" class="form-label">ID Number</label>
                                <input type="text" class="form-control shadow" name="idnumber" id="idnumber">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="d-flex align-items-end mb-1">
                            <div class="me-2">
                                <label for="name" class="form-label">Name</label>
                                <input required type="text" class="form-control shadow" name="firstname" id="firstname" placeholder="First Name" style="width: 175px;">
                            </div>
                            <div class="me-2">
                                <label for="middlename" class="form-label">&nbsp;</label>
                                <input type="text" class="form-control shadow" name="middlename" id="middlename" placeholder="MI" style="width: 50px;">
                            </div>
                            <div>
                                <label for="lastname" class="form-label">&nbsp;</label>
                                <input required type="text" class="form-control shadow" name="lastname" id="lastname" placeholder="Last Name" style="width: 175px;">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-1">
                            <label for="username" class="form-label">User Name</label>
                            <input required type="text" class="form-control shadow" name="username" id="username">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-1">
                            <label for="email" class="form-label">Email</label>
                            <input required type="email" name="email" class="form-control shadow" id="email">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-1">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control shadow" id="password" minlength="8" required>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label for="securityquestion" class="form-label">Security Question</label>
                    </div>
                    <select required class="form-select mb-2 shadow" name="securityquestion" id=securityquestion>
                        <option selected disabled value="">--Select Security Question--</option>
                        <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                        <option value="What is the name of your first pet?">What is the name of your first pet?</option>
                        <option value="What was your first car?">What was your first car?</option>
                        <option value="What elementary school did you attend?">What elementary school did you attend?
                        </option>
                        <option value="What is the name of the town where you were born?">What is the name of the town
                            where
                            you were born?</option>
                    </select>
                    <input required type="text" name="securityanswer" class="form-control mb-3 shadow" id="securityanswer">
                    <div class="form-group">
                        <div>
                            <button type="submit" class="btn btn-primary form-control">Create my account</button>
                            <span class="ca">Already have an account? <a href="/thesis-mgmt/login.php" class="signup-link">Login</a></span>
                        </div>
                    </div>
                </div>
        </div>

        </form>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
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