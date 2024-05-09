<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");
if (isset($_SESSION['username']) && isset($_SESSION['userid']))
{
	?>

	<!DOCTYPE html>
	<html>

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Reports</title>

		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
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

			/* Custom card styling */
			.card {
				transition: transform 0.3s ease;
				border: none;
				background-color: #f2f1ee;
				/* Lightened card background color */
				border-radius: 10px;
				/* Add border radius for rounded corners */
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
				/* Add a subtle shadow */
				max-width: 300px;
				/* Set maximum width to avoid stretching */
				margin: 0 auto;
				/* Center the card horizontally */
			}

			.card:hover {
				transform: translateY(-5px);
			}

			.card-body {
				padding: 1rem;
			}

			.card-text {
				margin-bottom: 0;
				/* Remove bottom margin for the file name */
				color: #343a40;
				/* Set text color for file name */
				text-align: center;
				/* Center the file name */
			}

			.card-footer {
				padding: 0.75rem 1rem;
				/* Add padding to the card footer */
			}

			.btn-primary {
				background-color: #e89e67;
				/* Orange button background color */
				border-color: #e89e67;
				/* Orange button border color */
			}

			.btn-primary:hover {
				background-color: #d17d3a;
				/* Darker shade of orange for hover background */
				border-color: #d17d3a;
				/* Darker shade of orange for hover border */
			}
		</style>
	</head>

	<body class="content">
		<header>
			<h3 style="position:absolute;margin-top:20px;">Reports</h3>
			<?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/header.php"); ?>
			<?php include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/sidebar.php"); ?>
			<hr>
		</header>

		<div class='container'>
			<?php
			if (isset($_SESSION['role']) && $_SESSION['role'] == 'Research Coordinator')
			{
				$sqlGetFiles = "SELECT * FROM files WHERE Type = 'Report'";
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
								<div class="card shadow">
									<div class="card-body">
										<p class="card-text" style="font-size: 0.9rem;"><?php echo $fileName; ?></p>
										<!-- Adjust font-size for the file name -->
									</div>
									<div class="card-footer d-flex justify-content-center">
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
				<?php
			} else
			{
				echo "<div class='container'>
        <div id='thesisContainer' class='card w-100 mb-3'>
            <div class='card-body'>
                <div style='font-size:16px;'class='alert alert-danger' role='alert>
                    <span class='icon'><i style='font-size:18px;' class='fa-regular fa-circle-xmark'></i></span>
                    You don't have access to this page. Contact your research coordinator for help.
                </div>
            </div>
        </div>
        </div>";
			}
			?>

			<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
			<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>

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