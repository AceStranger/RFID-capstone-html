<?php
include "dbh.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
}

if (@isset($_SESSION['request'])) {
    if ($_SESSION['request'] === 'Delete Officer Account') { 
        unset($_SESSION['formResult']);
        $officerID = $_SESSION['officer-account-id']; 
        $officerQuery = "SELECT * FROM officer WHERE `officer_id` = $officerID"; 
        $officerQueryResult = mysqli_query($conn, $officerQuery);
        $officerData = mysqli_fetch_assoc($officerQueryResult);
        $usersID = $officerData['user_id']; 

        $selectQuery = "SELECT * FROM `user` WHERE `user_id` = $usersID";
        $selectQueryResult = mysqli_query($conn, $selectQuery);
        $selectedUserData = mysqli_fetch_assoc($selectQueryResult);
        $userRole = "";
        if ($selectedUserData) {
            $userRole = $selectedUserData['user_role'];
            if (str_contains($userRole, ", officer")) { 
                $userRole = str_replace(", officer", "", $userRole); 
            } elseif (str_contains($userRole, ",officer")) {
                $userRole = str_replace(",officer", "", $userRole);
            } elseif (str_contains($userRole, "officer, ")) {
                $userRole = str_replace("officer, ", "", $userRole);
            } elseif (str_contains($userRole, "officer,")) {
                $userRole = str_replace("officer,", "", $userRole);
            } elseif (str_contains($userRole, "officer")) {
                $userRole = str_replace("officer", "", $userRole);
            }
        }
        $updateQuery = "UPDATE `user` SET `user_role` = '$userRole' WHERE `user_id` = $usersID";
        $updateResult = mysqli_query($conn, $updateQuery);

        if ($updateResult) {
            $_SESSION['formResult'] = "Officer Removed Successfully!";
            $_SESSION['UserSelected'] = false;
            header("Location: adminOfficerPage.php");
        } else {
            $_SESSION['formResult'] = "Error Updating User Role!";
            $_SESSION['UserSelected'] = false;
            header("Location: adminOfficerPage.php");
        }

        $deleteQuery = "DELETE FROM `officer` WHERE officer_id = '$officerID'";
        $deleteResult = mysqli_query($conn, $deleteQuery);
        if ($deleteResult) {
            $_SESSION['UserSelected'] = false;
            header("Location: adminOfficerPage.php");
        }
    }
}
?>
