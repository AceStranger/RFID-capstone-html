<?php
session_start();
include "dbh.php";
if(@!isset($_SESSION['user_ID']) || $_SESSION['user_ID'] === null) {
    header("Location: LogInPage.html");
    exit();
}
$user_ID = @$_SESSION['user_ID'];
$query = "SELECT * FROM user WHERE user_id = $user_ID";
$result = mysqli_query($conn, $query);
$userInfo = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization</title>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminOfficerAssignPage.css">
    <script src="js\adminSidebar.js"></script>
</head>
<body>
    <div class="main-content-container">
        <?php include_once"adminSidebar.php";?>
        <div class="main-content">
            <div class="organization-data-content">

                <div class="organization-data-content-top-part">
                    <h1 class="organization-data-content-head-text">OFFICER</h1>
                </div>
                
                <div class="organization-data-content-main-content">
                    <div class="organization-data-content-info-content">
                        <?php
                            if (isset($_SESSION['officer-id']) && isset($_SESSION['request'])) {
                                $officerID = $_SESSION['officer-id'];
                                $request = $_SESSION['request'];
                            } else {
                                echo $_SESSION['officer-id'];
                                echo "<br/>-";
                                echo $_SESSION['request'];
                                die("Officer ID or request type is missing.");
                            }

                            $query = "SELECT * FROM officer WHERE officer_id = $officerID LIMIT 1";
                            $result = mysqli_query($conn, $query);
                            $officerInfo = $result->fetch_assoc();


                            $inputAvailability = "";
                            if ($request === "view-officer") {
                                $inputAvailability = "disabled";
                            } elseif ($request === "edit-officer") {
                                $inputAvailability = "";
                            }

                            // Define buttons based on the request type
                            $btn = '';
                            if ($request === "view-officer") {
                                $btn = '
                                    <div class="officer-data-content-btn">
                                        <button type="submit" name="eSubmit" class="officer-data-btn-edit">EDIT</button>
                                    </div>';
                            } 
                            if ($request === "edit-officer") {
                                $btn = '
                                    <div class="officer-data-content-btn">
                                        <button type="submit" name="uSubmit" class="user-update">UPDATE</button>
                                        <button type="submit" name="cSubmit" class="user-cancel">CANCEL</button>
                                    </div>';
                            }

                            $usersID = htmlspecialchars($officerInfo["user_id"]);
                            $organizationsID = htmlspecialchars($officerInfo["organization_id"]);


                            $usersQuery = "SELECT 
                                        CONCAT(u.user_firstname, ' ', u.user_middlename, ' ', u.user_lastname, ' ', u.user_suffixname) AS full_name 
                                        FROM user u WHERE user_id = '$usersID' LIMIT 1
                                    ";
                            $usersQueryResult = mysqli_query($conn, $usersQuery);
                            $usersInfo = $usersQueryResult->fetch_assoc();

                            $organizationInfo = '';

                            if (isset($_SESSION['organization-id']) && isset($_SESSION['request'])) {
                                $organizationID = $_SESSION['organization-id'];
                                if($organizationsID === $organizationID) {
                                    $query = "SELECT *, organization_responsibility FROM organization WHERE organization_id = $organizationID";
                                    $result = mysqli_query($conn, $query);
                                    $organizationInfo = $result->fetch_assoc();
                                } else {
                                    die("Organization is not the same as the one from the one you sent and from the Database.");
                                }
                                $request = $_SESSION['request'];
                            } else {
                                die("Event ID or request type is missing.");
                            }




                            $usersFullname = $usersInfo['full_name'];
                            $officersPosition = htmlspecialchars($officerInfo["officer_position"]);
                            $officersDuty = htmlspecialchars($officerInfo["officer_duty"]);
                            $officersResponsibility = htmlspecialchars($officerInfo["officer_responsibility"]);
                            $officersAssignedDuty = htmlspecialchars($officerInfo["officer_assign_duty"]);
                            $organizationName = htmlspecialchars(string: $organizationInfo["organization_name"]);
                            $organizationResponsibility = htmlspecialchars($organizationInfo["organization_responsibility"]);
                            $organizationResponsibility = strtolower($organizationResponsibility);



                            // Define arrays for different program levels
                            $primarySchool = [];
                            $secondarySchool = [];
                            $college = [];

                            // Adjust query logic to retrieve programs based on responsibility
                            $programQuery = "SELECT 
                                                program_id, program_name, department_id, program_level, 
                                                program_year_grade_level, section 
                                            FROM `program`";

                            $result = mysqli_query($conn, $programQuery);

                            if ($result && mysqli_num_rows($result) > 0) {
                                while($programInfoRow = mysqli_fetch_array($result)){
                                    $programLevel = $programInfoRow['program_level'];

                                    if (str_contains($programLevel, "primary")) {
                                        $primarySchool[] = $programInfoRow['program_name'];
                                    }
                                    if (str_contains($programLevel, "secondary")) {
                                        $secondarySchool[] = $programInfoRow['program_name'];
                                    }
                                    if (str_contains($programLevel, "college")) {
                                        $college[] = $programInfoRow['program_name'];
                                    }
                                }
                            }
                            



                            // // Query to get all distinct program levels from the program table
                            // $programLevelQuery = "SELECT DISTINCT program_level FROM program";
                            // $programLevelResult = mysqli_query($conn, $programLevelQuery);
                            // $programLevels = [];

                            // if ($programLevelResult && mysqli_num_rows($programLevelResult) > 0) {
                            //     while ($row = mysqli_fetch_assoc($programLevelResult)) {
                            //         $programLevels[] = strtolower($row['program_level']); // Store in lowercase for comparison
                            //     }
                            // }

                            // $responsibilities = []; // Initialize the array to store responsibilities

                            // // Check if $organizationResponsibility contains commas
                            // if (strpos($organizationResponsibility, ',') !== false) {
                            //     // Split by comma into multiple responsibilities
                            //     $responsibilityParts = explode(',', $organizationResponsibility);

                            //     foreach ($responsibilityParts as $responsibility) {
                            //         $responsibility = trim($responsibility);
                            //         $details = explode(' - ', $responsibility);

                            //         // First word should be "All", we need to check the second word
                            //         if (strtolower($details[0]) === "all") {
                            //             if (isset($details[1])) {
                            //                 $secondWord = strtolower($details[1]);

                            //                 // Check if the second word is a program level
                            //                 if (in_array($secondWord, $programLevels)) {
                            //                     // Handle program level (e.g., "All - College", "All - Secondary")
                            //                     $programQuery = "SELECT DISTINCT program_name, program_year_grade_level, section 
                            //                                     FROM program 
                            //                                     WHERE program_level = '" . mysqli_real_escape_string($conn, $secondWord) . "'";
                            //                     $result = mysqli_query($conn, $programQuery);

                            //                     if ($result && mysqli_num_rows($result) > 0) {
                            //                         $programSections = []; // To store year and sections by program
                            //                         var_dump($row);  // Debugging line
                            //                         var_dump($responsibilities); // Debugging line
                            //                         while ($row = mysqli_fetch_assoc($result)) {
                            //                             $programSections[$row['program_name']][$row['program_year_grade_level']][] = $row['section'];
                            //                         }
                            //                         // Expand each program's years and sections
                            //                         foreach ($programSections as $programName => $yearSections) {
                            //                             foreach ($yearSections as $year => $sections) {
                            //                                 // Check if year contains a comma and split accordingly
                            //                                 $years = strpos($year, ',') !== false ? explode(',', $year) : [$year];

                            //                                 foreach ($years as $expandedYear) {
                            //                                     $expandedYear = trim($expandedYear); // Remove any surrounding spaces
                            //                                     $responsibilities[] = "All - $programName - $expandedYear"; // All year level

                            //                                     // Check if section contains a comma and split accordingly
                            //                                     foreach ($sections as $section) {
                            //                                         $sectionValues = strpos($section, ',') !== false ? explode(',', $section) : [$section];

                            //                                         foreach ($sectionValues as $expandedSection) {
                            //                                             $expandedSection = trim($expandedSection); // Remove any surrounding spaces
                            //                                             $responsibilities[] = "$programName - $expandedYear - $expandedSection"; // For each section
                            //                                         }
                            //                                     }
                            //                                 }
                            //                             }
                            //                         }

                            //                     }
                            //                 } else {
                            //                     // Handle program name and level (e.g., "All - BSED", "All - BSCRIM - 1st YEAR")
                            //                     $programName = $details[1];

                            //                     // Query to get all years and sections for the program
                            //                     $query = "SELECT DISTINCT program_year_grade_level, section FROM program WHERE program_name = '" . mysqli_real_escape_string($conn, $programName) . "'";
                            //                     $result = mysqli_query($conn, $query);
                            //                     $yearSections = [];
                            //                     if ($result && mysqli_num_rows($result) > 0) {
                            //                         while ($row = mysqli_fetch_assoc($result)) {
                            //                             $yearSections[$row['program_year_grade_level']][] = $row['section'];
                            //                         }
                            //                     }
                                                
                            //                     foreach ($yearSections as $year => $sections) {
                            //                         // Check if year contains a comma and split accordingly
                            //                         $years = strpos($year, ',') !== false ? explode(',', $year) : [$year];
                                                
                            //                         foreach ($years as $expandedYear) {
                            //                             $expandedYear = trim($expandedYear); // Remove any surrounding spaces
                            //                             // Responsibility for each year
                            //                             $responsibilities[] = "All - $programName - $expandedYear";
                                                
                            //                             // Check if section contains a comma and split accordingly
                            //                             foreach ($sections as $section) {
                            //                                 $sectionValues = strpos($section, ',') !== false ? explode(',', $section) : [$section];
                                                
                            //                                 foreach ($sectionValues as $expandedSection) {
                            //                                     $expandedSection = trim($expandedSection); // Remove any surrounding spaces
                            //                                     // Responsibility for each section
                            //                                     $responsibilities[] = "$programName - $expandedYear - $expandedSection";
                            //                                 }
                            //                             }
                            //                         }
                            //                     }
                                                
                            //                 }
                            //             }
                            //         } else {
                            //             // Handle other responsibilities without "All" (specific program, year, and section)
                            //             if (isset($details[0]) && isset($details[1]) && isset($details[2])) {
                            //                 $program = $details[0] ?? null;
                            //                 $year = $details[1] ?? null;
                            //                 $section = $details[2] ?? null;

                            //                 if ($program && $year && $section) {
                            //                     // Split year and section if they contain commas
                            //                     $years = strpos($year, ',') !== false ? explode(',', $year) : [$year];
                            //                     $sections = strpos($section, ',') !== false ? explode(',', $section) : [$section];
                                            
                            //                     foreach ($years as $expandedYear) {
                            //                         $expandedYear = trim($expandedYear); // Remove any surrounding spaces
                            //                         foreach ($sections as $expandedSection) {
                            //                             $expandedSection = trim($expandedSection); // Remove any surrounding spaces
                            //                             $responsibilities[] = "$program - $expandedYear - $expandedSection";
                            //                         }
                            //                     }
                            //                 } elseif ($program && $year) {
                            //                     // Query to get sections for the year and program
                            //                     $query = "SELECT DISTINCT section FROM program WHERE program_name = '" . mysqli_real_escape_string($conn, $program) . "' AND program_year_grade_level = '" . mysqli_real_escape_string($conn, $year) . "'";
                            //                     $result = mysqli_query($conn, $query);
                                            
                            //                     if ($result && mysqli_num_rows($result) > 0) {
                            //                         while ($row = mysqli_fetch_assoc($result)) {
                            //                             $responsibilities[] = "$program - $year - {$row['section']}";
                            //                         }
                            //                     } else {
                            //                         $responsibilities[] = "$program - $year";
                            //                     }
                            //                 } else {
                            //                     $responsibilities[] = $program;
                            //                 }
                                            
                            //             }
                            //         }
                            //     }
                            // } else {
                            //     // Single responsibility case
                            //     $details = explode(' - ', $organizationResponsibility);

                            //     if (strtolower($details[0]) === "all") {
                            //         if (isset($details[1])) {
                            //             $programName = $details[1];

                            //             // Query to get all years and sections for the program
                            //             $query = "SELECT DISTINCT program_year_grade_level, section FROM program WHERE program_name = '" . mysqli_real_escape_string($conn, $programName) . "'";
                            //             $result = mysqli_query($conn, $query);
                            //             $yearSections = [];
                            //             if ($result && mysqli_num_rows($result) > 0) {
                            //                 while ($row = mysqli_fetch_assoc($result)) {
                            //                     // Split the year and section if they contain commas
                            //                     $years = strpos($row['program_year_grade_level'], ',') !== false ? explode(',', $row['program_year_grade_level']) : [$row['program_year_grade_level']];
                            //                     $sections = strpos($row['section'], ',') !== false ? explode(',', $row['section']) : [$row['section']];
                                                
                            //                     // Store year and sections in the array
                            //                     foreach ($years as $year) {
                            //                         $year = trim($year); // Remove any surrounding spaces
                            //                         foreach ($sections as $section) {
                            //                             $section = trim($section); // Remove any surrounding spaces
                            //                             $yearSections[$year][] = $section;
                            //                         }
                            //                     }
                            //                 }
                            //             }
                                        
                            //             foreach ($yearSections as $year => $sections) {
                            //                 // Responsibility for each year level
                            //                 $responsibilities[] = "All - $programName - $year";
                            //                 foreach ($sections as $section) {
                            //                     // Responsibility for each section
                            //                     $responsibilities[] = "$programName - $year - $section";
                            //                 }
                            //             }
                                        
                            //         }
                            //     } else {
                            //         $program = $details[0] ?? null;
                            //         $year = $details[1] ?? null;
                            //         $section = $details[2] ?? null;
                            //         if ($program) {
                            //             if ($year && $section) {
                            //                 // Split year and section by commas if they contain multiple values
                            //                 $years = strpos($year, ',') !== false ? explode(',', $year) : [$year];
                            //                 $sections = strpos($section, ',') !== false ? explode(',', $section) : [$section];
                                            
                            //                 // Loop through years and sections and add them to responsibilities
                            //                 foreach ($years as $y) {
                            //                     $y = trim($y); // Remove any surrounding spaces
                            //                     foreach ($sections as $s) {
                            //                         $s = trim($s); // Remove any surrounding spaces
                            //                         $responsibilities[] = "$program - $y - $s"; // Add year-section combination
                            //                     }
                            //                 }
                            //             } elseif ($year) {
                            //                 // Query to get sections for the year and program if only year is provided
                            //                 $query = "SELECT DISTINCT section FROM program WHERE program_name = '" . mysqli_real_escape_string($conn, $program) . "' AND program_year_grade_level = '" . mysqli_real_escape_string($conn, $year) . "'";
                            //                 $result = mysqli_query($conn, $query);
                                    
                            //                 if ($result && mysqli_num_rows($result) > 0) {
                            //                     // If sections are found, loop through them
                            //                     while ($row = mysqli_fetch_assoc($result)) {
                            //                         // Split section if it contains commas
                            //                         $sections = strpos($row['section'], ',') !== false ? explode(',', $row['section']) : [$row['section']];
                            //                         foreach ($sections as $section) {
                            //                             $responsibilities[] = "$program - $year - $section"; // Add the responsibility
                            //                         }
                            //                     }
                            //                 } else {
                            //                     $responsibilities[] = "$program - $year"; // If no sections, just add year
                            //                 }
                            //             } else {
                            //                 $responsibilities[] = $program; // If only program is provided
                            //             }
                            //         }                                    
                            //     }
                            // }


                            $assignDutyOptions = [
                                "Head of the Organization", "Second-in-Command of the Organization", "Organization Members"
                            ];                        

                        ?>
                        <form action='adminOfficerAssignDataHandler.php' method='POST' id="organization-data-content-info-form-content" class='organization-data-content-info-form-content'>
                            <div class='organization-data-content-info-col'>
                                <input type="hidden" id="officer-id" name="officer-id" value="<?php echo $officerID;?>">
                                <input type='hidden' id='organization-id' name='organization-id' value='<?php echo $organizationID;?>'>
                                <div class='organization-data-content-info-row'>
                                    <label for='organizationName'>Organization Name:</label>
                                    <input type='text' name='organizationName' id='organizationName' value='<?php echo $organizationName;?>'  disabled>
                                </div>
                                <div class='organization-data-content-info-row'>
                                    <label for='organizationResponsibilty'>Organization Responsibilty:</label>
                                    <input type='text' name='organizationResponsibilty' id='organizationResponsibilty' value='<?php echo $organizationResponsibility;?>' disabled>
                                </div>
                                <div class='organization-data-content-info-row'>
                                    <label for='officer-name'>Officer's Name:</label>
                                    <input type='text' name='officer-name' id='officer-name' value='<?php echo $usersFullname;?>' disabled>
                                </div>
                                <div class='organization-data-content-info-row'>
                                    <label for='officer-position'>Officer's Position:</label>
                                    <input type='text' name='officer-position' id='officer-position' value='<?php echo $officersPosition;?>' disabled>
                                </div>
                                <div class='organization-data-content-info-row'>
                                    <label for='officer-duty'>Officer's Duty:</label>
                                    <select name='officer-duty' id='officer-duty' <?php echo $inputAvailability; ?>>
                                        <option value="" disabled selected>Select Duty</option>
                                        <?php foreach ($assignDutyOptions as $options): ?>
                                            <option value="<?php echo $options; ?>" <?php echo ($officersDuty === $options) ? 'selected' : ''; ?>>
                                                <?php echo $options; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="organization-data-content-info-row">
                                    <label for="officer-responsibility">Officer's Responsibility:</label>
                                    <input type="text" name="officer-responsibility" id="officer-responsibility" value="<?php echo  $officersResponsibility; ?>" readonly>

                                    <button type="button" id="show-responsibility-list-btn" <?php echo $inputAvailability; ?>>Select Responsibility</button>

                                    <div class="responsibility-list" id="responsibility-list" style="display: none;">
                                        <!-- The responsibility list will be dynamically populated here -->
                                    </div>

                                    <!-- Container for selected responsibilities -->
                                    <div id="selected-responsibilities">
                                        <!-- Selected responsibilities will appear here with a minus button -->
                                    </div>
                                </div>





                                <div class='organization-data-content-info-row'>
                                    <label for='officer-assign-duty'>Officer's Assigned Duty:</label>
                                    <select name='officer-assign-duty' id='officer-assign-duty' <?php echo $inputAvailability; ?>>
                                        <option value="" disabled>Select Duty</option>
                                        <option value="Manage Event Registration" <?php echo (str_contains($officersAssignedDuty, needle: "Manage Event Registration")) ? 'selected' : ''; ?> >Manage Event Registration</option>
                                        <option value="Manage Event Report" <?php echo (str_contains($officersAssignedDuty, "Manage Event Report")) ? 'selected' : ''; ?>>Manage Event Report</option>
                                        <option value="Manage Event Attendance" <?php echo (str_contains($officersAssignedDuty, "Manage Event Attendance")) ? 'selected' : ''; ?>>Manage Event Attendance</option>
                                    </select>
                                </div>


                                <?php echo $btn;?>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>  
    </div>
    

    <script>

        document.addEventListener('DOMContentLoaded', function () {
            const responsibilityList = document.getElementById('responsibility-list');
            const responsibilityInput = document.getElementById('officer-responsibility');
            const showResponsibilityListBtn = document.getElementById('show-responsibility-list-btn');
            const selectedResponsibilitiesContainer = document.getElementById('selected-responsibilities');

            // Show the responsibility list when the button is clicked
            showResponsibilityListBtn.addEventListener('click', function () {
                responsibilityList.style.display = 'block';
                fetchResponsibilities();
            });

            // Hide the responsibility list when clicking outside
            document.addEventListener('click', function (event) {
                if (!responsibilityList.contains(event.target) && event.target !== showResponsibilityListBtn) {
                    responsibilityList.style.display = 'none';
                }
            });
            function capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
            }
            async function fetchResponsibilities() {
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
                    console.log(data);
                    if (data.success) {
                        const responsibilities = data.programs; // Assuming the correct key is 'programs'
                        displayResponsibilities(responsibilities);
                    } else {
                        console.error('Error fetching responsibilities:', data.message || 'No message returned');
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                }
            }
            function displayResponsibilities(responsibilities) {
                const responsibilityListDiv = document.getElementById('responsibility-list');
                responsibilityListDiv.innerHTML = ''; // Reset the responsibility list

                console.log("responsibilities", responsibilities);
                
                const organizationResponsibility = document.getElementById('organizationResponsibilty').value;
                console.log("organizationResponsibility: ", organizationResponsibility);

                // Process each responsibility and display based on the parsed parts
                const orgResponsibilityParts = organizationResponsibility.split(',');

                orgResponsibilityParts.forEach(part => {
                    const partArray = part.trim().split('-');
                    
                    if (partArray[0].includes('All') || partArray[0].includes('all')) {
                        const secondWord = partArray[1]?.trim().toLowerCase();

                        if (secondWord) {
                            // Normalize all program levels and program names in the responsibilities array
                            const normalizedResponsibilities = responsibilities.map(responsibility => {
                                const programLevels = Array.isArray(responsibility.program_level)
                                    ? responsibility.program_level
                                    : [responsibility.program_level];
                                
                                return {
                                    ...responsibility,
                                    normalizedProgramLevels: programLevels.map(level => level.toLowerCase().trim()),
                                    programName: responsibility.program_name.toLowerCase().trim() // Normalize program name too
                                };
                            });

                            // First, try to match the program level
                            const matchingResponsibilities = normalizedResponsibilities.filter(responsibility => 
                                responsibility.normalizedProgramLevels.includes(secondWord)
                            );

                            if (matchingResponsibilities.length > 0) {
                                console.log("Match found for program level: ", secondWord);
                                displayAllByProgramLevel(responsibilityListDiv, matchingResponsibilities, secondWord);
                            } else {
                                // If no match for program level, check if it's a program name
                                const programNameMatch = normalizedResponsibilities.filter(responsibility =>
                                    responsibility.programName === secondWord
                                );

                                if (programNameMatch.length > 0) {
                                    console.log("Match found for program name: ", secondWord);
                                    displayAllByProgramName(responsibilityListDiv, programNameMatch[0]);
                                } else {
                                    console.log("No match found for program level or name.");
                                }
                            }
                        }
                    } else {
                        // Parse the responsibility parts: program name, year, and section
                        const programName = partArray[0].trim();
                        const year = partArray[1]?.trim();
                        const section = partArray[2]?.trim();

                        if (year && section) {
                            // Display responsibility with program_name, year, and section
                            displaySpecificResponsibility(responsibilityListDiv, programName, year, section);
                        } else if (year) {
                            // Display responsibility by program_name and year
                            displaySpecificResponsibility(responsibilityListDiv, programName, year);
                        } else {
                            // Display responsibility by program_name
                            displaySpecificResponsibility(responsibilityListDiv, programName);
                        }
                    }
                });

                responsibilityListDiv.style.display = 'block'; // Show the list
            }

            // Helper function to display all responsibilities by program name
            function displayAllByProgramName(responsibilityListDiv, responsibility) {
                const programDiv = document.createElement('div');
                programDiv.classList.add('program-item');
                const programName = responsibility.program_name;
                programDiv.innerHTML = `<strong>${programName}</strong>`;
                responsibilityListDiv.appendChild(programDiv);

                console.log(`Program name "${programName}" added to the list`);

                // Add "All - Program Name" option
                const allProgramOption = document.createElement('div');
                allProgramOption.classList.add('responsibility-item');
                allProgramOption.textContent = `All - ${programName}`;
                allProgramOption.addEventListener('click', function () {
                    addResponsibility(`All - ${programName}`);
                });
                programDiv.appendChild(allProgramOption);

                // Loop through years for the current program
                const years = responsibility.program_year_grade_levels;
                console.log("Years array for program:", years);

                years.forEach(year => {
                    const yearDiv = document.createElement('div');
                    yearDiv.classList.add('year-item');
                    yearDiv.innerHTML = `<strong>All - ${programName} - ${year}</strong>`;

                    // Add "All - Program Name - Year" option
                    const allYearOption = document.createElement('div');
                    allYearOption.classList.add('responsibility-item');
                    allYearOption.textContent = `All - ${programName} - ${year}`;
                    allYearOption.addEventListener('click', function () {
                        addResponsibility(`All - ${programName} - ${year}`);
                    });
                    yearDiv.appendChild(allYearOption);

                    // Display sections for the current year
                    responsibility.program_sections.forEach(section => {
                        const sectionItem = document.createElement('div');
                        sectionItem.classList.add('responsibility-item');
                        sectionItem.textContent = `${programName} - ${year} - ${section}`;
                        sectionItem.addEventListener('click', function () {
                            addResponsibility(`${programName} - ${year} - ${section}`);
                        });
                        yearDiv.appendChild(sectionItem);
                    });

                    programDiv.appendChild(yearDiv);
                });
            }

            // Helper function to display all responsibilities by program level
            function displayAllByProgramLevel(responsibilityListDiv, responsibility, programLevel) {
                const programDiv = document.createElement('div');
                programDiv.classList.add('program-item');
                const capitalizedProgramLevel = capitalizeFirstLetter(programLevel);
                programDiv.innerHTML = `<strong>${capitalizedProgramLevel}</strong>`;
                responsibilityListDiv.appendChild(programDiv);

                console.log(`Program level "${capitalizedProgramLevel}" added to the list`);

                // Add "All - Program Level" option
                const allProgramLevelOption = document.createElement('div');
                allProgramLevelOption.classList.add('responsibility-item');
                allProgramLevelOption.textContent = `All - ${capitalizedProgramLevel}`;
                allProgramLevelOption.addEventListener('click', function () {
                    addResponsibility(`All - ${capitalizedProgramLevel}`);
                });
                programDiv.appendChild(allProgramLevelOption);

                // Loop through responsibility array to display each responsibility
                responsibility.forEach((res, index) => {
                    console.log(`Responsibility at index ${index}: `, res);

                    const programName = res.program_name; // Access program_name directly
                    const programLevel = res.program_level;

                    // Display program name
                    const programNameDiv = document.createElement('div');
                    programNameDiv.classList.add('program-item');
                    programNameDiv.innerHTML = `<strong>${programName}</strong>`;
                    programDiv.appendChild(programNameDiv);

                    // Display years for the program
                    const years = res.program_year_grade_levels;
                    console.log("Years array for program:", years);

                    years.forEach(year => {
                        const yearDiv = document.createElement('div');
                        yearDiv.classList.add('year-item');
                        yearDiv.innerHTML = `<strong>All - ${programName} - ${year}</strong>`;

                        // Add "All - Program Level - Year" option
                        const allYearOption = document.createElement('div');
                        allYearOption.classList.add('responsibility-item');
                        allYearOption.textContent = `All - ${programName} - ${year}`;
                        allYearOption.addEventListener('click', function () {
                            addResponsibility(`All - ${programName} - ${year}`);
                        });
                        yearDiv.appendChild(allYearOption);

                        // Display sections for each year
                        res.program_sections.forEach(section => {
                            const sectionItem = document.createElement('div');
                            sectionItem.classList.add('responsibility-item');
                            sectionItem.textContent = `${programName} - ${year} - ${section}`;
                            sectionItem.addEventListener('click', function () {
                                addResponsibility(`${programName} - ${year} - ${section}`);
                            });
                            yearDiv.appendChild(sectionItem);
                        });

                        // Append yearDiv to programNameDiv
                        programNameDiv.appendChild(yearDiv);
                    });
                });
            }


            // Helper function to display a specific responsibility (program, year, and section)
            function displaySpecificResponsibility(responsibilityListDiv, programName, year = '', section = '') {
                const responsibilityItem = document.createElement('div');
                responsibilityItem.classList.add('responsibility-item');
                responsibilityItem.textContent = `${programName} ${year ? `- ${year}` : ''} ${section ? `- ${section}` : ''}`;
                responsibilityItem.addEventListener('click', function () {
                    addResponsibility(`${programName} ${year ? `- ${year}` : ''} ${section ? `- ${section}` : ''}`);
                });
                responsibilityListDiv.appendChild(responsibilityItem);
            }

            if (responsibilityInput.value === null || responsibilityInput.value.trim() !== "") {
                addResponsibility(responsibilityInput.value);
            }
            // Add responsibility to the input field and selected list
function addResponsibility(responsibility) {
    const responsibilityInput = document.getElementById('officer-responsibility');

    // Add responsibility to the input field, avoiding duplicates
    if (!responsibilityInput.value.split(', ').includes(responsibility)) {
        if (responsibilityInput.value) {
            responsibilityInput.value += ', ' + responsibility;
        } else {
            responsibilityInput.value = responsibility;
        }
    }

    console.log("Added responsibility: " + responsibility);
    console.log("Updated input field value: " + responsibilityInput.value);

    // Add the responsibility to the selected list if not already present
    const selectedResponsibilitiesContainer = document.getElementById('selected-responsibilities');
    const existingItem = Array.from(selectedResponsibilitiesContainer.children).find(
        item => item.textContent.startsWith(responsibility)
    );

    if (!existingItem) {
        const selectedItem = document.createElement('div');
        selectedItem.classList.add('selected-responsibility');
        selectedItem.textContent = responsibility;

        // Create a minus button for removing the responsibility
        const minusButton = document.createElement('button');
        minusButton.textContent = ' -';
        minusButton.classList.add('remove-responsibility');

        // When the minus button is clicked, remove the responsibility
        minusButton.addEventListener('click', function () {
            selectedItem.remove();
            // Remove responsibility from the input field
            const responsibilities = responsibilityInput.value.split(', ').filter(item => item !== responsibility);
            responsibilityInput.value = responsibilities.join(', ');
            console.log("Removed responsibility: " + responsibility);
            console.log("Updated input field value: " + responsibilityInput.value);
        });

        selectedItem.appendChild(minusButton);
        selectedResponsibilitiesContainer.appendChild(selectedItem);
    }

    // Optionally hide the responsibility list after selection
    const responsibilityList = document.getElementById('responsibility-list');
    if (responsibilityList) {
        responsibilityList.style.display = 'none';
    }
}


            // Assign the addResponsibility function to each responsibility item
            document.querySelectorAll('.responsibility-item').forEach(item => {
                item.addEventListener('click', function () {
                    addResponsibility(this.innerText);
                });
            });


        // JavaScript to handle edit, update, and cancel functionalities
            const editButton = document.querySelector(".officer-data-btn-edit");
            const formElements = document.querySelectorAll("#officer-duty, #officer-assign-duty");
            const buttonContainer = document.querySelector(".officer-data-content-btn");

            editButton?.addEventListener("click", (e) => {
                e.preventDefault();

                // Enable the form inputs
                formElements.forEach(element => {
                    element.disabled = false;
                    showResponsibilityListBtn.disabled = false;
                });

                // Replace Edit button with Update and Cancel buttons
                buttonContainer.innerHTML = `
                    <button type="button" id="update-btn" class="user-update">UPDATE</button>
                    <button type="button" id="cancel-btn" class="user-cancel">CANCEL</button>
                `;

                const updateButton = document.querySelector("#update-btn");
                const cancelButton = document.querySelector("#cancel-btn");

                updateButton.addEventListener("click", handleUpdate);
                cancelButton.addEventListener("click", handleCancel);
            });


            const handleUpdate = async () => {
    const form = document.getElementById("organization-data-content-info-form-content");
    const formElements = form.querySelectorAll("input, select, textarea");
    const showResponsibilityListBtn = document.getElementById("show-responsibility-list-btn");

    // Enable form elements before sending the data
    formElements.forEach(element => element.disabled = false);
    showResponsibilityListBtn.disabled = false;

    // Gather form data
    const formData = new FormData(form);

    try {
        const response = await fetch('updateOfficerDataHandler.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            alert(data.message);  // Show success message

            // Restore the Edit button
            buttonContainer.innerHTML = `
                <button type="submit" name="eSubmit" class="officer-data-btn-edit">EDIT</button>
            `;

            // Disable form elements after update
            formElements.forEach(element => element.disabled = true);
            showResponsibilityListBtn.disabled = true;
        } else {
            alert(data.message);  // Show error message
        }
    } catch (error) {
        console.error('Error:', error);  // Log any errors to the console
        alert('An error occurred while updating the data.');
    }
};


            const handleCancel = () => {
                // Disable the form inputs
                formElements.forEach(element => {
                    element.disabled = true;
                });

                // Restore the Edit button
                buttonContainer.innerHTML = `
                    <button type="submit" name="eSubmit" class="officer-data-btn-edit">EDIT</button>
                `;

                // Reattach the edit button functionality
                const restoredEditButton = document.querySelector(".officer-data-btn-edit");
                restoredEditButton.addEventListener("click", (e) => {
                    e.preventDefault();
                    formElements.forEach(element => {
                        element.disabled = false;
                    });
                });
            };

            
        });

    </script>
</body>
</html>