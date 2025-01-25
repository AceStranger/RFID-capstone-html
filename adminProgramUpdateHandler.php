<?php
session_start();
include "dbh.php";

// Verify if user is authenticated
if (!isset($_SESSION['user_ID'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Validate POST data
$program_id = filter_input(INPUT_POST, 'program_id', FILTER_SANITIZE_NUMBER_INT);
$program_name = filter_input(INPUT_POST, 'program_name', FILTER_SANITIZE_STRING);
$department_name = filter_input(INPUT_POST, 'department_name', FILTER_SANITIZE_STRING);
$program_level = filter_input(INPUT_POST, 'program_level', FILTER_SANITIZE_STRING);
$year_grade_level = filter_input(INPUT_POST, 'year_grade_level', FILTER_SANITIZE_STRING);
$section = filter_input(INPUT_POST, 'section', FILTER_SANITIZE_STRING);

if (!$program_id || !$program_name || !$department_name || !$program_level || !$year_grade_level || !$section) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

// Get department_id for the department name
$deptQuery = "SELECT department_id FROM department WHERE department_name = ?";
$stmt = $conn->prepare($deptQuery);
$stmt->bind_param("s", $department_name);
$stmt->execute();
$deptResult = $stmt->get_result();
$department = $deptResult->fetch_assoc();
$department_id = $department['department_id'] ?? null;

if (!$department_id) {
    echo json_encode(['success' => false, 'message' => 'Department not found']);
    exit();
}

// Update the program
$updateQuery = "UPDATE program SET program_name = ?, department_id = ?, program_level = ?, program_year_grade_level = ?, section = ? WHERE program_id = ?";
$stmt = $conn->prepare($updateQuery);
$stmt->bind_param("sisssi", $program_name, $department_id, $program_level, $year_grade_level, $section, $program_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
