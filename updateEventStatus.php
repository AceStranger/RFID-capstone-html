<?php
date_default_timezone_set('Asia/Manila');
function updateEventStatus($conn) {
    // Get the current date and time
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');

    // SQL query to get all events
    $query = "SELECT event_id, event_date, event_time_start, event_time_end FROM event";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $eventId = $row['event_id'];
            $eventDate = $row['event_date'];
            $eventTimeStart = $row['event_time_start'];
            $eventTimeEnd = $row['event_time_end'];

            $eventStatus = 0; // Default to "incoming"

            if ($eventDate > $currentDate || ($eventDate == $currentDate && $eventTimeStart > $currentTime)) {
                // Event is "incoming"
                $eventStatus = 0;
            } elseif ($eventDate == $currentDate && $eventTimeStart <= $currentTime && $currentTime <= $eventTimeEnd) {
                // Event is "ongoing"
                $eventStatus = 1;
            } elseif ($eventDate < $currentDate || ($eventDate == $currentDate && $currentTime > $eventTimeEnd)) {
                // Event is "ended"
                $eventStatus = 2;
            }

            // Update the event status in the database
            $updateQuery = "UPDATE event SET event_status = ? WHERE event_id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("ii", $eventStatus, $eventId);
            $stmt->execute();
        }
    }
}
