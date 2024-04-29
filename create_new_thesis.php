<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");

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
        <link rel="stylesheet" href="https://unpkg.com/@jarstone/dselect/dist/css/dselect.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
        <link rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
        <link rel="stylesheet" href="/thesis-mgmt/css/styles.css">
    </head>

    <body class="content">
        <h3 style="position:absolute;margin-top:20px;">Create New Thesis</h3>
        <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/header.php"); ?>
        <?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/sidebar.php"); ?>

        <hr>
        <div class="container d-flex justify-content-center align-items-center ">
            <form id="createForm" class="border shadow p-3 rounded new-thesis-form thesis-modal-color">
                <div id="response"></div>
                <div class="mb-3">
                    <div class="form-group">
                        <div class="mb-3">
                            <label for="title" class="form-label thesis-text-color">Title</label>
                            <input type="text" class="form-control shadow" name="title" id="title" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-3">
                            <label for="proponents" class="form-label thesis-text-color">Proponents</label>
                            <?php
                            $query_Students = "SELECT UserId, Name FROM users WHERE Role = 'Student' ORDER BY Name ASC";
                            $students = $con->query($query_Students);

                            echo "<select name='proponents' class='form-control shadow' placeholder='Select Proponents' id='proponents' multiple>";
                            foreach ($students as $student)
                            {
                                echo '<option value="' . $student["UserId"] . '">' . $student["Name"] . '</option>';
                            }

                            echo "</select>";

                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="mb-3">
                            <label for="select_adviser" class="form-label thesis-text-color">Adviser</label>

                            <?php
                            $query_Advisers = "SELECT UserId, Name FROM users WHERE Role = 'Adviser' ORDER BY Name ASC";
                            $advisers = $con->query($query_Advisers);

                            echo "<select name='select_adviser' class='form-select' id='select_adviser'>
                    <option value=''>Select Adviser</option>";
                            foreach ($advisers as $adviser)
                            {
                                echo '<option value="' . $adviser["UserId"] . '">' . $adviser["Name"] . '</option>';
                            }

                            echo "</select>";

                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-3">
                            <label for="select_instructor" class="form-label thesis-text-color">Instructor</label>
                            <?php
                            $query_Instructor = "SELECT UserId, Name FROM users WHERE Role = 'Instructor' ORDER BY Name ASC";
                            $instructors = $con->query($query_Instructor);

                            echo "<select name='select_instructor' class='form-select' id='select_instructor'>
                    <option value=''>Select Instructor</option>";
                            foreach ($instructors as $instructor)
                            {
                                echo '<option value="' . $instructor["UserId"] . '">' . $instructor["Name"] . '</option>';
                            }

                            echo "</select>";

                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-3">
                            <label for="year" class="form-label thesis-text-color">School Year</label>
                            <input type="number" name="year" class="form-control shadow" id="year">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-3">
                            <label for="dateofdefense" class="form-label thesis-text-color">Date of Final Defense</label>
                            <input type="date" name="dateofdefense" class="form-control shadow" id="dateofdefense">
                        </div>
                    </div>
                    <div class="form-group">
                        <div>
                            <button type="submit" class="btn btn-primary form-control">Submit</button>
                        </div>
                    </div>
                </div>
                <div id="loadingIcon" style="display: none;">
                    <span class="spinner-border spinner-border-sm"></span> Loading...
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
        <script src="https://unpkg.com/@jarstone/dselect/dist/js/dselect.js"></script>
        <script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            $('#createForm').submit(function (event) {
                // Prevent the default form submission
                event.preventDefault();

                // Show loading icon
                $('#loadingIcon').show();

                var studentIds = [];
                var selectedElements = $('.choices__list--multiple .choices__item--selectable[aria-selected="true"]');

                selectedElements.each(function () {
                    var dataValue = $(this).data('value');
                    studentIds.push(dataValue);
                });
                var studentIdsString = studentIds.join(',');

                var form = document.getElementById('createForm');

                var formData = new FormData(form);
                formData.append('studentIds', studentIdsString);

                // AJAX request
                $.ajax({
                    type: "POST",
                    url: "php/thesis-creation.php",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        // Hide loading icon
                        $('#loadingIcon').hide();

                        if (data == 'success') {
                            window.location.href = '/thesis-mgmt/thesis/index.php';
                        } else {
                            $('#response').html('<div class="alert alert-danger">' + data + '</div>');
                        }

                    },
                    error: function (xhr, status, error) {
                        // Hide loading icon
                        $('#loadingIcon').hide();

                        // Handle errors
                        console.error(xhr.responseText);
                        $('#response').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                    }
                });
            });
        </script>
        <script>
            var select_box_element = document.querySelector('#select_adviser');
            dselect(select_box_element, {
                search: true
            });

            var select_box_element = document.querySelector('#select_instructor');
            dselect(select_box_element, {
                search: true
            });

            var multipleCancelButton = new Choices('#proponents', {
                removeItemButton: true,
                searchResultLimit: 5,
                renderChoiceLimit: 5
            });

            $('#multiple-select-field').select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder'),
                closeOnSelect: false,
            });
        </script>
    </body>

    </html> <?php } else
{
    header("Location: /thesis-mgmt/login.php");
} ?>