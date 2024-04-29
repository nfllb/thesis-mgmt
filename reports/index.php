<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");
if (isset($_SESSION['username']) && isset($_SESSION['userid']))
{
	?>

	<!DOCTYPE html>
	<html>

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
			integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<link rel="stylesheet" href="./css/index.css">
		<title>Reports</title>
	</head>

	<body>
		<?php
		include 'sidebar.php';
		?>
		<?php
		echo "<div class='container'>";
		$sqlGetFiles = "SELECT * FROM files";
		$result = mysqli_query($con, $sqlGetFiles);

		if (mysqli_num_rows($result) > 0)
		{
			echo "<div class='row row-cols-1 row-cols-md-3 g-4'>";
			while ($file = mysqli_fetch_assoc($result))
			{
				$fileName = $file['filename'];
				$doc_code = $file['DocumentCode'];
				echo "<div class='col'>
							<div class='card'>
								<img src='images/word_doc_logo.png' class='' alt='logo'>
								<div class='card-body'>
									<p class='card-text' style='font-size: 13px;'>$fileName</p>
									<button type='button' class='btn btn-info btn-sm' >$doc_code</button>
								</div>
							</div>
						</div>";
			}

			echo "</div>";
		} else
		{
			//Display when no record found;
		}

		echo "</div>";

		?>
		<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
			integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
			crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
			integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
			crossorigin="anonymous"></script>
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
	</body>

	</html>

<?php } else
{
	header("Location: /thesis-mgmt/login.php");
} ?>