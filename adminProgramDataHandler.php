<?php
session_start();
include "dbh.php";

header('Content-Type: application/json');

// Check if user is authenticated
if (!isset($_SESSION['user_ID'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Handle delete request
$data = json_decode(file_get_contents("php://input"), true);
if (isset($data['program_id'])) {
    $program_id = filter_var($data['program_id'], FILTER_SANITIZE_NUMBER_INT);

    if ($program_id) {
        $deleteQuery = "DELETE FROM program WHERE program_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $program_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Program deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete program']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid program ID']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Program ID not provided']);
}
?>
