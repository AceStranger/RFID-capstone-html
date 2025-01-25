<?php
session_start();
include "dbh.php";
if(@!isset($_SESSION['user_ID']) || $_SESSION['user_ID'] === null) {
    header("Location: LogInPage.html");
    exit();
}
$user_ID = $_SESSION['user_ID'];
$query = "SELECT * FROM user WHERE user_id = $user_ID";
$result = mysqli_query($conn, $query);
$userInfo = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/headerStyle.css"> 
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminUserProfile.css">
    <script src="js\adminSidebar.js"></script>
    <script src="js\adminUser.js"></script>
    <script defer src="js/usermenu.js"></script> 
</head>
<body>
    <div class="main-content-container">
        <?php
        
            if (str_contains($userInfo['user_role'], "admin") ||
                str_contains($userInfo['user_role'], "officer") ||
                str_contains($userInfo['user_role'], "dean")){
                include_once"adminSidebar.php";
            } else {
                include_once"header.php";
            }
         ?>
        
        <div class="main-content">
            <div class="user-profile-content">
                
                <?php 
                    $useraccountid = isset($_SESSION['user-account-id']) ? $_SESSION['user-account-id'] : null;
                    $request = isset($_SESSION['request']) ? $_SESSION['request'] : null;
                    


                    $inputAvailability = "";
                    $btn = "";

                    if ($request === "view-profile") {
                        $inputAvailability = "disabled";
                        $btn = '
                            <input type="submit" name="eSubmit" id="user-profile-btn-action-btn-edit" class="user-profile-btn-action-btn" value="Edit">
                            <input type="submit" name="dSubmit" id="user-profile-btn-action-btn-delete" class="user-profile-btn-action-btn" value="Delete">

                            ';
                    } elseif ($request === "edit-profile") {
                        $inputAvailability = "";
                        $btn = '
                            <input type="submit" name="uSubmit" id="user-profile-btn-action-btn-update" class="user-profile-btn-action-btn" value="Update">
                            <input type="submit" name="cSubmit" id="user-profile-btn-action-btn-cancel" class="user-profile-btn-action-btn" value="Cancel">
                            <script>
                                const uBtn = document.getElementById("user-profile-btn-action-btn-update");
                                const cBtn = document.getElementById("user-profile-btn-action-btn-cancel");

                                uBtn.addEventListener("click", (e) => {
                                    if (confirm("Are you sure you want to update?") === false) {
                                        e.preventDefault(); // Prevent form submission if cancelled
                                    }
                                });

                                cBtn.addEventListener("click", (e) => {
                                    if (confirm("Are you sure you want to cancel?") === false) {
                                        e.preventDefault(); // Prevent form submission if cancelled
                                    }
                                });
                            </script>
                            ';
                    } elseif ($request === "delete-profile") {
                        $inputAvailability = "disabled";
                        $btn = '
                            <input type="submit" name="dcSubmit" id="user-profile-btn-action-btn-delete" class="user-profile-btn-action-btn" value="Delete">
                            <input type="submit" name="cSubmit" id="user-profile-btn-action-btn-cancel" class="user-profile-btn-action-btn" value="Cancel">
                            <script>
                                const cBtn = document.getElementById("user-profile-btn-action-btn-cancel");
                                const dBtn = document.getElementById("user-profile-btn-action-btn-delete");

                                cBtn.addEventListener("click", (e) => {
                                    if (confirm("Are you sure you want to cancel?") === false) {
                                        e.preventDefault(); // Prevent form submission if cancelled
                                    }
                                });

                                dBtn.addEventListener("click", (e) => {
                                    if (confirm("Are you sure you want to delete this profile?") === false) {
                                        e.preventDefault(); // Prevent form submission if cancelled
                                    }
                                });

                            </script>
                            ';
                    }
                    $useraccountid = mysqli_real_escape_string($conn, $useraccountid);
                    $userAccQuery = "SELECT * FROM user WHERE user_id = $useraccountid";
                    $userAccQueryResult = mysqli_query($conn, $userAccQuery);
                    if (mysqli_num_rows($userAccQueryResult) > 0) { 
                        while($row = mysqli_fetch_assoc($userAccQueryResult)) {

                            $usersSchoolID = $row['user_school_id'];
                            $usersFname = $row['user_firstname'];
                            $usersMname = $row['user_middlename'];
                            $usersLname = $row['user_lastname'];
                            $usersSname = $row['user_suffixname'];
                            $usersGender = $row['user_gender'];
                            $usersPFP = $row['user_img'];
                            $usersAddress = $row['user_address'];
                            $usersPNumber = $row['user_phone_number'];
                            $usersEmail = $row['user_phone_number'];
                            $usersRole = $row['user_role'];
                            $usersLastLoginDate = $row['user_last_login_date'];
                            $usersCreatedAt = $row['user_creation_date'];


                            if (!str_contains($userInfo['user_role'], "admin")){
                                $btn = "";
                            }


                            //  Student Info
                            $studentInfo = '';
                            $studentID = '';
                            $studentProgramID = '';
                            $studentYearGradeLevel = '';
                            $studentSection = '';
                            $programId = '';
                            $programName = '';
                            $programDepartmentID = '';
                            $departmentName = '';
                            $programOptions = "";


                            //  Officer Info
                            $officerInfo = '';
                            $officerID = '';
                            $officerOrganizationID = '';
                            $officerPosition = '';
                            $organizationName = '';
                            $organizationName = '';

                            

                            //  Dean Info
                            $deanInfo = '';
                            $deanID = '';
                            $deanDepartmentID = '';
                            $departmentName = '';
                            
                            

                            //  Admin Info
                            $adminInfo = "";
                            $adminAuthorityLevel = "";
                            $adminDateAdded = "";


                            // Check if there is a comma in the string
                            if (strpos($usersRole, ",") !== false) {
                                // If there's a comma, split the string into an array
                                $userRoles = explode(",", $usersRole);
                            } else {
                                // If there's no comma, treat it as a single role
                                $userRoles = [$usersRole]; // Create an array with a single element
                            }
                            
                            if (str_contains($usersRole , "student")) {
                        
                                $userStudentQuery = "SELECT * FROM student WHERE user_id = $useraccountid";
                                $userStudentQueryResult = mysqli_query($conn, $userStudentQuery);
                                if (mysqli_num_rows($userStudentQueryResult) > 0) {
                                    while($Studentrow = mysqli_fetch_assoc($userStudentQueryResult)) {
                                        $studentID = $Studentrow['student_id'];
                                        $studentProgramID = $Studentrow['program_id'];
                                        $studentYearGradeLevel = $Studentrow['year/grade_level'];
                                        $studentSection = $Studentrow['section'];
                        

                                        // Fetch all available programs
                                        $allProgramsQuery = "SELECT * FROM program";
                                        $allProgramsQueryResult = mysqli_query($conn, $allProgramsQuery);
                                        
                                        if (mysqli_num_rows($allProgramsQueryResult) > 0) {
                                            while ($programRow = mysqli_fetch_assoc($allProgramsQueryResult)) {
                                                $programID = $programRow['program_id'];
                                                $programName = $programRow['program_name'];
                                                $selected = ($studentProgramID == $programID) ? "selected" : "";
                                                if ($studentProgramID == $programID) {
                                                    $programDepartmentID = $programRow['department_id'];
                                                }

                                                $programOptions .= "<option value='$programID' $selected>$programName</option>";
                                            }
                                        }
                                        // Assuming $programDepartmentID has been assigned a value in the previous loop
                                        if (!empty($programDepartmentID)) {
                                            $departmentQuery = "SELECT * FROM department WHERE department_id = $programDepartmentID";
                                            $departmentQueryResult = mysqli_query($conn, $departmentQuery);
                                            
                                            if (mysqli_num_rows($departmentQueryResult) > 0) {
                                                while ($Departmentrow = mysqli_fetch_assoc($departmentQueryResult)) {
                                                    $departmentName = $Departmentrow['department_name'];
                                                }
                                            } else {
                                                // Handle case where no results are returned
                                                $departmentName = "Department not found";
                                            }
                                        } else {
                                            // Handle case where $programDepartmentID is empty or null
                                            $departmentName = "No department ID available";
                                        }
    
                        
                                    }
                                }
                        
                                $studentInfo = "
                                    <div class='user-profile-content-student-info'>
                                        <div class='user-info-content-container'>
                                            <div class='user-info-content-col-1'>
                                                <div class='user-info-content-info-field user-info-content-input-field'>
                                                    <label for='student-department'>Department:</label>
                                                    <input disabled type='text' id='student-department' name='student-department' value='$departmentName'>
                                                </div>
                                                <div class='user-info-content-info-field user-info-content-input-field'>
                                                    <label for='student-program'>Program:</label>
                                                    <select $inputAvailability id='student-program' name='student-program'>
                                                        <option value='' selected>None</option>
                                                        $programOptions
                                                    </select>
                                                </div>
                                            </div>
                                            <div class='user-info-content-col-2'>
                                                <div class='user-info-content-info-field user-info-content-input-field'>
                                                    <label for='student-year-grade-level'>Year/Grade level:</label>
                                                    <input $inputAvailability type='text' id='student-year-grade-level' name='student-year-grade-level' value='$studentYearGradeLevel'>
                                                </div>
                                                <div class='user-info-content-info-field user-info-content-input-field'>
                                                    <label for='student-section'>Section:</label>
                                                    <input $inputAvailability type='text' id='student-section' name='student-section' value='$studentSection'>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ";
                            } 
                            if (str_contains($usersRole , "officer")) {

                                $userOfficerQuery = "SELECT * FROM officer WHERE user_id = $useraccountid";
                                $userOfficerQueryResult = mysqli_query($conn, $userOfficerQuery);
                                if (mysqli_num_rows($userOfficerQueryResult) > 0) {
                                    while($Officerrow = mysqli_fetch_assoc($userOfficerQueryResult)) {
                                        $officerID = $Officerrow['officer_id'];
                                        $officerOrganizationID = $Officerrow['organization_id'];
                                        $officerPosition = $Officerrow['officer_position'];

                                        // Fetch all available organizations
                                        $OrganizationQuery = "SELECT * FROM organization";
                                        $OrganizationQueryResult = mysqli_query($conn, $OrganizationQuery);
                                        $organizationOptions = "";

                                        if (mysqli_num_rows($OrganizationQueryResult) > 0) {
                                            while ($Organizationrow = mysqli_fetch_assoc($OrganizationQueryResult)) {
                                                $organizationID = $Organizationrow['organization_id'];
                                                $organizationName = $Organizationrow['organization_name'];

                                                // Check if the organization is the one the officer belongs to
                                                $selected = ($officerOrganizationID == $organizationID) ? "selected" : "";

                                                // Add the option to the dropdown
                                                $organizationOptions .= "<option value='$organizationID' $selected>$organizationName</option>";
                                            }
                                        }
                                    }
                                }
                                $officerInfo = "
                                
                                        <div class='user-profile-content-officer-info'>
                                            <div class='user-info-content-container'>
                                                <div class='user-info-content-col-1'>
                                                    <div class='user-info-content-info-field user-info-content-input-field'>
                                                        <label for='organization-name'>Organization Name:</label>
                                                        <select $inputAvailability id='organization-name' name='organization-name'>
                                                            $organizationOptions
                                                        </select>

                                                    </div>
                                                </div>
                                                <div class='user-info-content-col-2'>
                                                    <div class='user-info-content-info-field user-info-content-input-field'>
                                                        <label for='officer-position'>Officer's Position:</label>
                                                        <input $inputAvailability type='text' id='officer-position' name='officer-position' value='$officerPosition'>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                
                                ";




                            } 
                            if (str_contains($usersRole , "dean")) {
                                

                                $userDeanQuery = "SELECT * FROM dean WHERE user_id = $useraccountid";
                                $userDeanQueryResult = mysqli_query($conn, $userDeanQuery);
                                if (mysqli_num_rows($userDeanQueryResult) > 0) {
                                    while ($Deanrow = mysqli_fetch_assoc($userDeanQueryResult)) {
                                        $deanID = $Deanrow['dean_id'];
                                        $deanDepartmentID = $Deanrow['department_id'];
                                        
                                        // Fetch all departments
                                        $departmentQuery = "SELECT * FROM department";
                                        $departmentQueryResult = mysqli_query($conn, $departmentQuery);
                                        $departmentOptions = "";
                                        if(!empty($deanDepartmentID)) {
                                            if (mysqli_num_rows($departmentQueryResult) > 0) {
                                                while ($Departmentrow = mysqli_fetch_assoc($departmentQueryResult)) {
                                                    $departmentName = $Departmentrow['department_name'];
                                                    $departmentID = $Departmentrow['department_id'];
                                
                                                    // Check if this is the department the dean belongs to
                                                    $selected = ($deanDepartmentID == $departmentID) ? "selected" : "";
                                
                                                    // Add option to the dropdown
                                                    $departmentOptions .= "<option value='$departmentID' $selected>$departmentName</option>";
                                                }
                                            } else {
                                                // Handle case where no results are returned
                                                $departmentName = "Department not found";
                                            }
                                        } else {
                                            // Handle case where $programDepartmentID is empty or null
                                            $departmentName = "No department ID available";
                                        }
                                    }
                                }
                                $deanInfo = "
                                    <div class='user-profile-content-dean-info'>
                                        <div class='user-info-content-container'>
                                            <div class='user-info-content-col-1'>
                                                <div class='user-info-content-info-field user-info-content-input-field'>
                                                    <label for='dean-department'>Department:</label>
                                                    <select $inputAvailability id='dean-department' name='dean-department'>
                                                        $departmentOptions
                                                    </select>
                                                </div>
                                            </div>
                                            <div class='user-info-content-col-2'>
                                            </div>
                                        </div>
                                    </div>
                                
                                ";



                            } 
                            if (str_contains($usersRole , "admin")) {
                                $userAdminQuery = "SELECT * FROM `admin` WHERE user_id = $useraccountid";
                                $userAdminQueryResult = mysqli_query($conn, $userAdminQuery);
                                if (mysqli_num_rows($userAdminQueryResult) > 0) {
                                    while($adminRow = mysqli_fetch_assoc($userAdminQueryResult)) {
                                        $adminID = $adminRow['admin_id'];
                                        $adminAuthorityLevel = $adminRow['admin_authority'];
                                        $adminDateAdded = $adminRow['date_added'];
                                        
                                    }
                                }
                                $adminInfo = "
                                
                                        <div class='user-profile-content-admin-info'>
                                            <div class='user-info-content-container'>
                                                <div class='user-info-content-col-1'>
                                                    <div class='user-info-content-info-field user-info-content-input-field'>
                                                        <label for='admin-authority-level'>Administrator Level:</label>
                                                        <input $inputAvailability type='text' id='admin-authority-level' name='admin-authority-level' value='$adminAuthorityLevel'>
                                                    </div>
                                                </div>
                                                <div class='user-info-content-col-2'>
                                                </div>
                                            </div>
                                        </div>
                                
                                ";
                            } 


                            echo "
                            
                            <form action='adminUserAccount.php' method='post' class='user-profile-content-form' enctype='multipart/form-data'>
                                ";

                                if (str_contains($userInfo['user_role'], "admin") ||
                                    str_contains($userInfo['user_role'], "officer") ||
                                    str_contains($userInfo['user_role'], "dean")){
                                    echo "
                                        <div class='user-profile-content-top-part'>
                                            <h1 class='user-profile-content-head-text'>PROFILE</h1>
                                            <div class='user-profile-content-btn'>
                                                <input type='hidden' name='user-id' id='user-id' value='$useraccountid'>
                                                ";
                                                echo $btn;
                                                echo "
                                            </div>
                                        </div>";
                                } else {
                                    include_once"header.php";
                                }

                                echo "
                                <div class='user-profile-content-info'>
                                    <div class='user-profile-content-info-content'>
                                        <div class='user-profile-content-user-info'>
                                            <div class='user-info-content-container'>
                                                <div class='user-info-content-col-1'>
                                                    <div class='user-info-content-info-field user-info-content-input-field'>
                                                        <input type='hidden' name='userpfpfilepath' value='". $usersPFP ."'>
                                                        <input $inputAvailability type='file' accept='image/jpeg, image/png, image/gif ' id='user-picture' name='user-picture' placehole='Choose Image'>

                                                        <img src='". $usersPFP ."' alt='' id='user-profile-info-content-picture' class='user-profile-info-content-picture'>
                                                    </div>
                                                    <script>
                                                        const userPictureInput = document.getElementById('user-picture');
                                                        const userProfilePicture = document.getElementById('user-profile-info-content-picture');
                                                        userPictureInput.addEventListener('change', (event) => {
                                                            const file = event.target.files[0];
                                                            if (file) {
                                                                const reader = new FileReader();
                                                                reader.onload = (e) => {
                                                                userProfilePicture.src = e.target.result;
                                                                };
                                                                reader.readAsDataURL(file);
                                                            }
                                                        });
                                                    </script>
                                                    <div class='user-info-content-info-field user-info-content-input-field'>
                                                        <label for='user-gender'>Gender:</label>
                                                        <input $inputAvailability type='text' id='user-gender' name='user-gender' value='$usersGender'>
                                                    </div>
                                                    <div class='user-info-content-info-field user-info-content-input-field'>
                                                        <label for='user-address'>Address:</label>
                                                        <textarea $inputAvailability name='user-address' id='user-address'>$usersAddress</textarea>
                                                    </div>
                                                </div>
                                                <div class='user-info-content-col-2'>
                                                    <div class='user-info-content-info-field user-info-content-input-field'>
                                                        <label for='user-first-name'>First Name:</label>
                                                        <input $inputAvailability type='text' id='user-first-name' name='user-first-name' value='$usersFname'>
                                                    </div>
                                                    <div class='user-info-content-info-field user-info-content-input-field'>
                                                        <label for='user-middle-name'>Middle Name:</label>
                                                        <input $inputAvailability type='text' id='user-middle-name' name='user-middle-name' value='$usersMname'>
                                                    </div>
                                                    <div class='user-info-content-info-field user-info-content-input-field'>
                                                        <label for='user-last-name'>Last Name:</label>
                                                        <input $inputAvailability type='text' id='user-last-name' name='user-last-name' value='$usersLname'>
                                                    </div>
                                                    <div class='user-info-content-info-field user-info-content-input-field'>
                                                        <label for='user-suffix'>Suffix Name:</label>
                                                        <input $inputAvailability type='text' id='user-suffix' name='user-suffix' value='$usersSname'>
                                                    </div>
                                                    <div class='user-info-content-info-field user-info-content-input-field'>
                                                        <label for='user-pnumber'>Phone Number:</label>
                                                        <input $inputAvailability type='text' id='user-pnumber' name='user-pnumber' value='$usersPNumber'>
                                                    </div>
                                                    <div class='user-info-content-info-field user-info-content-input-field'>
                                                        <label for='user-school-id'>School ID:</label>
                                                        <input $inputAvailability type='text' id='user-school-id' name='user-school-id' value='$usersSchoolID'>
                                                    </div>
                                                    <div class='user-info-content-info-field user-info-content-input-field'>
                                                        <label for='user-role'>Role:</label>
                                                        <input disabled type='text' list='role-list' id='user-role' name='user-role' value='$usersRole'>

                                                        <datalist id='role-list'>
                                                            <option value='student'>
                                                            <option value='officer'>
                                                            <option value='dean'>
                                                            <option value='admin'>
                                                        </datalist>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>

                                        ";
                                        
                                        echo $studentInfo;
                                        echo $officerInfo;
                                        echo $deanInfo;
                                        echo $adminInfo;
                                        
                                        echo "


                                            <div class='user-profile-content-user-account-info'>
                                                <div class='user-info-content-container'>
                                                    <div class='user-info-content-col-1'>
                                                        <div class='user-info-content-info-field user-info-content-input-field'>
                                                            <label for='dean-last-login'>Last Login Date:</label>
                                                            <input disabled type='datetime' id='dean-last-login' name='dean-last-login' value='$usersLastLoginDate'>
                                                        </div>
                                                    </div>
                                                    <div class='user-info-content-col-2'>
                                                        <div class='user-info-content-info-field user-info-content-input-field'>
                                                            <label for='dean-date-created'>Date created at:</label>
                                                            <input disabled type='datetime' id='dean-date-created' name='dean-date-created' value='$usersCreatedAt'>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class='user-profile-content-attendance-history-info'>
                                                <div class='attendance-history-content'>
                                                    <div class='attendance-history-content-head-text'>
                                                        <h3>Event Attendance History</h3>
                                                    </div>
                                                    <table class='attendance-history-table'>
                                                        <thead>
                                                            <tr class='attendance-history-row'>
                                                                <th class='attendance-history-content-event-name' rowspan='2'>Event Name</th>
                                                                <th class='attendance-history-content-event-date' rowspan='2'>Event Date</th>
                                                                <th class='attendance-history-content-event-organizer' rowspan='2'>Event Organizer</th>
                                                                <th class='attendance-history-content-event-am' colspan='2'>AM</th>
                                                                <th class='attendance-history-content-event-pm' colspan='2'>PM</th>
                                                            </tr>
                                                            <tr class='attendance-history-row'>
                                                                <th class='attendance-history-content-event-am-tin'>Time In</th>
                                                                <th class='attendance-history-content-event-am-tout'>Time Out</th>
                                                                <th class='attendance-history-content-event-pm-tin'>Time In</th>
                                                                <th class='attendance-history-content-event-pm-tout'>Time Out</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody> ";
                                                        

$userAccQuery = "
    SELECT 
        e.event_name,
        e.event_date,
        e.event_organizer,
        o.organization_name,
        a.attendance_am_time_in,
        a.attendance_am_time_out,
        a.attendance_pm_time_in,
        a.attendance_pm_time_out,
        e.event_time_duration,
        e.attendance_duration_am_time_in_start,
        e.attendance_duration_am_time_in_end,
        e.attendance_duration_am_time_out_start,
        e.attendance_duration_am_time_out_end,
        e.attendance_duration_pm_time_in_start,
        e.attendance_duration_pm_time_in_end,
        e.attendance_duration_pm_time_out_start,
        e.attendance_duration_pm_time_out_end
    FROM attendance a
    JOIN event e ON a.event_id = e.event_id
    JOIN organization o ON e.event_organizer = o.organization_id
    WHERE a.user_id = $useraccountid
    ORDER BY e.event_date DESC
";

$result = $conn->query($userAccQuery);
                                                        
                                                     
while ($row = $result->fetch_assoc()) {
    // Extract event details
    $event_name = $row['event_name'];
    $event_date = $row['event_date'];
    $event_organizer = $row['organization_name'];
    $attendance_am_time_in = $row['attendance_am_time_in'];
    $attendance_am_time_out = $row['attendance_am_time_out'];
    $attendance_pm_time_in = $row['attendance_pm_time_in'];
    $attendance_pm_time_out = $row['attendance_pm_time_out'];
    $event_time_duration = $row['event_time_duration'];
    
    // Set placeholders for time
    $am_time_in = "~";
    $am_time_out = "~";
    $pm_time_in = "~";
    $pm_time_out = "~";

    // Check AM Time In and Out
    if ($event_time_duration == "Whole Day - AM Time In & AM Time Out & PM Time In & PM Time Out") {
        if (strtotime($attendance_am_time_in) >= strtotime($row['attendance_duration_am_time_in_start']) && strtotime($attendance_am_time_in) <= strtotime($row['attendance_duration_am_time_in_end'])) {
            $am_time_in = $attendance_am_time_in;
        }
        if (strtotime($attendance_am_time_out) >= strtotime($row['attendance_duration_am_time_out_start']) && strtotime($attendance_am_time_out) <= strtotime($row['attendance_duration_am_time_out_end'])) {
            $am_time_out = $attendance_am_time_out;
        }
    }
    // Check PM Time In and Out
    if ($event_time_duration == "Whole Day - AM Time In & AM Time Out & PM Time In & PM Time Out") {
        if (strtotime($attendance_pm_time_in) >= strtotime($row['attendance_duration_pm_time_in_start']) && strtotime($attendance_pm_time_in) <= strtotime($row['attendance_duration_pm_time_in_end'])) {
            $pm_time_in = $attendance_pm_time_in;
        }
        if (strtotime($attendance_pm_time_out) >= strtotime($row['attendance_duration_pm_time_out_start']) && strtotime($attendance_pm_time_out) <= strtotime($row['attendance_duration_pm_time_out_end'])) {
            $pm_time_out = $attendance_pm_time_out;
        }
    }

    // For "Half Day" events:
    if ($event_time_duration == "Half Day - AM Time In & AM Time Out") {
        if (strtotime($attendance_am_time_in) >= strtotime($row['attendance_duration_am_time_in_start']) && strtotime($attendance_am_time_in) <= strtotime($row['attendance_duration_am_time_in_end'])) {
            $am_time_in = $attendance_am_time_in;
        }
        if (strtotime($attendance_am_time_out) >= strtotime($row['attendance_duration_am_time_out_start']) && strtotime($attendance_am_time_out) <= strtotime($row['attendance_duration_am_time_out_end'])) {
            $am_time_out = $attendance_am_time_out;
        }
    }

    if ($event_time_duration == "Half Day - PM Time In & PM Time Out") {
        if (strtotime($attendance_pm_time_in) >= strtotime($row['attendance_duration_pm_time_in_start']) && strtotime($attendance_pm_time_in) <= strtotime($row['attendance_duration_pm_time_in_end'])) {
            $pm_time_in = $attendance_pm_time_in;
        }
        if (strtotime($attendance_pm_time_out) >= strtotime($row['attendance_duration_pm_time_out_start']) && strtotime($attendance_pm_time_out) <= strtotime($row['attendance_duration_pm_time_out_end'])) {
            $pm_time_out = $attendance_pm_time_out;
        }
    }

    // Display the row in the table
    echo "
    <tr class='attendance-history-row'>
        <td class='attendance-history-content-event-name'>$event_name</td>
        <td class='attendance-history-content-event-date'>$event_date</td>
        <td class='attendance-history-content-event-organizer'>$event_organizer</td>
        <td class='attendance-history-content-event-am-tin'>$am_time_in</td>
        <td class='attendance-history-content-event-am-tout'>$am_time_out</td>
        <td class='attendance-history-content-event-pm-tin'>$pm_time_in</td>
        <td class='attendance-history-content-event-pm-tout'>$pm_time_out</td>
    </tr>";
}   
    echo "
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            
                            ";
                        }
                    } else {
                        echo "User not found!";
                    }
                    
                ?>
                
            </div>
        </div>  
    </div>
    
</body>
</html>