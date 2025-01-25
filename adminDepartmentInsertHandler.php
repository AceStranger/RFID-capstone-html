<?php
header('Content-Type: application/json');

// Database connection
require_once 'db_connection.php';

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input
    $department_name = mysqli_real_escape_string($conn, $_POST['department_name'] ?? '');

    // Validate input
    if (empty($department_name)) {
        $response['success'] = false;
        $response['message'] = 'Department name is required.';
        echo json_encode($response);
        exit;
    }

    // Check if department already exists
    $checkQuery = "SELECT department_id FROM department WHERE department_name = '$department_name'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $response['success'] = false;
        $response['message'] = 'Department already exists.';
        echo json_encode($response);
        exit;
    }

    // Insert department into the database
    $insertQuery = "INSERT INTO department (department_name) VALUES ('$department_name')";
    if (mysqli_query($conn, $insertQuery)) {
        $response['success'] = true;
        $response['message'] = 'Department added successfully!';
    } else {
        $response['success'] = false;
        $response['message'] = 'Database error: ' . mysqli_error($conn);
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
