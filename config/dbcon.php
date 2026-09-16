<?php
// Connection
// Enable error reporting (Disable this in production)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$servername = "localhost";
$username = "YOUR_DB_USERNAME";
$password = "YOUR_DB_PASSWORD";
$dbname = "YOUR_DB_NAME";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);


// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);  // Log the error
    die("Database connection error. Please try again later."); // Generic message for the user
}

// Set the charset to avoid charset issues
$conn->set_charset("utf8mb4");

?>