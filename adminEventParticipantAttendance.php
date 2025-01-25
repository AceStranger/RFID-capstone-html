<?php
session_start();
include "dbh.php";
include "updateEventStatus.php";

if(@!isset($_SESSION['user_ID']) || $_SESSION['user_ID'] === null) {
    header("Location: LogInPage.html");
    exit();
}
if(@!isset($_SESSION['event-id'])){
    header("Location: adminReportPage.php");
}
$user_ID = @$_SESSION['user_ID'];
$query = "SELECT * FROM user WHERE user_id = $user_ID";
$result = mysqli_query($conn, $query);
$userInfo = $result->fetch_assoc();

updateEventStatus($conn);

$eventID = $_SESSION['event-id'];
$query = "SELECT * FROM event WHERE event_id = $eventID";
$result = mysqli_query($conn, $query);
$eventInfo = $result->fetch_assoc();
$eventParticipants = $eventInfo['event_participant'];

$eventInfoObject = json_encode($eventInfo);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Participants</title>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminEventParticipantAttendance.css">
    <script src="js\adminSidebar.js"></script>
</head>
<body>
    <div class="main-content-container">
        <?php include_once"adminSidebar.php";?>
        <div class="main-content">
            <div class="event-paticipant-attendance-data-content">
                <div class="event-paticipant-attendance-data-content-top-part">
                    <h1 class="event-paticipant-attendance-data-content-head-text">EVENT PARTICIPANT</h1>
                </div>
                <div class="event-paticipant-attendance-data-content-main-content">
                    <div class="event-paticipant-attendance-data-contents">
                        <input type="hidden" name="event-id" id="event-id" value="<?php echo $eventID;?>">
                        <input type="hidden" name="event-participant" id="event-participant" value="<?php echo $eventParticipants;?>">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const eventId = document.getElementById('event-id').value;
            const eventParticipant = document.getElementById('event-participant').value;
            const eventInfoObject = <?php echo $eventInfoObject;?>;

            if (eventId && eventParticipant) {
                fetch('fetchParticipantData.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ event_id: eventId, event_participant: eventParticipant })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log(data);
                            
                            displayParticipantAttendance(data.event_participant, data.event_participant.attendance, eventInfoObject);
                        } else {
                            console.error("Error fetching data:", data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Function to display participants and attendance
async function displayParticipantAttendance(participants, attendance, eventInfo) {
    const attendanceContainer = document.querySelector('.event-paticipant-attendance-data-contents');
    attendanceContainer.innerHTML = ''; // Clear previous content

    // Build a table dynamically
    const table = document.createElement('table');

    // Table header
    const headerRow = document.createElement('tr');
    headerRow.innerHTML = `
        <th>Student Name</th>
        <th>Program Name</th>
        <th>Year/Grade Level</th>
        <th>Section</th>
        ${eventInfo.event_time_duration.includes("AM") ? `
            <th>AM Time In</th>
            <th>AM Time Out</th>
        ` : ''}
        ${eventInfo.event_time_duration.includes("PM") ? `
            <th>PM Time In</th>
            <th>PM Time Out</th>
        ` : ''}
    `;
    table.appendChild(headerRow);

    // Iterate over participants by program level, program name, and year/section
    for (const [programLevel, programs] of Object.entries(participants)) {
        for (const [programName, years] of Object.entries(programs)) {
            for (const [yearLevel, sections] of Object.entries(years)) {
                for (const [section, students] of Object.entries(sections)) {
                    // Ensure students is an array (if not, skip this section)
                    if (!Array.isArray(students)) {
                        // console.error(`Expected an array for section "${section}", but got:`, students);
                        continue;
                    }

                    // Fetch student names and attendance data for each student
                    for (const student of students) {
                        const user_id = student.user_id;
                        const attendanceRecord = attendance.find(a => a.user_id === user_id) || {};

                        // Fetch student name (first and last) from user table
                        const studentName = await fetchStudentName(user_id);

                        // Check for the event's time duration and create the proper table columns
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${studentName}</td>
                            <td>${programName}</td>
                            <td>${yearLevel}</td>
                            <td>${section}</td>
                            ${eventInfo.event_time_duration.includes("AM") ? `
                                <td>${formatTime(attendanceRecord.attendance_am_time_in) || '~'}</td>
                                <td>${formatTime(attendanceRecord.attendance_am_time_out) || '~'}</td>
                            ` : ''}
                            ${eventInfo.event_time_duration.includes("PM") ? `
                                <td>${formatTime(attendanceRecord.attendance_pm_time_in) || '~'}</td>
                                <td>${formatTime(attendanceRecord.attendance_pm_time_out) || '~'}</td>
                            ` : ''}
                        `;
                        table.appendChild(row);
                    }
                }
            }
        }
    }
    attendanceContainer.appendChild(table);
}

// Function to fetch student name from the user table (using user_id)
async function fetchStudentName(user_id) {
    try {
        const response = await fetch(`getUserName.php?user_id=${user_id}`);
        const data = await response.json();
        return data.user_firstname + ' ' + data.user_lastname;
    } catch (error) {
        console.error("Error fetching student name:", error);
        return "Unknown Student";
    }
}
            // Function to format time from 24-hour to 12-hour format
            function formatTime(time) {
                if (!time) return null;
                const [hours, minutes] = time.split(':').map(Number);
                const period = hours >= 12 ? 'PM' : 'AM';
                const formattedHours = hours % 12 || 12; // Convert to 12-hour format
                return `${formattedHours}:${minutes.toString().padStart(2, '0')} ${period}`;
            }
        });
    </script>

</body>
</html>