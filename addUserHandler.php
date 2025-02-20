<?php

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "dbh.php";
    include "logActivity.php";
    session_start();

    // Retrieve form data
    $role = $_POST['role'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $pnumber = $_POST['pnumber'] ?? '';
    $schoolid = $_POST['schoolid'] ?? '';
    $rfid_card_uid = $_POST['rfid-card-uid'] ?? '';
    $fname = $_POST['fname'] ?? '';
    $mname = $_POST['mname'] ?? '';
    $lname = $_POST['lname'] ?? '';
    $sname = $_POST['sname'] ?? '';
    $email = $_POST['email'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $address = $_POST['address'] ?? '';
    $pfpPic = $_FILES['pfpPic'] ?? null;

    // Check for duplicate school ID
    $checkQuery = $conn->prepare("SELECT user_ID FROM user WHERE user_school_id = ?");
    $checkQuery->bind_param("s", $schoolid);
    $checkQuery->execute();
    $checkResult = $checkQuery->get_result();

    if ($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'duplicate_schoolid']);
        exit;
    }
    if (!empty($rfid_card_uid)){
        // Check for duplicate school ID
        $checkQuery = $conn->prepare("SELECT user_ID FROM user WHERE user_rfid_card_UID = ?");
        $checkQuery->bind_param("s", $rfid_card_uid);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo json_encode(['success' => false, 'error' => 'duplicate_CardUID']);
            exit;
        }
    }
    // Handle profile picture upload
    $pfpPath = '';
    if ($pfpPic && $pfpPic['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/$schoolid/";
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                echo json_encode(['success' => false, 'error' => 'upload_dir_creation_failed']);
                exit();
            }
        }

        $fileName = basename($pfpPic['name']);
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = uniqid("profile_", true) . ".$fileExtension";
        $pfpPath = $uploadDir . $newFileName;

        if (!move_uploaded_file($pfpPic['tmp_name'], $pfpPath)) {
            echo json_encode(['success' => false, 'error' => 'file_upload_failed']);
            exit();
        }
    }
    $password = password_hash($password, PASSWORD_DEFAULT);
    // Insert user data
    $insertQuery = $conn->prepare("INSERT INTO user (user_role, user_name, user_password, user_phone_number, user_rfid_card_UID, user_school_id, user_firstname, user_middlename, user_lastname, user_suffixname, user_gender, user_address, user_email, user_img) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insertQuery->bind_param("ssssssssssssss", $role, $username, $password, $pnumber, $rfid_card_uid, $schoolid, $fname, $mname, $lname, $sname, $gender, $address, $email, $pfpPath);

    if ($insertQuery->execute()) {
        $userId = $conn->insert_id;

        $roleSuccess = true;

        // Insert additional fields based on role
        if (stripos($role, 'student') !== false) {
            $program = $_POST['program'] ?? '';
            $year = $_POST['year'] ?? '';
            $section = $_POST['section'] ?? '';
            $studentQuery = $conn->prepare("INSERT INTO student (`user_id`, `program_id`, `year/grade_level`, `section`) VALUES (?, ?, ?, ?)");
            $studentQuery->bind_param("isss", $userId, $program, $year, $section);

            if ($studentQuery->execute()) {
                $roleSuccess = true;
            } else {
                $roleSuccess = false;
            }
        }

        if (stripos($role, 'dean') !== false) {
            $department = $_POST['department'] ?? '';
            $deanQuery = $conn->prepare("INSERT INTO dean (user_id, department_id) VALUES (?, ?)");
            $deanQuery->bind_param("is", $userId, $department);

            if ($deanQuery->execute()) {
                $roleSuccess = true;
            } else {
                $roleSuccess = false;
            }
        }

        if (stripos($role, 'officer') !== false) {
            $organizationId = $_POST['organization'] ?? '';
            $position = $_POST['position'] ?? '';
            $duty = $_POST['assign_duty'] ?? '';
            $officerQuery = $conn->prepare("INSERT INTO officer (user_id, organization_id, officer_position, officer_duty) VALUES (?, ?, ?, ?)");
            $officerQuery->bind_param("isss", $userId, $organizationId, $position, $duty);

            if ($officerQuery->execute()) {
                $roleSuccess = true;
            } else {
                $roleSuccess = false;
            }
        }

        // Log the activity for adding the user
        $action_type = 'Add User';
        $entity = 'User';
        $entity_id = $userId;
        $user_id = $_SESSION['user_ID']; // Assuming you have the logged-in user ID in session
        $description = "User '$username' with role '$role' created by the user.";
        logActivity($action_type, $entity, $entity_id, $user_id, $description, $conn);

        if ($roleSuccess) {
            echo json_encode(['success' => true, 'message' => 'User and role-specific fields created successfully!']);
        } else {
            echo json_encode(['success' => true, 'message' => 'User created successfully, but role-specific fields failed!']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'insert_failed']);
    }

    // Close connections
    $insertQuery->close();
    $checkQuery->close();
    $conn->close();
}
?>
