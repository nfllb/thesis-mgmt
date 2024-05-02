<?php
// Get the file path from the URL parameter
$filePath = $_GET['file'];

// Send the file to the client for download
header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
header("Content-Disposition: attachment; filename=" . basename($filePath));
header("Content-Length: " . filesize($filePath));
readfile($filePath);

// Delete the temporary file
unlink($filePath);
?>