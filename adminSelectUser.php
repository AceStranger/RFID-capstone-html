<?php 
include "dbh.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Request Type Assignments
    $requestMappings = [
        'rfidSelectUserSubmit' => "RFID Selecting user",
        'DeanSelectUserSubmit' => "Dean Selecting user",
        'OfficerSelectUserSubmit' => "Officer Selecting user",
        'StudentSelectUserSubmit' => "Student Selecting user"
    ];
    
    // Check for user selection requests
    foreach ($requestMappings as $postKey => $requestType) {
        if (isset($_POST[$postKey])) {
            $_SESSION['request'] = $requestType;
            header("Location: adminSelectUserPage.php");
            exit();
        }
    }

    // Handle user ID selection
    if (isset($_POST['selectSubmit'])) {
        $userid = $_POST['user-id'];
        $request = $_POST['request'];

        if (!empty($request) && !empty($userid)) { // Ensure both request and userid are not empty
            $_SESSION['USERSID'] = $userid;
            $_SESSION['UserSelected'] = true;

            switch ($request) {
                case 'RFID Selecting user':
                    header("Location: adminRFIDAssign.php");
                    exit();
                case 'Dean Selecting user':
                    header("Location: adminDeanPage.php");
                    exit();
                case 'Officer Selecting user':
                    header("Location: adminOfficerPage.php");
                    exit();
                case 'Student Selecting user':
                    header("Location: adminStudentPage.php");
                    exit();
                default:
                    unset($_SESSION['USERSID']);
                    $_SESSION['UserSelected'] = false;
                    header("Location: adminDashboard.php");
                    exit();
            }
        } else {
            echo "<script>alert('Error: Missing user ID or request type.');</script>";
        }
    }

    // Handle cancellation
    if (isset($_POST['cancelSelectUser'])) {
        $request = $_POST['request'];
        
        if (!empty($request)) {
            $_SESSION['UserSelected'] = false;
            switch ($request) {
                case 'RFID Selecting user':
                    header("Location: adminRFIDAssign.php");
                    break;
                case 'Dean Selecting user':
                    header("Location: adminDeanPage.php");
                    break;
                case 'Officer Selecting user':
                    header("Location: adminOfficerPage.php");
                    break;
                case 'Student Selecting user':
                    header("Location: adminStudentPage.php");
                    break;
                default:
                    header("Location: adminUserPage.php");
                    break;
            }
            exit();
        }
    }
}
?>
