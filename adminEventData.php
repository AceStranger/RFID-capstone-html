<?php
session_start();
include "dbh.php";
date_default_timezone_set('Asia/Manila');
include "updateEventStatus.php";
updateEventStatus($conn);
if(@!isset($_SESSION['user_ID']) || $_SESSION['user_ID'] === null) {
    header("Location: LogInPage.html");
    exit();
}
$user_ID = @$_SESSION['user_ID'];
$query = "SELECT * FROM user WHERE user_id = $user_ID";
$result = mysqli_query($conn, $query);
$userInfo = $result->fetch_assoc();

$officerInfo="";
$officerDuty="";
$officerResponsibility="";
$officerAssignDuty = "";

if(str_contains($userInfo['user_role'], "officer")) {
    $query = "SELECT * FROM officer WHERE user_id = $user_ID LIMIT 1";
    $result = mysqli_query($conn, $query);
    $officerInfo = $result->fetch_assoc();
    $officerDuty = strtoupper($officerInfo['officer_duty']);
    $officerResponsibility = $officerInfo['officer_responsibility'];
    $officerAssignDuty = $officerInfo['officer_assign_duty'];
}



if (isset($_SESSION['event-id']) && isset($_SESSION['request'])) {
    $eventID = $_SESSION['event-id'];
    $request = $_SESSION['request'];
} else {
    die("Event ID or request type is missing.");
}

$query = "SELECT * FROM event WHERE event_id = $eventID";
$result = mysqli_query($conn, $query);
$eventInfo = $result->fetch_assoc();

$eventName = htmlspecialchars($eventInfo["event_name"]);
$eventDate = htmlspecialchars($eventInfo["event_date"]);
$eventTimeStart = htmlspecialchars(string: $eventInfo["event_time_start"]);
$eventTimeEnd = htmlspecialchars($eventInfo["event_time_end"]);
$eventPlace = htmlspecialchars($eventInfo["event_place"]);
$eventStatus = htmlspecialchars($eventInfo["event_status"]);
$eventOrganizer = htmlspecialchars($eventInfo["event_organizer"]);
$eventParticipant = htmlspecialchars($eventInfo["event_participant"]);
$eventPenalty = htmlspecialchars($eventInfo["penalty"]);
$eventTimeDuration = htmlspecialchars($eventInfo["event_time_duration"]);

// Determine the current time
$currentTime = date('H:i:s');

// Default value for selected time
$selectedTime = 'None';

// Retrieve and sanitize event time ranges
$amTimeInStart = htmlspecialchars($eventInfo["attendance_duration_am_time_in_start"]);
$amTimeInEnd = htmlspecialchars($eventInfo["attendance_duration_am_time_in_end"]);
$amTimeOutStart = htmlspecialchars($eventInfo["attendance_duration_am_time_out_start"]);
$amTimeOutEnd = htmlspecialchars($eventInfo["attendance_duration_am_time_out_end"]);
$pmTimeInStart = htmlspecialchars($eventInfo["attendance_duration_pm_time_in_start"]);
$pmTimeInEnd = htmlspecialchars($eventInfo["attendance_duration_pm_time_in_end"]);
$pmTimeOutStart = htmlspecialchars($eventInfo["attendance_duration_pm_time_out_start"]);
$pmTimeOutEnd = htmlspecialchars($eventInfo["attendance_duration_pm_time_out_end"]);

// Ensure all times are in the same format (HH:MM:SS)
$currentTime = date('H:i:s', strtotime($currentTime));

// Check time ranges for AM
if ($currentTime >= $amTimeInStart && $currentTime <= $amTimeInEnd) {
    $selectedTime = 'amTimeIn';
} elseif ($currentTime >= $amTimeOutStart && $currentTime <= $amTimeOutEnd) {
    $selectedTime = 'amTimeOut';
}
// Check time ranges for PM
elseif ($currentTime >= $pmTimeInStart && $currentTime <= $pmTimeInEnd) {
    $selectedTime = 'pmTimeIn';
} elseif ($currentTime >= $pmTimeOutStart && $currentTime <= $pmTimeOutEnd) {
    $selectedTime = 'pmTimeOut';
}


$startBtn = '';


$query = "SELECT organization_name FROM organization WHERE organization_id = $eventOrganizer";
$result = mysqli_query($conn, $query);
$organizationInfo = $result->fetch_assoc();
$organizationName = $organizationInfo["organization_name"];


