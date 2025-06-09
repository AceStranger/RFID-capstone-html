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
    <title>User Profile</title>
    <?php include "titleIcon.php" ;?>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/headerStyle.css"> 
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminUserProfile.css">
    <?php
    
        if (str_contains($userInfo['user_role'], "admin") ||
            str_contains($userInfo['user_role'], "officer") ||
            str_contains($userInfo['user_role'], "dean")){
            echo '<script src="js/adminSidebar.js"></script>';
        } else {
            echo '<script src="js/user.js"></script>';
        }
        ?>
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
                include_once "header.php";
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
                        $inputAvailability = "readonly";
                        $btn = '
                            <input type="submit" name="eSubmit" id="user-profile-btn-action-btn-edit" class="user-profile-btn-action-btn" value="Edit">
                            <input type="submit" name="dSubmit" id="user-profile-btn-action-btn-delete" class="user-profile-btn-action-btn" value="Delete">

                            ';
                    } elseif ($request === "edit-profile") {
                        $inputAvailability = "enabled";
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
                        $inputAvailability = "readonly";
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
                    $userAccQuery = "SELECT * FROM user WHERE user_id = $useraccountid LIMIT 1";
                    $userAccQueryResult = mysqli_query($conn, $userAccQuery);
                ?>
                <?php if (mysqli_num_rows($userAccQueryResult) > 0):?> 
                    <?php while($row = mysqli_fetch_assoc($userAccQueryResult)):?>
                        <?php 
                            $username = $row['user_name'];
                            $password = $row['user_password'];
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
                                $studentUpdateCheckbox = "";
                                if (str_contains($userInfo['user_role'], "admin") && 
                                $request === "edit-profile"){
                                    $studentUpdateCheckbox = "
                                        <div class='user-info-content-info-field user-info-content-input-field'>
                                            <input $inputAvailability type='checkbox' id='update-student-role' name='update-student-role' value='1'>
                                            <label for='update-student-role'>Include Student Information in Update</label>
                                        </div>";
                                }
                                $studentInfo = "
                                    <div class='user-profile-content-student-info'>
                                        <div class='user-info-content-container'>
                                            <div class='user-info-content-col-1'>
                                                <div class='user-info-content-info-field user-info-content-input-field'>
                                                    <label for='student-department'>Department:</label>
                                                    <input readonly type='text' id='student-department' name='student-department' value='$departmentName'>
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
                                                ". $studentUpdateCheckbox ."
                                            </div>
                                        </div>
                                    </div>
                                ";
                            } 
                            if (str_contains($usersRole , "officer")) {

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
                                $officerUpdateCheckbox = "";
                                $userOfficerQuery = "SELECT * FROM officer WHERE user_id = $useraccountid";
                                $userOfficerQueryResult = mysqli_query($conn, $userOfficerQuery);
                                if (mysqli_num_rows($userOfficerQueryResult) > 0) {
                                    while($Officerrow = mysqli_fetch_assoc($userOfficerQueryResult)) {
                                        $officerID = $Officerrow['officer_id'];
                                        $officerOrganizationID = $Officerrow['organization_id'];
                                        $officerPosition = $Officerrow['officer_position'];

                                    }
                                    
                                    if (str_contains($userInfo['user_role'], "admin") && 
                                    $request === "edit-profile"){
                                        $officerUpdateCheckbox = "
                                            <div class='user-info-content-info-field user-info-content-input-field'>
                                                <input $inputAvailability type='checkbox' id='update-officer-role' name='update-officer-role' value='1'>
                                                <label for='update-officer-role'>Include Officer Information in Update</label>
                                            </div>";
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
                                                        ". $officerUpdateCheckbox ."
                                                    </div>
                                                </div>
                                            </div>
                                    
                                    ";
                                }else {
                                    if (str_contains($userInfo['user_role'], "admin") && 
                                    $request === "edit-profile"){
                                        $officerUpdateCheckbox = "
                                            <div class='user-info-content-info-field user-info-content-input-field'>
                                                <input $inputAvailability type='checkbox' id='update-officer-role' name='update-officer-role' value='1'>
                                                <label for='update-officer-role'>Include Officer Information in Update</label>
                                            </div>";
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
                                                            <input $inputAvailability type='text' id='officer-position' name='officer-position' value=''>
                                                        </div>
                                                        ". $officerUpdateCheckbox ."
                                                    </div>
                                                </div>
                                            </div>
                                    
                                    ";
                                }
                                




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
                                $deanUpdateCheckbox = "";
                                if (str_contains($userInfo['user_role'], "admin") && 
                                $request === "edit-profile"){
                                    $deanUpdateCheckbox = "
                                        <div class='user-info-content-info-field user-info-content-input-field'>
                                            <input $inputAvailability type='checkbox' id='update-dean-role' name='update-dean-role' value='1'>
                                            <label for='update-dean-role'>Include Dean Information in Update</label>
                                        </div>";
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
                                                ". $deanUpdateCheckbox . "
                                            </div>
                                        </div>
                                    </div>
                                ";



                            } 
                        ?>

                        <form action='adminUserAccount.php' method='post' class='user-profile-content-form' enctype='multipart/form-data'>

                            <?php if (str_contains($userInfo['user_role'], "admin") ||
                                str_contains($userInfo['user_role'], "officer") ||
                                str_contains($userInfo['user_role'], "dean")):?>
                                    <div class='user-profile-content-top-part'>
                                        <h1 class='user-profile-content-head-text'>PROFILE</h1>
                                        <div class='user-profile-content-btn'>
                                            <input type='hidden' name='user-id' id='user-id' value='<?php echo $useraccountid;?>'>
                                            <?php echo $btn;?>
                                        </div>
                                    </div>
                            <?php endif;?>
                            <div class='user-profile-content-info'>
                                <div class='user-profile-content-info-content'>
                                    <div class='user-profile-content-user-info'>
                                        <div class='user-info-content-container'>
                                            <div class='user-info-content-col-1'>
                                                <div class="user-info-content-info-field user-info-content-input-field">
                                                    <label for="user-name">Username:</label>
                                                    <input <?php echo $inputAvailability;?> type="text" name="user-name" id="user-name" value="<?php echo $username;?>">
                                                </div>
                                                <div class="user-info-content-info-field user-info-content-input-field">
                                                    <label for="password">Password:</label>
                                                    <input <?php echo $inputAvailability;?> type="text" name="password" id="password" value="">
                                                    <?php if(str_contains($userInfo['user_role'], "admin") && 
                                                        $request === "edit-profile"):?>
                                                        <button type="button" id="reset-password-btn">Generate Password</button>
                                                        
                                                        <script>
                                                            const resetPasswordBtn = document.getElementById("reset-password-btn");
                                                            const passwordInput = document.getElementById("password");

                                                            resetPasswordBtn.addEventListener("click", () => {
                                                                const newPassword = generatePassword(10);
                                                                passwordInput.value = newPassword;
                                                            });

                                                            function generatePassword(length) {
                                                                const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                                                                let result = "";
                                                                for (let i = 0; i < length; i++) {
                                                                    result += characters.charAt(Math.floor(Math.random() * characters.length));
                                                                }
                                                                return result;
                                                            }
                                                        </script>
                                                    <?php endif;?>
                                                </div>
                                                <div class='user-info-content-info-field picture-input-field user-info-content-input-field'>
                                                    <input type='hidden' name='userpfpfilepath' value='<?php echo $usersPFP;?>'>
                                                    <input <?php echo $inputAvailability === "readonly" ? "disabled" : "enabled";?> type='file' accept='image/jpeg, image/png, image/gif ' id='user-picture' name='user-picture' placehole='Choose Image'>

                                                    <img src='<?php echo $usersPFP;?> ' alt='' id='user-profile-info-content-picture' class='user-profile-info-content-picture'>
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
                                                    <select <?= $inputAvailability ?> name="user-gender" id="user-gender">
                                                        <?php foreach (["male" => "Male", "female" => "Female"] as $value => $text): ?>
                                                            <option value="<?= $value ?>" <?= $usersGender === $text ? 'selected' : ''; ?>><?= $text ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                        

                                                </div>
                                                <div class='user-info-content-info-field user-info-content-input-field'>
                                                    <label for='user-address'>Address:</label>
                                                    <textarea <?php echo $inputAvailability;?> name='user-address' id='user-address'><?php echo $usersAddress;?> </textarea>
                                                </div>
                                            </div>
                                            <div class='user-info-content-col-2'>
                                                <?php if(str_contains($userInfo['user_role'], "admin") && 
                                                    $request === "edit-profile"):?>
                                                    <div class="user-info-content-info-field">
                                                        <input <?php echo $inputAvailability;?> type="checkbox" id="include-user-credentials" name="include-user-credentials" value="1">
                                                        <label for="include-user-credentials">Include Username and Password in Update</label>
                                                    </div>
                                                <?php endif;?>
                                                <div class='user-info-content-info-field user-info-content-input-field'>
                                                    <label for='user-first-name'>First Name:</label>
                                                    <input <?php echo $inputAvailability;?> type='text' id='user-first-name' name='user-first-name' value='<?php echo $usersFname;?> '>
                                                </div>
                                                <div class='user-info-content-info-field user-info-content-input-field'>
                                                    <label for='user-middle-name'>Middle Name:</label>
                                                    <input <?php echo $inputAvailability;?> type='text' id='user-middle-name' name='user-middle-name' value='<?php echo $usersMname;?> '>
                                                </div>
                                                <div class='user-info-content-info-field user-info-content-input-field'>
                                                    <label for='user-last-name'>Last Name:</label>
                                                    <input <?php echo $inputAvailability;?> type='text' id='user-last-name' name='user-last-name' value='<?php echo $usersLname;?> '>
                                                </div>
                                                <div class='user-info-content-info-field user-info-content-input-field'>
                                                    <label for='user-suffix'>Suffix Name:</label>
                                                    <input <?php echo $inputAvailability;?> type='text' id='user-suffix' name='user-suffix' value='<?php echo $usersSname;?> '>
                                                </div>
                                                <div class='user-info-content-info-field user-info-content-input-field'>
                                                    <label for='user-pnumber'>Phone Number:</label>
                                                    <input <?php echo $inputAvailability;?> type='text' id='user-pnumber' name='user-pnumber' value='<?php echo $usersPNumber;?> '>
                                                </div>
                                                <div class='user-info-content-info-field user-info-content-input-field'>
                                                    <label for='user-school-id'>School ID:</label>
                                                    <input <?php echo $inputAvailability;?> type='text' id='user-school-id' name='user-school-id' value='<?php echo $usersSchoolID;?> '>
                                                </div>
                                                <div class='user-info-content-info-field user-info-content-input-field'>
                                                    <label for='user-role'>Role:</label>
                                                    <input readonly type='text' list='role-list' id='user-role' name='user-role' value='<?php echo $usersRole;?> '>

                                                    <datalist id='role-list'>
                                                        <option value='student'>
                                                        <option value='officer'>
                                                        <option value='dean'>
                                                        <option value='admin'>
                                                    </datalist>
                                                </div>
                                                <?php if(str_contains($userInfo['user_role'], "admin") && 
                                                    $request === "edit-profile"):?>
                                                    <div class="user-info-content-info-field">
                                                        <input <?php echo $inputAvailability;?> type="checkbox" id="include-user-info" name="include-user-info" value="1">
                                                        <label for="include-user-info">Include User Information in Update</label>
                                                    </div>
                                                <?php endif;?>


                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                        echo $studentInfo;
                                        echo $officerInfo;
                                        echo $deanInfo;
                                        
                                    ?>

                                    <div class='user-profile-content-user-account-info'>
                                        <div class='user-info-content-container'>
                                            <div class='user-info-content-col-1'>
                                                <div class='user-info-content-info-field user-info-content-input-field'>
                                                    <label for='dean-last-login'>Last Login Date:</label>
                                                    <input readonly type='datetime' id='dean-last-login' name='dean-last-login' value='<?php echo $usersLastLoginDate;?>'>
                                                </div>
                                            </div>
                                            <div class='user-info-content-col-2'>
                                                <div class='user-info-content-info-field user-info-content-input-field'>
                                                    <label for='dean-date-created'>Date created at:</label>
                                                    <input readonly type='datetime' id='dean-date-created' name='dean-date-created' value='<?php echo $usersCreatedAt;?>'>
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
                                                <tbody> 
                                                    <?php
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
                                                            $event_name = $row['event_name'] ?? null;
                                                            $event_date = $row['event_date'] ?? null;
                                                            $event_organizer = $row['organization_name'] ?? null;
                                                            
                                                            $attendance_am_time_in_raw = $row['attendance_am_time_in'] ?? null;
                                                            $attendance_am_time_out_raw = $row['attendance_am_time_out'] ?? null;
                                                            $attendance_pm_time_in_raw = $row['attendance_pm_time_in'] ?? null;
                                                            $attendance_pm_time_out_raw = $row['attendance_pm_time_out'] ?? null;
                                                            $event_time_duration_raw = $row['event_time_duration'] ?? null;
                                                            $attendance_duration_am_time_in_start_raw = $row['attendance_duration_am_time_in_start'] ?? null;
                                                            $attendance_duration_am_time_in_end_raw = $row['attendance_duration_am_time_in_end'] ?? null;
                                                            $attendance_duration_am_time_out_start_raw = $row['attendance_duration_am_time_out_start'] ?? null;
                                                            $attendance_duration_am_time_out_end_raw = $row['attendance_duration_am_time_out_end'] ?? null;
                                                            $attendance_duration_pm_time_in_start_raw = $row['attendance_duration_pm_time_in_start'] ?? null;
                                                            $attendance_duration_pm_time_in_end_raw = $row['attendance_duration_pm_time_in_end'] ?? null;
                                                            $attendance_duration_pm_time_out_start_raw = $row['attendance_duration_pm_time_out_start'] ?? null;
                                                            $attendance_duration_pm_time_out_end_raw = $row['attendance_duration_pm_time_out_end'] ?? null;

                                                            // Convert times to Unix timestamps, ensuring they are valid.
                                                            // If strtotime returns false (e.g., empty string, invalid format), set to null.
                                                            $attendance_am_time_in = $attendance_am_time_in_raw ? strtotime($attendance_am_time_in_raw) : null;
                                                            $attendance_am_time_out = $attendance_am_time_out_raw ? strtotime($attendance_am_time_out_raw) : null;
                                                            $attendance_pm_time_in = $attendance_pm_time_in_raw ? strtotime($attendance_pm_time_in_raw) : null;
                                                            $attendance_pm_time_out = $attendance_pm_time_out_raw ? strtotime($attendance_pm_time_out_raw) : null;
                                                            $event_time_duration = $event_time_duration_raw; // This is a string, no strtotime needed

                                                            $attendance_duration_am_time_in_start = $attendance_duration_am_time_in_start_raw ? strtotime($attendance_duration_am_time_in_start_raw) : null;
                                                            $attendance_duration_am_time_in_end = $attendance_duration_am_time_in_end_raw ? strtotime($attendance_duration_am_time_in_end_raw) : null;
                                                            $attendance_duration_am_time_out_start = $attendance_duration_am_time_out_start_raw ? strtotime($attendance_duration_am_time_out_start_raw) : null;
                                                            $attendance_duration_am_time_out_end = $attendance_duration_am_time_out_end_raw ? strtotime($attendance_duration_am_time_out_end_raw) : null;
                                                            $attendance_duration_pm_time_in_start = $attendance_duration_pm_time_in_start_raw ? strtotime($attendance_duration_pm_time_in_start_raw) : null;
                                                            $attendance_duration_pm_time_in_end = $attendance_duration_pm_time_in_end_raw ? strtotime($attendance_duration_pm_time_in_end_raw) : null;
                                                            $attendance_duration_pm_time_out_start = $attendance_duration_pm_time_out_start_raw ? strtotime($attendance_duration_pm_time_out_start_raw) : null;
                                                            $attendance_duration_pm_time_out_end = $attendance_duration_pm_time_out_end_raw ? strtotime($attendance_duration_pm_time_out_end_raw) : null;

                                                            // Set placeholders for time to display
                                                            $am_time_in_display = "~";
                                                            $am_time_out_display = "~";
                                                            $pm_time_in_display = "~";
                                                            $pm_time_out_display = "~";

                                                            // Check AM Time In and Out
                                                            if ($event_time_duration == "Whole Day - AM Time In & AM Time Out & PM Time In & PM Time Out") {
                                                                // Only check if the attendance time and duration boundaries are not null
                                                                if ($attendance_am_time_in !== null && $attendance_duration_am_time_in_start !== null && $attendance_duration_am_time_in_end !== null) {
                                                                    if ($attendance_am_time_in >= $attendance_duration_am_time_in_start && $attendance_am_time_in <= $attendance_duration_am_time_in_end) {
                                                                        $am_time_in_display = date('H:i:s', $attendance_am_time_in);
                                                                    }
                                                                }
                                                                if ($attendance_am_time_out !== null && $attendance_duration_am_time_out_start !== null && $attendance_duration_am_time_out_end !== null) {
                                                                    if ($attendance_am_time_out >= $attendance_duration_am_time_out_start && $attendance_am_time_out <= $attendance_duration_am_time_out_end) {
                                                                        $am_time_out_display = date('H:i:s', $attendance_am_time_out);
                                                                    }
                                                                }
                                                            }

                                                            // Check PM Time In and Out
                                                            if ($event_time_duration == "Whole Day - AM Time In & AM Time Out & PM Time In & PM Time Out") {
                                                                if ($attendance_pm_time_in !== null && $attendance_duration_pm_time_in_start !== null && $attendance_duration_pm_time_in_end !== null) {
                                                                    if ($attendance_pm_time_in >= $attendance_duration_pm_time_in_start && $attendance_pm_time_in <= $attendance_duration_pm_time_in_end) {
                                                                        $pm_time_in_display = date('H:i:s', $attendance_pm_time_in);
                                                                    }
                                                                }
                                                                if ($attendance_pm_time_out !== null && $attendance_duration_pm_time_out_start !== null && $attendance_duration_pm_time_out_end !== null) {
                                                                    if ($attendance_pm_time_out >= $attendance_duration_pm_time_out_start && $attendance_pm_time_out <= $attendance_duration_pm_time_out_end) {
                                                                        $pm_time_out_display = date('H:i:s', $attendance_pm_time_out);
                                                                    }
                                                                }
                                                            }

                                                            // For "Half Day - AM" events:
                                                            if ($event_time_duration == "Half Day - AM Time In & AM Time Out") {
                                                                if ($attendance_am_time_in !== null && $attendance_duration_am_time_in_start !== null && $attendance_duration_am_time_in_end !== null) {
                                                                    if ($attendance_am_time_in >= $attendance_duration_am_time_in_start && $attendance_am_time_in <= $attendance_duration_am_time_in_end) {
                                                                        $am_time_in_display = date('H:i:s', $attendance_am_time_in);
                                                                    }
                                                                }
                                                                if ($attendance_am_time_out !== null && $attendance_duration_am_time_out_start !== null && $attendance_duration_am_time_out_end !== null) {
                                                                    if ($attendance_am_time_out >= $attendance_duration_am_time_out_start && $attendance_am_time_out <= $attendance_duration_am_time_out_end) {
                                                                        $am_time_out_display = date('H:i:s', $attendance_am_time_out);
                                                                    }
                                                                }
                                                            }

                                                            // For "Half Day - PM" events:
                                                            if ($event_time_duration == "Half Day - PM Time In & PM Time Out") {
                                                                if ($attendance_pm_time_in !== null && $attendance_duration_pm_time_in_start !== null && $attendance_duration_pm_time_in_end !== null) {
                                                                    if ($attendance_pm_time_in >= $attendance_duration_pm_time_in_start && $attendance_pm_time_in <= $attendance_duration_pm_time_in_end) {
                                                                        $pm_time_in_display = date('H:i:s', $attendance_pm_time_in);
                                                                    }
                                                                }
                                                                if ($attendance_pm_time_out !== null && $attendance_duration_pm_time_out_start !== null && $attendance_duration_pm_time_out_end !== null) {
                                                                    if ($attendance_pm_time_out >= $attendance_duration_pm_time_out_start && $attendance_pm_time_out <= $attendance_duration_pm_time_out_end) {
                                                                        $pm_time_out_display = date('H:i:s', $attendance_pm_time_out);
                                                                    }
                                                                }
                                                            }

                                                            // Display the row in the table
                                                            echo "
                                                            <tr class='attendance-history-row'>
                                                                <td class='attendance-history-content-event-name'>$event_name</td>
                                                                <td class='attendance-history-content-event-date'>$event_date</td>
                                                                <td class='attendance-history-content-event-organizer'>$event_organizer</td>
                                                                <td class='attendance-history-content-event-am-tin'>$am_time_in_display</td>
                                                                <td class='attendance-history-content-event-am-tout'>$am_time_out_display</td>
                                                                <td class='attendance-history-content-event-pm-tin'>$pm_time_in_display</td>
                                                                <td class='attendance-history-content-event-pm-tout'>$pm_time_out_display</td>
                                                            </tr>";
                                                        }   
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    <?php endwhile;?>
                <?php else:?>
                    User not found!
                <?php endif;?>
            </div>
        </div>  
    </div>
    
</body>
</html>