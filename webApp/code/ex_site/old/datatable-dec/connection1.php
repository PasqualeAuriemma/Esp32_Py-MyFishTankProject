<?php
$conn = new mysqli("localhost","root","","my_myfishtank");

// Check connection
if ($conn -> connect_errno) {
  echo "Failed to connect to MySQL: " . $conn -> connect_error;
  exit();
}

?>