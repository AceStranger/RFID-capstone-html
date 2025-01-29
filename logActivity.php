<?php
include 'dbh.php';

function logActivity($action_type, $entity, $entity_id, $user_id, $description, $conn) {
    // Sanitize inputs
    $action_type = mysqli_real_escape_string($conn, $action_type);
    $entity = mysqli_real_escape_string($conn, $entity);
    $entity_id = (int) $entity_id;
    $user_id = (int) $user_id;
    $description = mysqli_real_escape_string($conn, $description);

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO activity_logs (action_type, entity, entity_id, user_id, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('ssiss', $action_type, $entity, $entity_id, $user_id, $description);

    // Execute the query
    if ($stmt->execute()) {
        return true;  // Success
    } else {
        return false; // Error
    }

    $stmt->close();
}
?>
