<?php 
    session_start();
    include "dbh.php";
    if(isset($_SESSION['user_ID'])){
        $user_ID = $_SESSION['user_ID'];
        $userquery = "SELECT * FROM user WHERE user_id = $user_ID";
        $userresult = mysqli_query($conn, $userquery);
        
        $userInfo = mysqli_fetch_assoc($userresult);
        $userfn = $userInfo['user_firstname'].' '.$userInfo['user_middlename'].' '.$userInfo['user_lastname'];
        $userpfp = $userInfo['user_img'];

        $studentquery = "SELECT * FROM `student` WHERE `user_id`=$user_ID";
        $studentresult = mysqli_query($conn, $studentquery);
        $studentinfo = mysqli_fetch_assoc($studentresult);
        $studentprogram = $studentinfo["program_id"];
        $studentlevel = $studentinfo["year/grade_level"];
        $studentsection = $studentinfo["section"];

        $programquery = "SELECT * FROM `program` WHERE `program_id`=$studentprogram";
        $programresult = mysqli_query($conn, $programquery);
        $programinfo = mysqli_fetch_assoc($programresult);

        $programname = $programinfo["program_name"];


    } else{
        header("Location: LogInPage.html");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="css/headerStyle.css"> 
    <link rel="stylesheet" href="css/body.css"> 
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/event.css">
    <script defer src="js\jquery-3.7.1.js"></script>
    <script defer src="js/usermenu.js"></script> 
</head>
<body>
    <?php include_once"header.php";?>
    <div class="event-content">
        <div class="event-content-title">Event</div>
        <?php
// Assume user program, year, and section are already fetched
$userProgram = $studentprogram; // Example: user's program ID
$userYear = $studentlevel; // Example: user's year/grade level
$userSection = $studentsection; // Example: user's section

// Fetch all organizations
$query = "SELECT * FROM organization";
$organizationResult = mysqli_query($conn, $query);

// Fetch program data for mapping
$programQuery = "SELECT * FROM program";
$programResult = mysqli_query($conn, $programQuery);

$programMap = [];
while ($programRow = $programResult->fetch_assoc()) {
    $programMap[$programRow['program_id']] = [
        'name' => $programRow['program_name'],
        'level' => $programRow['program_level'],
        'section' => $programRow['program_level']
    ];
}

// Student's program and level
$studentProgramName = $programMap[$userProgram]['name'];
$studentProgramLevel = $programMap[$userProgram]['level'];
$matchingOrganizations = [];
while ($org = $organizationResult->fetch_assoc()) {
    $responsibility = $org['organization_responsibility'];
    $parts = array_map('trim', explode(' - ', $responsibility)); // Trim to avoid space issues

    $isIncluded = false;
    if (isset($parts[0]) && $parts[0] === 'All') {
        // Responsibility like "All - College"
        if (isset($parts[1]) && $parts[1] === $studentProgramLevel) {
            $isIncluded = true;
        } elseif (isset($parts[0], $parts[1]) && $parts[0] === 'All' && $parts[1] === $studentProgramName) {
            // Responsibility like "All - BSIT"
            $isIncluded = true;
        } 
    }elseif (isset($parts[0], $parts[1]) && $parts[0] === $studentProgramName && $parts[1] === $userYear) {
        // Responsibility like "BSIT - 1st YEAR"
        $isIncluded = true;
    } elseif (isset($parts[0], $parts[1], $parts[2]) && $parts[0] === $studentProgramName && $parts[1] === $userYear && $parts[2] === $userSection) {
        // Responsibility like "BSIT - 1st YEAR - C"
        $isIncluded = true;
    }

    if ($isIncluded) {
        $matchingOrganizations[] = $org;
    }
}

?>
<div class="organization-event-content-container">
    <?php foreach ($matchingOrganizations as $organization): ?>
        <div class="organization-event-content">
            <table class="organization-event-content-table-content">
                <thead class="organization-event-content-table-head">
                    <input type="hidden" name="organization-id" id="organization-id" value="<?php echo htmlspecialchars($organization['organization_id']);  ?>">
                    <input type="hidden" name="user-id" id="user-id" value="<?php echo htmlspecialchars($user_ID);  ?>">
                    <input type="hidden" name="program-name" id="program-name" value="<?php echo htmlspecialchars($studentProgramName);  ?>">
                    <input type="hidden" name="program-level" id="program-level" value="<?php echo htmlspecialchars($studentProgramLevel);  ?>">
                    <input type="hidden" name="student-year" id="student-year" value="<?php echo htmlspecialchars($userYear);  ?>">
                    <input type="hidden" name="student-section" id="student-section" value="<?php echo htmlspecialchars($userSection);  ?>">
                    <tr class="organization-event-content-table-row">
                        <th colspan="9" class="organization-event-content-organization-name">
                            <?= htmlspecialchars($organization['organization_name']) ?>
                        </th>
                        <th rowspan="3" class="organization-event-content-event-btn">▼</th>
                    </tr>
                    <tr class="organization-event-content-table-row">
                        <th rowspan="2" class="organization-event-content-event-name">Event Name</th>
                        <th rowspan="2" class="organization-event-content-event-location">Venue</th>
                        <th rowspan="2" class="organization-event-content-event-Status">Status</th>
                        <th rowspan="2" class="organization-event-content-event-date">Date</th>
                        <th colspan="2" class="organization-event-content-event-AM">AM</th>
                        <th colspan="2" class="organization-event-content-event-PM">PM</th>
                        <th rowspan="2" class="organization-event-content-event-Penalty">Penalty</th>
                    </tr>
                    <tr class="organization-event-content-table-row">
                        <th class="organization-event-content-event-am-time-in">Time In</th>
                        <th class="organization-event-content-event-am-time-out">Time Out</th>
                        <th class="organization-event-content-event-pm-time-in">Time In</th>
                        <th class="organization-event-content-event-pm-time-out">Time Out</th>
                    </tr>
                </thead>
                <tbody class="organization-event-content-table-body">
                    <tr>
                    </tr>
                </tbody>
            </table>
            <script>

                document.addEventListener("DOMContentLoaded", function () {
                    
                    const organizationId = document.getElementById("organization-id").value;
                    const userID = document.getElementById("user-id").value;
                    const studentProgramName = document.getElementById("program-name").value;
                    const studentProgramLevel = document.getElementById("program-level").value;
                    const studentYear = document.getElementById("student-year").value;
                    const studentSection = document.getElementById("student-section").value;

                    // Fetch event data
                    fetch("getEvents.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({ organization_id: organizationId,
                            userID: userID
                         }),
                    })
                    .then((response) => response.json())
                    .then((events) => {
                        const tableBody = document.querySelector(".organization-event-content-table-body");
                        tableBody.innerHTML = ""; // Clear the existing rows

                        events.forEach((event) => {
                            const participants = event.event_participant.split(",").map((p) => p.trim());
                            let shouldDisplay = false;

                            participants.forEach((participant) => {
                                const parts = participant.split("-").map((p) => p.trim());

                                if (parts[0].toLowerCase() === "all") {
                                    if (
                                        ["primary", "secondary", "college"].includes(parts[1]?.toLowerCase()) &&
                                        parts[1]?.toLowerCase() === studentProgramLevel.toLowerCase()
                                    ) {
                                        shouldDisplay = true;
                                    } else if (
                                        parts[1]?.toLowerCase() === studentProgramName.toLowerCase()
                                    ) {
                                        shouldDisplay = true;
                                    }
                                } else if (parts[0]?.toLowerCase() === studentProgramName.toLowerCase()) {
                                    if (parts[1]?.toLowerCase() === studentYear.toLowerCase()) {
                                        if (parts[2]) {
                                            // Check section if present
                                            if (parts[2]?.toLowerCase() === studentSection.toLowerCase()) {
                                                shouldDisplay = true;
                                            }
                                        } else {
                                            shouldDisplay = true;
                                        }
                                    }
                                }
                            });

                            // Handle the time values based on event_time_duration
                            let amTimeIn, amTimeOut, pmTimeIn, pmTimeOut;

                            if (event.attendance) {
                                // If attendance is empty, handle based on event status
                                if (event.event_status === 2) {
                                    amTimeIn = "x";
                                    amTimeOut = "x";
                                    pmTimeIn = "x";
                                    pmTimeOut = "x";
                                    console.log("event status is end or 0");
                                    
                                } else {
                                    amTimeIn = "~";
                                    amTimeOut = "~";
                                    pmTimeIn = "~";
                                    pmTimeOut = "~";
                                    console.log("event status is end or 0 is false");
                                }
                            } else {
                                console.log("event.attendance is empty");
                                // If attendance exists, use its values
                                amTimeIn = event.attendance.am_time_in;
                                amTimeOut = event.attendance.am_time_out;
                                pmTimeIn = event.attendance.pm_time_in;
                                pmTimeOut = event.attendance.pm_time_out;

                            }
                            if (event.event_time_duration === "Whole Day - AM Time In & AM Time Out & PM Time In & PM Time Out") {
                                // Do nothing, keep the values as they are
                            } else if (event.event_time_duration === "Whole Day - AM Time In & PM Time Out") {
                                amTimeOut = "~";
                                pmTimeIn = "~";
                            } else if (event.event_time_duration === "Half Day - AM Time In & AM Time Out") {
                                pmTimeIn = "~";
                                pmTimeOut = "~";
                            } else if (event.event_time_duration === "Half Day - PM Time In & PM Time Out") {
                                amTimeIn = "~";
                                amTimeOut = "~";
                            }


                            const statusText = getEventStatus(event.event_status);

                            // Display the event if conditions match
                            if (shouldDisplay) {
                                const row = `
                                    <tr>
                                        <td>${event.event_name}</td>
                                        <td>${event.event_place}</td>
                                        <td>${statusText}</td>
                                        <td>${event.event_date}</td>
                                        <td>${amTimeIn}</td>
                                        <td>${amTimeOut}</td>
                                        <td>${pmTimeIn}</td>
                                        <td>${pmTimeOut}</td>
                                        <td>${event.penalty || "None"}</td>
                                        <td></td>
                                    </tr>
                                `;
                                tableBody.insertAdjacentHTML("beforeend", row);
                            }
                        });
                    })
                    .catch((error) => {
                        console.error("Error fetching events:", error);
                    });
                });
                
                // Function to convert event status to human-readable string
                function getEventStatus(status) {
                    switch(status) {
                        case 0: return 'Incoming'; // 0 = Incoming
                        case 1: return 'Ongoing'; // 1 = Ongoing
                        case 2: return 'End'; // 2 = End
                        default: return 'Unknown'; // Default fallback
                    }
                }

            </script>
        </div>
    <?php endforeach; ?>
</div>

    </div>
    <?php 
        include_once("footer.php");
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Get all toggle buttons in the organization-event-content tables
            const toggleButtons = document.querySelectorAll(
                ".organization-event-content-event-btn"
            );

            toggleButtons.forEach((button) => {
                button.addEventListener("click", function () {
                    // Find the tbody within the same organization-event-content
                    const tableBody = this
                        .closest(".organization-event-content")
                        .querySelector(".organization-event-content-table-body");

                    // Toggle the "close" class and the button text
                    if (tableBody.classList.contains("close")) {
                        tableBody.classList.remove("close");
                        tableBody.style.display = "table-row-group"; // Show the tbody
                        this.textContent = "▲"; // Change the button symbol
                    } else {
                        tableBody.classList.add("close");
                        tableBody.style.display = "none"; // Hide the tbody
                        this.textContent = "▼"; // Reset the button symbol
                    }
                });
            });
        });

    </script>
    <script src="js\EventPage.js"></script>
</body>
</html>