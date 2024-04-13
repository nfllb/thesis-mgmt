<?php
session_start();
if (!isset($_SESSION['username']) && !isset($_SESSION['userid']))
{
    ?>

    <!DOCTYPE html>
    <html>

    <head>
        <title>Thesis Management</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">

        <style>
            body {
                font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
                font-size: 13px;
                background-color: #ECDFD7;
            }

            .form-label {
                margin-bottom: 1px;
            }

            .form-control,
            .form-select {
                font-size: 13px;
                border-radius: 0.75rem;
            }

            .btn-primary {
                background-color: #D2691E;
                border-color: #D2691E;
            }

            .btn-primary:hover,
            .btn-primary:focus {
                background-color: #E58B4B;
                border-color: #E58B4B;
                box-shadow: #E58B4B;
            }

            .ca {
                font-size: 11px;
                padding: 10px;
                text-decoration: none;
                color: #444;
                float: right;
            }

            .shadow {
                box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important
            }
        </style>
    </head>

    <body>
        <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh">
            <form class="border shadow p-3 rounded" action="php/credential-check.php" method="post" style="width: 350px;">
                <h3 class="text-center p-3">Login</h3>
                <?php if (isset($_GET['error']))
                { ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $_GET['error'] ?>
                    </div>
                <?php } ?>
                <div class="mb-3">
                    <label for="username" class="form-label">User Name</label>
                    <input type="text" class="form-control shadow" name="username" id="username">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control shadow mb1" id="password">
                    <a href="#" class="link" style="color:#D2691E;font-size:12px;">Forgot Password?</a></span>
                </div>

                <button type="submit" class="btn btn-primary form-control">Log in</button>
                <span class="ca">Don't have an account? <a href="./signup.php" style="color:#D2691E;"
                        class="link">Signup</a></span>
            </form>
        </div>
    </body>

    </html>
<?php } else
{
    header("Location: index.php");
}
?>