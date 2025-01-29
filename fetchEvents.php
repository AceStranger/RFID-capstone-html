<?php
require_once 'dbh.php'; // Ensure your database connection is included

// Fetch all events from the database
$query = "SELECT event.*, organization.organization_name 
          FROM event
          LEFT JOIN organization ON event.event_organizer = organization.organization_id
          ORDER BY `date_added` DESC";

$result = mysqli_query($conn, $query);

// Prepare the response array
$events = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $eventDate = date("m/d/Y", strtotime($row['event_date']));
        $eventTime = date("h:i A", strtotime($row['event_time_start'])) . ' - ' . date("h:i A", strtotime($row['event_time_end']));

        // Map status to text
        $statusText = '';
        switch ($row['event_status']) {
            case 0:
                $statusText = 'Incoming';
                break;
            case 1:
                $statusText = 'Ongoing';
                break;
            case 2:
                $statusText = 'End';
                break;
        }

        $events[] = [
            'event_id' => $row['event_id'],
            'event_name' => $row['event_name'],
            'event_place' => $row['event_place'],
            'event_status' => $statusText,
            'event_date' => $eventDate,
            'event_time' => $eventTime,
            'organization_name' => $row['organization_name']
        ];
    }
}

// Return the events as JSON
header('Content-Type: application/json');
echo json_encode($events);
?>