$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

if (
    $officerAssignDuty === "Manage Event Attendance" &&
    $currentDate === $eventDate && (
        ($currentTime >= $amTimeInStart && $currentTime <= $amTimeInEnd) ||
        ($currentTime >= $amTimeOutStart && $currentTime <= $amTimeOutEnd) ||
        ($currentTime >= $pmTimeInStart && $currentTime <= $pmTimeInEnd) ||
        ($currentTime >= $pmTimeOutStart && $currentTime <= $pmTimeOutEnd)
    )
) {
    $startBtn = '
        <button type="button" id="event-start" name="event-start" class="event-start">START</button>
    ';
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event</title>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminEventData.css">
    <script src="js\adminSidebar.js"></script>
</head>
<body>
    <div class="main-content-container">
        <?php include_once"adminSidebar.php";?>
        <div class="main-content">
            <div class="event-data-content">

                <div class="event-data-content-top-part">
                    <h1 class="event-data-content-head-text">EVENT</h1>
                    <!-- <button type="button" id="event-start" name="event-start" class="event-start">START</button> -->
                    <?php 
                    
                    
                        echo  $startBtn;
                    ?>
                </div>
                
                <div class="event-data-content-main-content">
                    <div class="event-data-content-info-content">
                        <?php

                            $inputAvailability = $request === "view-event" ? "disabled" : "";

                            // Define buttons based on the request type
                            $btn = '';
                            if(str_contains($officerAssignDuty, "Manage Event Registration")) {

                                if ($request === "view-event") {
                                    $btn = '
                                        <div class="event-data-content-btn">
                                            <button type="submit" name="eSubmit" class="event-data-btn-edit">EDIT</button>
                                            <button type="submit" name="dSubmit" class="user-delete">DELETE</button>
                                            <script>
                                                document.addEventListener("click", function (event) {
                                                    if (event.target.classList.contains("user-delete")) {
                                                        event.preventDefault(); // Prevent form submission immediately
                                                        
                                                        const confirmation = confirm("Are you sure you want to delete this event?");
                                                        
                                                        if (confirmation) {
                                                            // Find the form that contains the delete button
                                                            const form = event.target.closest("form");

                                                            // Create a hidden input to pass the value of dSubmit if not already present
                                                            if (form && !form.querySelector("input[name=dSubmit]")) {
                                                                const input = document.createElement("input");
                                                                input.type = "hidden";
                                                                input.name = "dSubmit";
                                                                input.value = "1"; // Set the value for dSubmit
                                                                form.appendChild(input);
                                                            }
                                                            form.submit();
                                                        }
                                                    }
                                                });


                                            </script>
                                        </div>';
                                } elseif ($request === "edit-event") {
                                    $btn = '
                                        <div class="event-data-content-btn">
                                            <button type="submit" name="uSubmit" class="user-update">UPDATE</button>
                                            <button type="submit" name="cancelSubmit" class="user-cancel">CANCEL</button>
                                        </div>';
                                }
                            }
                        ?>
                        <form action='adminEventDataHandler.php' method='POST' class='event-data-content-info-form-content'>
                            <div class='event-data-content-info-col'>
                                <input type='hidden' name='event-id' value='<?php echo $eventID; ?>'>
                                <div class='event-data-content-info-row'>
                                    <label for='eventName'>Event Name:</label>
                                    <input type='text' name='eventName' id='eventName' value='<?php echo $eventName; ?>' <?php echo $inputAvailability; ?>>
                                </div>
                                <div class='event-data-content-info-row'>
                                    <label for='eventOrganizer'>Event Organizer:</label>
                                    <input type='text' name='eventOrganizer' id='eventOrganizer' value='<?php echo $organizationName; ?>' disabled>
                                </div>
                                <div class='event-data-content-info-row'>
                                    <label for='eventParticipant'>Event Participant:</label>
                                    <input type='text' name='eventParticipant' id='eventParticipant' value='<?php echo $eventParticipant; ?>' <?php echo $inputAvailability; ?>>
                                </div>
                                <div class='event-data-content-info-row'>
                                    <label for='penalty'>Penalty:</label>
                                    <input type='text' name='penalty' id='penalty' value='<?php echo $eventPenalty; ?>' <?php echo $inputAvailability; ?>>
                                </div>
                                <?php echo $btn; ?>
                            </div>
                            <div class='event-data-content-info-col'>
                                <div class='event-data-content-info-row'>
                                    <label for='eventPlace'>Event Location:</label>
                                    <input type='text' name='eventPlace' id='eventPlace' value='<?php echo $eventPlace; ?>' <?php echo $inputAvailability; ?>>
                                </div>
                                <div class='event-data-content-info-row'>
                                    <label for='eventDate'>Date:</label>
                                    <input type='date' name='eventDate' id='eventDate' value='<?php echo $eventDate; ?>' <?php echo $inputAvailability; ?>>
                                </div>
                                <div class='event-data-content-info-row'>
                                    <label for='eventStatus'>Status:</label>
                                    <select name='eventStatus' id='eventStatus' disabled>
                                        <option value="0" <?php echo ($eventStatus == 0) ? 'selected' : ''; ?>>Incoming</option>
                                        <option value="1" <?php echo ($eventStatus == 1) ? 'selected' : ''; ?>>Ongoing</option>
                                        <option value="2" <?php echo ($eventStatus == 2) ? 'selected' : ''; ?>>End</option>
                                    </select>
                                </div>

                                <div class='event-data-content-info-row'>
                                    <label for='eventTimeStart'>Time In:</label>
                                    <input type='time' name='eventTimeStart' id='eventTimeStart' value='<?php echo $eventTimeStart; ?>' <?php echo $inputAvailability; ?>>
                                </div>
                                <div class='event-data-content-info-row'>
                                    <label for='eventTimeEnd'>Time Out:</label>
                                    <input type='time' name='eventTimeEnd' id='eventTimeEnd' value='<?php echo $eventTimeEnd; ?>' <?php echo $inputAvailability; ?>>
                                </div>
                            </div>
                        </form>

                    </div>

                    <div class='scanned-content-container'>
                        <div class='scanned-content'>
                            <form action='adminAttendanceHandler.php' class='attendance-handler-form' method='post'>
                                <div class='scanned-content-info-col'>
                                    <!-- Hidden Fields -->
                                    <input type='hidden' name='eventID' value='<?php echo $eventID;?>'>
                                    <input type='hidden' name='rfid-tag' id='rfid-tag' value=''>

                                    <div class='scanned-content-info-row'>
                                        <div class='scanned-content-info-row'>
                                            <label for='user-school-id'>School ID:</label>
                                            <input type='text' name='user-school-id' id='user-school-id' value='' placeholder='Enter User Info'>
                                        </div>
                                        <div class='scanned-content-info-row'>
                                            <label for='user-name'>Name:</label>
                                            <input type='hidden' name='user-id' id='user-id' value=''>
                                            <input type='text' name='user-name' id='user-name' value='' placeholder='Enter User Info'>
                                        </div> 
                                    </div>
                                    <div class='scanned-content-info-row'>
                                        <div class='scanned-content-info-row'>
                                            <label for='user-profile-image'>Profile Picture:</label>
                                            <img id='user-profile-image' src='' alt='User Profile'>
                                        </div>
                                        <!-- Time Select Dropdown -->
                                        <div class='scanned-content-info-row'>
                                            <label for='timeSelect'>Select Time:</label>
                                            <select name='timeSelect' id='timeSelect' required>
                                                <option value='None' <?php echo ($selectedTime == 'None' ? 'selected' : ''); ?>>None</option>
                                                <option value='amTimeIn' <?php echo ($selectedTime == 'amTimeIn' ? 'selected' : ''); ?>>AM Time In Start</option>
                                                <option value='amTimeOut' <?php echo ($selectedTime == 'amTimeOut' ? 'selected' : ''); ?>>AM Time In End</option>
                                                <option value='pmTimeIn' <?php echo ($selectedTime == 'pmTimeIn' ? 'selected' : ''); ?>>PM Time In Start</option>
                                                <option value='pmTimeOut' <?php echo ($selectedTime == 'pmTimeOut' ? 'selected' : ''); ?>>PM Time In End</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class='scanned-content-info-row'>
                                        <label for='response'>Response:</label>
                                        <textarea id='response-input' placeholder='Response will be displayed here' readonly></textarea>
                                    </div>
                                    <!-- Submit Button -->
                                    <div class='scanned-content-btn'>
                                        <button type='button' name='scanRFID' id='scan-rfid' class='scan-rfid'>Scan RFID</button>
                                        <button type='button' name='stop-attendance' id='stop-attendance' class='stop-attendance'>Stop Attendance</button>
                                    </div>
                                </div>
                            </form>

                            <div class='scanned-content-table'>
                                <table id='attendanceTable' class='attendance-table'>
                                    <thead>
                                        <tr>
                                            <th>School ID</th>
                                            <th>Name</th>
                                            <th>Year/Grade</th>
                                            <th>Section</th>
                                            <th>AM Time In</th>
                                            <th>AM Time Out</th>
                                            <th>PM Time In</th>
                                            <th>PM Time Out</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Rows will be dynamically added here -->
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

<script>


    // Ensure the 'scanned-content-container' element exists before using it
    const scannedContainer = document.querySelector('.scanned-content-container');
    const eventStartBtn = document.getElementById("event-start");
    const eventStartScan = document.getElementById("scan-rfid");
    const eventStartBtnClose = document.getElementById("stop-attendance");

    if(eventStartScan) {
        eventStartScan.addEventListener("click", function () {
            scanRFID();
        });
    }
    if(eventStartBtnClose) {
        eventStartBtnClose.addEventListener("click", function () {
            // Toggle the display of the scannedContainer
            if (scannedContainer.style.display === "flex") {
                scannedContainer.style.display = "none";
            } else {
                scannedContainer.style.display = "flex";
            }
        });
    }
    // Check if both elements exist before attaching event listener
    if (eventStartBtn && scannedContainer) {
        eventStartBtn.addEventListener("click", function() {
            // Toggle the display of the scannedContainer
            if (scannedContainer.style.display === "flex") {
                scannedContainer.style.display = "none";
            } else {
                scannedContainer.style.display = "flex";
            }
        });

    }
    //  else {
    //     console.error("Required elements not found: 'event-start' or 'scanned-content-container'.");
    // }

    async function scanRFID() {
    let port;
    try {
        // Request a port and open a connection
        port = await navigator.serial.requestPort();
        await port.open({ baudRate: 9600 });

        const textDecoder = new TextDecoderStream();
        const readableStreamClosed = port.readable.pipeTo(textDecoder.writable);
        const reader = textDecoder.readable.getReader();

        console.log("Connected to the serial port.");

        let buffer = "";
        let lastUIDLine = ""; // To store the most recent "Card UID: ..." line

        while (true) {
            const { value, done } = await reader.read();
            if (done) {
                console.log("Stream closed.");
                reader.releaseLock();
                break;
            }
            if (value) {
                buffer += value; // Append the new data to the buffer
                const lines = buffer.split("\n"); // Split buffer by lines

                for (let line of lines) {
                    line = line.trim(); // Trim whitespace

                    console.log("line:", line); // Debugging
                    if (line.startsWith("Card UID:")) {
                        lastUIDLine = line; // Save the UID line
                    } else if (line === "Card reading end." && lastUIDLine) {
                        // Extract the UID from the last "Card UID: ..." line
                        const uid = lastUIDLine.replace("Card UID:", "").trim();
                        console.log("UID Detected:", uid);

                        // Update the RFID tag input field
                        document.getElementById("rfid-tag").value = uid;

                        // Call fetchUserData with the detected UID
                        fetchUserData(uid);

                        // Clear lastUIDLine to avoid re-processing
                        lastUIDLine = "";
                    }
                }

                // Keep any incomplete line in the buffer for the next iteration
                buffer = lines[lines.length - 1];
            }
        }
    } catch (error) {
        console.error("Error during RFID scan:", error);
    } finally {
        // Ensure the port is closed
        if (port && port.readable) {
            await port.close();
            console.log("Port closed safely.");
        }
    }
}




    async function scanNFC() {
        try {
            // Check if the Web NFC API is available in the browser
            if ("NFCReader" in window) {
                const reader = new NFCReader();

                console.log("Waiting for NFC tag...");

                reader.onreading = (event) => {
                    const uid = event.serialNumber; // Get the NFC tag's UID
                    console.log("UID Detected:", uid);

                    // Update the RFID tag input field with the detected UID
                    document.getElementById("rfid-tag").value = uid;

                    // Call the fetchUserData function to get user info
                    fetchUserData(uid);
                };

                // Start the NFC reader
                await reader.scan();
            } else {
                console.error("Web NFC is not supported on this device.");
            }
        } catch (error) {
            console.error("Error during NFC scan:", error);
        }
    }

    // Function to send RFID tag to the server using Fetch API
    function fetchUserData(rfidTag) {
        fetch('rfidTagUserHandler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'rfidTag=' + encodeURIComponent(rfidTag)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const responseInput = document.getElementById('response-input');

            if (data.success) {
                // Populate user information
                document.getElementById('user-id').value = data.user.id;
                document.getElementById('user-school-id').value = data.user.schoolID;
                document.getElementById('user-name').value = data.user.name;

                const profileImgElement = document.getElementById('user-profile-image');
                if (data.user.profile_picture) {
                    profileImgElement.src = data.user.profile_picture;
                    profileImgElement.style.display = 'block';
                } else {
                    profileImgElement.style.display = 'none';
                }

                responseInput.value = 'User found: ' + data.user.name;

                // Call attendance check after successful user data fetch
                checkAttendance(data.user.id);
            } else {
                alert('No user found for this RFID tag');
                responseInput.value = 'Error: No user found for this RFID tag';
            }
        })
        .catch(error => {
            console.error("Error fetching user data:", error);
            document.getElementById('response-input').value = 'Error fetching user data. Please try again.';
        });
    }

    function checkAttendance(userID) {
        const eventID = document.querySelector('input[name="eventID"]').value;
        const timeSelect = document.getElementById('timeSelect').value;
        const responseInput = document.getElementById('response-input');

        fetch('adminAttendanceHandler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'checkAttendance',
                eventID: eventID,
                userID: userID,
                timeSelect: timeSelect
            })
        })
        .then(response => response.text())
        .then(text => {
            console.log("Response Text:", text);  // Check the exact response
            let data;
            try {
                data = JSON.parse(text);  // Try to parse the response as JSON
            } catch (e) {
                console.error("Error parsing JSON:", e);
                responseInput.value = 'Error parsing response';
                return;
            }

            if (data.success) {
                if (data.recordExists) {
                    if (data.timeColumnValue !== "00:00:00" || data.timeColumnValue === "") {
                        updateAttendanceTime(eventID, userID, timeSelect);
                        responseInput.value = 'Attendance time updated successfully';
                    } else {
                        responseInput.value = 'Attendance already recorded for this time slot';
                    }
                } else {
                    createAttendanceRecord(eventID, userID, timeSelect);
                    responseInput.value = 'New attendance record created';
                }
                updateAttendanceTable(data);
            } else {
                responseInput.value = 'Error checking attendance';
            }
        });
    }






    // Function to create a new attendance record
    function createAttendanceRecord(eventID, userID, timeSelect) {
        console.log('Time Select:', timeSelect);

        fetch('adminAttendanceHandler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'createAttendance',
                eventID: eventID,
                userID: userID,
                timeSelect: timeSelect
            })
        })
        .then(response => response.json())
        .then(data => {
            const responseInput = document.getElementById('response-input');
            if (data.success) {
                console.log('Attendance record created successfully');
                responseInput.value = 'Attendance record created successfully';

            } else {
                console.error('Error creating attendance record:', data.message || 'No specific error message');
                responseInput.value = 'Error creating attendance record';
            }
        })
        .catch(error => {
            // This will log network errors or unexpected issues
            console.error('Network or unexpected error:', error);
            const responseInput = document.getElementById('response-input');
            responseInput.value = 'Network or unexpected error';
        });
    }



    // Function to update the attendance time
    function updateAttendanceTime(eventID, userID, timeSelect) {
        fetch('adminAttendanceHandler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'updateAttendance',
                eventID: eventID,
                userID: userID,
                timeSelect: timeSelect
            })
        })
        .then(response => response.json())
        .then(data => {
            const responseInput = document.getElementById('response-input');
            if (data.success) {
                console.log('Attendance time updated successfully');
                responseInput.value = 'Attendance time updated successfully';

            } else {
                console.error('Error updating attendance time:', data.message || 'No specific error message');
                responseInput.value = 'Error updating attendance time';
            }
        })
        .catch(error => {
            // This will log network errors or unexpected issues
            console.error('Network or unexpected error:', error);
            const responseInput = document.getElementById('response-input');
            responseInput.value = 'Network or unexpected error';
        });
    }






    function updateAttendanceTable(data) {
        const tableBody = document.querySelector("#attendanceTable tbody");

        // Check if the row for the user already exists
        let row = tableBody.querySelector(`tr[data-user-id="${data.user.user_id}"]`);
        if (!row) {
            // Create a new row if it doesn't exist
            row = document.createElement("tr");
            row.setAttribute("data-user-id", data.user.user_id);

            row.innerHTML = `
                <td>${data.user.user_school_id}</td>
                <td>${data.user.full_name}</td>
                <td>${data.user['year/grade_level']}</td>
                <td>${data.user.section}</td>
                <td>${data.attendance?.attendance_am_time_in || ''}</td>
                <td>${data.attendance?.attendance_am_time_out || ''}</td>
                <td>${data.attendance?.attendance_pm_time_in || ''}</td>
                <td>${data.attendance?.attendance_pm_time_out || ''}</td>
            `;
            tableBody.appendChild(row);
        } else {
            // Update existing row
            row.cells[4].textContent = data.attendance?.attendance_am_time_in || row.cells[4].textContent;
            row.cells[5].textContent = data.attendance?.attendance_am_time_out || row.cells[5].textContent;
            row.cells[6].textContent = data.attendance?.attendance_pm_time_in || row.cells[6].textContent;
            row.cells[7].textContent = data.attendance?.attendance_pm_time_out || row.cells[7].textContent;
        }

        // Dispatch custom event to reapply column visibility
        document.dispatchEvent(new Event("rowAdded"));
    }



    document.addEventListener("DOMContentLoaded", function () {
        const table = document.getElementById("attendanceTable");
        const columnsToShow = {
            "Whole Day - AM Time In & AM Time Out & PM Time In & PM Time Out": [5, 6, 7, 8],
            "Whole Day - AM Time In & PM Time Out": [5, 8],
            "Half Day - AM Time In & AM Time Out": [5, 6],
            "Half Day - PM Time In & PM Time Out": [7, 8]
        };
        const eventTimeDuration = <?php echo json_encode($eventInfo['event_time_duration']); ?>;

        const columns = columnsToShow[eventTimeDuration];

        // Helper function to toggle visibility
        const toggleColumnVisibility = (columnsToShow) => {
            // Hide all attendance columns
            for (let i = 5; i <= 8; i++) {
                table.querySelectorAll(`thead th:nth-child(${i}), tbody td:nth-child(${i})`).forEach(col => {
                    col.style.display = "none"; // Hide all by default
                });
            }

            // Show relevant columns
            if (columnsToShow) {
                columnsToShow.forEach(index => {
                    table.querySelectorAll(`thead th:nth-child(${index}), tbody td:nth-child(${index})`).forEach(col => {
                        col.style.display = "table-cell"; // Show relevant ones
                    });
                });
            }
        };

        // Initial column toggle based on eventTimeDuration
        toggleColumnVisibility(columns);

        // Add listener for dynamically added rows
        document.addEventListener("rowAdded", function () {
            toggleColumnVisibility(columns);
        });
    });



