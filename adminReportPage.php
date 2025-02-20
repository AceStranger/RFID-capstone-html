<?php
    session_start();
    include "dbh.php";

    if (!isset($_SESSION['user_ID']) || $_SESSION['user_ID'] === null) {
        header("Location: LogInPage.html");
        exit();
    }

    $user_ID = $_SESSION['user_ID'];
    $query = "SELECT * FROM user WHERE user_id = $user_ID";
    $result = mysqli_query($conn, $query);
    $userInfo = $result->fetch_assoc();

    

    $officerInfo="";
    $officerDuty="";
    $officerResponsibility="";
    $officerAssignDuty = "";
    $organizationID = "";
    $eventsQuery = "";
    $isRoleDean = false;
    
    if(str_contains($userInfo['user_role'], "officer")) {
        $query = "SELECT * FROM officer WHERE user_id = $user_ID LIMIT 1";
        $isRoleDean = false;
        $result = mysqli_query($conn, $query);
        $officerInfo = $result->fetch_assoc();
        $officerDuty = strtoupper($officerInfo['officer_duty']);
        $officerResponsibility = $officerInfo['officer_responsibility'];
        $officerAssignDuty = $officerInfo['officer_assign_duty'];
        $organizationID = $officerInfo['organization_id'];
        $eventsQuery = "
            SELECT event_id, event_name, event_date 
            FROM event
            WHERE event_organizer = $organizationID
        ";
    } elseif (str_contains($userInfo['user_role'], "dean")){
        $isRoleDean = true;
        var_dump($isRoleDean);
        $query = "SELECT * FROM dean WHERE user_id = $user_ID LIMIT 1";
        $result = mysqli_query($conn, $query);
        $deanInfo = $result->fetch_assoc();
        $departmentID = $deanInfo['department_id'];

        $query = "SELECT * FROM `program` WHERE program.department_id = $departmentID";
        $result = mysqli_query($conn, $query);
        $programInfo = $result->fetch_assoc();
        $programID = $programInfo['program_id'];
        $programName = $programInfo['program_name'];
        $programLevel = $programInfo['program_level'];
        
        // Escape any special characters in the program name and level to prevent regex errors
        $programName = mysqli_real_escape_string($conn, $programName);
        $programLevel = mysqli_real_escape_string($conn, $programLevel);
        
        // Update the query to use RLIKE for regex matching
        $eventsQuery = "SELECT * 
            FROM event 
            WHERE event_participant RLIKE '\\b$programName\\b' 
            OR event_participant RLIKE '\\b$programLevel\\b'";
        
    }

    $isOfficerAssignDuty = false;
    if(str_contains($officerAssignDuty, "Manage Event Report")) {
        $isOfficerAssignDuty = true;
    } else {
        $isOfficerAssignDuty = false;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Report</title>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminReportPage.css">
    <script src="js/adminSidebar.js"></script>
    <!-- CanvasJS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>
    <div class="main-content-container">
        <?php include_once "adminSidebar.php"; ?>

        <div class="main-content">
            <div class="report-content">
                <div class="report-content-top-part">
                    <h1 class="report-content-head-text">Event Attendance Report</h1>
                </div>

                <div class="report-content-report-content">
                    <div class="report-content-top-part-filter">
                        <form action="" method="get" enctype="multipart/form-data" class="report-content-form" id="report-content-form">
                            <div class="report-content-search-input-container">
                                <label for="report-content-filter-select-event">Select Event:</label>
                                <select name="report-content-filter-select-event" id="report-content-filter-select-event">
                                    <?php
                                        $eventsResult = mysqli_query($conn, $eventsQuery);
                                        while ($event = mysqli_fetch_assoc($eventsResult)) {
                                            $formattedDate = date("F j, Y", strtotime($event['event_date']));
                                            echo "<option value='{$event['event_id']}' >{$event['event_name']} - $formattedDate</option>";
                                        }
                                    ?>
                                </select>
                                <input type="submit" value="Submit">
                            </div>
                        </form>
                        
                    </div>

                    <div class="report-content-main-content-container">
                        <div class="report-content-content-container">
                            <div class="report-content-content-info">
                                <div>
                                    <p class="no-event-selected">Please select an event to view details.</p>
                                    
                                    <h2 class="event-name-info">Event: </h2>
                                    <p class="event-date-info">Date: </p>
                                    <p class="event-place-info">Location: </p>
                                    <label for="attendance-select">Select Attendance Type:</label>
                                    <select id="attendance-select"></select>
                                </div>
                                <div>
                                    <?php if ($isOfficerAssignDuty):?>
                                        <form action="showParticipantList.php" method="post">
                                            <input type="hidden" name="event-id" id="event-id" value="">
                                            <input type="hidden" name="event-participants" id="event-participants" value="">
                                            <input type="submit" name="showParticipant-btn" value="Show Participants Attendance">
                                        </form>
                                    <?php endif;?>
                                    
                                    <?php if ($isRoleDean === true):?>
                                        <input type="hidden" name="event-id" id="event-id" value="">
                                        <input type="hidden" name="event-participants" id="event-participants" value="">
                                    <?php endif;?>
                                </div>
                            </div>
                            <div class="report-content-content-participants-container">
                                <div class="report-content-content-participants">
                                    <h3>Participants</h3>
                                </div>

                            </div>

                            <div class="report-content-content-chart-container">
                                <h3>Chart</h3>
                                <div class="report-content-content-chart">
                                    
                                    <div id="chartContainer"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
        </div>
    </div>

    <script>
        
        // Fetch event data when the form is submitted
        const form = document.getElementById('report-content-form');
        form.addEventListener('submit', async (event) => {
            event.preventDefault(); // Prevent form submission

            const formData = new FormData(form);
            const response = await fetch('get_event_data.php', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                const data = await response.json();
                console.log(data);
                const eventID = document.getElementById("event-id");

                // Check if elements exist on the page
                const eventParticipants = document.getElementById("event-participants");
                if (eventParticipants) {
                    // Assign values if the elements are present
                    eventID.value = data.event.event_id;
                    eventParticipants.value = data.event.event_participant;

                    // Optional: Additional logic for when these elements exist
                    console.log("Elements exist. Values assigned.");
                } else {
                    // Handle the case where elements do not exist
                    console.warn("Either event participants is not present on the page.");
                }
 
                displayEventData(data.event, data.attendance, data.participants, data.programs);
            } else {
                console.error('Failed to fetch event data');
            }
        });

// Display event data in the DOM
function displayEventData(eventDetails, attendanceData, participantGroups, programs) {
    // Display event details
    const eventInfo = document.querySelector('.report-content-content-info');
    let selectOptions = '';

    if (eventDetails.event_time_duration === "Whole Day - AM Time In & AM Time Out & PM Time In & PM Time Out") {
        selectOptions = `
            <option value="am_time_in">AM Time In</option>
            <option value="am_time_out">AM Time Out</option>
            <option value="pm_time_in">PM Time In</option>
            <option value="pm_time_out">PM Time Out</option>
        `;
    } else if (eventDetails.event_time_duration === "Whole Day - AM Time In & PM Time Out") {
        selectOptions = `
            <option value="am_time_in">AM Time In</option>
            <option value="pm_time_out">PM Time Out</option>
        `;
    } else if (eventDetails.event_time_duration === "Half Day - AM Time In & AM Time Out") {
        selectOptions = `
            <option value="am_time_in">AM Time In</option>
            <option value="am_time_out">AM Time Out</option>
        `;
    } else if (eventDetails.event_time_duration === "Half Day - PM Time In & PM Time Out") {
        selectOptions = `
            <option value="pm_time_in">PM Time In</option>
            <option value="pm_time_out">PM Time Out</option>
        `;
    }

    selectOptions += `<option value="All" selected>All</option>`; // Add 'All' option

    const noEventSelected = document.querySelector('.no-event-selected');
    noEventSelected.innerHTML = "";
    const eventNameInfo = document.querySelector('.event-name-info');
    eventNameInfo.innerHTML = `Event: ${eventDetails.event_name}`;
    const eventDateInfo = document.querySelector('.event-date-info');
    eventDateInfo.innerHTML = `Date: ${eventDetails.event_date}`;
    const eventPlaceInfo = document.querySelector('.event-place-info');
    eventPlaceInfo.innerHTML = `Location: ${eventDetails.event_place}`;
    const attendanceSelect = document.getElementById('attendance-select');
    attendanceSelect.innerHTML = selectOptions;

    const selectedAttendance = attendanceSelect.value;
    console.log("selectedAttendance", selectedAttendance);
    

    // Group participants based on event_participant field
    const groupedParticipants = groupParticipants(eventDetails.event_participant, participantGroups, programs, selectedAttendance, eventDetails, attendanceData);
    console.log("groupParticipants returned: ", groupedParticipants);

    // Display participant groups
    const participantContainer = document.querySelector('.report-content-content-participants');

    displayParticipants(groupedParticipants, eventDetails.event_participant, programs, participantGroups);

    
    attendanceSelect.addEventListener('change', (e) => {
        const selectedAttendance = e.target.value;
        const groupedParticipants = groupParticipants(eventDetails.event_participant, participantGroups, programs, selectedAttendance, eventDetails, attendanceData);
        console.log("groupParticipants returned: ", groupedParticipants);
        displayParticipants(groupedParticipants, eventDetails.event_participant, programs, participantGroups);

    });
}






function aggregateData(data, eventParticipant, programs, participants) {
    const aggregatedData = {};

    // Split the participants by comma and process each group
    const participantGroups = eventParticipant.split(',');
    const validProgramLevels = ['college', 'secondary', 'primary'];

    participantGroups.forEach(group => {
        const groupDetails = group.trim().split('-').map(item => item.trim());

        if (groupDetails[0].toLowerCase() === "all" && groupDetails.length > 1) {
            const programLevel = groupDetails[1];

            // Handle program level (All-College, All-Secondary, etc.)
            if (validProgramLevels.includes(programLevel.toLowerCase())) {
                if (!aggregatedData[programLevel]) {
                    aggregatedData[programLevel] = { total: 0, attended: 0, absent: 0, children: {} };
                }

                for (const programName in data[programLevel]) {
                    if (!aggregatedData[programLevel].children[programName]) {
                        aggregatedData[programLevel].children[programName] = { total: 0, attended: 0, absent: 0, children: {} };
                    }

                    for (const yearGradeLevel in data[programLevel][programName]) {
                        const yearData = data[programLevel][programName][yearGradeLevel];
                        if (!aggregatedData[programLevel].children[programName].children[yearGradeLevel]) {
                            aggregatedData[programLevel].children[programName].children[yearGradeLevel] = { total: 0, attended: 0, absent: 0, children: {} };
                        }

                        for (const section in yearData) {
                            const sectionData = yearData[section];
                            aggregatedData[programLevel].children[programName].children[yearGradeLevel].children[section] = sectionData;

                            // Update totals for year/grade level
                            aggregatedData[programLevel].children[programName].children[yearGradeLevel].total += sectionData.total;
                            aggregatedData[programLevel].children[programName].children[yearGradeLevel].attended += sectionData.attended;
                            aggregatedData[programLevel].children[programName].children[yearGradeLevel].absent += sectionData.absent;
                        }
                    }

                    // Sum totals from year levels to program level
                    const programChildren = aggregatedData[programLevel].children[programName].children;
                    for (const yearLevel in programChildren) {
                        aggregatedData[programLevel].children[programName].total += programChildren[yearLevel].total;
                        aggregatedData[programLevel].children[programName].attended += programChildren[yearLevel].attended;
                        aggregatedData[programLevel].children[programName].absent += programChildren[yearLevel].absent;
                    }

                    // Update totals for the program level
                    aggregatedData[programLevel].total += aggregatedData[programLevel].children[programName].total;
                    aggregatedData[programLevel].attended += aggregatedData[programLevel].children[programName].attended;
                    aggregatedData[programLevel].absent += aggregatedData[programLevel].children[programName].absent;
                }
                
            } 
            else {
                const programName = groupDetails[1];
                
                // If a third word (groupDetails[2]) exists, it specifies a year/grade level
                if (groupDetails[2]) {
                    const yearGradeLevel = groupDetails[2];

                    // Ensure program exists in the aggregated data
                    if (!aggregatedData[programName]) {
                        aggregatedData[programName] = { total: 0, attended: 0, absent: 0, children: {} };
                    }

                    // Validate and process the specified year/grade level
                    if (data[programName] && data[programName][yearGradeLevel]) {
                        if (!aggregatedData[programName].children[yearGradeLevel]) {
                            aggregatedData[programName].children[yearGradeLevel] = { total: 0, attended: 0, absent: 0, children: {} };
                        }

                        for (const section in data[programName][yearGradeLevel]) {
                            const sectionData = data[programName][yearGradeLevel][section];

                            // Ensure the section exists in the aggregated data
                            if (!aggregatedData[programName].children[yearGradeLevel].children[section]) {
                                aggregatedData[programName].children[yearGradeLevel].children[section] = { total: 0, attended: 0, absent: 0 };
                            }

                            // Update totals for the section
                            aggregatedData[programName].children[yearGradeLevel].children[section].total += sectionData.total;
                            aggregatedData[programName].children[yearGradeLevel].children[section].attended += sectionData.attended;
                            aggregatedData[programName].children[yearGradeLevel].children[section].absent += sectionData.absent;
                        }

                        // Update totals for the year/grade level
                        const yearData = data[programName][yearGradeLevel];
                        aggregatedData[programName].children[yearGradeLevel].total += Object.values(yearData).reduce((sum, section) => sum + section.total, 0);
                        aggregatedData[programName].children[yearGradeLevel].attended += Object.values(yearData).reduce((sum, section) => sum + section.attended, 0);
                        aggregatedData[programName].children[yearGradeLevel].absent += Object.values(yearData).reduce((sum, section) => sum + section.absent, 0);
                        

                        // Update totals for the program name
                        aggregatedData[programName].total += aggregatedData[programName].children[yearGradeLevel].total;
                        aggregatedData[programName].attended += aggregatedData[programName].children[yearGradeLevel].attended;
                        aggregatedData[programName].absent += aggregatedData[programName].children[yearGradeLevel].absent;
                    }
                } else {
                    // If no year/grade level is specified, group by program name
                    if (!aggregatedData[programName]) {
                        aggregatedData[programName] = { total: 0, attended: 0, absent: 0, children: {} };
                    }
                    
                    for (const key in data[programName]) {
                        const yearGradeLevel = key; // Each key corresponds to a year/grade level
                        const yearData = data[programName][key];

                        if (!aggregatedData[programName].children[yearGradeLevel]) {
                            aggregatedData[programName].children[yearGradeLevel] = { total: 0, attended: 0, absent: 0, children: {} };
                        }

                        for (const section in yearData) {
                            const sectionData = yearData[section];
                            aggregatedData[programName].children[yearGradeLevel].children[section] = sectionData;

                            // Update totals
                            aggregatedData[programName].children[yearGradeLevel].total += sectionData.total;
                            aggregatedData[programName].children[yearGradeLevel].attended += sectionData.attended;
                            aggregatedData[programName].children[yearGradeLevel].absent += sectionData.absent;
                        }
                        // Update totals for the program name
                        aggregatedData[programName].total += aggregatedData[programName].children[yearGradeLevel].total;
                        aggregatedData[programName].attended += aggregatedData[programName].children[yearGradeLevel].attended;
                        aggregatedData[programName].absent += aggregatedData[programName].children[yearGradeLevel].absent;
                    }
                }
            }

        } 
        else if (groupDetails.length > 1) {
            // Handle specific program name and year/grade level (e.g., BSIT-1st Year)
            const programName = groupDetails[0];
            const yearGradeLevel = groupDetails[1];
            const section = groupDetails[2]; // Optional third word specifying the section

            const program = programs.find(p => p.program_name.toLowerCase() === programName.toLowerCase());
            if (program && data[programName] && data[programName][yearGradeLevel]) {
                if (!aggregatedData[programName]) {
                    aggregatedData[programName] = { total: 0, attended: 0, absent: 0, children: {} };
                }

                if (section) {
                    // If a specific section is provided
                    const sectionData = data[programName][yearGradeLevel][section];
                    if (sectionData) {
                        if (!aggregatedData[programName].children[yearGradeLevel]) {
                            aggregatedData[programName].children[yearGradeLevel] = { total: 0, attended: 0, absent: 0, children: {} };
                        }
                        if (!aggregatedData[programName].children[yearGradeLevel].children[section]) {
                            aggregatedData[programName].children[yearGradeLevel].children[section] = { total: 0, attended: 0, absent: 0 };
                        }

                        // Update totals for the specified section
                        aggregatedData[programName].children[yearGradeLevel].children[section].total += sectionData.total;
                        aggregatedData[programName].children[yearGradeLevel].children[section].attended += sectionData.attended;
                        aggregatedData[programName].children[yearGradeLevel].children[section].absent += sectionData.absent;

                        // Update totals for the year/grade level
                        aggregatedData[programName].children[yearGradeLevel].total += sectionData.total;
                        aggregatedData[programName].children[yearGradeLevel].attended += sectionData.attended;
                        aggregatedData[programName].children[yearGradeLevel].absent += sectionData.absent;
                        // Update totals for the program name
                        aggregatedData[programName].total += aggregatedData[programName].children[yearGradeLevel].total;
                        aggregatedData[programName].attended += aggregatedData[programName].children[yearGradeLevel].attended;
                        aggregatedData[programName].absent += aggregatedData[programName].children[yearGradeLevel].absent;
                    }
                } else {
                    // If no specific section is provided, process all sections in the year/grade level
                    for (const sectionKey in data[programName][yearGradeLevel]) {
                        const sectionData = data[programName][yearGradeLevel][sectionKey];

                        if (!aggregatedData[programName].children[yearGradeLevel]) {
                            aggregatedData[programName].children[yearGradeLevel] = { total: 0, attended: 0, absent: 0, children: {} };
                        }
                        if (!aggregatedData[programName].children[yearGradeLevel].children[sectionKey]) {
                            aggregatedData[programName].children[yearGradeLevel].children[sectionKey] = { total: 0, attended: 0, absent: 0 };
                        }

                        // Update totals for each section
                        aggregatedData[programName].children[yearGradeLevel].children[sectionKey].total += sectionData.total;
                        aggregatedData[programName].children[yearGradeLevel].children[sectionKey].attended += sectionData.attended;
                        aggregatedData[programName].children[yearGradeLevel].children[sectionKey].absent += sectionData.absent;

                        // Update totals for the year/grade level
                        aggregatedData[programName].children[yearGradeLevel].total += sectionData.total;
                        aggregatedData[programName].children[yearGradeLevel].attended += sectionData.attended;
                        aggregatedData[programName].children[yearGradeLevel].absent += sectionData.absent;
                    }
                    
                    // Update totals for the program name
                    aggregatedData[programName].total += aggregatedData[programName].children[yearGradeLevel].total;
                    aggregatedData[programName].attended += aggregatedData[programName].children[yearGradeLevel].attended;
                    aggregatedData[programName].absent += aggregatedData[programName].children[yearGradeLevel].absent;
                }
            }
        }


    });
    console.log(aggregatedData);
    
    return aggregatedData;
}



function renderNestedView(data, container) {
    container.removeEventListener('click', handleCollapsibleClick); // Clear previous listeners

    for (const key in data) {
        if (key === "total" || key === "attended" || key === "absent") continue;

        const levelData = data[key];

        const collapsible = document.createElement('div');
        const content = document.createElement('div');
        collapsible.setAttribute("class", "collapsible");
        collapsible.textContent = `${key} - Total: ${levelData.total}, Attended: ${levelData.attended}, Absent: ${levelData.absent}`;
        collapsible.dataset.total = levelData.total;
        collapsible.dataset.attended = levelData.attended;
        collapsible.dataset.absent = levelData.absent;

        collapsible.addEventListener('click', function (e) {
            e.stopPropagation(); // Prevent parent collapsibles from triggering

            // Toggle visibility
            this.classList.toggle('active');
            const content = this.nextElementSibling;
            if (content) content.classList.toggle('visible');

            // Update chart based on clicked collapsible
            updateChart(this.dataset.total, this.dataset.attended, this.dataset.absent);
        });

        container.appendChild(collapsible);
        content.setAttribute("class", "content");

        if (levelData.children && Object.keys(levelData.children).length > 0) {
            const childContainer = document.createElement('div');
            renderNestedView(levelData.children, childContainer);
            content.appendChild(childContainer);
        }

        container.appendChild(content);
    }
    console.log("renderNestedView run");

    // Use event delegation for collapsible items
    addCollapsibleListeners(container);
}


function addCollapsibleListeners(container) {
    container.addEventListener('click', handleCollapsibleClick);
}

function handleCollapsibleClick(e) {
    // Check if the clicked element is a collapsible
    if (e.target && e.target.classList.contains('collapsible')) {
        // Stop event propagation to prevent triggering parent collapsible
        e.stopPropagation();
        
        // Toggle 'active' class on the clicked element
        e.target.classList.toggle('active');
        const content = e.target.nextElementSibling;
        console.log("triggered click");
        
        if (content) {
            content.classList.toggle('visible');
        }
    }
}

// Main function to display participants
function displayParticipants(data, eventParticipant, programs, participants) {
    const participantContainer = document.querySelector('.report-content-content-participants');
    participantContainer.innerHTML = ""; // Clear previous content

    const sectionTitle = document.createElement("h3");
    sectionTitle.textContent = "Participants";
    participantContainer.appendChild(sectionTitle);

    // Aggregate the data
    const aggregatedData = aggregateData(data, eventParticipant, programs, participants);
    console.log("aggregatedDataa", aggregatedData);
    
    const chartData = prepareAllCharts(aggregatedData, eventParticipant, programs, participants);
    console.log("asda", chartData);
    
    initializeChart(chartData);
    // Render the nested view
    renderNestedView(aggregatedData, participantContainer);
}



function prepareAllCharts(aggregatedData) {
    const charts = [];

    function traverseData(data, path = []) {
        for (const key in data) {
            const node = data[key];
            const currentPath = [...path, key];

            // Check if current node has data to display
            if (node.total !== undefined) {
                const chartData = {
                    labels: [key], // Use the current level key as the label
                    datasets: [
                        {
                            label: "Total",
                            data: [node.total || 0],
                            backgroundColor: "rgba(75, 192, 192, 0.6)",
                            borderColor: "rgba(75, 192, 192, 1)",
                            borderWidth: 1
                        },
                        {
                            label: "Attended",
                            data: [node.attended || 0],
                            backgroundColor: "rgba(54, 162, 235, 0.6)",
                            borderColor: "rgba(54, 162, 235, 1)",
                            borderWidth: 1
                        },
                        {
                            label: "Absent",
                            data: [node.absent || 0],
                            backgroundColor: "rgba(255, 99, 132, 0.6)",
                            borderColor: "rgba(255, 99, 132, 1)",
                            borderWidth: 1
                        }
                    ]
                };

                charts.push({ title: currentPath.join(" > "), data: chartData });
            }

            // Recursively process children
            if (node.children) {
                traverseData(node.children, currentPath);
            }
        }
    }
    traverseData(aggregatedData);
    return charts;
}

let participantChart; 

function initializeChart() {
    const chartContainer = document.getElementById('chartContainer');
    chartContainer.innerHTML = ""; // Clear previous content

    const canvas = document.createElement('canvas');
    canvas.id = 'participantChart';
    chartContainer.appendChild(canvas);

    const ctx = canvas.getContext('2d');
    participantChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Total", "Attended", "Absent"],
            datasets: [{
                label: "Participants",
                data: [0, 0, 0], // Initially empty
                backgroundColor: [
                    "rgba(75, 192, 192, 0.6)",
                    "rgba(54, 162, 235, 0.6)",
                    "rgba(255, 99, 132, 0.6)"
                ],
                borderColor: [
                    "rgba(75, 192, 192, 1)",
                    "rgba(54, 162, 235, 1)",
                    "rgba(255, 99, 132, 1)"
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Participant Statistics' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Number of Participants' }
                }
            }
        }
    });
}

