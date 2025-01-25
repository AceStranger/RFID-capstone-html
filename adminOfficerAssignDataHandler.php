<?php 

include "dbh.php";
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['vSubmit']) || isset($_POST['cancelSubmit'])) {
        $_SESSION['officer-id'] = $_POST['officer-id'];
        $_SESSION['organization-id'] = $_POST['organization-id'];
        $_SESSION['request'] = "view-officer";
        header("Location: adminOfficerAssignPage.php");
    }
    if(isset($_POST['eSubmit'])) {
        $_SESSION['officer-id'] = $_POST['officer-id'];
        $_SESSION['organization-id'] = $_POST['organization-id'];
        $_SESSION['request'] = "edit-officer";
        header("Location: adminOfficerAssignPage.php");
    }
    if(isset($_POST['dSubmit'])) {
        $_SESSION['officer-id'] = $_POST['officer-id'];
        $_SESSION['organization-id'] = $_POST['organization-id'];
        $_SESSION['request'] = "delete-officer";
        header("Location: adminOfficerAssignPage.php");
    }
    // Cancel action
    if (isset($_POST['cSubmit'])) {
        $_SESSION['officer-id'] = $_POST['officer-id'];
        $_SESSION['organization-id'] = $_POST['organization-id'];
        $_SESSION['request'] = "view-officer";
        header("Location: adminOfficerAssignPage.php");
        exit();
    }


    // // Update action
    // if (isset($_POST['uSubmit'])) {
    //     $officerId = $_POST['officer-id'];
    //     $organizationId = $_POST['organization-id'];
    //     $officerDuty = $_POST['officer-duty'] ?? null;
    //     $officerResponsibility = $_POST['officer-responsibility'] ?? null;
    //     $assignDuty = $_POST['officer-assign-duty'] ?? null;

    //     if ($officerDuty && $officerResponsibility && $assignDuty) {
    //         $query = "UPDATE officer 
    //                   SET officer_duty = ?, officer_responsibility = ?, officer_assign_duty = ?
    //                   WHERE officer_id = ? AND organization_id = ?";
    //         $stmt = $conn->prepare($query);
    //         $stmt->bind_param("sssss", $officerDuty, $officerResponsibility, $assignDuty, $officerId, $organizationId);

    //         if ($stmt->execute()) {
    //             $_SESSION['success_message'] = "Officer details updated successfully.";
    //         } else {
    //             $_SESSION['error_message'] = "Failed to update officer details. Please try again.";
    //         }

    //         $stmt->close();
    //     } else {
    //         $_SESSION['error_message'] = "All fields are required to update officer details.";
    //     }

    //     header("Location: adminOfficerAssignPage.php");
    //     exit();
    // }
}


?>