<?php
include "logActivity.php";
    session_start();
    // Check if a user role exists
    function checkUserRoleExists($conn, $useraccountid, $role) {
        $queries = [
            'student' => "SELECT COUNT(*) FROM student WHERE user_id = ?",
            'officer' => "SELECT COUNT(*) FROM officer WHERE user_id = ?",
            'dean' => "SELECT COUNT(*) FROM dean WHERE user_id = ?"
        ];
        
        if (isset($queries[$role])) {
            $stmt = $conn->prepare($queries[$role]);
            $stmt->bind_param("i", $useraccountid);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_row()[0];
            return $count > 0;
        }
        return false;
    }
    // Create a user role account if not exists
    function createUserRole($conn, $useraccountid, $role) {
        $queries = [
            'student' => "INSERT INTO student (user_id) VALUES (?)",
            'officer' => "INSERT INTO officer (user_id) VALUES (?)",
            'dean' => "INSERT INTO dean (user_id) VALUES (?)"
        ];
        
        if (isset($queries[$role])) {
            $stmt = $conn->prepare($queries[$role]);
            $stmt->bind_param("i", $useraccountid);
            $stmt->execute();
        }
    }
    
    function updateStudent($conn, $useraccountid, $studentData) {
        $query = "UPDATE `student` 
            SET 
                `program_id` = ?, 
                `year/grade_level` = ?, 
                `section` = ?
            WHERE `user_id` = ?
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", 
            $studentData['student_program'], 
            $studentData['student_year_grade_level'], 
            $studentData['student_section'], 
            $useraccountid
        );
        $stmt->execute();
    }

    function updateDean($conn, $useraccountid, $deanData) {
        $query = "UPDATE `dean` 
            SET 
                `department_id` = ?
            WHERE `user_id` = ?
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", 
            $deanData['dean_department'], 
            $useraccountid
        );
        $stmt->execute();
    }

    function updateOfficer($conn, $useraccountid, $officerData) {
        $query = "UPDATE `officer` 
            SET 
                `organization_id` = ?, 
                `officer_position` = ?
            WHERE `user_id` = ?
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", 
            $officerData['officer_organization_name'], 
            $officerData['officer_position'], 
            $useraccountid
        );
        $stmt->execute();
    }
    function updateUserCredentials($conn, $useraccountid, $credentialsData) {
        // Hash the password for security purposes
        $hashedPassword = password_hash($credentialsData['user_password'], PASSWORD_DEFAULT);
        
        // Update the query to use the correct fields for username and password
        $query = "UPDATE `user` 
                  SET `user_name` = ?, 
                      `user_password` = ?
                  WHERE `user_id` = ?";
        
        // Prepare and bind parameters
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", 
            $credentialsData['user_name'],   // username
            $hashedPassword,                 // hashed password
            $useraccountid                   // user id
        );
        
        // Execute the query
        if (!$stmt->execute()) {
            setError("Error updating user credentials: " . $stmt->error);
        }
    }

    function getUserRole($conn, $userId) {
        $query = "SELECT user_role FROM user WHERE user_id = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['user_role'] ?? null;
    }

    function deleteRoleData($conn, $userId, $role) {
        $queries = [
            'student' => "DELETE FROM student WHERE user_id = ?",
            'officer' => "DELETE FROM officer WHERE user_id = ?",
            'dean' => "DELETE FROM dean WHERE user_id = ?"
        ];
    
        if (isset($queries[$role])) {
            $stmt = $conn->prepare($queries[$role]);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();
        }
    }

    function setError($message) {
        $_SESSION['error'] = $message;
        header("Location: adminUserProfile.php");
        exit();
    }

    function redirectToProfile($request, $userId) {
        $_SESSION['user-account-id'] = $userId;
        $_SESSION['request'] = $request;
        header("Location: adminUserProfile.php");
        exit();
    }
    

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['pSubmit']) || isset($_POST['cSubmit'])) {
            // Redirect to view profile
            redirectToProfile('view-profile', $_POST['user-id']);
        }
        elseif (isset($_POST['eSubmit'])) {
            // Redirect to edit profile
            redirectToProfile('edit-profile', $_POST['user-id']);
        }
        elseif (isset($_POST['dSubmit'])) {
            // Redirect to delete profile
            redirectToProfile('delete-profile', $_POST['user-id']);
        }
        elseif (isset($_POST['dcSubmit'])) {
            include "dbh.php";
            // Process delete profile
            $userId = $_POST['user-id'];
        
            try {
                $conn->begin_transaction();
                $userAccRole = getUserRole($conn, $userId);

                foreach (['student', 'officer', 'dean'] as $role) {
                    if (str_contains($userAccRole, $role)) {
                        deleteRoleData($conn, $userId, $role);
                    }
                }

                $userDeleteQuery = "DELETE FROM user WHERE user_id = ?";
                $stmt = $conn->prepare($userDeleteQuery);
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $stmt->close();

                $conn->commit();


                // Log the activity for deleting a user
                $action_type = 'Delete User';
                $entity = 'User';
                $entity_id = $userId;
                $user_id =  $_SESSION['user_ID'];
                $description = "User with ID '$userId' and role(s) '$userAccRole' deleted by admin.";
                logActivity($action_type, $entity, $entity_id, $user_id, $description, $conn);

                $_SESSION['message'] = "User profile and associated role-specific data successfully deleted.";
                
                header("Location: adminUserPage.php");
                exit();
            } catch (Exception $e) {
                $conn->rollback();
                error_log("Transaction failed: " . $e->getMessage());
                setError("Error deleting user profile: " . $e->getMessage());
            }
        
        }
        
        elseif (isset($_POST['uSubmit'])) {
            include "dbh.php";

            // Collect user information with isset checks
            $useraccountid = mysqli_real_escape_string($conn, $_POST['user-id']);
            $userAccRole = getUserRole($conn, $useraccountid); 
            if(array_key_exists('update-student-role', $_POST) && 
                !empty($_POST['update-student-role'])) {
                $studentData = [
                    'student_program' => isset($_POST['student-program']) ? $_POST['student-program'] : '',
                    'student_year_grade_level' => isset($_POST['student-year-grade-level']) ? $_POST['student-year-grade-level'] : '',
                    'student_section' => isset($_POST['student-section']) ? $_POST['student-section'] : ''
                ];
                if (str_contains($userAccRole, "student")) {
                    try {
                        updateStudent($conn, $useraccountid, $studentData);

                        // Log the student role update
                        logActivity(
                            'Update Role',
                            'Student',
                            $useraccountid,
                            $_SESSION['user_ID'],  // Admin user who is updating
                            "Updated student role with program: {$studentData['student_program']}, year: {$studentData['student_year_grade_level']}, section: {$studentData['student_section']}",
                            $conn
                        );
                    } catch (Exception $e) {
                        setError("Error updating student: " . $e->getMessage());
                    }
                }
            }
            if(array_key_exists('update-officer-role', $_POST) && 
                !empty($_POST['update-officer-role'])) {
                $officerData = [
                    'officer_organization_name' => isset($_POST['organization-name']) ? $_POST['organization-name'] : '',
                    'officer_position' => isset($_POST['officer-position']) ? $_POST['officer-position'] : ''
                ];
                if (str_contains($userAccRole, "officer")) {
                    try {
                        updateOfficer($conn, $useraccountid, $officerData);

                        // Log the officer role update
                        logActivity(
                            'Update Role',
                            'Officer',
                            $useraccountid,
                            $_SESSION['user_ID'],  // Admin user who is updating
                            "Updated officer role with organization: {$officerData['officer_organization_name']}, position: {$officerData['officer_position']}",
                            $conn
                        );
                    } catch (Exception $e) {
                        setError("Error updating officer: " . $e->getMessage());
                    }
                }
            }
            if(array_key_exists('update-dean-role', $_POST) && 
                !empty($_POST['update-dean-role'])) {
                $deanData = [
                    'dean_department' => isset($_POST['dean-department']) ? $_POST['dean-department'] : ''
                ];
                if (str_contains($userAccRole, "dean")) {
                    try {
                        updateDean($conn, $useraccountid, $deanData);
                        
                        // Log the dean role update
                        logActivity(
                            'Update Role',
                            'Dean',
                            $useraccountid,
                            $_SESSION['user_ID'],  // Admin user who is updating
                            "Updated dean role with department: {$deanData['dean_department']}",
                            $conn
                        );
                    } catch (Exception $e) {
                        setError("Error updating dean: " . $e->getMessage());
                    }
                }
            }
            if(array_key_exists('include-user-credentials', $_POST) && 
                !empty($_POST['include-user-credentials'])) {
                $username = isset($_POST['user-name']) ? $_POST['user-name'] : '';
                $password = isset($_POST['password']) ? $_POST['password'] : '';
            
                // Hash the password before updating it in the database
                if (!empty($password)) {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                } else {
                    // If the password is empty, you might want to skip updating it or handle as necessary
                    $hashedPassword = null; // Or handle the password update condition differently if required
                }
            
                // Prepare the update query for the user credentials
                if ($hashedPassword) {
                    $userUpdateQuery = "
                        UPDATE `user` 
                        SET 
                            `user_name` = ?, 
                            `user_password` = ? 
                        WHERE `user_id` = ?
                    ";
                    $stmt = $conn->prepare($userUpdateQuery);
                    $stmt->bind_param("ssi", $username, $hashedPassword, $useraccountid);
                } else {
                    // If no password is provided, only update the username
                    $userUpdateQuery = "
                        UPDATE `user` 
                        SET 
                            `user_name` = ? 
                        WHERE `user_id` = ?
                    ";
                    $stmt = $conn->prepare($userUpdateQuery);
                    $stmt->bind_param("si", $username, $useraccountid);
                }
            
                try {
                    $stmt->execute();
            
                    // Log the update of user credentials
                    logActivity(
                        'Update Credentials',
                        'User',
                        $useraccountid,
                        $_SESSION['user_ID'],  // Admin user who is updating
                        "Updated user credentials with new username: $username",
                        $conn
                    );
                } catch (Exception $e) {
                    setError("Error updating user credentials: " . $e->getMessage());
                }
            }
            if(array_key_exists('include-user-info', $_POST) && 
                !empty($_POST['include-user-info'])) {
                $usersGender = isset($_POST['user-gender']) ? mysqli_real_escape_string($conn, $_POST['user-gender']) : '';
                $usersAddress = isset($_POST['user-address']) ? mysqli_real_escape_string($conn, $_POST['user-address']) : '';
                $usersFName = isset($_POST['user-first-name']) ? mysqli_real_escape_string($conn,$_POST['user-first-name']) : '';
                $usersMName = isset($_POST['user-middle-name']) ? mysqli_real_escape_string($conn,$_POST['user-middle-name']) : '';
                $usersLName = isset($_POST['user-last-name']) ? mysqli_real_escape_string($conn,$_POST['user-last-name']) : '';
                $usersSuffix = isset($_POST['user-suffix']) ? mysqli_real_escape_string($conn,$_POST['user-suffix']) : '';
                $usersPNumber = isset($_POST['user-pnumber']) ? mysqli_real_escape_string($conn,$_POST['user-pnumber']) : '';
                $usersSchoolID = isset($_POST['user-school-id']) ? mysqli_real_escape_string($conn,$_POST['user-school-id']) : '';
                $userpfpfilepath = isset($_POST['userpfpfilepath']) ? mysqli_real_escape_string($conn,$_POST['userpfpfilepath']) : '';
                $userRole = isset($_POST['user-role']) ? mysqli_real_escape_string($conn,$_POST['user-role']) : '';
                
                // Initialize an array to hold the roles to process
                $rolesToProcess = [];

                // Check if $userRole has a comma
                if (strpos($userRole, ',') !== false) {
                    // If it has a comma, split it into an array
                    $splitRoles = explode(",", $userRole);
                    // Trim whitespace from each role and filter out any empty strings
                    $rolesToProcess = array_filter(array_map('trim', $splitRoles), 'strlen');
                } else {
                    // If it doesn't have a comma, treat it as a single role
                    $trimmedRole = trim($userRole);
                    if (!empty($trimmedRole)) {
                        $rolesToProcess[] = $trimmedRole;
                    }
                }
                // Process each role found
                foreach ($rolesToProcess as $role) {
                    // Ensure the role is not an empty string after trimming
                    if (!empty($role)) {
                        // Check if the role exists for the user; if not, create it
                        if (!checkUserRoleExists($conn, $useraccountid, $role)) {
                            createUserRole($conn, $useraccountid, $role);
                            
                            // Log the activity for creating the user role
                            logActivity(
                                'Create Role',
                                ucfirst($role), // Capitalize the role for display
                                $useraccountid,
                                $_SESSION['user_ID'],  // Admin user who is creating the role
                                "Created the '$role' role for the user.",
                                $conn
                            );
                        }
                    }
                }


                // Initialize the profile picture path with the existing one
                $usersPFPfilePath = $userpfpfilepath;

                // Check if a new file was uploaded
                if (isset($_FILES['user-picture']) && $_FILES['user-picture']['error'] === UPLOAD_ERR_OK) {

                    // First, get the old picture's path from the database to delete it later
                    $oldImagePathQuery = "SELECT `user_img` FROM `user` WHERE `user_id` = ?";
                    $stmt_old_img = $conn->prepare($oldImagePathQuery);
                    $stmt_old_img->bind_param("i", $useraccountid);
                    $stmt_old_img->execute();
                    $result_old_img = $stmt_old_img->get_result();
                    if ($row_old_img = $result_old_img->fetch_assoc()) {
                        $oldPfpPath = $row_old_img['user_img'];
                    }
                    $stmt_old_img->close();


                    $file = $_FILES['user-picture'];
                    $usersSchoolID = trim($usersSchoolID);
                    $uploadDir = "uploads/$usersSchoolID/";
                    $absoluteUploadDir = realpath(".") . '/' . $uploadDir;
                    error_log($absoluteUploadDir);

                    if (!file_exists($absoluteUploadDir)) {
                        if (!mkdir($absoluteUploadDir, 0777, true)) {
                            error_log("Failed to create directory: " . $absoluteUploadDir);
                            setError("Failed to create directory: $absoluteUploadDir");
                            exit();
                        }
                    }
                    
    
                    $fileName = basename($file['name']);
                    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                    $newFileName = uniqid("profile_", true) . ".$fileExtension";
                    $usersPFPfilePath = $uploadDir . $newFileName;
    
                    if (move_uploaded_file($file['tmp_name'], $usersPFPfilePath)) {
                        // If a new picture is uploaded successfully, delete the old one
                        if (!empty($oldPfpPath) && file_exists($oldPfpPath)) {
                            unlink($oldPfpPath);
                        }
                    } else {
                        setError("Error uploading the file.");
                        // If the new file upload fails, revert to the old file path
                        $usersPFPfilePath = $userpfpfilepath;
                    }
                } else {
                    // If no file was uploaded, leave $usersPFPfilePath as ''
                    $usersPFPfilePath = $userpfpfilepath;
                }
                
                $userUpdateQuery = "
                    UPDATE `user` 
                    SET 
                        `user_img` = ?, 
                        `user_gender` = ?, 
                        `user_address` = ?, 
                        `user_firstname` = ?, 
                        `user_middlename` = ?, 
                        `user_lastname` = ?, 
                        `user_suffixname` = ?, 
                        `user_phone_number` = ?, 
                        `user_role` = ?, 
                        `user_school_id` = ?
                    WHERE `user_id` = ?
                ";
                $stmt = $conn->prepare($userUpdateQuery);
                $stmt->bind_param(
                    "ssssssssssi", 
                    $usersPFPfilePath, $usersGender, $usersAddress, $usersFName, $usersMName, 
                    $usersLName, $usersSuffix, $usersPNumber, $userRole, $usersSchoolID, $useraccountid
                );
                try {
                    $stmt->execute();

                    // Log the user information update
                    logActivity(
                        'Update Info',
                        'User',
                        $useraccountid,
                        $_SESSION['user_ID'],  // Admin user who is updating
                        "Updated user information including gender, address, name, and phone number",
                        $conn
                    );
                } catch (Exception $e) {
                    setError("Error updating user: " . $e->getMessage());
                }
                
            }
            $_SESSION['request'] = 'view-profile';
            header("Location: adminUserProfile.php");
            exit();
        }
    }

    
?>