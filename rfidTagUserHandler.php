<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Include database connection (replace with your actual database connection code)
require_once 'dbh.php'; // Assumes the DB connection is set up

// Check if the RFID tag is provided via POST
if (isset($_POST['rfidTag'])) {
    $rfidTag = mysqli_real_escape_string($conn, $_POST['rfidTag']);
    
    // Fetch user details based on user_id (assuming you have a users table)
    $userQuery = "SELECT * FROM `user` WHERE `user_rfid_card_UID` = '$rfidTag' LIMIT 1";
    $userResult = mysqli_query($conn, $userQuery);
    
    if ($userResult && mysqli_num_rows($userResult) > 0) {
        $userInfo = mysqli_fetch_assoc($userResult);
        
        // Return the user information along with their image as a JSON response
        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $userInfo['user_id'],
                'schoolID' => $userInfo['user_school_id'],
                'name' => $userInfo['user_firstname'] . " " . $userInfo['user_middlename'] . " " . $userInfo['user_lastname'] . " " . $userInfo['user_suffixname'],
                'profile_picture' => $userInfo['user_img'], // Add profile image path
            ]
        ]);
    } else {
        // User not found in the users table
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} else {
    // If RFID tag is not provided
    echo json_encode(['success' => false, 'message' => 'RFID tag not provided']);
}
?>
