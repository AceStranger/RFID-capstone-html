<?php
session_start();
include "dbh.php";
if(@!isset($_SESSION['user_ID']) || $_SESSION['user_ID'] === null) {
    header("Location: LogInPage.html");
    exit();
}
$user_ID = @$_SESSION['user_ID'];
$query = "SELECT * FROM user WHERE user_id = $user_ID";
$result = mysqli_query($conn, $query);
$userInfo = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event</title>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminRFIDPage.css">
    <script src="js\adminSidebar.js"></script>
</head>
<body>
    <div class="main-content-container">
        <?php include_once"adminSidebar.php";?>
        <div class="main-content">
            <div class="rfid-data-content">
                <div class="rfid-data-content-top-part">
                    <h1 class="rfid-data-content-head-text">RFID</h1>
                    <div class="rfid-data-content-btn">
                        <form action="adminSelectUser.php" method="post">
                            <button type="submit" name="rfidSelectUserSubmit" class="rfid-data-btn-add">Add</button>
                        </form>
                    </div>
                </div>
                
                <div class="rfid-data-content-main-content">
                    
                    <form method="get" class="rfid-data-content-form" enctype="multipart/form-data">
                        <div class="rfid-data-content-filter-content">
                            <div class="rfid-data-content-search-input-container">
                                <label for="rfid-data-content-filter-select-col">Column :</label>
                                <select name="rfid-data-content-filter-select-col" id="rfid-data-content-filter-select-col">
                                    <option value="none">None</option>
                                    <option value="School ID">School ID</option>
                                    <option value="Name">Name</option>
                                    <option value="RFID">RFID</option>
                                    <option value="Date Added">Date Added</option>
                                </select>
                                <input type="text" name="rfid-data-content-search-input" id="rfid-data-content-search-input" placeholder="Search...">
                                <input type="submit" name="filterSubmit" id="filterSubmit" value="Search">
                            </div>
                        </div>
                    </form>
                    <div class="rfid-data-content-info-content">
                        <table class="rfid-data-content-table">
                            <thead class="rfid-data-content-head">
                                <tr class="rfid-data-content-row">
                                    <th class="rfid-data-content-school-id">School ID</th>
                                    <th class="rfid-data-content-name">Name</th>
                                    <th class="rfid-data-content-rfid-code">RFID CARD UID</th>
                                    <th class="rfid-data-content-date-added">Date Added</th>
                                    <th class="rfid-data-content-btn-edit"></th>
                                    <th class="rfid-data-content-btn-delete"></th>
                                </tr>
                            </thead>
                            <tbody class="rfid-data-content-body">
                                <?php 
                                    // Check if search form is submitted
                                    if (isset($_GET['filterSubmit'])) {
                                        $searchColumn = $_GET['rfid-data-content-filter-select-col'];
                                        $searchTerm = $_GET['rfid-data-content-search-input'];

                                        // Basic query
                                        $rfidQuery = "SELECT * FROM rfid";

                                        // Apply filter based on user input
                                        if ($searchColumn != 'none' && !empty($searchTerm)) {
                                            switch ($searchColumn) {
                                                case 'School ID':
                                                    $rfidQuery .= " WHERE rfid.user_id IN (SELECT user_id FROM user WHERE user_school_id LIKE '%$searchTerm%')";
                                                    break;
                                                case 'Name':
                                                    $rfidQuery .= " WHERE rfid.user_id IN (SELECT user_id FROM user WHERE CONCAT(user_firstname, ' ', user_middlename, ' ', user_lastname, ' ', user_suffixname) LIKE '%$searchTerm%')";
                                                    break;
                                                case 'RFID':
                                                    $rfidQuery .= " WHERE rfid_tag LIKE '%$searchTerm%'";
                                                    break;
                                                case 'Date Added':
                                                    $rfidQuery .= " WHERE date_added LIKE '%$searchTerm%'";
                                                    break;
                                            }
                                        }
                                    } else {
                                        // Default query if no filter is applied
                                        $rfidQuery = "SELECT * FROM rfid";
                                    }

                                    $result = mysqli_query($conn, $rfidQuery);

                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            // Access data from rfid table (r) and user table (u)
                                            $rfidID = $row['rfid_id']; // rfid table
                                            $usersID = $row['user_id']; // rfid table
                                            $usersRFIDTag = $row['rfid_tag']; // rfid table
                                            $usersRFIDDateAdded = $row['date_added']; // rfid table
                                            $usersSchoolID = ""; // user table
                                            $fullName = ''; // user table
                                            $usersQuery = "SELECT * FROM user WHERE user_id = '$usersID'";
                                            $usersQueryResult = mysqli_query($conn, $usersQuery);
                                            if (mysqli_num_rows($usersQueryResult) > 0) {
                                                while ($usersQueryRow = mysqli_fetch_assoc($usersQueryResult)) {
                                                    $usersSchoolID = $usersQueryRow['user_school_id']; // user table
                                                    $fullName = $usersQueryRow['user_firstname'] . ' ' . $usersQueryRow['user_middlename'] . ' ' . $usersQueryRow['user_lastname'] . ' ' . $usersQueryRow['user_suffixname']; // user table
                                                }
                                            }

                                            // Format the date to match the datetime-local input format
                                            $formattedDate = date("Y-m-d\TH:i", strtotime($usersRFIDDateAdded));
                                    
                                            echo "
                                                <tr class='rfid-data-content-row'>
                                                    <form action='rfidUserHandler.php' method='post'>
                                                        <input type='hidden' name='rfid_id' value='" . $rfidID . "'>
                                                        <td class='rfid-data-content-school-id'>" . htmlspecialchars($usersSchoolID) . "</td>
                                                        <td class='rfid-data-content-name'>" . htmlspecialchars($fullName) . "</td>
                                                        <td class='rfid-data-content-rfid-code'>" . htmlspecialchars($usersRFIDTag) . "</td>
                                                        <td class='rfid-data-content-date-added'>
                                                            <input type='datetime-local' id='dateadded' name='dateadded' value='" . htmlspecialchars($formattedDate) . "'>
                                                        </td>
                                                        <td class='rfid-data-content-btn-edit'>
                                                            <button>Edit</button>
                                                        </td>
                                                        <td class='rfid-data-content-btn-delete'>
                                                            <button type='submit' name='rfidDeleteSubmit'>Delete</button>
                                                        </td>
                                                    </form>
                                                </tr>
                                            ";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No RFID data found.</td></tr>";
                                    }
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>  
    </div>
    

    <script>
    </script>
</body>
</html>