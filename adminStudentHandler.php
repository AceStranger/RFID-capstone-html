<?php
include "dbh.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
}

if (@isset($_SESSION['request'])) {
    if ($_SESSION['request'] === 'Delete Student Account') {  // Changed 'Delete Officer Account' to 'Delete Student Account'
        unset($_SESSION['formResult']);
        $studentID = $_SESSION['student-account-id'];  // Changed 'officer-account-id' to 'student-account-id'
        $studentQuery = "SELECT * FROM student WHERE `student_id` = $studentID";  // Changed 'officer' to 'student'
        $studentQueryResult = mysqli_query($conn, $studentQuery);
        $studentData = mysqli_fetch_assoc($studentQueryResult);
        $usersID = $studentData['user_id'];

        $selectQuery = "SELECT * FROM `user` WHERE `user_id` = $usersID";
        $selectQueryResult = mysqli_query($conn, $selectQuery);
        $selectedUserData = mysqli_fetch_assoc($selectQueryResult);
        $userRole = "";
        if ($selectedUserData) {
            $userRole = $selectedUserData['user_role'];
            if (str_contains($userRole, ", student")) {  // Changed 'officer' to 'student'
                $userRole = str_replace(", student", "", $userRole);  // Changed 'officer' to 'student'
            } elseif (str_contains($userRole, ",student")) {
                $userRole = str_replace(",student", "", $userRole);
            } elseif (str_contains($userRole, "student, ")) {  // Changed 'officer' to 'student'
                $userRole = str_replace("student, ", "", $userRole);  // Changed 'officer' to 'student'
            } elseif (str_contains($userRole, "student,")) {
                $userRole = str_replace("student,", "", $userRole);  // Changed 'officer' to 'student'
            } elseif (str_contains($userRole, "student")) {  // Changed 'officer' to 'student'
                $userRole = str_replace("student", "", $userRole);  // Changed 'officer' to 'student'
            }
        }
        $updateQuery = "UPDATE `user` SET `user_role` = '$userRole' WHERE `user_id` = $usersID";
        $updateResult = mysqli_query($conn, $updateQuery);

        if ($updateResult) {
            $_SESSION['formResult'] = "Student Removed Successfully!";
            $_SESSION['UserSelected'] = false;
            header("Location: adminStudentPage.php");  // Changed 'adminOfficerPage.php' to 'adminStudentPage.php'
        } else {
            $_SESSION['formResult'] = "Error Updating User Role!"; 
            $_SESSION['UserSelected'] = false;
            header("Location: adminStudentPage.php");  // Changed 'adminOfficerPage.php' to 'adminStudentPage.php'
        }

        $deleteQuery = "DELETE FROM `student` WHERE student_id = '$studentID'";  // Changed 'officer' to 'student'
        $deleteResult = mysqli_query($conn, $deleteQuery);
        if ($deleteResult) {
            $_SESSION['UserSelected'] = false;
            header("Location: adminStudentPage.php");  // Changed 'adminOfficerPage.php' to 'adminStudentPage.php'
        }
    }
}
?>
