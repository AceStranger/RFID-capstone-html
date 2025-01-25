<?php
// Include database connection
include 'dbh.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the action from the POST request
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'checkAttendance':
            checkAttendance($conn);
            break;

        case 'createAttendance':
            createAttendance($conn);
            break;

        case 'updateAttendance':
            updateAttendance($conn);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
}

function checkAttendance($conn) {
    $eventID = $_POST['eventID'];
    $userID = $_POST['userID'];
    $timeSelect = $_POST['timeSelect'];
    $timeColumn = mapTimeSelectToColumn($timeSelect);
    error_log("Received timeSelect: " . $timeSelect);
    error_log("Received timeSelect: " . $timeColumn);


    if (!$timeColumn) {
        echo json_encode(['success' => false, 'message' => 'Invalid time selection']);
        return;
    }

    // Check if attendance record exists
    $query = "SELECT * FROM attendance WHERE event_id = ? AND user_id = ? AND attendance_date = CURDATE() LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $eventID, $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch user info
    $userQuery = $userQuery = "SELECT 
        u.user_id, 
        u.user_school_id,
        CONCAT(u.user_firstname, ' ', u.user_middlename, ' ', u.user_lastname, ' ', u.user_suffixname) AS full_name, 
        s.`year/grade_level`, 
        s.section 
    FROM 
        user u 
    INNER JOIN 
        student s ON u.user_id = s.user_id 
    WHERE 
        u.user_id = ?
    LIMIT 1";
    $userStmt = $conn->prepare($userQuery);
    $userStmt->bind_param("i", $userID);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    $user = $userResult->fetch_assoc();

    if ($result && $result->num_rows > 0) {
        $attendanceRecord = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'recordExists' => true,
            'attendance' => $attendanceRecord, // Pass full attendance record
            'user' => $user, // Include user info
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'recordExists' => false,
            'attendance' => null, // No attendance record
            'user' => $user, // Include user info
        ]);
    }
    
}


function createAttendance($conn) {
    $eventID = $_POST['eventID'];
    $userID = $_POST['userID'];
    $timeSelect = $_POST['timeSelect'];
    $timeColumn = mapTimeSelectToColumn($timeSelect);

    if (!$timeColumn) {
        echo json_encode(['success' => false, 'message' => 'Invalid time selection']);
        return;
    }

    $insertQuery = "INSERT IGNORE INTO attendance (`event_id`, `user_id`, `attendance_date`, `$timeColumn`) VALUES (?, ?, CURDATE(), CURTIME())";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ii", $eventID, $userID);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Attendance record already exists']);
    }
}

function updateAttendance($conn) {
    $eventID = $_POST['eventID'];
    $userID = $_POST['userID'];
    $timeSelect = $_POST['timeSelect'];
    $timeColumn = mapTimeSelectToColumn($timeSelect);

    if (!$timeColumn) {
        echo json_encode(['success' => false, 'message' => 'Invalid time selection']);
        return;
    }

    $updateQuery = "UPDATE attendance SET $timeColumn = CURTIME() WHERE event_id = ? AND user_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ii", $eventID, $userID);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);

    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating attendance']);
    }
}

function mapTimeSelectToColumn($timeSelect) {
    $columns = [
        'amTimeIn' => 'attendance_am_time_in',
        'amTimeOut' => 'attendance_am_time_out',
        'pmTimeIn' => 'attendance_pm_time_in',
        'pmTimeOut' => 'attendance_pm_time_out',
    ];
    return $columns[$timeSelect] ?? null;
}
?>
