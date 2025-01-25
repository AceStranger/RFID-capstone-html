<?php
session_start();
include "dbh.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(@isset($_POST['rfidAssign'])){
        $userID = mysqli_real_escape_string($conn, $_POST['users-id']);
        $rfidTag = mysqli_real_escape_string($conn, $_POST['rfid-tag']);

        
        // Check if the RFID tag is not empty
        if (!empty($rfidTag)) {
            // Check if RFID tag already exists in the database
            $checkRFIDQuery = "SELECT * FROM rfid WHERE rfid_tag = '$rfidTag'";
            $checkRFIDResult = mysqli_query($conn, $checkRFIDQuery);

            if (mysqli_num_rows($checkRFIDResult) > 0) {
                // RFID tag already assigned to another user
                echo "<script>alert('This RFID tag is already assigned to another user.'); window.location.href = 'selectUserPage.php';</script>";
                exit();
            } else {
                // Insert or update the RFID entry for the user
                $insertQuery = "INSERT INTO rfid (user_id, rfid_tag, date_added) VALUES ('$userID', '$rfidTag', NOW())
                                ON DUPLICATE KEY UPDATE rfid_tag = '$rfidTag', date_added = NOW()";
                $insertResult = mysqli_query($conn, $insertQuery);

                if ($insertResult) {
                    // Success - redirect to confirmation page or provide success message
                    echo "<script>alert('RFID tag successfully assigned!');</script>";
                    header ("Location: adminRFIDPage.php");
                } else {
                    // Error inserting or updating the RFID
                    echo "<script>alert('Failed to assign RFID tag. Please try again.'); window.location.href = 'selectUserPage.php';</script>";
                }
            }
        } else {
            // RFID tag is empty
            echo "<script>alert('RFID tag cannot be empty.'); window.location.href = 'selectUserPage.php';</script>";
        }
    } else {
        // User ID or RFID tag missing
        echo "<script>alert('Missing user ID or RFID tag.'); window.location.href = 'selectUserPage.php';</script>";
    }
}
// Check if user ID and RFID tag are provided
if (isset($_GET['users-id']) && isset($_GET['rfid-tag'])) {
    $userID = mysqli_real_escape_string($conn, $_GET['users-id']);
    $rfidTag = mysqli_real_escape_string($conn, $_GET['rfid-tag']);

    // Check if the RFID tag is not empty
    if (!empty($rfidTag)) {
        // Check if RFID tag already exists in the database
        $checkRFIDQuery = "SELECT * FROM rfid WHERE rfid_tag = '$rfidTag'";
        $checkRFIDResult = mysqli_query($conn, $checkRFIDQuery);

        if (mysqli_num_rows($checkRFIDResult) > 0) {
            // RFID tag already assigned to another user
            echo "<script>alert('This RFID tag is already assigned to another user.'); window.location.href = 'selectUserPage.php';</script>";
            exit();
        } else {
            // Insert or update the RFID entry for the user
            $insertQuery = "INSERT INTO rfid (user_id, rfid_tag, date_added) VALUES ('$userID', '$rfidTag', NOW())
                            ON DUPLICATE KEY UPDATE rfid_tag = '$rfidTag', date_added = NOW()";
            $insertResult = mysqli_query($conn, $insertQuery);

            if ($insertResult) {
                // Success - redirect to confirmation page or provide success message
                echo "<script>alert('RFID tag successfully assigned!'); window.location.href = 'selectUserPage.php';</script>";
            } else {
                // Error inserting or updating the RFID
                echo "<script>alert('Failed to assign RFID tag. Please try again.'); window.location.href = 'selectUserPage.php';</script>";
            }
        }
    } else {
        // RFID tag is empty
        echo "<script>alert('RFID tag cannot be empty.'); window.location.href = 'selectUserPage.php';</script>";
    }
} else {
    // User ID or RFID tag missing
    echo "<script>alert('Missing user ID or RFID tag.'); window.location.href = 'selectUserPage.php';</script>";
}
?>
