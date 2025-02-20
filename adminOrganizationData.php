<?php
session_start();
include "dbh.php";

// Ensure the user is logged in
if(@!isset($_SESSION['user_ID']) || $_SESSION['user_ID'] === null) {
    header("Location: LogInPage.html");
    exit();
}

$user_ID = @$_SESSION['user_ID'];
$query = "SELECT * FROM user WHERE user_id = $user_ID";
$result = mysqli_query($conn, $query);
$userInfo = $result->fetch_assoc();

$userRoles = $userInfo['user_role'];

// Fetch officer info
$query = "SELECT * FROM officer WHERE user_id = $user_ID LIMIT 1";
$result = mysqli_query($conn, $query);
$officerInfo = $result->fetch_assoc();

// Initialize request to avoid warnings
$request = isset($_SESSION['request']) ? $_SESSION['request'] : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization</title>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminOrganizationData.css">
    <script src="js\adminSidebar.js"></script>
</head>
<body>
    <div class="main-content-container">
        <?php include_once"adminSidebar.php";?>
        <div class="main-content">
            <div class="organization-data-content">

                <div class="organization-data-content-top-part">
                    <h1 class="organization-data-content-head-text">ORGANIZATION</h1>
                </div>
                
                <div class="organization-data-content-organization-data-account-content">
                    <?php

                        if (isset($_SESSION['organization-id']) && isset($_SESSION['request'])) {
                            $organizationID = $_SESSION['organization-id'];
                            $request = $_SESSION['request']; // $request is already initialized
                        } elseif (str_contains($userRoles, "officer")) {
                            $organizationID = $officerInfo['organization_id'];
                        } else {
                            die("Event ID or request type is missing.");
                        }

                        $query = "SELECT * FROM organization WHERE organization_id = $organizationID";
                        $result = mysqli_query($conn, $query);
                        $organizationInfo = $result->fetch_assoc();

                        $inputAvailability = "";
                        if($request === "view-organization") {
                            $inputAvailability = "disabled";
                        } elseif ($request === "" || !isset($request) || $request === null || !isset($_SESSION['organization-id']) 
                        || str_contains($userRoles, "officer") && !str_contains($userRoles, "admin")) {
                            $inputAvailability = "disabled";
                        }

                        $organizationName = htmlspecialchars($organizationInfo["organization_name"]);
                        $organizationResponsibility = htmlspecialchars($organizationInfo["organization_responsibility"]);


                    ?>
                    <form action='adminOrganizationDataHandler.php' method='POST' class='organization-data-content-info-form-content'>
                        <div class='organization-data-content-info-col'>
                            <input type='hidden' name='organization-id' value='<?php echo $organizationID;?>'>
                            <div class='organization-data-content-info-row'>
                                <label for='organizationName'>Organization Name:</label>
                                <input type='text' name='organizationName' id='organizationName' value='<?php echo $organizationName;?>' <?php echo $inputAvailability;?>>
                            </div>
                            <div class='organization-data-content-info-row'>
                                <label for='organizationResponsibilty'>Organization Responsibilty:</label>
                                <input type='text' name='organizationResponsibilty' id='organizationResponsibilty' value='<?php echo $organizationResponsibility;?>' <?php echo $inputAvailability;?>>
                            </div>

                            <?php if (str_contains($userRoles, "admin")): ?>
                                <?php if ($request === "edit-organization"): ?>
                                    <div class='organization-data-content-info-row'>
                                        <button type="submit" name="uSubmit" id="update-organization">Update</button>
                                        <button type="submit" name="cSubmit" id="cancel-edit">Cancel</button>
                                    </div>
                                <?php else: ?>
                                    <div class='organization-data-content-info-row'>
                                        <button type="submit" name="eSubmit" id="edit-organization">Edit</button>
                                        <button type="submit" name="dSubmit" id="delete-organization">Delete</button>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </form>

                    <?php
                        $sql = "
                            SELECT 
                                u.user_id, 
                                CONCAT(u.user_firstname, ' ', u.user_middlename, ' ', u.user_lastname, ' ', u.user_suffixname) AS full_name, 
                                officer.officer_id,
                                officer.officer_position,
                                officer.officer_duty,
                                officer.officer_responsibility,
                                o.organization_id,
                                o.organization_name,
                                o.organization_responsibility
                            FROM user u
                            JOIN officer ON officer.user_id  = u.user_id  AND organization_id  = $organizationID
                            JOIN organization o ON o.organization_id  = $organizationID 
                        ";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $result = $stmt->get_result();

                    ?>

                    <div class="organization-data-content-main-content">
                        <div class="organization-data-content-table-content">
                            <table class="organization-data-content-table">
                                <thead class="organization-data-content-head">
                                    <tr class="organization-data-content-row">
                                        <th class="organization-data-content-officer-name" >Officer Name</th>
                                        <th class="organization-data-content-officer-position" >Position</th>
                                        <th class="organization-data-content-officer-duty" >Duty</th>
                                        <th class="organization-data-content-officer-responsibility" >Responsibility</th>
                                        <th class="organization-data-content-btn-actions" >Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="organization-data-content-body">
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr class="organization-data-content-row">
                                            <form action="adminOfficerAssignDataHandler.php" method="post">
                                                <input type="hidden" name="organization-id" value="<?php echo $row['organization_id'];?>">
                                                <input type="hidden" name="officer-id" value="<?php echo $row['officer_id'];?>">
                                                <td class="organization-data-content-officer-name"><?php echo htmlspecialchars($row['full_name']); ?></td>
                                                <td class="organization-data-content-officer-position"><?php echo htmlspecialchars($row['officer_position']); ?></td>
                                                <td class="organization-data-content-officer-duty">
                                                    <?php echo htmlspecialchars($row['officer_duty']); ?>
                                                </td>
                                                <td class="organization-data-content-officer-responsibility"><?php echo htmlspecialchars($row['officer_responsibility']); ?></td>

                                                <?php 
                                                if (                                            
                                                    (@$officerInfo['officer_duty'] === "Head of the Organization" || 
                                                    @$officerInfo['officer_duty'] === "Second-in-Command of the Organization") &&
                                                    ($row['officer_duty'] !== "Second-in-Command of the Organization" || 
                                                    $row['officer_duty'] !== "Head of the Organization")
                                                    ) : ?>
                                                    <td class="organization-data-content-btn-Actions">
                                                        <button type="submit" name="vSubmit" >View</button>
                                                        <button type="submit" name="eSubmit" >Edit</button>
                                                    </td>
                                                <?php else: ?>
                                                    <td class="organization-data-content-btn-Actions">
                                                    </td>
                                                <?php endif; ?>
                                            </form>
                                        </tr>
                                    <?php endwhile; ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</body>
</html>
