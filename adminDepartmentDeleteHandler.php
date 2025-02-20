<?php
session_start();
include "dbh.php";
require_once 'logActivity.php';

$data = json_decode(file_get_contents("php://input"));
$department_id = $data->department_id;

if (!$department_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid department ID.']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM department WHERE department_id = ?");
$stmt->bind_param("i", $department_id);

if ($stmt->execute()) {
    // Log the activity after successfully deleting the department
    $action_type = 'Delete Department';
    $entity = 'Department';
    $entity_id = $department_id;
    $user_id = $_SESSION['user_ID']; // Assuming user ID is stored in session
    $description = "Department with ID '$department_id' deleted by user '$user_id'.";
    logActivity($action_type, $entity, $entity_id, $user_id, $description, $conn); // Log activity
    
    echo json_encode(['success' => true, 'message' => 'Department deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete department.']);
}

$stmt->close();
$conn->close();
?>
