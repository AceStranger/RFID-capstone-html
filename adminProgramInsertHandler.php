<?php
header('Content-Type: application/json');

// Database connection
require_once 'dbh.php';

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input data
    $program_name = mysqli_real_escape_string($conn, $_POST['program_name'] ?? '');
    $department_name = mysqli_real_escape_string($conn, $_POST['department_name'] ?? '');
    $program_level = mysqli_real_escape_string($conn, $_POST['program_level'] ?? '');
    $year_grade_level = mysqli_real_escape_string($conn, $_POST['year_grade_level'] ?? '');
    $section = mysqli_real_escape_string($conn, $_POST['section'] ?? '');

    // Validate required fields
    if (empty($program_name) || empty($department_name) || empty($program_level) || empty($year_grade_level) || empty($section)) {
        $response['success'] = false;
        $response['message'] = 'All fields are required.';
        echo json_encode($response);
        exit;
    }

    // Retrieve department ID based on department name
    $departmentQuery = "SELECT department_id FROM department WHERE department_name = '$department_name'";
    $departmentResult = mysqli_query($conn, $departmentQuery);

    if (mysqli_num_rows($departmentResult) > 0) {
        $departmentRow = mysqli_fetch_assoc($departmentResult);
        $department_id = $departmentRow['department_id'];
    } else {
        // If department doesn't exist, return an error
        $response['success'] = false;
        $response['message'] = 'Department does not exist.';
        echo json_encode($response);
        exit;
    }

    // Insert program data into the database
    $insertQuery = "INSERT INTO program (program_name, department_id, program_level, program_year_grade_level, section)
                    VALUES ('$program_name', '$department_id', '$program_level', '$year_grade_level', '$section')";

    if (mysqli_query($conn, $insertQuery)) {
        $response['success'] = true;
        $response['message'] = 'Program added successfully!';
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
