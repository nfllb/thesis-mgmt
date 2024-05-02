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
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
			integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
		<link rel="stylesheet" href="/thesis-mgmt/css/styles.css">
		<style>
			#logo {
				height: 100px;
				width: 100px;
			}

			.col {
				display: flex;
				justify-content: center;
			}

			.card {
				display: flex;
				flex-direction: column;
				background-color: #f4ede8;
			}

			.card-body {
				flex-grow: 1;
			}
		</style>
		<title>Reports</title>
	</head>

	<body class="content">
		<div>
			<h3 style="position:absolute;margin-top:20px;">Reports</h3>
			<?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/header.php"); ?>
			<?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/sidebar.php"); ?>
			<hr>
		</div>

		<div class='container'>
			<?php
			$sqlGetFiles = "SELECT * FROM files";
			$result = mysqli_query($con, $sqlGetFiles);

			if (mysqli_num_rows($result) > 0)
			{ ?>
				<div class="row row-cols-1 row-cols-md-3 g-4">
					<?php
					while ($file = mysqli_fetch_assoc($result))
					{
						$fileName = $file['filename'];
						$doc_code = $file['DocumentCode'];
						?>
						<div class="col">
							<div class="card">
								<div class="text-center">
									<img id="logo" src="/thesis-mgmt/images/word_doc_logo.png" alt="logo">
								</div>
								<div class="card-body">
									<p class="card-text" style="font-size: 13px;"><?php echo $fileName; ?></p>
									<button id="gotoreport" value="<?php echo $doc_code; ?>" type="button"
										class="btn btn-primary btn-sm"><?php echo $doc_code; ?></button>
								</div>
							</div>
						</div>

						<?php
					}
					echo "</div>";
			} else
			{
				//Display when no record found;
			}
			?>

			</div>

			<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
			<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
				integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
				crossorigin="anonymous"></script>
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
				integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
				crossorigin="anonymous"></script>

			<script>
				$(document).on('click', '#gotoreport', function (e) {
					e.preventDefault();
					var doc_code = $(this).val();
					var phpFile = doc_code + '.php';

					$.ajax({
						type: "GET",
						url: phpFile,
						contentType: false,
						cache: false,
						processData: false,
						success: function (data) {
							window.location.href = phpFile;
						}
					});

				});
			</script>
	</body>

	</html>

<?php } else
{
	header("Location: /thesis-mgmt/login.php");
} ?>