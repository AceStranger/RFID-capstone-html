<?php 
$localhost = "localhost";
$username = "root";
$password = "";
$dbname = 'rfid';
// Create connection
$conn = new mysqli($localhost, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}