function updateChart(total, attended, absent) {
    if (!participantChart) return;

    participantChart.data.datasets[0].data = [total, attended, absent];
    participantChart.update();
}



function groupParticipants(eventParticipant, participants, programs, selectedAttendance, eventDetails, attendanceData) {
    const participantGroups = eventParticipant.split(',');
    const groupedParticipants = {};

    participantGroups.forEach(group => {
        const groupDetails = group.trim().split('-').map(item => item.trim());

        if (groupDetails[0].toLowerCase() === "all" && groupDetails.length > 1) {
            const programLevel = groupDetails[1];
            const validProgramLevels = ['college', 'secondary', 'primary'];
            

            if (validProgramLevels.includes(programLevel.toLowerCase())) {
                // Filter programs by the given programLevel
                const matchingPrograms = programs.filter(program => 
                    program.program_level.toLowerCase() === programLevel.toLowerCase()
                );
                matchingPrograms.forEach(program => {
                    participants.forEach(participant => {
                        if (Number(participant.program_id) === Number(program.program_id)) {
                            // Initialize groupedParticipants for this program, year/grade level, and section
                            if (!groupedParticipants[programLevel]) {
                                groupedParticipants[programLevel] = {};
                            }
                            if (!groupedParticipants[programLevel][program.program_name]) {
                                groupedParticipants[programLevel][program.program_name] = {};
                            }
                            if (!groupedParticipants[programLevel][program.program_name][participant["year/grade_level"]]) {
                                groupedParticipants[programLevel][program.program_name][participant["year/grade_level"]] = {};
                            }
                            if (!groupedParticipants[programLevel][program.program_name][participant["year/grade_level"]][participant.section]) {
                                groupedParticipants[programLevel][program.program_name][participant["year/grade_level"]][participant.section] = {
                                    total: 0,
                                    attended: 0,
                                    absent: 0
                                };
                            }

                            // Aggregate total, attended, and absent counts
                            groupedParticipants[programLevel][program.program_name][participant["year/grade_level"]][participant.section].total += 1;

                            // Determine attendance fields based on the selectedAttendance
                            const attendanceFields = selectedAttendance === "All"
                                ? ["am_time_in", "am_time_out", "pm_time_in", "pm_time_out"]
                                : [selectedAttendance];

                            // Check if the participant attended
                            const isAttended = attendanceData.some(attendance => {
                                if (attendance.user_id === participant.user_id && attendance.event_id === eventDetails.event_id) {
                                    return attendanceFields.some(field => {
                                        const attendanceTime = attendance[`attendance_${field}`];
                                        return attendanceTime && isTimeInRange(attendanceTime, eventDetails[`attendance_duration_${field}_start`], eventDetails[`attendance_duration_${field}_end`]);
                                    });
                                }
                                return false;
                            });

                            // Update attendance counts
                            if (isAttended) {
                                groupedParticipants[programLevel][program.program_name][participant["year/grade_level"]][participant.section].attended += 1;
                            } else {
                                groupedParticipants[programLevel][program.program_name][participant["year/grade_level"]][participant.section].absent += 1;
                            }
                        }
                    });
                });
            } 
            else {
                // If it's not a valid program level, treat groupDetails[1] as a program name
                const programName = groupDetails[1];

                participants.forEach(participant => {
                    const program = programs.find(p => p.program_name.toLowerCase() === programName.toLowerCase());
                    
                    if (program) {
                        // Check for the participant's program and year/grade level match
                        if (Number(participant.program_id) === Number(program.program_id)) {
                            if (!groupedParticipants[programName]) {
                                groupedParticipants[programName] = {};
                            }
                            if (!groupedParticipants[programName][participant["year/grade_level"]]) {
                                groupedParticipants[programName][participant["year/grade_level"]] = {};
                            }
                            if (!groupedParticipants[programName][participant["year/grade_level"]][participant.section]) {
                                groupedParticipants[programName][participant["year/grade_level"]][participant.section] = {
                                    total: 0,
                                    attended: 0,
                                    absent: 0
                                };
                            }

                            // Aggregate total, attended, and absent counts
                            groupedParticipants[programName][participant["year/grade_level"]][participant.section].total += 1;

                            // Determine attendance fields based on the selectedAttendance
                            const attendanceFields = selectedAttendance === "All"
                                ? ["am_time_in", "am_time_out", "pm_time_in", "pm_time_out"]
                                : [selectedAttendance];

                            
                            // Check if the participant attended
                            const isAttended = attendanceData.some(attendance => {
                                // Ensure the attendance record matches the participant and event
                                if (attendance.user_id === participant.user_id && attendance.event_id === eventDetails.event_id) {
                                    // Check attendance for the relevant fields
                                    return attendanceFields.some(field => {
                                        const attendanceTime = attendance["attendance_"+field];
                                        
                                        return attendanceTime && isTimeInRange(attendanceTime, eventDetails[`attendance_duration_${field}_start`], eventDetails[`attendance_duration_${field}_end`]);
                                    });
                                }
                                return false;
                            });

                            // Update the attendance count based on the result
                            if (isAttended) {
                                
                                groupedParticipants[programName][participant["year/grade_level"]][participant.section].attended += 1;
                        
                            } else {
                                groupedParticipants[programName][participant["year/grade_level"]][participant.section].absent += 1;
                            
                            }

                        }
                    }
                });
            }
        } else if (groupDetails.length > 1) {
            const programName = groupDetails[0];
            const yearGradeLevel = groupDetails[1];

            const program = programs.find(p => p.program_name.toLowerCase() === programName.toLowerCase());

            if (program) {
                participants.forEach(participant => {
                    // Now handling the program and yearGradeLevel match, and considering section
                    if (Number(participant.program_id) === Number(program.program_id)) {
                        // Check if section is included in group details
                        if (groupDetails.length === 3) {
                            const section = groupDetails[2];
                            if (participant.section === section && participant["year/grade_level"] === yearGradeLevel) {
                                if (!groupedParticipants[programName]) {
                                    groupedParticipants[programName] = {};
                                }
                                if (!groupedParticipants[programName][yearGradeLevel]) {
                                    groupedParticipants[programName][yearGradeLevel] = {};
                                }
                                if (!groupedParticipants[programName][yearGradeLevel][section]) {
                                    groupedParticipants[programName][yearGradeLevel][section] = {
                                        total: 0,
                                        attended: 0,
                                        absent: 0
                                    };
                                }
                                // Aggregate total, attended, and absent counts
                                groupedParticipants[programName][participant["year/grade_level"]][participant.section].total += 1;

                                // Determine attendance fields based on the selectedAttendance
                                const attendanceFields = selectedAttendance === "All"
                                    ? ["am_time_in", "am_time_out", "pm_time_in", "pm_time_out"]
                                    : [selectedAttendance];

                                
                                // Check if the participant attended
                                const isAttended = attendanceData.some(attendance => {
                                    // Ensure the attendance record matches the participant and event
                                    if (attendance.user_id === participant.user_id && attendance.event_id === eventDetails.event_id) {
                                        // Check attendance for the relevant fields
                                        return attendanceFields.some(field => {
                                            const attendanceTime = attendance["attendance_"+field];
                                            
                                            return attendanceTime && isTimeInRange(attendanceTime, eventDetails[`attendance_duration_${field}_start`], eventDetails[`attendance_duration_${field}_end`]);
                                        });
                                    }
                                    return false;
                                });

                                // Update the attendance count based on the result
                                if (isAttended) {
                                    groupedParticipants[programName][participant["year/grade_level"]][participant.section].attended += 1;
                                } else {
                                    groupedParticipants[programName][participant["year/grade_level"]][participant.section].absent += 1;
                                }

                            }
                        } 
                        else if (participant["year/grade_level"] === yearGradeLevel) {
                            if (!groupedParticipants[programName]) {
                                groupedParticipants[programName] = {};
                            }
                            if (!groupedParticipants[programName][yearGradeLevel]) {
                                groupedParticipants[programName][yearGradeLevel] = {};
                            }
                            if (!groupedParticipants[programName][yearGradeLevel][participant.section]) {
                                groupedParticipants[programName][yearGradeLevel][participant.section] = {
                                    total: 0,
                                    attended: 0,
                                    absent: 0
                                };
                            }
                            // Aggregate total, attended, and absent counts
                            groupedParticipants[programName][yearGradeLevel][participant.section].total += 1;

                            // Determine attendance fields based on the selectedAttendance
                            const attendanceFields = selectedAttendance === "All"
                                ? ["am_time_in", "am_time_out", "pm_time_in", "pm_time_out"]
                                : [selectedAttendance];

                            
                            // Check if the participant attended
                            const isAttended = attendanceData.some(attendance => {
                                // Ensure the attendance record matches the participant and event
                                if (attendance.user_id === participant.user_id && attendance.event_id === eventDetails.event_id) {
                                    // Check attendance for the relevant fields
                                    return attendanceFields.some(field => {
                                        const attendanceTime = attendance["attendance_"+field];
                                        
                                        return attendanceTime && isTimeInRange(attendanceTime, eventDetails[`attendance_duration_${field}_start`], eventDetails[`attendance_duration_${field}_end`]);
                                    });
                                }
                                return false;
                            });


                            // Update the attendance count based on the result
                            if (isAttended) {
                                groupedParticipants[programName][participant["year/grade_level"]][participant.section].attended += 1;
                            } else {
                                groupedParticipants[programName][participant["year/grade_level"]][participant.section].absent += 1;
                            }

                        }
                    }
                });
            }
        }
    });

    // Convert the grouped participants to arrays for each section
    // Object.keys(groupedParticipants).forEach(programLevel => {
    //     Object.keys(groupedParticipants[programLevel]).forEach(programName => {
    //         Object.keys(groupedParticipants[programLevel][programName]).forEach(yearGradeLevel => {
    //             Object.keys(groupedParticipants[programLevel][programName][yearGradeLevel]).forEach(section => {
    //                 const group = groupedParticipants[programLevel][programName][yearGradeLevel][section];
    //                 // Convert the single group data into an array
    //                 groupedParticipants[programLevel][programName][yearGradeLevel][section] = [group];
    //             });
    //         });
    //     });
    // });

    return groupedParticipants;
}


        // Helper function to check if a time is within a given range
        function isTimeInRange(time, startTime, endTime) {
            const timeObj = new Date(`1970-01-01T${time}Z`);
            const startObj = new Date(`1970-01-01T${startTime}Z`);
            const endObj = new Date(`1970-01-01T${endTime}Z`);
            return timeObj >= startObj && timeObj <= endObj;
        }
        console.log("isTimeInRange : 08:02:00, 08:00:00, 08:30:00", isTimeInRange("07:02:22","08:00:00","08:30:00"));
        
    </script>

</body>
</html>
