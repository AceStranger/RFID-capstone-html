<?php
// Database connection
include 'dbh.php'; // Replace with your actual database connection file

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and decode the JSON payload
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['organization_id'])) {
        $organizationId = $data['organization_id'];
        $userID = $data['userID'];

        // Fetch events
        $query_events = "
            SELECT * 
            FROM event
            WHERE event_organizer = ?";
        $stmt_events = $conn->prepare($query_events);
        $stmt_events->bind_param('s', $organizationId);
        $stmt_events->execute();
        $result_events = $stmt_events->get_result();

        // Initialize an array to store event data
        $events = [];
        while ($row = $result_events->fetch_assoc()) {
            $event_id = $row['event_id'];
            $events[$event_id] = [
                'event_id' => $row['event_id'],
                'event_name' => $row['event_name'],
                'event_date' => $row['event_date'],
                'event_time_duration' => $row['event_time_duration'],
                'event_time_start' => $row['event_time_start'],
                'event_time_end' => $row['event_time_end'],
                'event_place' => $row['event_place'],
                'event_description' => $row['event_description'],
                'event_status' => $row['event_status'],
                'event_post' => $row['event_post'],
                'event_organizer' => $row['event_organizer'],
                'event_participant' => $row['event_participant'],
                'penalty' => $row['penalty'],
                'attendance_duration_am_time_in_start' => $row['attendance_duration_am_time_in_start'],
                'attendance_duration_am_time_in_end' => $row['attendance_duration_am_time_in_end'],
                'attendance_duration_am_time_out_start' => $row['attendance_duration_am_time_out_start'],
                'attendance_duration_am_time_out_end' => $row['attendance_duration_am_time_out_end'],
                'attendance_duration_pm_time_in_start' => $row['attendance_duration_pm_time_in_start'],
                'attendance_duration_pm_time_in_end' => $row['attendance_duration_pm_time_in_end'],
                'attendance_duration_pm_time_out_start' => $row['attendance_duration_pm_time_out_start'],
                'attendance_duration_pm_time_out_end' => $row['attendance_duration_pm_time_out_end'],
                'attendance' => [] // Placeholder for attendance data
            ];
        }

        // Fetch attendance data for each event
        foreach ($events as $event_id => &$event) {
            $query_attendance = "SELECT * 
                FROM attendance 
                WHERE event_id = ? and user_id = ? LIMIT 1";
            $stmt_attendance = $conn->prepare($query_attendance);
            $stmt_attendance->bind_param('ss', $event_id, $userID);
            $stmt_attendance->execute();
            $result_attendance = $stmt_attendance->get_result();

            // Store attendance data for the current event
            while ($attendance_row = $result_attendance->fetch_assoc()) {
                $event['attendance'][] = [
                    'attendance_id' => $attendance_row['attendance_id'],
                    'user_id' => $attendance_row['user_id'],
                    'attendance_date' => $attendance_row['attendance_date'],
                    'attendance_am_time_in' => $attendance_row['attendance_am_time_in'],
                    'attendance_am_time_out' => $attendance_row['attendance_am_time_out'],
                    'attendance_pm_time_in' => $attendance_row['attendance_pm_time_in'],
                    'attendance_pm_time_out' => $attendance_row['attendance_pm_time_out']
                ];
            }

            // Close the attendance statement
            $stmt_attendance->close();
        }

        // Close the event statement
        $stmt_events->close();

        // Return the data as JSON
        echo json_encode(array_values($events)); // Return the events array as JSON
    } else {
        // Invalid input
        http_response_code(400);
        echo json_encode(['error' => 'Missing organization_id']);
    }
} else {
    // Invalid request method
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method']);
}
?>
