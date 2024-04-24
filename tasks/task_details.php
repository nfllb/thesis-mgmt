<?php
session_start();
include './../dbconnect.php';

if (isset($_SESSION['username']) && isset($_SESSION['userid']))
{
    $thesisId = $_GET['thesisId'];

    //$thesisId = 1;
    echo $thesisId;
    function get_enum_values($con, $table, $field)
    {

        $query = "SELECT SUBSTRING(COLUMN_TYPE, 6, LENGTH(COLUMN_TYPE) - 6) AS enum FROM information_schema.COLUMNS WHERE TABLE_NAME = '$table' AND COLUMN_NAME = '$field'";
        $result = mysqli_query($con, $query);
        $value = mysqli_fetch_assoc($result);

        return ($value);
    }

    $status_enum_values = get_enum_values($con, 'thesis_checklist_map', 'Status')['enum'];
    $status_enum_values_arr = explode(',', $status_enum_values);
    $status_enum_values_arr = str_replace('\'', '', $status_enum_values_arr);
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Task</title>

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" href="./../css/styles.css">
    </head>

    <body class="task-details">
        <div>
            <h3>Task Details</h3>
            <span>Name: <?php echo $_SESSION['name']; ?></span><br>
            <span>UserName: <?php echo $_SESSION['username']; ?></span><br>
            <span>UserId: <?php echo $_SESSION['userid']; ?></span><br>
            <span>Role: <?php echo $_SESSION['role']; ?></span><br>
            <a href="./../logout.php" class="btn btn-dark btn-sm">Logout</a>

            <?php
            $sql_WhereClause = " WHERE ThesisId = " . $thesisId;
            if ($_SESSION['role'] == 'Research Coordinator')
            {
                $sql_WhereClause = $sql_WhereClause . ' LIMIT 1';
            } else if ($_SESSION['role'] == 'Adviser')
            {
                $sql_WhereClause = $sql_WhereClause . ' AND Adviser = \'' . $_SESSION['name'] . '\'';
            } else if ($_SESSION['role'] == 'Instructor')
            {
                $sql_WhereClause = $sql_WhereClause . ' AND Instructor = \'' . $_SESSION['name'] . '\'';
            } else if ($_SESSION['role'] == 'Student')
            {
                $sql_WhereClause = $sql_WhereClause . ' AND Authors LIKE \'%' . $_SESSION['name'] . '%\'';
            }

            $sql_getThesisDetails = "SELECT ThesisId, Title, Authors, Adviser, Instructor FROM thesis_groupedstudents_vw" . $sql_WhereClause;
            $result_details = mysqli_query($con, $sql_getThesisDetails);
            if ($result_details && mysqli_num_rows($result_details) > 0)
            {
                $thesis = mysqli_fetch_assoc($result_details);
                $thesis_title = $thesis["Title"];
                $thesis_authors = $thesis["Authors"];
                $thesis_adviser = $thesis["Adviser"];
                $thesis_instructor = $thesis["Instructor"];
                ?>
            </div>
            <hr>

            <h3 style='margin-left:10px;'><?php echo $thesis_title; ?></h3>


            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                            aria-expanded="true" aria-controls="collapseOne">
                            THESIS 1 / CAPSTONE 1 CHECKLIST
                        </button>
                    </h2>

                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body" style='width:90%;'>
                            <?php
                            $sql_Query = "SELECT * FROM thesis_checklist_vw WHERE Part = 1 AND ThesisId = " . $thesisId . " ORDER BY StepNumber ASC";
                            $result = mysqli_query($con, $sql_Query);
                            if ($result && mysqli_num_rows($result) > 0)
                            {
                                echo "<table id='checklistTable' class='table table-bordered table-sm blk-border'>
                                    <thead class='center-middle-text'>
                                        <tr>
                                            <th style='width: 5%;' scope='col'>Step No.</th>
                                            <th style='width: 45%;' class='w-25 p-3' scope='col'>Task</th>
                                            <th style='width: 20%;' scope='col'>Assignee</th>
                                            <th style='width: 15%; 'scope='col'>Status</th>
                                            <th style='width: 15%;' scope='col'>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class='table-group-divider'>";
                                $prev_step = null;
                                echo $prev_step;
                                $index = 0;
                                while ($step = mysqli_fetch_assoc($result))
                                {
                                    $step_number = $step["StepNumber"];
                                    $step_name = $step["TaskName"];
                                    $step_status = $step["Status"];
                                    $step_assignee = $step["Assignee"];


                                    $bkgrnd_color = '';
                                    $disabled = '';
                                    $show_action = false;
                                    $show_status = false;
                                    $step_assigneed_to_user = false;
                                    if (($step["Action"] == 'Approval' || $step["Action"] == 'Upload') && $step_status != 'Completed' && $index > 0 && $prev_step != null && $prev_step["Status"] != 'Completed')
                                    {
                                        $bkgrnd_color = 'table-secondary';
                                    } else if ($step_status == 'Completed')
                                    {
                                        $bkgrnd_color = 'table-success';
                                        $disabled = 'disabled';
                                    } else if ($step_status == 'In Progress')
                                    {
                                        $bkgrnd_color = 'table-warning';
                                    } else if ($step_status == 'Not Started')
                                    {
                                        $bkgrnd_color = 'table-secondary';
                                    }

                                    $assignee = $step_assignee;
                                    if ($assignee == 'Researchers')
                                    {
                                        $assignee = 'Student';
                                    }

                                    if ($index > 0 && $prev_step != null)
                                    {
                                        if ($prev_step["Status"] != 'Completed')
                                        {
                                            $disabled = 'disabled';
                                        } else if ($prev_step['Status'] == 'Completed' && $step_status != 'Completed' && str_contains($assignee, $_SESSION['role']))
                                        {
                                            $show_action = true;
                                            $show_status = true;
                                            $step_assigneed_to_user = true;
                                        }
                                    } else if ($index == 0 && $step_status != 'Completed' && str_contains($assignee, $_SESSION['role']))
                                    {
                                        $show_action = true;
                                        $show_status = true;
                                        $step_assigneed_to_user = true;
                                    }

                                    echo "<tr style='font-size:.875rem;' class='" . $bkgrnd_color . "'>";
                                    echo "<td class='p-3' scope='row'><center>$step_number</center></td>
                                        <td class='w-25 p-3'>$step_name</td>
                                        <td class='w-25 p-3'>$step_assignee</td>";
                                    /* Status Column */
                                    if ($step_status == 'Completed')
                                    {
                                        echo "<td style='width: 10%;'>
                                            <center>
                                                <h5>
                                                    <span class='task_status_completed badge badge-success'>Completed</span>
                                                </h5>
                                            </center>
                                        </td>";
                                    } else if ($step["Action"] == 'Manual' && $step_status != 'Completed' && $show_status)
                                    {
                                        echo "<td style='width: 10%;'>
                                            <select class='form-select mb-2 shadow' id=statusValue" . $step_number . " " . $disabled . ">";
                                        foreach ($status_enum_values_arr as $status)
                                        {
                                            $selected = '';
                                            if ($status == $step_status)
                                            {
                                                $selected = 'selected';
                                            }
                                            echo "<option value='$status' " . $selected . ">$status</option>";
                                        }

                                        echo "</td>";
                                    } else if ($step["Action"] == 'Manual' && $step_status != 'Completed' && $show_status == false && $prev_step["Status"] == 'Completed')
                                    {
                                        echo "<td style='width: 10%;'>
                                            <center>
                                                <h5>
                                                    <span class='task_status_not_started badge badge-success'>$step_status</span>
                                                </h5>
                                            </center>
                                        </td>";

                                    } else if ($step["Action"] == 'Approval' && $step_status == 'Not Started' && $prev_step["Status"] == 'Completed')
                                    {
                                        echo "<td style='width: 10%;'>
                                            <center>
                                                <h5>
                                                <span class='task_status_not_started badge badge-success'>$step_status</span>
                                                </h5>
                                            </center>
                                        </td>";
                                    } else if (($step["Action"] == 'Approval' && $step_status == 'In Progress'))
                                    {
                                        echo "<td style='width: 10%;'>
                                            <center>
                                                <h5>
                                                    <span class='task_status_inprogress badge badge-success'>$step_status</span>
                                                </h5>
                                            </center>
                                        </td>";
                                    } else
                                    {
                                        echo "<td style='width: 10%;'></td>";
                                    }
                                    /* End Of Status Column */

                                    /* Action Column */
                                    $show_approval = true;
                                    $select_unapproved = "SELECT ThesisChecklistApprovalId FROM thesis_checklist_approval_map WHERE Approved = 0 AND ThesisId = " . $thesisId . " AND CheckListId = " . $step_number . " AND ApproverId = " . $_SESSION['userid'];
                                    $select_unapproved_result = mysqli_query($con, $select_unapproved);
                                    if ($select_unapproved_result && mysqli_num_rows($select_unapproved_result) == 0)
                                    {
                                        $show_approval = false;
                                    }
                                    if ($show_action && $show_approval && $step["Action"] == 'Approval')
                                    {
                                        $step_file_name = '';
                                        echo "<td>
                                            <center class='vertical-center'>
                                                <button type='button'  value=" . $step_number . " class='btn btn-success btn-sm' id='approve'>Approve</button>
                                                <button type='button'  value=" . $step_number . " class='btn btn-danger btn-sm' id='reject'>Reject</button>
                                            </center>
                                            <span id='errMsg'></span>
                                        </td>";
                                    } else if ($show_action && $step["Action"] == 'Manual')
                                    {
                                        $step_file_name = '';
                                        echo "<td>
                                            <center>
                                                <button type='button' value=" . $step_number . " class='btn btn-success btn-sm saveStepBtn vertical-center'>Save</button>
                                            </center>
                                        </td>";
                                    } else if ($show_action && $step["Action"] == 'Upload')
                                    {
                                        $step_file_name = $step["UploadedFileName"];
                                        echo "<td>
                                            <form>
                                                <center class='vertical-center'>
                                                    <input type='file' id='file' />
                                                    <br />
                                                    <button type='button' value=" . $step_number . " class='btn btn-success' id='upload'>Upload</button>
                                                    <div id='msg'></div>
                                                </center>
                                            </form>
                                        </td>";
                                    } else
                                    {
                                        echo "<td></td>";
                                    }
                                    /* End of Action Column */
                                    echo "</tr>";

                                    $prev_step = $step;
                                    $index++;
                                }
                                echo "</tbody>
                                </table>";
                            } else
                                echo "<div class='container'>
                                    <div id='thesisContainer' class='card w-100 mb-3'>
                                        <div class='card-body'>
                                            <div style='font-size:16px;'class='alert alert-danger' role='alert>
                                                <span class='icon'><i style='font-size:18px;' class='fa-regular fa-circle-xmark'></i></span>
                                                No checklist record exists for thesis. Contact your research coordinator for help.
                                            </div>
                                        </div>
                                    </div>
                                </div>";
                            ?>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            THESIS 2 / CAPSTONE 2 CHECKLIST
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <strong>This is the second item's accordion body.</strong> It is hidden by default, until
                            the
                            collapse plugin adds the appropriate classes that we use to style each element. These
                            classes
                            control the overall appearance, as well as the showing and hiding via CSS transitions. You
                            can
                            modify any of this with custom CSS or overriding our default variables. It's also worth
                            noting that
                            just about any HTML can go within the <code>.accordion-body</code>, though the transition
                            does limit
                            overflow.
                        </div>
                    </div>
                </div>
            </div> <?php } else
            {
                echo "<div class='container'>
            <div id='thesisContainer' class='card w-100 mb-3'>
                <div class='card-body'>
                    <div style='font-size:16px;'class='alert alert-danger' role='alert>
                        <span class='icon'><i style='font-size:18px;' class='fa-regular fa-circle-xmark'></i></span>
                        You don't have access to this thesis record. Contact your research coordinator for help.
                    </div>
                </div>
            </div>
            </div>";
            } ?>

        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
            integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
            crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
            integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
            crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>

        <script>
            $(document).on('click', '.saveStepBtn', function (e) {
                e.preventDefault();

                var step_number = $(this).val();
                var new_step_status = document.getElementById('statusValue' + step_number).value;
                console.log(step_number + ' - ' + new_step_status);

                $.ajax({
                    type: "POST",
                    url: "save_task.php",
                    data: {
                        'update_step': true,
                        'thesis_Id': <?php echo $thesisId; ?>,
                        'step_number': step_number,
                        'action': 'Manual',
                        'new_step_status': new_step_status
                    },
                    success: function (response) {

                        console.log(response);
                        new_step_status = '';
                        window.location.reload();
                    }
                });

            });

        </script>

        <script>
            $(document).on('click', '#upload', function (e) {
                e.preventDefault();
                var step_number = $(this).val();

                $(this).attr('disabled', 'disabled');
                var file = $('#file');
                var file_length = file[0].files.length;
                var file_data = file.prop('files')[0];
                var step_file_name = <?php echo '\'' . $step_file_name . '\''; ?>;

                var formData = new FormData();
                formData.append('file', file_data);
                formData.append('thesis_Id', <?php echo $thesisId; ?>);
                formData.append('upload_file_step', true);
                formData.append('step_number', step_number);
                formData.append('action', 'Upload');
                formData.append('new_step_status', 'Completed');
                formData.append('step_file_name', step_file_name);

                $.ajax({
                    type: "POST",
                    url: "save_task.php",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        console.log(data);
                        if (data == "success") {
                            window.location.reload();
                        } else {
                            $("#msg").empty();
                            $("<center class='text-danger'>" + data + "</center>").appendTo($("#msg"));
                            $('#file').val('');
                        }

                        $('#upload').removeAttr('disabled');

                    }
                });

            });
        </script>

        <script>
            $(document).on('click', '#approve', function (e) {
                e.preventDefault();
                var step_number = $(this).val();

                $(this).attr('disabled', 'disabled');

                var formData = new FormData();
                formData.append('thesis_Id', <?php echo $thesisId; ?>);
                formData.append('approve_step', true);
                formData.append('step_number', step_number);
                formData.append('action', 'Approval');
                formData.append('new_step_status', 'Completed');

                $.ajax({
                    type: "POST",
                    url: "save_task.php",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        console.log(data);
                        if (data == "success") {
                            window.location.reload();
                        } else {
                            $("<center class='text-danger'>" + data + "</center>").appendTo($("#errMsg"));
                        }

                        $('#approve').removeAttr('disabled');

                    }
                });

            });
        </script>

        <script>
            $(document).on('click', '#reject', function (e) {
                e.preventDefault();
                var step_number = $(this).val();

                $(this).attr('disabled', 'disabled');

                var formData = new FormData();
                formData.append('thesis_Id', <?php echo $thesisId; ?>);
                formData.append('reject_step', true);
                formData.append('step_number', step_number);
                formData.append('action', 'Approval');
                formData.append('new_step_status', 'Completed');

                $.ajax({
                    type: "POST",
                    url: "save_task.php",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        console.log(data);
                        if (data == "success") {
                            window.location.reload();
                        } else {
                            $("<center class='text-danger'>" + data + "</center>").appendTo($("#errMsg"));
                        }

                        $('#approve').removeAttr('disabled');

                    }
                });

            });
        </script>

    </body>

    </html> <?php } else
{
    header("Location: ./../login.php");
} ?>