<?php
session_start();
// Include database connection
include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");

// Fetch options from the database table
// Modify the SQL query according to your database structure
$sql_WhereClause = ' WHERE ';
if ($_SESSION['role'] == 'Research Coordinator')
{
    $sql_WhereClause = $sql_WhereClause . '1';
} else if ($_SESSION['role'] == 'Adviser')
{
    $sql_WhereClause = $sql_WhereClause . 'Adviser = \'' . $_SESSION['name'] . '\'';
} else if ($_SESSION['role'] == 'Instructor')
{
    $sql_WhereClause = $sql_WhereClause . 'Instructor = \'' . $_SESSION['name'] . '\'';
} else if ($_SESSION['role'] == 'Student')
{
    $sql_WhereClause = $sql_WhereClause . 'Authors LIKE \'%' . $_SESSION['name'] . '%\'';
}

$query = "SELECT ThesisId, Title FROM thesis_groupedstudents_vw" . $sql_WhereClause . ' ORDER BY ThesisId';
$result = mysqli_query($con, $query);

// Generate options based on fetched data
$options = '';
while ($row = mysqli_fetch_assoc($result))
{
    $options .= '<option value="' . $row['ThesisId'] . '">' . $row['Title'] . '</option>';
}

// Output options
?>
<select class="form-select" id="selectedThesis">
    <option value="none" disabled selected>Select Thesis</option>
    <?php echo $options; ?>
</select>