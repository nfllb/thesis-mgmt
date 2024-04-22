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
            <span>Role: <?php echo $_SESSION['role']; ?></span><br>
            <a href="./../logout.php" class="btn btn-dark btn-sm">Logout</a>
        </div>
        <hr>

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
                        $sql_Query = "SELECT * FROM thesis_checklist_vw WHERE ThesisId = " . $thesisId . " ORDER BY StepNumber ASC";
                        $result = mysqli_query($con, $sql_Query);
                        if ($result && mysqli_num_rows($result) > 0)
                        {
                            echo "<table id='checklistTable' class='table table-bordered table-sm blk-border'>
                                    <thead class='center-middle-text'>
                                        <tr>" .
                                // <th style='width: 3%; 'scope='col'></th>
                                "<th style='width: 5%;' scope='col'>Step No.</th>
                                            <th style='width: 35%;' class='w-25 p-3' scope='col'>Task</th>
                                            <th style='width: 30%;' scope='col'>Assignee</th>
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
                                // $step_completed = $step["Completed"];
                                $step_number = $step["StepNumber"];
                                $step_name = $step["TaskName"];
                                $step_assignee = $step["Assignee"];
                                $step_status = $step["Status"];

                                $bkgrnd_color = '';
                                $disabled = '';
                                $show_action = true;
                                if (($step["Action"] == 'Approval' || $step["Action"] == 'Upload') && $step_status != 'Completed' && $index > 0 && $prev_step != null && $prev_step["Status"] != 'Completed')
                                {
                                    $bkgrnd_color = 'table-secondary';
                                } else if ($step_status == 'Completed')
                                {
                                    $bkgrnd_color = 'table-success';
                                    $disabled = 'disabled';
                                    $show_action = false;
                                } else if ($step_status == 'In Progress')
                                {
                                    $bkgrnd_color = 'table-warning';
                                } else if ($step_status == 'Not Started')
                                {
                                    $bkgrnd_color = 'table-secondary';
                                }

                                if ($index > 0 && $prev_step != null)
                                {
                                    if ($prev_step["Status"] != 'Completed')
                                    {

                                        $disabled = 'disabled';
                                        $show_action = false;
                                    }
                                }
                                echo "<tr style='font-size:.875rem;' class='" . $bkgrnd_color . "'>";
                                //"<td><input type='checkbox' aria-label='Step Completed? '";
                                // if ($step_completed == 1)
                                // {
                                //     echo "checked ";
                                // }
                    
                                // echo "></td>" . "
                                echo "<td scope='row'>$step_number</td>
                                        <td class='w-25 p-3'>$step_name</td>
                                        <td class='w-25 p-3'>$step_assignee</td>
                                        <td style='width: 10%;'>";
                                if ($step["Action"] == 'Manual')
                                {
                                    echo "<select class='form-select mb-2 shadow' id=statusValue" . $step_number . " " . $disabled . ">";
                                    foreach ($status_enum_values_arr as $status)
                                    {
                                        $selected = '';
                                        if ($status == $step_status)
                                        {
                                            $selected = 'selected';
                                        }
                                        echo "<option value='$status' " . $selected . ">$status</option>";
                                    }
                                }

                                echo "</td>";
                                if ($show_action && $step["Action"] == 'Approval')
                                {
                                    echo "<td>
                                        <button type='button' class='btn btn-success btn-sm'>Approve</button>
                                        <button type='button' class='btn btn-danger btn-sm'>Reject</button>
                                     </td>";
                                } else if ($show_action && $step["Action"] == 'Manual')
                                {
                                    echo "<td><button type='button' value=" . $step_number . " class='btn btn-success btn-sm saveStepBtn'>Save</button></td>";
                                } else if ($show_action && $step["Action"] == 'Upload')
                                {
                                    // <div class='statusMsg'></div>
                                    //             <input type='file' class='form-control' id='file' name='file' required />
                                    //             <button type='button'  value=" . $step_number . " class='btn btn-success uploadFileBtn'>Upload</button>
                                    // <input type='submit' name='submit' class='btn btn-success' value='Upload'/>
                    

                                    // <form id=fupForm' enctype='multipart/form-data'>
                                    //             <div class='statusMsg'></div>
                                    //             <input type='file' class='form-control' id='file' name='file' required />
                                    //             <button type='submit' name='submit' class='btn btn-primary'>Upload</button>
                                    //         </form>
                                    echo "<td>
                                                <form>
                                                    <input type='file' id='file' />
                                                    <br />
                                                    <center><button type='button' value=" . $step_number . " class='btn btn-success' id='upload'>Upload</button></center>
                                                    <br />
                                                    <div id='msg'></div>
                                                </form>
                                        </td>";
                                } else
                                {
                                    echo "<td></td>";
                                }
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

                        //var res = jQuery.parseJSON(response);
                        console.log(response);
                        new_step_status = '';
                        $('#checklistTable').load(location.href + " #checklistTable");

                    }
                });

            });

        </script>

        <script>
            // $(document).on('submit', '#fupForm', function (e) {
            //     e.preventDefault();

            //     var formData = new FormData(this);
            //     formData.append("upload_file_step", true);

            //     $.ajax({
            //         type: 'POST',
            //         url: 'save_task.php',
            //         data: formData,
            //         contentType: false,
            //         processData: false,
            //         beforeSend: function () {
            //             $('.submitBtn').attr("disabled", "disabled");
            //             $('#fupForm').css("opacity", ".5");
            //         },
            //         success: function (response) {
            //             console.log(response);
            //             $('.statusMsg').html('');
            //             if (response.status == 1) {
            //                 $('#fupForm')[0].reset();
            //                 $('.statusMsg').html('<p class="alert alert-success">' + response.message + '</p>');
            //             } else {
            //                 $('.statusMsg').html('<p class="alert alert-danger">' + response.message + '</p>');
            //             }
            //             $('#fupForm').css("opacity", "");
            //             $(".submitBtn").removeAttr("disabled");
            //         }
            //     });
            // });

            $(document).on('click', '#upload', function (e) {
                e.preventDefault();
                var step_number = $(this).val();

                $(this).attr('disabled', 'disabled');
                var file = $('#file');
                var file_length = file[0].files.length;
                var file_data = file.prop('files')[0];

                var formData = new FormData();
                formData.append('file', file_data);
                formData.append('thesis_Id', <?php echo $thesisId; ?>);
                formData.append('upload_file_step', true);
                formData.append('step_number', step_number);
                formData.append('action', 'Upload');
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
                            $("#msg").empty();
                            $("<center class='text-success'>Successfully uploaded!</center>").appendTo($("#msg"));
                            $('#file').val('');
                        } else {
                            $("#msg").empty();
                            $("<center class='text-danger'>" + data + "</center>").appendTo($("#msg"));
                            $('#file').val('');
                        }

                        $('#upload').removeAttr('disabled');

                    }
                });

            });

            // File type validation
            $("#file").change(function () {
                var file = this.files[0];
                var fileType = file.type;
                var match = ['application/pdf', 'application/msword', 'application/vnd.ms-office', 'image/jpeg', 'image/png', 'image/jpg'];
                if (!((fileType == match[0]) || (fileType == match[1]) || (fileType == match[2]) || (fileType == match[3]) || (fileType == match[4]) || (fileType == match[5]))) {
                    alert('Sorry, only PDF, DOC, JPG, JPEG, & PNG files are allowed to upload.');
                    $("#file").val('');
                    return false;
                }
            });
        </script>

    </body>

    </html> <?php } else
{
    header("Location: login.php");
} ?>