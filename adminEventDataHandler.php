<?php

include "dbh.php";
include "updateEventStatus.php";

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
    } elseif (isset($_POST['dSubmit'])) {
        $_SESSION['event-id'] = $_POST['event-id'];
        $_SESSION['request'] = 'delete-event';
        header("Location: adminEventData.php");
        exit();
    }
    // Check if "Add New Event" button was clicked
    if (isset($_POST['add-new-event-submit'])) {

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
            header("Location: adminEventPage.php");
        } else {
            header("Location: adminEventPage.php"); 
        }
        exit();
    } elseif (isset($_POST['add-new-event-submit-cancel'])) {
        header("Location: adminEventPage.php"); 
        exit();
    } elseif (isset($_POST['uSubmit'])) {
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

        // Update query
        $updateQuery = "UPDATE event SET
            event_name = '$eventName',
            event_participant = '$eventParticipant',
            event_place = '$eventPlace',
            event_date = '$eventDate',
            event_status = '$eventStatus',
            event_time_start = '$eventTimeStart',
            event_time_end = '$eventTimeEnd',
            penalty = '$penalty'
            WHERE event_id = '$eventID'";

        if (mysqli_query($conn, $updateQuery)) {
            header("Location: adminEventPage.php");
            exit();
        } else {
            echo "Error updating event: " . mysqli_error($conn);
        }
    }
}

?>
