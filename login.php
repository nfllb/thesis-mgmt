<?php
session_start();
if (!isset($_SESSION['username']) && !isset($_SESSION['userid'])) {
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Thesis Management</title>

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" href="/thesis-mgmt/css/styles.css">
    </head>

    <body>
        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <form class="border shadow p-3 rounded login-form thesis-modal-color" action="/thesis-mgmt/php/credential-check.php" method="post">
                <h3 class="text-center thesis-text-color">Login</h3>
                <?php if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <span class="icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                        <?= $_GET['error'] ?>
                    </div>
                <?php } ?>
                <div class="mb-3">
                    <label for="username" class="form-label thesis-text-color">User Name</label>
                    <input required type="text" class="form-control shadow" name="username" id="username">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label thesis-text-color">Password</label>
                    <input required type="password" name="password" class="form-control shadow mb1" id="password">
                    <a href="/thesis-mgmt/forgotpassword/index.php" class="forgot-password">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-primary form-control">Log in</button>
                <span class="ca">Don't have an account? <a href="/thesis-mgmt/signup.php" class="signup-link">Signup</a></span>
            </form>
        </div>
    </body>

    </html>
<?php } else {
    header("Location: index.php");
}
?>