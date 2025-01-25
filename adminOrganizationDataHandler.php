<?php

include "dbh.php";


session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['vSubmit']) || isset($_POST['cancelSubmit']) || isset($_POST['cSubmit'])) {
        $_SESSION['organization-id'] = $_POST['organization-id'];
        $_SESSION['request'] = 'view-organization';
        header("Location: adminOrganizationData.php");
        exit();
    } elseif (isset($_POST['eSubmit'])) {
        $_SESSION['organization-id'] = $_POST['organization-id'];
        $_SESSION['request'] = 'edit-organization';
        header("Location: adminOrganizationData.php");
        exit();
    } elseif (isset($_POST['dSubmit'])) {
        $_SESSION['organization-id'] = $_POST['organization-id'];
        $_SESSION['request'] = 'delete-organization';
        header("Location: adminOrganizationData.php");
        exit();
    } elseif (isset($_POST['uSubmit'])) {
        // Handle update organization request
        $organizationID = $_POST['organization-id'];
        $organizationName = mysqli_real_escape_string($conn, $_POST['organizationName']);
        $organizationResponsibility = mysqli_real_escape_string($conn, $_POST['organizationResponsibilty']);

        $updateQuery = "UPDATE organization 
                        SET organization_name = '$organizationName', 
                            organization_responsibility = '$organizationResponsibility' 
                        WHERE organization_id = $organizationID";

        if (mysqli_query($conn, $updateQuery)) {
            $_SESSION['organization-id'] = $organizationID;
            $_SESSION['request'] = 'view-organization';
            header("Location: adminOrganizationData.php");
            exit();
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    }
    // Check if "Add New Event" button was clicked
    if (isset($_POST['add-new-event-submit'])) {
        $user_ID = @$_SESSION['user_ID'];
        $query = "SELECT * FROM officer WHERE user_id = $user_ID";
        $result = mysqli_query($conn, $query);
        $officerInfo = $result->fetch_assoc();
        $organizationID = $officerInfo['organization_id'];


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
        $eventDescription = mysqli_real_escape_string($conn, $_POST['event-description']);
        
        // Handle participants
        $participants = mysqli_real_escape_string($conn, $_POST['event-participants']);
        
        // Handle optional time fields
        $amTimeIn = mysqli_real_escape_string($conn, $_POST['am-time-in']);
        $pmTimeIn = mysqli_real_escape_string($conn, $_POST['pm-time-in']);
        $amTimeInStart = mysqli_real_escape_string($conn, $_POST['am-time-in-start']);
        $pmTimeInStart = mysqli_real_escape_string($conn, $_POST['pm-time-in-start']);
        $amTimeInEnd = mysqli_real_escape_string($conn, $_POST['am-time-in-end']);
        $pmTimeInEnd = mysqli_real_escape_string($conn, $_POST['pm-time-in-end']);
        $amTimeOut = mysqli_real_escape_string($conn, $_POST['am-time-out']);
        $pmTimeOut = mysqli_real_escape_string($conn, $_POST['pm-time-out']);
        $amTimeOutStart = mysqli_real_escape_string($conn, $_POST['am-time-out-start']);
        $pmTimeOutStart = mysqli_real_escape_string($conn, $_POST['pm-time-out-start']);
        $amTimeOutEnd = mysqli_real_escape_string($conn, $_POST['am-time-out-end']);
        $pmTimeOutEnd = mysqli_real_escape_string($conn, $_POST['pm-time-out-end']);

        // Handle file upload for event image
        $eventImage = '';
        if (isset($_FILES['event-image']) && $_FILES['event-image']['error'] == 0) {
            $imagePath = "uploads/$organizationID/";
            $eventImage = $imagePath . basename($_FILES['event-image']['name']);
            move_uploaded_file($_FILES['event-image']['tmp_name'], $eventImage);
        }

        // Insert query
        $insertQuery = "INSERT INTO event (
            event_name, event_date, event_time_duration, event_place, event_time_start, event_time_end, event_description, event_bg_picture, event_participant, event_organizer,
            attendance_am_time_in, attendance_am_time_out, attendance_pm_time_in, attendance_pm_time_out,
            attendance_duration_am_time_in_start, attendance_duration_am_time_in_end, 
            attendance_duration_am_time_out_start, attendance_duration_am_time_out_end,
            attendance_duration_pm_time_in_start, attendance_duration_pm_time_in_end,
            attendance_duration_pm_time_out_start, attendance_duration_pm_time_out_end
        ) VALUES (
            '$eventName', '$eventDate', '$eventTimeDuration', '$eventLocation', '$eventTimeStart', '$eventTimeEnd', '$eventDescription', '$eventImage', '$participants', '$organizationID',
            '$amTimeIn', '$amTimeOut', '$pmTimeIn', '$pmTimeOut', 
            '$amTimeInStart', '$amTimeInEnd', 
            '$amTimeOutStart', '$amTimeOutEnd',
            '$pmTimeInStart', '$pmTimeInEnd',
            '$pmTimeOutStart', '$pmTimeOutEnd'
        )";

        if (mysqli_query($conn, $insertQuery)) {
            echo "New event added successfully!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['add-new-event-submit-cancel'])) {
        // Redirect to a specific page (e.g., event list or dashboard) when "Cancel" is clicked
        header("Location: adminEventPage.php");  // Replace 'eventListPage.php' with your target page
        exit();
    }
}






?>