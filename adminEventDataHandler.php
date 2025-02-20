<?php

include "dbh.php";
include "updateEventStatus.php";
include "logActivity.php";
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['vSubmit']) || isset($_POST['cancelSubmit'])) {
        $_SESSION['event-id'] = $_POST['event-id'];
        $_SESSION['request'] = 'view-event';
        header("Location: adminEventData.php");
        exit();
    } elseif (isset($_POST['eSubmit'])) {
        $_SESSION['event-id'] = $_POST['event-id'];
        $_SESSION['request'] = 'edit-event';
        header("Location: adminEventData.php");
        exit();
    } elseif (isset($_POST['dSubmit']) && isset($_POST['event-id'])) {
        echo "post recieved ";
        // Delete event logic
        $eventID = mysqli_real_escape_string($conn, $_POST['event-id']);
        // Delete event query
        $deleteQuery = "DELETE FROM event WHERE event_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $eventID);
        if ($stmt->execute()) {
            // Log the activity
            $action_type = 'Delete Event';
            $entity = 'Event';
            $entity_id = $eventID;
            $user_id = $_SESSION['user_ID']; // Get the logged-in user ID
            $description = "Event with ID '$eventID' deleted by the user.";
            logActivity($action_type, $entity, $entity_id, $user_id, $description, $conn);

            // Redirect back to the admin event page
            header("Location: adminEventPage.php?status=success&message=Event deleted successfully");
            exit();
        } else {
            // Handle query failure
            header("Location: adminEventPage.php?status=error&message=Failed to delete event");
            exit();
        }
    } elseif (isset($_POST['add-new-event-submit'])) {

        $organizationID = mysqli_real_escape_string($conn, $_POST['eventOrganizer']);

        // Get the officer's organization
        $orgQuery = "SELECT * FROM organization WHERE organization_id = $organizationID";
        $orgResult = mysqli_query($conn, $orgQuery);
        $organization = $orgResult->fetch_assoc();
        $officerOrganization = $organization['organization_name'];
        
        // Collect form data
        $eventName = mysqli_real_escape_string($conn, $_POST['event_name']);
        $eventDate = mysqli_real_escape_string($conn, $_POST['event_date']);
        $eventLocation = mysqli_real_escape_string($conn, $_POST['event_location']);
        $eventTimeDuration = mysqli_real_escape_string($conn, $_POST['event-time-duration']);
        $eventTimeStart = mysqli_real_escape_string($conn, $_POST['event-time-start']);
        $eventTimeEnd = mysqli_real_escape_string($conn, $_POST['event-time-end']);
        
        // Handle participants
        $participants = mysqli_real_escape_string($conn, $_POST['event-participants']);
        
        // Handle optional time fields
        $amTimeInStart = mysqli_real_escape_string($conn, $_POST['am-time-in-start']);
        $pmTimeInStart = mysqli_real_escape_string($conn, $_POST['pm-time-in-start']);
        $amTimeInEnd = mysqli_real_escape_string($conn, $_POST['am-time-in-end']);
        $pmTimeInEnd = mysqli_real_escape_string($conn, $_POST['pm-time-in-end']);
        $amTimeOutStart = mysqli_real_escape_string($conn, $_POST['am-time-out-start']);
        $pmTimeOutStart = mysqli_real_escape_string($conn, $_POST['pm-time-out-start']);
        $amTimeOutEnd = mysqli_real_escape_string($conn, $_POST['am-time-out-end']);
        $pmTimeOutEnd = mysqli_real_escape_string($conn, $_POST['pm-time-out-end']);


        // Determine event_status
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');

        if ($currentDate == $eventDate && $currentTime >= $eventTimeStart && $currentTime <= $eventTimeEnd) {
            $eventStatus = 1; // Event is ongoing
        } elseif ($currentDate > $eventDate || ($currentDate == $eventDate && $currentTime > $eventTimeEnd)) {
            $eventStatus = 2; // Event has ended
        } else {
            $eventStatus = 0; // Event is in the future (default)
        }

        // Insert query
        $insertQuery = "INSERT INTO event (
            event_name, event_date, event_time_duration, event_place, event_time_start, event_time_end, event_participant, event_organizer,
            attendance_duration_am_time_in_start, attendance_duration_am_time_in_end, 
            attendance_duration_am_time_out_start, attendance_duration_am_time_out_end,
            attendance_duration_pm_time_in_start, attendance_duration_pm_time_in_end,
            attendance_duration_pm_time_out_start, attendance_duration_pm_time_out_end,
            event_status
        ) VALUES (
            '$eventName', '$eventDate', '$eventTimeDuration', '$eventLocation', '$eventTimeStart', '$eventTimeEnd', '$participants', '$organizationID',
            '$amTimeInStart', '$amTimeInEnd', 
            '$amTimeOutStart', '$amTimeOutEnd',
            '$pmTimeInStart', '$pmTimeInEnd',
            '$pmTimeOutStart', '$pmTimeOutEnd',
            '$eventStatus'
        )";

        if (mysqli_query($conn, $insertQuery)) {
            // Log the activity
            $action_type = 'Create Event';
            $entity = 'Event';
            $entity_id = mysqli_insert_id($conn);  // Get the ID of the newly inserted event
            $user_id = $_SESSION['user_ID'];  // Get the logged-in user ID
            $description = "New event '$eventName' created by the user.";
            logActivity($action_type, $entity, $entity_id, $user_id, $description, $conn);
            header("Location: adminEventPage.php");
        } else {
            header("Location: adminEventPage.php"); 
        }
        exit();
    } elseif (isset($_POST['add-new-event-submit-cancel'])) {
        header("Location: adminEventPage.php"); 
        exit();
    }elseif (isset($_POST['uSubmit'])) {
        // Collect the form data for updating
        $eventID = mysqli_real_escape_string($conn, $_POST['event-id']);
        $eventName = mysqli_real_escape_string($conn, $_POST['eventName']);
        $eventParticipant = mysqli_real_escape_string($conn, $_POST['eventParticipant']);
        $penalty = mysqli_real_escape_string($conn, $_POST['penalty']);
        $eventPlace = mysqli_real_escape_string($conn, $_POST['eventPlace']);
        $eventDate = mysqli_real_escape_string($conn, $_POST['eventDate']);
        $eventStatus = mysqli_real_escape_string($conn, $_POST['eventStatus']);
        $eventTimeStart = mysqli_real_escape_string($conn, $_POST['eventTimeStart']);
        $eventTimeEnd = mysqli_real_escape_string($conn, $_POST['eventTimeEnd']);
    
        // Initialize update fields
        $updateFields = [
            "event_name = '$eventName'",
            "event_participant = '$eventParticipant'",
            "event_place = '$eventPlace'",
            "event_date = '$eventDate'",
            "event_status = '$eventStatus'",
            "event_time_start = '$eventTimeStart'",
            "event_time_end = '$eventTimeEnd'",
            "penalty = '$penalty'"
        ];
    
        // Append attendance times only if they exist in the form submission
        if (isset($_POST['attendance_duration_am_time_in_start'])) {
            $amTimeInStart = mysqli_real_escape_string($conn, $_POST['attendance_duration_am_time_in_start']);
            $updateFields[] = "attendance_duration_am_time_in_start = '$amTimeInStart'";
        }
        if (isset($_POST['attendance_duration_am_time_in_end'])) {
            $amTimeInEnd = mysqli_real_escape_string($conn, $_POST['attendance_duration_am_time_in_end']);
            $updateFields[] = "attendance_duration_am_time_in_end = '$amTimeInEnd'";
        }
        if (isset($_POST['attendance_duration_am_time_out_start'])) {
            $amTimeOutStart = mysqli_real_escape_string($conn, $_POST['attendance_duration_am_time_out_start']);
            $updateFields[] = "attendance_duration_am_time_out_start = '$amTimeOutStart'";
        }
        if (isset($_POST['attendance_duration_am_time_out_end'])) {
            $amTimeOutEnd = mysqli_real_escape_string($conn, $_POST['attendance_duration_am_time_out_end']);
            $updateFields[] = "attendance_duration_am_time_out_end = '$amTimeOutEnd'";
        }
        if (isset($_POST['attendance_duration_pm_time_in_start'])) {
            $pmTimeInStart = mysqli_real_escape_string($conn, $_POST['attendance_duration_pm_time_in_start']);
            $updateFields[] = "attendance_duration_pm_time_in_start = '$pmTimeInStart'";
        }
        if (isset($_POST['attendance_duration_pm_time_in_end'])) {
            $pmTimeInEnd = mysqli_real_escape_string($conn, $_POST['attendance_duration_pm_time_in_end']);
            $updateFields[] = "attendance_duration_pm_time_in_end = '$pmTimeInEnd'";
        }
        if (isset($_POST['attendance_duration_pm_time_out_start'])) {
            $pmTimeOutStart = mysqli_real_escape_string($conn, $_POST['attendance_duration_pm_time_out_start']);
            $updateFields[] = "attendance_duration_pm_time_out_start = '$pmTimeOutStart'";
        }
        if (isset($_POST['attendance_duration_pm_time_out_end'])) {
            $pmTimeOutEnd = mysqli_real_escape_string($conn, $_POST['attendance_duration_pm_time_out_end']);
            $updateFields[] = "attendance_duration_pm_time_out_end = '$pmTimeOutEnd'";
        }
    
        // Construct update query dynamically
        $updateQuery = "UPDATE event SET " . implode(", ", $updateFields) . " WHERE event_id = '$eventID'";
    
        if (mysqli_query($conn, $updateQuery)) {
            // Log the activity
            $action_type = 'Update Event';
            $entity = 'Event';
            $entity_id = $eventID;
            $user_id = $_SESSION['user_ID'];  // Get the logged-in user ID
            $description = "Event '$eventName' updated by the user.";
            logActivity($action_type, $entity, $entity_id, $user_id, $description, $conn);  // Log the activity
    
            header("Location: adminEventPage.php");
            exit();
        } else {
            error_log ("Error updating event: " . mysqli_error($conn));

        }
    }
    
}

?>
