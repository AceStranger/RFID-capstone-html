<?php
session_start();
include "dbh.php";
$user_ID = $_SESSION['user_ID'];
$query = "SELECT * FROM user WHERE user_id = $user_ID";
$result = mysqli_query($conn, $query);
$userInfo = $result->fetch_assoc();

// Fetch activity logs for the logged-in user
$activityQuery = "SELECT * FROM activity_logs WHERE user_id = $user_ID ORDER BY timestamp DESC";
$activityResult = mysqli_query($conn, $activityQuery);  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log</title>
    <?php include "titleIcon.php" ;?>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminAcitivityLog.css">
    <script src="js\adminSidebar.js"></script>
</head>
<body>
    <div class="main-content-container">
        <?php include_once"adminSidebar.php";?>
        <div class="main-content">
            <div class="activity-log-content">
                <div class="activity-log-content-top-part">
                    <h1 class="activity-log-content-head-text">ACTIVITY LOG</h1>
                </div>
                <div class="activity-log-content-activity-log-account-content">
                    <div class="activity-log-account-content-main-content">

                        <table class="activity-log-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Action Type</th>
                                    <th>Entity</th>
                                    <th>Entity ID</th>
                                    <th>Description</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($log = mysqli_fetch_assoc($activityResult)) { ?>
                                    <tr>
                                        <td><?php echo $log['id']; ?></td>
                                        <td><?php echo htmlspecialchars($log['action_type']); ?></td>
                                        <td><?php echo htmlspecialchars($log['entity']); ?></td>
                                        <td><?php echo htmlspecialchars($log['entity_id']); ?></td>
                                        <td><?php echo htmlspecialchars($log['description']); ?></td>
                                        <td><?php echo htmlspecialchars($log['timestamp']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            
        </div>  
    </div>
</body>
</html>