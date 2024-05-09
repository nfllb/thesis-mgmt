// upload.js
function uploadFile() {
	// Create a file input element
	var fileInput = document.createElement("input");
	fileInput.type = "file";
	fileInput.style.display = "none";
	document.body.appendChild(fileInput);

	// Trigger click event on the file input element
	fileInput.click();

	// When a file is selected, submit the form
	fileInput.addEventListener(
		"change",
		function () {
			var file = fileInput.files[0];
			var formData = new FormData();
			formData.append("file", file);

			// Create and send AJAX request
			var xhr = new XMLHttpRequest();
			xhr.open(
				"POST",
				"/thesis-mgmt/add-new-document.php",
				true
			); // Specify the URL of the PHP script here
			xhr.onload = function () {
				if (xhr.status === 200) {
					// Upload successful
					showToast(
						"Upload successful",
						"success"
					);
				} else {
					// Upload error
					showToast(
						"Error uploading file",
						"error"
					);
				}
			};
			xhr.send(formData);
		}
	);
}

function showToast(message, type) {
	// Create toaster element
	var toaster = document.createElement("div");
	toaster.className = "toaster " + type;
	toaster.textContent = message;

	// Set position and size
	toaster.style.position = "fixed";
	toaster.style.top = "20px";
	toaster.style.right = "20px";
	toaster.style.width = "300px";
	toaster.style.padding = "15px";
	toaster.style.borderRadius = "10px";
	toaster.style.zIndex = "9999";

	// Append toaster to the body
	document.body.appendChild(toaster);

	// Display the toaster
	toaster.style.display = "block";

	// Fade out and remove the toaster after 3 seconds
	setTimeout(function () {
		toaster.style.opacity = "0";
		setTimeout(function () {
			document.body.removeChild(toaster);
		}, 3000);
	}, 3000);
}
