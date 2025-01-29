<?php
// Include the database connection
include 'dbh.php'; // Make sure to include your db connection file

// Get the raw POST data from the frontend
$data = json_decode(file_get_contents("php://input"));

// Check if the program_id is set and not empty
if (isset($data->program_id) && !empty($data->program_id)) {
    $programId = $data->program_id;

    // Prepare the DELETE SQL query
    $sql = "DELETE FROM program WHERE program_id = ?";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind the program_id parameter
        $stmt->bind_param("i", $programId);

        // Execute the query
        if ($stmt->execute()) {
            // Check if any rows were affected
            if ($stmt->affected_rows > 0) {
                // Successfully deleted the program
                echo json_encode(['success' => true, 'message' => 'Program deleted successfully!']);
            } else {
                // No program found with the provided id
                echo json_encode(['success' => false, 'message' => 'No program found with the provided ID.']);
            }
        } else {
            // Error executing the query
            echo json_encode(['success' => false, 'message' => 'Error deleting the program. Please try again.']);
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        // Error preparing the query
        echo json_encode(['success' => false, 'message' => 'Error preparing the query.']);
    }
} else {
    // If program_id is missing or invalid
    echo json_encode(['success' => false, 'message' => 'Invalid program ID.']);
}

// Close the database connection
$conn->close();
?>
