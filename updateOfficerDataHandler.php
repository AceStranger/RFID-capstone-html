<?php
// Assuming you have a database connection already set up
include 'dbh.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the POST data
    $officerID = $_POST['officer-id'];
    $officerDuty = $_POST['officer-duty'];
    $officerResponsibility = $_POST['officer-responsibility'];
    $officerAssignDuty = $_POST['officer-assign-duty'];

    // Prepare an update query
    $query = "UPDATE officer SET 
                officer_duty = ?, 
                officer_responsibility = ?, 
                officer_assign_duty = ? 
            WHERE officer_id = ?";

    // Prepare statement
    $stmt = $conn->prepare($query);

    // Bind parameters
    $stmt->bind_param('sssi', 
        $officerDuty, 
        $officerResponsibility, 
        $officerAssignDuty, 
        $officerID
    );

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Data updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update data.']);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
