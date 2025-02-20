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

$query = "SELECT * FROM officer WHERE user_id = $user_ID";
$result = mysqli_query($conn, $query);
$officerInfo = $result->fetch_assoc();

$officerInfoObject = json_encode($officerInfo);

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
    <?php include "titleIcon.php" ;?>
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
                    <button id="print-btn">Print</button>
                </div>
                <input type="hidden" name="event-id" id="event-id" value="<?php echo $eventID;?>">
                <input type="hidden" name="event-participant" id="event-participant" value="<?php echo $eventParticipants;?>">
                <div class="event-paticipant-attendance-data-content-main-content">
                    <div class="event-paticipant-attendance-data-contents">
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
            const officerInfoObject = <?php echo $officerInfoObject;?>;

            document.getElementById('print-btn').addEventListener('click', () => {
                const printWindow = window.open(' ', '_blank', 'width=800,height=600');
                const printSection = document.querySelector(".event-paticipant-attendance-data-contents");
                const eventInfoContainer = document.querySelector(".event-info");
                const programTables = document.querySelectorAll(".program-table");

                printWindow.document.write('<html><head><title>Event Participants</title>');
                printWindow.document.write('<style>');
                printWindow.document.write(`
                    body {
                        font-family: Arial, sans-serif;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    th, td {
                        border: 1px solid #000;
                        padding: 8px;
                        text-align: left;
                    }
                    th {
                        background-color: #f2f2f2;
                    }
                    h1, h3 {
                        text-align: center;
                    }
                    .program-table {
                        page-break-after: always;
                    }
                    .program-table:first-of-type {
                        page-break-after: auto;
                    }
                `);
                printWindow.document.write('</style>');
                printWindow.document.write('</head><body>');
                printWindow.document.write('<h1>Event Participants</h1>');

                // Include event info at the top of each page
                programTables.forEach((table, index) => {
                    if (index > 0) {
                        printWindow.document.write('<div style="page-break-before: always;"></div>');
                    }
                    printWindow.document.write(eventInfoContainer.outerHTML);
                    printWindow.document.write(table.outerHTML);
                });

                printWindow.document.write('</body></html>');
                printWindow.document.close();

                printWindow.print();
            });


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
                            
                            displayParticipantAttendance(data.event_participant, data.event_participant.attendance, eventInfoObject, officerInfoObject, data.event_participant.students);
                        } else {
                            console.error("Error fetching data:", data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            async function displayParticipantAttendance(participants, attendance, eventInfo, officerInfoObject, studentsList) {
                const attendanceContainer = document.querySelector('.event-paticipant-attendance-data-contents');
                attendanceContainer.innerHTML = ''; // Clear previous content
                const amTimeInStart = eventInfo.attendance_duration_am_time_in_start;
                const amTimeInEnd = eventInfo.attendance_duration_am_time_in_end;
                const amTimeOutStart = eventInfo.attendance_duration_am_time_out_start;
                const amTimeOutEnd = eventInfo.attendance_duration_am_time_out_end;
                

                const pmTimeInStart = eventInfo.attendance_duration_pm_time_in_start;
                const pmTimeInEnd = eventInfo.attendance_duration_pm_time_in_end;
                const pmTimeOutStart = eventInfo.attendance_duration_pm_time_out_start;
                const pmTimeOutEnd = eventInfo.attendance_duration_pm_time_out_end;




                // Extract and format event time details
                const formatTime = (timeString) => {
                    const date = new Date(`1970-01-01T${timeString}`);
                    const hours = date.getHours();
                    const minutes = date.getMinutes();
                    const period = hours >= 12 ? 'PM' : 'AM';
                    const formattedHours = hours % 12 || 12;
                    const formattedMinutes = minutes < 10 ? `0${minutes}` : minutes;
                    return `${formattedHours}:${formattedMinutes} ${period}`;
                };

                const eventTimeStart = formatTime(eventInfo.event_time_start);
                const eventTimeEnd = formatTime(eventInfo.event_time_end);

                // Fetch the organization name
                const organizationName = await fetchOrganizationName(eventInfo.event_organizer);

                // Create the event information section
                const eventInfoHTML = `
                    <div class="event-info">
                        <h3>${eventInfo.event_name}</h3>
                        <p><strong>Place:</strong> ${eventInfo.event_place}</p>
                        <p><strong>Date:</strong> ${eventInfo.event_date}</p>
                        <p><strong>Time:</strong> ${eventTimeStart} - ${eventTimeEnd}</p>
                        <p><strong>Duration:</strong> ${eventInfo.event_time_duration}</p>
                        <p><strong>Organizer:</strong> ${organizationName}</p>
                    </div>
                `;
                attendanceContainer.innerHTML = eventInfoHTML + attendanceContainer.innerHTML; 


                // Define the program levels we are interested in
                const programLevels = ["College", "Secondary", "Primary"];
                let programLevelFound = false; // Flag to track if a program level is found

                // Get the value of the event-participant input field
                const eventParticipantValue = document.getElementById("event-participant").value;

                // Check if any of the program levels are in the event-participant value
                programLevelFound = programLevels.some(level => eventParticipantValue.includes(level));

                // Ensure participants object has the expected structure
                if (!participants || typeof participants.participant !== 'object') {
                    console.error("Invalid participants structure:", participants);
                    return;
                }
                participants = participants.participant;
                const filteredParticipants = processOfficerResponsibilities(
                    participants,
                    officerInfoObject.officer_responsibility
                );
                
                participants = filteredParticipants;
                for (const programLevel of programLevels) {
                    const levelParticipants = participants[programLevel];
                    if (!levelParticipants) {
                        continue; // Skip this iteration and move to the next program level
                    }
                    
                    programLevelFound = true; // Mark that we found a program level
                    const programLevelSection = document.createElement('div');
                    programLevelSection.innerHTML = `<h3>${programLevel}</h3>`;
                    attendanceContainer.appendChild(programLevelSection);
                    
                    // Iterate over each program under the program level
                    for (const [programName, years] of Object.entries(levelParticipants)) {
                        const table = document.createElement('table');
                        table.classList.add('program-table');

                        // Determine which columns to show based on event time duration
                        let showAMTimeIn = false;
                        let showAMTimeOut = false;
                        let showPMTimeIn = false;
                        let showPMTimeOut = false;

                        if (eventInfo.event_time_duration.includes("Whole Day")) {
                            if (eventInfo.event_time_duration.includes("AM Time In")) showAMTimeIn = true;
                            if (eventInfo.event_time_duration.includes("AM Time Out")) showAMTimeOut = true;
                            if (eventInfo.event_time_duration.includes("PM Time In")) showPMTimeIn = true;
                            if (eventInfo.event_time_duration.includes("PM Time Out")) showPMTimeOut = true;
                        } else if (eventInfo.event_time_duration.includes("Half Day")) {
                            if (eventInfo.event_time_duration.includes("AM")) {
                                showAMTimeIn = true;
                                showAMTimeOut = true;
                            } 
                            else if (eventInfo.event_time_duration.includes("PM")) {
                                showPMTimeIn = true;
                                showPMTimeOut = true;
                            }
                        }

                        const headerRow = document.createElement('tr');
                        headerRow.innerHTML = `
                            <th>Student Name</th>
                            <th>Program Name</th>
                            <th>Year/Grade Level</th>
                            <th>Section</th>
                            ${showAMTimeIn ? `<th>AM Time In</th>` : ''}
                            ${showAMTimeOut ? `<th>AM Time Out</th>` : ''}
                            ${showPMTimeIn ? `<th>PM Time In</th>` : ''}
                            ${showPMTimeOut ? `<th>PM Time Out</th>` : ''}
                        `;

                        // Create the program name header row
                        const programNameHR = document.createElement('tr');
                        programNameHR.innerHTML = `
                            <th colspan="${4 + 
                            (showAMTimeIn ? 1 : 0) + 
                            (showAMTimeOut ? 1 : 0) + 
                            (showPMTimeIn ? 1 : 0) + 
                            (showPMTimeOut ? 1 : 0)}">
                                ${programName}
                            </th>
                        `;
                        table.appendChild(programNameHR);
                        table.appendChild(headerRow);
                        programLevelSection.appendChild(table);
                        for (const [yearLevel, sections] of Object.entries(years)) {
                            for (const [section, students] of Object.entries(sections)) {
                                // Ensure `students` is an array
                                if (!Array.isArray(students)) {
                                    console.error("Expected students to be an array but found:", students);
                                    continue; // Skip this section if `students` is not an array
                                }
                                
                                // Iterate over each student in the array
                                for (const student of students) {
                                    const attendanceRecord = attendance.find(a => a.user_id === student.user_id) || {};
                                    const studentName = fetchStudentName(student.user_id, studentsList);
                                

                                    const row = document.createElement('tr');
                                    row.innerHTML = `
                                        <td>${studentName}</td>
                                        <td>${programName}</td>
                                        <td>${yearLevel}</td>
                                        <td>${section}</td>
                                        ${showAMTimeIn ? `<td>${formatTimeAndCheckAttendance(attendanceRecord.attendance_am_time_in, amTimeInStart, amTimeInEnd) || '~'}</td>` : ''}
                                        ${showAMTimeOut ? `<td>${formatTimeAndCheckAttendance(attendanceRecord.attendance_am_time_out, amTimeOutStart, amTimeOutEnd) || '~'}</td>` : ''}
                                        ${showPMTimeIn ? `<td>${formatTimeAndCheckAttendance(attendanceRecord.attendance_pm_time_in, pmTimeInStart, pmTimeInEnd) || '~'}</td>` : ''}
                                        ${showPMTimeOut ? `<td>${formatTimeAndCheckAttendance(attendanceRecord.attendance_pm_time_out, pmTimeOutStart, pmTimeOutEnd) || '~'}</td>` : ''}
                                    `;
                                    table.appendChild(row);
                                }
                            }
                        }


                    }
                }
                if (!programLevelFound) {
                    const noProgramLevelSection = document.createElement('div');
                    noProgramLevelSection.innerHTML = `<h3>Programs</h3>`;
                    attendanceContainer.appendChild(noProgramLevelSection);

                    // Iterate over the programs and create a table
                    for (const [programName, years] of Object.entries(participants)) {
                        const table = document.createElement('table');
                        table.classList.add('program-table');
                        
                        // Determine which columns to show based on event time duration
                        let showAMTimeIn = false;
                        let showAMTimeOut = false;
                        let showPMTimeIn = false;
                        let showPMTimeOut = false;

                        if (eventInfo.event_time_duration.includes("Whole Day")) {
                            if (eventInfo.event_time_duration.includes("AM Time In")) showAMTimeIn = true;
                            if (eventInfo.event_time_duration.includes("AM Time Out")) showAMTimeOut = true;
                            if (eventInfo.event_time_duration.includes("PM Time In")) showPMTimeIn = true;
                            if (eventInfo.event_time_duration.includes("PM Time Out")) showPMTimeOut = true;
                        } else if (eventInfo.event_time_duration.includes("Half Day")) {
                            if (eventInfo.event_time_duration.includes("AM")) {
                                showAMTimeIn = true;
                                showAMTimeOut = true;
                            } else if (eventInfo.event_time_duration.includes("PM")) {
                                showPMTimeIn = true;
                                showPMTimeOut = true;
                            }
                        }

                        const headerRow = document.createElement('tr');
                        headerRow.innerHTML = `
                            <th>Student Name</th>
                            <th>Program Name</th>
                            <th>Year/Grade Level</th>
                            <th>Section</th>
                            ${showAMTimeIn ? `<th>AM Time In</th>` : ''}
                            ${showAMTimeOut ? `<th>AM Time Out</th>` : ''}
                            ${showPMTimeIn ? `<th>PM Time In</th>` : ''}
                            ${showPMTimeOut ? `<th>PM Time Out</th>` : ''}
                        `;

                        // Create the program name header row
                        const programNameHR = document.createElement('tr');
                        programNameHR.innerHTML = `
                            <th colspan="${4 + 
                            (showAMTimeIn ? 1 : 0) + 
                            (showAMTimeOut ? 1 : 0) + 
                            (showPMTimeIn ? 1 : 0) + 
                            (showPMTimeOut ? 1 : 0)}">
                                ${programName}
                            </th>
                        `;
                        table.appendChild(programNameHR);
                        table.appendChild(headerRow);
                        noProgramLevelSection.appendChild(table);

                        for (const [yearLevel, sections] of Object.entries(years)) {
                            for (const [section, students] of Object.entries(sections)) {
                                // Ensure `students` is an array
                                if (!Array.isArray(students)) {
                                    console.error("Expected students to be an array but found:", students);
                                    continue; // Skip this section if `students` is not an array
                                }

                                // Iterate over each student in the array
                                for (const student of students) {
                                    const attendanceRecord = attendance.find(a => a.user_id === student.user_id) || {};
                                    const studentName = fetchStudentName(student.user_id, studentsList);

                                    const row = document.createElement('tr');
                                    row.innerHTML = `
                                        <td>${studentName}</td>
                                        <td>${programName}</td>
                                        <td>${yearLevel}</td>
                                        <td>${section}</td>
                                        ${showAMTimeIn ? `<td>${formatTimeAndCheckAttendance(attendanceRecord.attendance_am_time_in, amTimeInStart, amTimeInEnd) || '~'}</td>` : ''}
                                        ${showAMTimeOut ? `<td>${formatTimeAndCheckAttendance(attendanceRecord.attendance_am_time_out, amTimeOutStart, amTimeOutEnd) || '~'}</td>` : ''}
                                        ${showPMTimeIn ? `<td>${formatTimeAndCheckAttendance(attendanceRecord.attendance_pm_time_in, pmTimeInStart, pmTimeInEnd) || '~'}</td>` : ''}
                                        ${showPMTimeOut ? `<td>${formatTimeAndCheckAttendance(attendanceRecord.attendance_pm_time_out, pmTimeOutStart, pmTimeOutEnd) || '~'}</td>` : ''}
                                    `;
                                    table.appendChild(row);
                                }
                            }
                        }

                    }
                }
            }

            function processOfficerResponsibilities(participants, officerResponsibility) {
                const responsibilities = officerResponsibility.split(",");
                const programLevels = ["College", "Secondary", "Primary"];
                const filteredParticipants = {}; // New object to store filtered data
                

                responsibilities.forEach((responsibility) => {
                    const parts = responsibility.trim().split("-");
                    const firstPart = parts[0].toLowerCase().trim();
                    if (parts.length === 2 && firstPart === "all") {
                        // Case for program level (e.g., "All - College")
                        const secondPart = parts[1].trim();
                        
                        if (programLevels.includes(secondPart)) {
                            // It is a program level, include all the participants under that program level
                            filteredParticipants[secondPart] = participants[secondPart] || {};
                        } else {
                            // It's a program name, include all the participants under that program name
                            filteredParticipants[secondPart] = participants[secondPart] || {};
                        }
                    } else if (parts.length === 2) {
                        // Case for program name with year level (e.g., "BSIT - 1st YEAR")
                        const programName = parts[0].trim();
                        const yearGradeLevel = parts[1].trim();

                        if (participants["College"] && participants["College"][programName]) {
                            if (!filteredParticipants["College"]) {
                                filteredParticipants["College"] = {};
                            }
                            if (!filteredParticipants["College"][programName]) {
                                filteredParticipants["College"][programName] = {};
                            }
                            if (yearGradeLevel && participants["College"][programName][yearGradeLevel]) {
                                filteredParticipants["College"][programName][yearGradeLevel] =
                                    participants["College"][programName][yearGradeLevel];
                            }
                        }
                    } else if (parts.length === 3) {
                        // Case for full structure (program, year, section)
                        const programName = parts[0]?.trim();
                        const yearGradeLevel = parts[1]?.trim();
                        const section = parts[2]?.trim();

                        if (participants["College"] && participants["College"][programName] && participants["College"][programName][yearGradeLevel]) {
                            if (!filteredParticipants["College"]) {
                                filteredParticipants["College"] = {};
                            }
                            if (!filteredParticipants["College"][programName]) {
                                filteredParticipants["College"][programName] = {};
                            }
                            if (!filteredParticipants["College"][programName][yearGradeLevel]) {
                                filteredParticipants["College"][programName][yearGradeLevel] = {};
                            }

                            filteredParticipants["College"][programName][yearGradeLevel][section] =
                                participants["College"][programName][yearGradeLevel][section];
                        }
                    }
                });

                return filteredParticipants;
            }

   

            function fetchStudentName(user_id, students) {
                
                const student = students.find(s => s.user_id === user_id);
                
                if (student) {
                    return student.firstname + ' ' + student.middlename + ' ' + student.lastname + ' ' + student.suffixname;
                } else {
                    return "Unknown Student";
                }
            }


            async function fetchOrganizationName(organizerId) {
                try {
                    const response = await fetch(`getOrganizationName.php`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ organizerId: organizerId })
                    });

                    const data = await response.json();
                    if (response.ok) {
                        
                        return data.organization_name || 'Unknown Organization';
                    } else {
                        console.error("Error fetching organization:", data.message);
                        return 'Unknown Organization';
                    }
                } catch (error) {
                    console.error("Fetch error:", error);
                    return 'Unknown Organization';
                }
            }

            // Function to format time from 24-hour to 12-hour format and check if it's attended or not
            function formatTimeAndCheckAttendance(time, timeStart, timeEnd) {
                if (!time) return null;
                
                // Check if the time falls within the attendance range
                const [hours, minutes] = time.split(':').map(Number);
                const currentTime = hours * 60 + minutes; // Convert to total minutes
                const [startHours, startMinutes] = timeStart.split(':').map(Number);
                const [endHours, endMinutes] = timeEnd.split(':').map(Number);
                const startTime = startHours * 60 + startMinutes;
                const endTime = endHours * 60 + endMinutes;

                if (currentTime < startTime || currentTime > endTime) {
                    return '~'; // Not within attendance range
                }
                const period = hours >= 12 ? 'PM' : 'AM';
                const formattedHours = hours % 12 || 12; // Convert to 12-hour format
                return `${formattedHours}:${minutes.toString().padStart(2, '0')} ${period}`;
            }
        });
    </script>

</body>
</html>