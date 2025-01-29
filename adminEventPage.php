<?php
session_start();
include "dbh.php";
include "updateEventStatus.php";

if(@!isset($_SESSION['user_ID']) || $_SESSION['user_ID'] === null) {
    header("Location: LogInPage.html");
    exit();
}
$user_ID = @$_SESSION['user_ID'];
$query = "SELECT * FROM user WHERE user_id = $user_ID";
$result = mysqli_query($conn, $query);
$userInfo = $result->fetch_assoc();


$query = "SELECT organization_id, officer_assign_duty FROM officer WHERE user_id = $user_ID LIMIT 1";
$result = mysqli_query($conn, $query);
$officerInfo = $result->fetch_assoc();
$organizationID = $officerInfo['organization_id'];
$officerDuty = $officerInfo['officer_assign_duty'];



$query = "SELECT organization_responsibility FROM organization WHERE organization_id = $organizationID LIMIT 1";
$result = mysqli_query($conn, $query);
$organizationInfo = $result->fetch_assoc();
$organizationResponsibility = $organizationInfo['organization_responsibility'];


updateEventStatus($conn);


// Define buttons based on the request type
$btn = '';
if(str_contains( $officerDuty, "Manage Event Registration")) {

    $btn = '
        <button type="button" id="add-new-event-btn-show" class="add-new-event-btn-show">Add New</button>
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
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminEvent.css">
    <script src="js\adminSidebar.js"></script>
</head>
<body>
    <div class="main-content-container">
        <?php include_once"adminSidebar.php";?>
        <div class="main-content">
            <div class="event-content">
                <div class="event-content-top-part">
                    <h1 class="event-content-head-text">EVENT</h1>
                    <div class="event-content-btn">
                        <?php echo $btn; ?>
                    </div>
                </div>
                <div class="add-new-event-form-container">
                    <form action="adminEventDataHandler.php" method="post" class="add-new-event-form-content">
                        <input type="hidden" name="eventOrganizer" value="<?php echo $organizationID;?>">
                        <input type="hidden" name="organizationResponsibilty" id="organizationResponsibilty" value="<?php echo $organizationResponsibility;?>">
                        <div class="form-head-txt">
                            <h2 class="form-head-txt">Add New Event</h2>
                        </div>
                        <div class="form-content-row">
                            <div class="form-content-row">
                                <label for="event_name">Event Name:</label>
                                <input type="text" id="event_name" name="event_name" required>
                            </div>
                            <div class="form-content-row">
                                <label for="event_date">Event Date:</label>
                                <input type="date" name="event_date" id="event_date">
                            </div>
                        </div>
                        
                        <div class="form-content-row">
                            <div class="form-content-row">
                                <label for="event_location">Event Location:</label>
                                <input type="text" id="event_location" name="event_location" required>
                            </div>
                        </div>
                        <div class="form-content-row">
                            <div class="form-content-row">
                                <label for="event-penalty">Penalty:</label>
                                <input type="number" id="event-penalty" name="event-penalty" required></input>
                                
                            </div>
                            <div class="form-content-row">
                                <label>Event Time:</label>
                                <input type="time" name="event-time-start" id="event-time-start">
                                <div class="add-new-event-label">—</div>
                                <input type="time" name="event-time-end" id="event-time-end">

                            </div>
                        </div>
                        
                        <div class="form-content-row">
                            <div class="form-content-row">
                                <label for="event-time-duration">Event Time Duration:</label>
                                <select name="event-time-duration" id="event-time-duration" required>
                                    <option value="" disabled selected>Select duration</option>
                                    <option value="Whole Day - AM Time In & AM Time Out & PM Time In & PM Time Out">
                                        Whole Day - AM Time In & AM Time Out & PM Time In & PM Time Out
                                    </option>
                                    <option value="Whole Day - AM Time In & PM Time Out">Whole Day - AM Time In & PM Time Out</option>
                                    <option value="Half Day - AM Time In & AM Time Out">Half Day - AM Time In & AM Time Out</option>
                                    <option value="Half Day - PM Time In & PM Time Out">Half Day - PM Time In & PM Time Out</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-content-row">
                            <div class="form-content-col">
                                <div class="add-new-event-label">Participant:</div>
                                <div id="participant-container">
                                    <!-- Hidden input to store selected participants -->
                                    <input type="hidden" name="event-participants" id="event-participants" value="">

                                    <!-- Button to toggle participant list -->
                                    <button type="button" id="show-program-list-btn">Select Participants</button>

                                    <!-- Participant list dropdown -->
                                     
                                    <div class="program-list" id="program-list" style="display: none;">
                                    </div>

                                </div>
                            </div>
                        </div>

                        
                        <div class="form-content-row">
                            <div class="form-content-row">
                                <label for="am-time-in-start">AM Time In Start:</label>
                                <input type="time" name="am-time-in-start" id="am-time-in-start">
                            </div>
                            <div class="form-content-row">
                                <label for="pm-time-in-start">PM Time In Start:</label>
                                <input type="time" name="pm-time-in-start" id="pm-time-in-start">
                            </div>
                        </div>
                        <div class="form-content-row">
                            <div class="form-content-row">
                                <label for="am-time-in-end">AM Time In End:</label>
                                <input type="time" name="am-time-in-end" id="am-time-in-end">
                            </div>
                            <div class="form-content-row">
                                <label for="pm-time-in-end">PM Time In End:</label>
                                <input type="time" name="pm-time-in-end" id="pm-time-in-end">
                            </div>
                        </div>
                        <div class="form-content-row">
                            <div class="form-content-row">
                                <label for="am-time-out-start">AM Time Out Start:</label>
                                <input type="time" name="am-time-out-start" id="am-time-out-start">
                            </div>
                            <div class="form-content-row">
                                <label for="pm-time-out-start">PM Time Out Start:</label>
                                <input type="time" name="pm-time-out-start" id="pm-time-out-start">
                            </div>
                        </div>
                        <div class="form-content-row">
                            <div class="form-content-row">
                                <label for="am-time-out-end">AM Time Out End:</label>
                                <input type="time" name="am-time-out-end" id="am-time-out-end">
                            </div>
                            <div class="form-content-row">
                                <label for="pm-time-out-end">PM Time Out End:</label>
                                <input type="time" name="pm-time-out-end" id="pm-time-out-end">
                            </div>
                        </div>
                        <div class="form-content-btn">
                            <button type="submit" name="add-new-event-submit">Add New Event</button>
                            <button type="submit" name="add-new-event-submit-cancel" id="add-new-event-btn-cancel" class="add-new-event-btn-cancel">Cancel</button>
                        </div>
                    </form>
                </div>

                <script>

                </script>
                    


                <div class="event-content-event-content">
                    <div class="event-content-top-part-filter">
                        <div class="event-content-search-input-container">
                            <label for="event-content-filter-select-status">Filter By Status:</label>
                            <select name="event-content-filter-select-status" id="event-content-filter-select-status">
                                <option value="" selected>All</option>
                                <option value="upcoming">Upcoming</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="end">End</option>
                            </select>
                            

                            
                            <label for="event-content-filter-select-col">Filter By:</label>
                            <select name="event-content-filter-select-col" id="event-content-filter-select-col">
                                <option value="" selected>None</option>
                                <option value="event_name">Event Name</option>
                                <option value="event_place">Place</option>
                            </select>
                            <input type="text" name="event-content-search-input" id="event-content-search-input" placeholder="Search..." value="<?php echo htmlspecialchars(@$_GET['event-content-search-input']); ?>">
                        </div>
                    </div>

                    <div class="event-content-main-content-container">
                        <div class="event-content-table-content-container">
                            <table id="event-content-table-content" class="event-content-table-content">
                                <thead class="event-content-table-head">
                                    <tr class="event-content-table-row">
                                        <th class="event-content-data-name">Name</th>
                                        <th class="event-content-data-place">Place</th>
                                        <th class="event-content-data-status">Status</th>
                                        <th class="event-content-data-date">Date</th>
                                        <th class="event-content-data-time">Time</th>
                                        <th class="event-content-data-organizer">Event Organizer</th>
                                        <th class="event-content-data-btn-action"></th>
                                    </tr>
                                </thead>
                                <tbody id="event-content-table-body" class="event-content-table-body">
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>
            
        </div>  
    </div>
    


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const eventTableBody = document.getElementById('event-content-table-body');
            const searchInput = document.getElementById('event-content-search-input');
            const statusFilter = document.getElementById('event-content-filter-select-status');
            const columnFilter = document.getElementById('event-content-filter-select-col');
            let events;
            // Fetch all events from the backend
            async function fetchEvents() {
                try {
                    const response = await fetch('fetchEvents.php');
                    events = await response.json();
                    displayEvents(events);
                } catch (error) {
                    console.error('Error in fetching or displaying events:', error);
                }
            }
            fetchEvents();
            // Display events in the table
            function displayEvents(events) {
                eventTableBody.innerHTML = ''; // Clear the current table content

                events.forEach(event => {
                    const row = document.createElement('tr');
                    row.classList.add('event-content-table-row');

                    row.innerHTML = `
                        <td class="event-content-data-name">${event.event_name}</td>
                        <td class="event-content-data-place">${event.event_place}</td>
                        <td class="event-content-data-status">${event.event_status}</td>
                        <td class="event-content-data-date">${event.event_date}</td>
                        <td class="event-content-data-time">${event.event_time}</td>
                        <td class="event-content-data-organizer">${event.organization_name}</td>
                        <td class="event-content-data-btn-action">
                            <form action="adminEventDataHandler.php" method="post">
                                <input type="hidden" name="event-id" value="${event.event_id}">
                                <button type="Submit" class="user-view" name="vSubmit">VIEW</button>
                                <button type="Submit" class="user-delete" name="dSubmit" value="1">DELETE</button>
                            </form>
                        </td>
                    `;
                    eventTableBody.appendChild(row);
                });

                // Confirmation for delete button
                document.addEventListener('click', function (event) {
                    if (event.target.classList.contains('user-delete')) {
                        event.preventDefault(); // Prevent form submission immediately
                        
                        const confirmation = confirm('Are you sure you want to delete this event?');
                        
                        if (confirmation) {
                            // Find the form that contains the delete button
                            const form = event.target.closest('form');

                            // Create a hidden input to pass the value of dSubmit if not already present
                            if (form && !form.querySelector('input[name="dSubmit"]')) {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'dSubmit';
                                input.value = '1'; // Set the value for dSubmit
                                form.appendChild(input);
                            }
                            form.submit();

                        }
                    }
                });


            }
            
            function filterEvents(events) {
                const searchQuery = searchInput.value.toLowerCase();
                const selectedStatus = statusFilter.value.toLowerCase();
                const selectedColumn = columnFilter.value;

                return events.filter(event => {
                    const matchesSearch = selectedColumn
                        ? event[selectedColumn]?.toLowerCase().includes(searchQuery)
                        : Object.values(event).some(value => value.toLowerCase().includes(searchQuery));

                    const matchesStatus = selectedStatus === "" || event.event_status.toLowerCase() === selectedStatus;

                    return matchesSearch && matchesStatus;
                });
            }

            // Event listeners for search and filter
            searchInput.addEventListener('input', () => {
                const filteredEvents = filterEvents(events);
                displayEvents(filteredEvents);
            });

            statusFilter.addEventListener('change', () => {
                const filteredEvents = filterEvents(events);
                displayEvents(filteredEvents);
            });

            columnFilter.addEventListener('change', () => {
                const filteredEvents = filterEvents(events);
                displayEvents(filteredEvents);
            });

            // Function to show/hide time inputs based on event time duration selection
            function updateTimeInputs() {
                const eventTimeDuration = document.getElementById("event-time-duration").value;
                // Get all the AM and PM input elements
                const amTimeInInputs = document.querySelectorAll("[id^='am-time-in']");
                const amTimeOutInputs = document.querySelectorAll("[id^='am-time-out']");
                const pmTimeInInputs = document.querySelectorAll("[id^='pm-time-in']");
                const pmTimeOutInputs = document.querySelectorAll("[id^='pm-time-out']");

                // Hide all time inputs initially
                amTimeInInputs.forEach(input => input.closest('.form-content-row').style.display = "none");
                amTimeOutInputs.forEach(input => input.closest('.form-content-row').style.display = "none");
                pmTimeInInputs.forEach(input => input.closest('.form-content-row').style.display = "none");
                pmTimeOutInputs.forEach(input => input.closest('.form-content-row').style.display = "none");

                // Show the time inputs based on the selected duration
                if (eventTimeDuration === "Whole Day - AM Time In & AM Time Out & PM Time In & PM Time Out") {
                    // Show all AM and PM time inputs for a whole day
                    amTimeInInputs.forEach(input => input.parentElement.style.display = "flex");
                    amTimeOutInputs.forEach(input => input.parentElement.style.display = "flex");
                    pmTimeInInputs.forEach(input => input.parentElement.style.display = "flex");
                    pmTimeOutInputs.forEach(input => input.parentElement.style.display = "flex");
                } else if (eventTimeDuration === "Whole Day - AM Time In & PM Time Out") {
                    // Show only AM and PM "In" and "Out" for whole day with AM time in and PM time out
                    amTimeInInputs.forEach(input => input.parentElement.style.display = "flex");
                    pmTimeOutInputs.forEach(input => input.parentElement.style.display = "flex");
                } else if (eventTimeDuration === "Half Day - PM Time In & PM Time Out") {
                    // Show only PM time inputs for half-day PM event
                    pmTimeInInputs.forEach(input => input.parentElement.style.display = "flex");
                    pmTimeOutInputs.forEach(input => input.parentElement.style.display = "flex");
                } else if (eventTimeDuration === "Half Day - AM Time In & AM Time Out") {
                    // Show only AM time inputs for half-day AM event
                    amTimeInInputs.forEach(input => input.parentElement.style.display = "flex");
                    amTimeOutInputs.forEach(input => input.parentElement.style.display = "flex");
                }
            }

            // Add event listener to run updateTimeInputs when the selection changes
            document.getElementById("event-time-duration").addEventListener("change", updateTimeInputs);

            window.onload = updateTimeInputs;
            


            const participantList = document.getElementById('program-list');
            const participantInput = document.getElementById('event-participants');
            const showParticipantListBtn = document.getElementById('show-program-list-btn');

            // Show the participant list when the button is clicked
            showParticipantListBtn.addEventListener('click', function () {
                participantList.style.display = 'block';
                fetchParticipants();
            });

            // Hide the participant list when clicking outside
            document.addEventListener('click', function (event) {
                if (!participantList.contains(event.target) && event.target !== showParticipantListBtn) {
                    participantList.style.display = 'none';
                }
            });

            // Fetch participants based on organization responsibilities
            async function fetchParticipants() {
                const organizationResponsibility = document.getElementById('organizationResponsibilty').value;

                try {
                    const response = await fetch('fetchProgramByID.php', {
                        method: 'POST',
                        body: JSON.stringify({ organizationResponsibility }),
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    });

                    const data = await response.json();
                    if (data.success) {
                        const programs = data.programs; // Assuming 'programs' contains participant data
                        displayParticipants(programs, organizationResponsibility);
                    } else {
                        console.error('Error fetching participants:', data.message || 'No message returned');
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                }
            }

            // Display participants in a hierarchical format
            function displayParticipants(programs, organizationResponsibility) {
                participantList.innerHTML = ''; // Clear existing content

                // Parse organization responsibilities
                const responsibilities = organizationResponsibility.split(',');

                responsibilities.forEach(responsibility => {
                    const parts = responsibility.trim().split('-');
                    const isAll = parts[0].trim().toLowerCase() === 'all';
                    const levelOrProgram = parts[1]?.trim();

                    if (isAll && levelOrProgram) {
                        // Display participants for the entire program level
                        const filteredPrograms = programs.filter(
                            program => 
                                String(program.program_level).trim().toLowerCase() === String(levelOrProgram).trim().toLowerCase() ||
                                String(program.program_name).trim().toLowerCase() === String(levelOrProgram).trim().toLowerCase()
                        );

                        console.log(filteredPrograms);

                        programs.forEach(program => {
                            console.log(`Program Level: "${program.program_level}", Program Name: "${program.program_name}", Comparing to: "${levelOrProgram}"`);
                        });

                        console.log(`Filtered Level or Program: "${levelOrProgram}"`);

                        displayAllParticipants(filteredPrograms, `All - ${capitalize(levelOrProgram)}`);
                    } else {
                        // Display specific program and its details
                        const programName = parts[0]?.trim();
                        const year = parts[1]?.trim();
                        const section = parts[2]?.trim();

                        displaySpecificParticipants(programs, programName, year, section);
                    }
                });

                participantList.style.display = 'block';
            }
            // Helper to display all participants for a program level
            function displayAllParticipants(programs, title) {
                const groupDiv = document.createElement('div');
                groupDiv.classList.add('program-group');

                const AllGroupDiv = document.createElement('div');
                AllGroupDiv.classList.add('program-year');

                // Wrap "All - program" title in a div with class "program-all" for adding as participant
                const allYearDiv = document.createElement('div');
                allYearDiv.classList.add('program-all');
                allYearDiv.innerHTML = title;
                allYearDiv.addEventListener('click', () => addParticipant(title)); // Add as participant
                AllGroupDiv.appendChild(allYearDiv);  // Insert the "All - program" title into the program-year div

                groupDiv.appendChild(AllGroupDiv); // Append to the groupDiv

                programs.forEach(program => {
                    const programDiv = document.createElement('div');
                    programDiv.classList.add('program-item');
                    programDiv.innerHTML = `<strong>${program.program_name}</strong>`;
                    AllGroupDiv.appendChild(programDiv); // Append program name to the AllGroupDiv

                    program.program_year_grade_levels.forEach(year => {
                        const yearDiv = document.createElement('div');
                        yearDiv.classList.add('program-year');
                        
                        // Wrap each year in a div for clarity
                        const yearTitleDiv = document.createElement('div');
                        yearTitleDiv.classList.add('program-year-title');
                        yearTitleDiv.innerHTML = `All - ${program.program_name} - ${year}`;
                        yearDiv.appendChild(yearTitleDiv);

                        yearTitleDiv.addEventListener('click', () => addParticipant(`All - ${program.program_name} - ${year}`)); // Add as participant
                        programDiv.appendChild(yearDiv);

                        program.program_sections.forEach(section => {
                            const sectionDiv = document.createElement('div');
                            sectionDiv.classList.add('program-section');
                            sectionDiv.innerHTML = `${program.program_name} - ${year} - ${section}`;
                            sectionDiv.addEventListener('click', () =>
                                addParticipant(`${program.program_name} - ${year} - ${section}`)
                            );
                            yearDiv.appendChild(sectionDiv);
                        });
                    });
                });

                participantList.appendChild(groupDiv);
            }

            // Helper to display specific participants
            function displaySpecificParticipants(programs, programName, year, section) {
                programs.forEach(program => {
                    if (program.program_name === programName) {
                        const programDiv = document.createElement('div');
                        programDiv.classList.add('program-item');

                        // Wrap "All - program" in a div with class "program-all" for adding as participant
                        const AllYearDiv = document.createElement('div');
                        AllYearDiv.classList.add('program-all');
                        AllYearDiv.innerHTML = `All - ${program.program_name}`;
                        AllYearDiv.addEventListener('click', () => addParticipant(`All - ${program.program_name}`)); // Add as participant
                        programDiv.appendChild(AllYearDiv);

                        if (year) {
                            const yearDiv = document.createElement('div');
                            yearDiv.classList.add('program-year');
                            const yearTitleDiv = document.createElement('div');
                            yearTitleDiv.classList.add('program-year-title');
                            yearTitleDiv.innerHTML = `${program.program_name} - ${year}`;
                            yearDiv.appendChild(yearTitleDiv);
                            programDiv.appendChild(yearDiv);

                            if (section) {
                                const sectionDiv = document.createElement('div');
                                sectionDiv.classList.add('program-section');
                                sectionDiv.innerHTML = `${program.program_name} - ${year} - ${section}`;
                                sectionDiv.addEventListener('click', () =>
                                    addParticipant(`${program.program_name} - ${year} - ${section}`)
                                );
                                yearDiv.appendChild(sectionDiv);
                            } else {
                                yearTitleDiv.addEventListener('click', () =>
                                    addParticipant(`${program.program_name} - ${year}`)
                                );
                            }
                        } else {
                            programDiv.addEventListener('click', () => addParticipant(program.program_name));
                        }

                        participantList.appendChild(programDiv);
                    }
                });
            }


            // Capitalize first letter of a string
            function capitalize(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }
            let selectedParticipants = {};
            const programList = document.getElementById('program-list');
            const inputField = document.getElementById('event-participants');
            const participantContainer = document.getElementById('participant-container');
            const showProgramListBtn = document.getElementById('show-program-list-btn');
            const addNewEventBtnOpen = document.getElementById('add-new-event-btn-show');
            const addNewEventBtnClose = document.getElementById('add-new-event-btn-cancel');
            const addNewEventContainer = document.querySelector('.add-new-event-form-container');

                // Show the program list when the "Select Participants" button is clicked
                showProgramListBtn.addEventListener('click', function () {
                    programList.style.display = 'block'; // Show the program list
                });

                // Hide the program list when clicking outside
                document.addEventListener('click', function (event) {
                    if (!programList.contains(event.target) && event.target !== showProgramListBtn) {
                        programList.style.display = 'none'; // Hide the program list
                    }
                });

                if (addNewEventBtnOpen) {
                    addNewEventBtnOpen.addEventListener('click', () => {
                        const addNewEventContainer = document.querySelector('.add-new-event-form-container');
                        if (addNewEventContainer) {
                            addNewEventContainer.style.display = "flex";
                        }
                    });
                }
                // Close the form
                addNewEventBtnClose.addEventListener('click', () => {
                    addNewEventContainer.style.display = "none";
                });


                // Show the program list when the input field is focused
                inputField.addEventListener('focus', () => {
                    programList.style.display = 'block';
                });

                // Show the program list when the "Select Participants" button is clicked
                showProgramListBtn.addEventListener('click', function () {
                    programList.style.display = 'block';
                });

                // Hide the program list when clicking outside
                document.addEventListener('click', function (event) {
                    if (!programList.contains(event.target) && event.target !== inputField && event.target !== showProgramListBtn) {
                        programList.style.display = 'none';
                    }
                });
            
            function addParticipant(program) {
                const [programName, programYear, programSection] = program.split(" - ");

                if (!selectedParticipants[programName]) {
                    selectedParticipants[programName] = [];
                }

                // Handle "All" conditions
                if (programYear === undefined || programYear === "All Levels") {
                    selectedParticipants[programName] = ['All Levels'];
                } else if (programYear === "All Year") {
                    // Only add "All Year" if no specific sections or years exist
                    if (
                        !selectedParticipants[programName].some(
                            entry => entry !== "All Levels" && entry !== "All Year"
                        )
                    ) {
                        selectedParticipants[programName] = ['All Year'];
                    }
                } else {
                    // Remove "All Levels" or "All Year" if adding a specific year/section
                    const indexAllLevels = selectedParticipants[programName].indexOf("All Levels");
                    const indexAllYear = selectedParticipants[programName].indexOf("All Year");

                    if (indexAllLevels > -1) {
                        selectedParticipants[programName].splice(indexAllLevels, 1);
                    }
                    if (indexAllYear > -1) {
                        selectedParticipants[programName].splice(indexAllYear, 1);
                    }

                    const formattedParticipant = programSection ? `${programYear} - ${programSection}` : programYear;
                    if (!selectedParticipants[programName].includes(formattedParticipant)) {
                        selectedParticipants[programName].push(formattedParticipant);
                    }
                }

                updateProgramDisplay();
                updateParticipantsInput();
                createParticipantBox(program); // Display the participant box
            }


            // Function to update the participants input field
            function updateParticipantsInput() {
                let participants = [];
                for (const programName in selectedParticipants) {
                    const selectedYears = selectedParticipants[programName];
                    if (selectedYears.includes("All Year")) {
                        participants.push(`${programName} - All Year`);
                    } else {
                        selectedYears.forEach(year => participants.push(`${programName} - ${year}`));
                    }
                }
                inputField.value = participants.join(', ');
                console.log("inputField value: " + inputField.value);
            }

            // Function to update the program display (highlight selected ones)
            function updateProgramDisplay() {
                const programList = document.querySelectorAll('.program-year');

                programList.forEach(item => {
                    const programText = item.innerText;
                    const [programName, programYear] = programText.split(" - ");

                    if (selectedParticipants[programName] && selectedParticipants[programName].includes(programYear)) {
                        item.classList.add('selected');
                    } else {
                        item.classList.remove('selected');
                    }

                    // Hide individual years if "All Year" is selected
                    if (selectedParticipants[programName] && selectedParticipants[programName].includes("All Year")) {
                        const individualYears = item.parentElement.querySelectorAll('.program-year');
                        individualYears.forEach(year => year.style.display = 'none');
                    } else {
                        item.style.display = 'block'; // Show individual years if not "All Year"
                    }
                });
            }

            // Function to create a participant box
            function createParticipantBox(program) {
                const participantBox = document.createElement('span');
                participantBox.className = 'participant-box';
                participantBox.id = `participant-box-${program.replace(/\s+/g, '_')}`;
                participantBox.textContent = program;

                const removeButton = document.createElement('span');
                removeButton.className = 'remove-button';
                removeButton.textContent = '✕';

                removeButton.onclick = function () {
                    participantContainer.removeChild(participantBox);
                    removeParticipant(program);
                };

                participantBox.appendChild(removeButton);
                participantContainer.insertBefore(participantBox, inputField);
            }

            function removeParticipant(program) {
                const [programName, programYear, programSection] = program.split(" - ");
                const formattedParticipant = programSection ? `${programYear} - ${programSection}` : programYear;

                // Check if the program exists in selectedParticipants
                if (selectedParticipants[programName]) {
                    if (formattedParticipant === "All Levels" || formattedParticipant === "All Year") {
                        // Remove all entries for the program
                        delete selectedParticipants[programName];
                    } else {
                        // Remove the specific year/section
                        const index = selectedParticipants[programName].indexOf(formattedParticipant);
                        if (index > -1) {
                            selectedParticipants[programName].splice(index, 1);
                        }

                        // Remove the program entirely if no years are left
                        if (selectedParticipants[programName].length === 0) {
                            delete selectedParticipants[programName];
                        }
                    }
                }

                // Update input field value
                updateParticipantsInput();

                // Update program display
                updateProgramDisplay();

                console.log("Updated selectedParticipants:", selectedParticipants);
                console.log("inputField value:", inputField.value);
            }


            // Update program list (hide/remove from the dropdown)
            function updateProgramList() {
                const programListItems = document.querySelectorAll('.program-item');
                programListItems.forEach(item => {
                    const programName = item.querySelector('.program-name').innerText;
                    const programYear = item.querySelector('.program-year').innerText;
                    const programText = `${programName} - ${programYear}`;

                    if (selectedParticipants[programName] && selectedParticipants[programName].includes(programYear)) {
                        item.classList.add('selected');
                    } else {
                        item.classList.remove('selected');
                    }
                });
            }

            // Helper function to remove participant from the dropdown list
            function addProgramBackToList(program) {
                const programListItems = document.querySelectorAll('.program-item');
                programListItems.forEach(item => {
                    const programText = item.querySelector('.program-year').innerText;
                    if (programText === program) {
                        item.style.display = 'block'; // Show the program in the dropdown
                    }
                });
            }

        });

    </script>


</body>
</html>