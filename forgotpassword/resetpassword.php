<?php ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Reset Password</title>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link rel="stylesheet" href="./../css/styles.css">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <form class="border shadow p-3 rounded login-form" action="./../php/resetpassword-check.php" method="post">
            <h3 class="text-center">Reset Password</h3>
            <div class="mb-3">
                <div class="form-group mb-4">
                    <div class="mb-1 ">
                        <label for="newpassword" class="form-label">New Password</label>
                        <input type="password" required class="form-control shadow" name="newpassword" id="newpassword">
                    </div>
                    <div class="mb-1 ">
                        <label for="confirmpassword" class="form-label">Confirm Password</label>
                        <input type="password" required class="form-control shadow" name="confirmpassword"
                            id="confirmpassword">
                    </div>
                </div>
                <?php if (isset($_GET['error']))
                { ?>
                    <div class="alert alert-danger" role="alert">
                        <span class="icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                        <?= $_GET['error'] ?>
                    </div>
                <?php } ?>
                <div class="form-group">
                    <div>
                        <button type="submit" class="btn btn-primary form-control shadow">Reset Password</button>
                    </div>
                </div>
            </div>
    </div>

    </form>
    </>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
</body>

</html>