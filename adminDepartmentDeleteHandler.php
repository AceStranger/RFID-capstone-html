<?php
session_start();
include "dbh.php";

$data = json_decode(file_get_contents("php://input"));
$department_id = $data->department_id;

if (!$department_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid department ID.']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM department WHERE department_id = ?");
$stmt->bind_param("i", $department_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Department deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete department.']);
}

$stmt->close();
$conn->close();
?>
