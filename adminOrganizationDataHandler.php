<?php

include "dbh.php";
include "logActivity.php";


session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['vSubmit']) || isset($_POST['cancelSubmit']) || isset($_POST['cSubmit'])) {
        $_SESSION['organization-id'] = $_POST['organization-id'];
        $_SESSION['request'] = 'view-organization';
        header("Location: adminOrganizationData.php");
        exit();
    } elseif (isset($_POST['eSubmit'])) {
        $_SESSION['organization-id'] = $_POST['organization-id'];
        $_SESSION['request'] = 'edit-organization';
        header("Location: adminOrganizationData.php");
        exit();
    } elseif (isset($_POST['dSubmit'])) {
        // Handle delete organization request
        $organizationID = $_POST['organization-id'];

        // Delete query
        $deleteQuery = "DELETE FROM organization WHERE organization_id = ?";

        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param('i', $organizationID);

        if ($stmt->execute()) {
            // Log the delete activity
            $action_type = 'Delete Organization';
            $entity = 'Organization';
            $entity_id = $organizationID;
            $user_id = $_SESSION['user_ID'];
            $description = "Organization with ID '$organizationID' deleted by user '$user_id'.";
            logActivity($action_type, $entity, $entity_id, $user_id, $description, $conn);

            $_SESSION['organization-id'] = $organizationID;
            $_SESSION['request'] = 'view-organization';
            header("Location: adminOrganizationPage.php");
            exit();
        } else {
            error_log("Error deleting record: " . mysqli_error($conn));
            $_SESSION['organization-id'] = $organizationID;
            $_SESSION['request'] = 'view-organization';
            header("Location: adminOrganizationPage.php");
            exit();
        }

        // Close the statement
        $stmt->close();
    } elseif (isset($_POST['uSubmit'])) {
        // Handle update organization request
        $organizationID = $_POST['organization-id'];
        $organizationName = mysqli_real_escape_string($conn, $_POST['organizationName']);
        $organizationResponsibility = mysqli_real_escape_string($conn, $_POST['organizationResponsibilty']);

        $updateQuery = "UPDATE organization 
                        SET organization_name = ?, 
                            organization_responsibility = ? 
                        WHERE organization_id = ?";

        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('ssi', $organizationName, $organizationResponsibility, $organizationID);

        if ($stmt->execute()) {
            // Log the update activity
            $action_type = 'Update Organization';
            $entity = 'Organization';
            $entity_id = $organizationID;
            $user_id = $_SESSION['user_ID'];
            $description = "Organization with ID '$organizationID' updated by user '$user_id'.";
            logActivity($action_type, $entity, $entity_id, $user_id, $description, $conn);
            
            $_SESSION['organization-id'] = $organizationID;
            $_SESSION['request'] = 'view-organization';
            header("Location: adminOrganizationData.php");
            exit();
        } else {
            error_log   ("Error updating record: " . mysqli_error($conn));
            $_SESSION['organization-id'] = $organizationID;
            $_SESSION['request'] = 'view-organization';
            header("Location: adminOrganizationData.php");
            exit();
        }
        // Close the statement
        $stmt->close();
    }
}






?>