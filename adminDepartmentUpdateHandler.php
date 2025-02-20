<?php
include "dbh.php";
require_once 'logActivity.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department_id = filter_input(INPUT_POST, 'department_id', FILTER_SANITIZE_NUMBER_INT);
    $department_name = filter_input(INPUT_POST, 'department_name', FILTER_SANITIZE_STRING);

    if (!$department_id || !$department_name) {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        exit;
    }

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE department SET department_name = ? WHERE department_id = ?");
    $stmt->bind_param("si", $department_name, $department_id);

    if ($stmt->execute()) {
        // Log the activity after successfully updating the department
        $action_type = 'Update Department';
        $entity = 'Department';
        $entity_id = $department_id;
        $user_id = $_SESSION['user_ID']; // Assuming user ID is stored in session
        $description = "Department '$department_name' with ID '$department_id' updated by user '$user_id'.";
        logActivity($action_type, $entity, $entity_id, $user_id, $description, $conn); // Log activity
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update department.']);
    }

    $stmt->close();
    $conn->close();
}
?>
