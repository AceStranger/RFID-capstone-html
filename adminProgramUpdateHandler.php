<?php
session_start();
include "dbh.php";

// Verify if user is authenticated
if (!isset($_SESSION['user_ID'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Validate POST data
$program_id = filter_input(INPUT_POST, 'program_id', FILTER_VALIDATE_INT);
$program_name = $_POST['program_name'] ?? null; // Allow raw input but validate or sanitize manually later if needed
$department_id = filter_input(INPUT_POST, 'department', FILTER_VALIDATE_INT);
$program_level = $_POST['program_level'] ?? null; // Allow raw input for text fields
$year_grade_level = $_POST['year_grade_level'] ?? null;
$section = $_POST['section'] ?? null;
error_log($program_id." ".$program_name." ".$department_id." ".$program_level." ".$year_grade_level." ".$section);
// Validate required inputs
if (!$program_id || !$program_name || $department_id === null || !$program_level || !$year_grade_level || !$section) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
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
