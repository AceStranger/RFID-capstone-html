<?php
include "dbh.php";
session_start();


if($_SERVER["REQUEST_METHOD"] == "POST"){

}
if(@isset($_SESSION['request'])){
    if($_SESSION['request'] === 'Delete Dean Account'){
        $deanID = $_SESSION['dean-account-id'];
        $deanQuery = "SELECT * FROM dean WHERE `dean_id` = $deanID";
        $deanQueryResult = mysqli_query($conn, $deanQuery);
        $deanData = mysqli_fetch_assoc($deanQueryResult);
        $usersID = $deanData['user_id'];

        $selectQuery = "SELECT * FROM `user` WHERE `user_id` = $usersID";
        $selectQueryResult = mysqli_query($conn, $selectQuery);
        $selectedUserData = mysqli_fetch_assoc($selectQueryResult);
        $userRole = "";
        if($selectedUserData) {
            $userRole = $selectedUserData['user_role'];
            if(str_contains($userRole, ", dean")){
                $userRole = str_replace(", dean", "", $userRole);
            } elseif(str_contains($userRole, ",dean")){
                $userRole = str_replace(",dean", "", $userRole);
            } elseif(str_contains($userRole, "dean, ")){
                $userRole = str_replace("dean, ", "", $userRole);
            } elseif(str_contains($userRole, "dean,")){
                $userRole = str_replace("dean,", "", $userRole);
            } elseif(str_contains($userRole, "dean")){
                $userRole = str_replace("dean", "", $userRole);
            }
        }
        $updateQuery = "UPDATE `user` SET `user_role` = '$userRole' WHERE `user_id` = $usersID";
        $updateResult = mysqli_query($conn, $updateQuery);
        
        if($updateResult) {
            $_SESSION['formResult'] = "Dean Added Successfully!";
            $_SESSION['UserSelected'] = false;
            header("Location: adminDeanPage.php");
        } else {
            $_SESSION['formResult'] = "Error Updating User Role!";
            $_SESSION['UserSelected'] = false;
            header("Location: adminDeanPage.php");
        }

        $deleteQuery = "DELETE FROM `dean` WHERE dean_id = '$deanID'";
        $deleteResult = mysqli_query($conn, $deleteQuery);
        if($deleteResult){
            $_SESSION['UserSelected'] = false;
            header("Location: adminDeanPage.php");
        }
    }
}







?>