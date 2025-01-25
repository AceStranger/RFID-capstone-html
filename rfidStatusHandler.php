<?php
session_start();
header('Content-Type: application/json');

// Initialize variables for the scanning and assigning status
$scanningStatus = 'scanning'; // This would be updated based on actual progress
$assigningStatus = 'failed'; // Update based on the outcome of RFID tag assignment

// You could replace the above static values with database or session values, for example:
if (isset($_SESSION['scanning_status'])) {
    $scanningStatus = $_SESSION['scanning_status'];
}
if (isset($_SESSION['assigning_status'])) {
    $assigningStatus = $_SESSION['assigning_status'];
}

// Return the current statuses in JSON format
echo json_encode([
    'scanning_status' => $scanningStatus,
    'assigning_status' => $assigningStatus
]);
?>
