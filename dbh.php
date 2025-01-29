<?php 
$localhost = "localhost";
$username = "root";
$password = "";
$dbname = 'rfid';
// Create connection
// Use a try-catch block for better error handling
try {
    $conn = new mysqli($localhost, $username, $password, $dbname);
    // Set the charset
    $conn->set_charset("utf8mb4");

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    // Log the error
    error_log($e->getMessage());

    // Show a generic message to the user
    echo "Connection error. Please try again later.";
}