</script>


<?php 

    $sql = "SELECT
        u.user_id,
        CONCAT(u.user_firstname, ' ', u.user_middlename, ' ', u.user_lastname, ' ', u.user_suffixname) AS full_name,
        p.program_name,
        s.program_id,
        s.`year/grade_level`, 
        s.section,
        a.attendance_id, a.attendance_date,
        a.attendance_am_time_in, a.attendance_am_time_out,
        a.attendance_pm_time_in, a.attendance_pm_time_out,
        e.event_time_duration,
        e.attendance_duration_am_time_in_start, e.attendance_duration_am_time_in_end,
        e.attendance_duration_am_time_out_start, e.attendance_duration_am_time_out_end,
        e.attendance_duration_pm_time_in_start, e.attendance_duration_pm_time_in_end,
        e.attendance_duration_pm_time_out_start, e.attendance_duration_pm_time_out_end
    FROM attendance a
    JOIN user u ON a.user_id = u.user_id
    JOIN student s ON a.user_id = s.user_id
    JOIN program p ON s.program_id = p.program_id
    JOIN event e ON a.event_id = e.event_id
    WHERE e.event_id = '$eventID';
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $show_am = false;
    $show_pm = false;
    $show_am_out = false;
    $show_pm_out = false;

    // Determine columns to display based on event_time_duration
    $event_time_duration = '';
    if ($row = $result->fetch_assoc()) {
        $event_time_duration = $row['event_time_duration'];
        if (in_array($event_time_duration, ['Whole Day - AM Time In & AM Time Out & PM Time In & PM Time Out', 'Whole Day - AM Time In & PM Time Out'])) {
            $show_am = true;
            $show_am_out = $event_time_duration === 'Whole Day - AM Time In & AM Time Out & PM Time In & PM Time Out';
            $show_pm = true;
            $show_pm_out = true;
        } elseif ($event_time_duration === 'Half Day - AM Time In & AM Time Out') {
            $show_am = true;
            $show_am_out = true;
        } elseif ($event_time_duration === 'Half Day - PM Time In & PM Time Out') {
            $show_pm = true;
            $show_pm_out = true;
        }
    }
