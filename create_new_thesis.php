<?php
session_start();
include './dbconnect.php';

if (isset($_SESSION['username']) && isset($_SESSION['userid']))
{
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create New Thesis</title>

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" href="./css/styles.css">
    </head>

    <body class="content">
        <h3 style="position:absolute;margin-top:20px;">Create New Thesis</h3>
        <?php include ('./header.php'); ?>
        <?php include ('./sidebar.php'); ?>

        <hr>
        <div class="container d-flex justify-content-center align-items-center ">
            <form id="createForm" class="border shadow p-3 rounded new-thesis-form thesis-modal-color">
                <!-- <h3 class="text-center thesis-text-color">Create New Thesis</h3> -->
                <div class="mb-3">
                    <div class="form-group">
                        <div class="mb-3">
                            <label for="title" class="form-label thesis-text-color">Title</label>
                            <input type="text" class="form-control shadow" name="title" id="title">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-3">
                            <label for="proponents" class="form-label thesis-text-color">Proponents</label>
                            <input type="proponents" name="proponents" class="form-control shadow" id="proponents">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-3">
                            <label for="adviser" class="form-label thesis-text-color">Adviser</label>
                            <input type="text" class="form-control shadow" name="adviser" id="adviser">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-3">
                            <label for="instructor" class="form-label thesis-text-color">Instructor</label>
                            <input type="text" name="instructor" class="form-control shadow" id="instructor">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-3">
                            <label for="year" class="form-label thesis-text-color">School Year</label>
                            <input type="text" name="year" class="form-control shadow" id="year">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-3">
                            <label for="dateofdefense" class="form-label thesis-text-color">Date of Final Defense</label>
                            <input type="text" name="dateofdefense" class="form-control shadow" id="dateofdefense">
                        </div>
                    </div>
                    <div class="form-group">
                        <div>
                            <button type="submit" class="btn btn-primary form-control"
                                onclick="submitForm()">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
            integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
            crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
            integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
            crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>

        <script>
            function submitForm() {
                var formData = $("#createForm").serialize();
                formData.append('action', 'create_new_thesis');
                $.ajax({
                    type: "POST",
                    url: "thesis-creation.php",
                    data: formData,
                    success: function (response) {
                        alert(response);
                    }
                });
            }
        </script>
    </body>

    </html> <?php } else
{
    header("Location: login.php");
} ?>