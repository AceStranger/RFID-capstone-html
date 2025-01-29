<?php
include "dbh.php";

// Check if the user_id is passed via GET request
if (isset($_GET['user_id'])) {
// getActivityLogs.php
    session_start();
    if (!isset($_SESSION['user_ID']) || $_SESSION['user_ID'] !== $user_id) {
        echo json_encode(['error' => 'Unauthorized access']);
        exit;
    }
    
    // Query to fetch activity logs for the specified user
    $activityQuery = "SELECT * FROM activity_logs WHERE user_id = $user_id ORDER BY timestamp DESC";
    $activityResult = mysqli_query($conn, $activityQuery);

    // Fetch the results as an associative array
    $activityLogs = [];
    while ($log = mysqli_fetch_assoc($activityResult)) {
        $activityLogs[] = $log;
    }

    // Return the activity logs as JSON
    echo json_encode($activityLogs);
} else {
    echo json_encode(['error' => 'No user ID provided']);
}
?>