?>
<table class="event-data-content-table">
    <thead class="event-data-content-head">
        <tr class="event-data-content-row">
            <th class="event-data-content-name">Name</th>
            <th class="event-data-content-program_name">Program/Course</th>
            <th class="event-data-content-year_grade_level">Year/Grade Level</th>
            <th class="event-data-content-section">Section</th>
            <?php if ($show_am): ?>
                <th class="event-data-content-am-in">AM In</th>
            <?php endif; ?>
            <?php if ($show_am_out): ?>
                <th class="event-data-content-am-out">AM Out</th>
            <?php endif; ?>
            <?php if ($show_pm): ?>
                <th class="event-data-content-pm-in">PM In</th>
            <?php endif; ?>
            <?php if ($show_pm_out): ?>
                <th class="event-data-content-pm-out">PM Out</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody class="event-data-content-body">
        <?php 
        // Reset result set pointer
        $stmt->data_seek(0); 
        while ($row = $result->fetch_assoc()): 
        ?>
            <tr class="event-data-content-row">
                <td class="event-data-content-name"><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td class="event-data-content-program_name"><?php echo htmlspecialchars($row['program_name']); ?></td>
                <td class="event-data-content-year_grade_level"><?php echo htmlspecialchars($row['year/grade_level']); ?></td>
                <td class="event-data-content-section"><?php echo htmlspecialchars($row['section']); ?></td>
                <?php if ($show_am): ?>
                    <td class="event-data-content-am-in"><?php echo getAttendanceStatus($row['attendance_am_time_in'], $row['attendance_duration_am_time_in_start'], $row['attendance_duration_am_time_in_end']); ?></td>
                <?php endif; ?>
                <?php if ($show_am_out): ?>
                    <td class="event-data-content-am-out"><?php echo getAttendanceStatus($row['attendance_am_time_out'], $row['attendance_duration_am_time_out_start'], $row['attendance_duration_am_time_out_end']); ?></td>
                <?php endif; ?>
                <?php if ($show_pm): ?>
                    <td class="event-data-content-pm-in"><?php echo getAttendanceStatus($row['attendance_pm_time_in'], $row['attendance_duration_pm_time_in_start'], $row['attendance_duration_pm_time_in_end']); ?></td>
                <?php endif; ?>
                <?php if ($show_pm_out): ?>
                    <td class="event-data-content-pm-out"><?php echo getAttendanceStatus($row['attendance_pm_time_out'], $row['attendance_duration_pm_time_out_start'], $row['attendance_duration_pm_time_out_end']); ?></td>
                <?php endif; ?>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php
// Close the connection
$stmt->close();
$conn->close();

// Function to check the attendance status
function getAttendanceStatus($attendance_time, $start_time, $end_time) {
    if (empty($attendance_time) || $attendance_time === '00:00:00' || $attendance_time === null) {
        return '~';
    }

    if (empty($start_time) || $start_time === '00:00:00' || empty($end_time) || $end_time === '00:00:00') {
        return '~';
    }

    if (strtotime($attendance_time) < strtotime($start_time)) {
        return 'Absent';
    }

    if (strtotime($attendance_time) > strtotime($end_time)) {
        return 'Late';
    }

    return 'Check';
}
?>

                </div>
            </div>
        </div>  
    </div>
    

    <script>
    </script>
</body>
</html>