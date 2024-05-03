<?php
// Include database connection
include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");

// Fetch options from the database table
// Modify the SQL query according to your database structure
$query = "SELECT ThesisId, Title FROM thesis";
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
    <option disabled selected>Select Thesis</option>
    <?php echo $options; ?>
</select>