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
    </head>

    <body>
        <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh">
            <form class="border shadow p-3 rounded" action="php/credential-check.php" method="post" style="width: 450px;">

                <h3 class="text-center p-3">Log in</h3>
                <div class="text-block-203">Don't have an account? &nbsp;<a href="/" class="link">Sign up</a></div>
                <?php if (isset($_GET['error']))
                { ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $_GET['error'] ?>
                    </div>
                <?php } ?>
                <div class="mb-3">
                    <label for="username" class="form-label">User Name</label>
                    <input type="text" class="form-control" name="username" id="username">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password">
                </div>

                <button type="submit" class="btn btn-primary">Log in</button>
            </form>
        </div>
    </body>

    </html>
<?php } else
{
    header("Location: index.php");
}
?>