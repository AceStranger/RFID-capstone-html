<?php
session_start();
include "dbh.php";
$user_ID = $_SESSION['user_ID'];
$query = "SELECT * FROM user WHERE user_id = $user_ID";
$result = mysqli_query($conn, $query);
$userInfo = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log</title>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminActivityLog.css">
    <script src="js\adminSidebar.js"></script>
</head>
<body>
    <div class="main-content-container">
        <div class="dashboard-sidebar-content">
            <div class="sidebar-content">
                <div class="sidebar-content-header">
                    <div>logo</div>
                </div>
                <div class="sidebar-content-user">
                    <div class="sidebar-user-avatar"></div>
                    <div class="sidebar-user-info">
                        <div class="sidebar-user-name"><?php echo $userInfo['user_firstname']; echo ' '; echo $userInfo['user_middlename']; echo ' '; echo $userInfo['user_lastname'];?></div>
                        <div class="sidebar-user-role"><?php echo $userInfo['user_role'];?></div>
                    </div>
                </div>
                <div class="sidebar-content-item sidebar-dashboard">
                    <div href="" class="sidebar-link-text">DASHBOARD</div>
                </div>
                <div class="sidebar-content-item sidebar-user">
                    <div class="sidebar-link-text">USERS</div>
                </div>
                <div class="sidebar-content-item sidebar-dean">
                    <div class="sidebar-link-text">DEAN</div>
                </div> 
                <div class="sidebar-content-item sidebar-officer">
                    <div class="sidebar-link-text">OFFICERS</div>
                </div>
                <div class="sidebar-content-item sidebar-student">
                    <div class="sidebar-link-text">STUDENTS</div>
                </div>
                <div class="sidebar-content-item sidebar-event">
                    <div class="sidebar-link-text">EVENTS</div>
                </div>
                <div class="sidebar-content-item sidebar-sanction">
                    <div class="sidebar-link-text">SANCTIONS</div>
                </div> 
                <div class="sidebar-content-item sidebar-report">
                    <div class="sidebar-link-text">REPORT</div>
                </div> 
                <div class="sidebar-content-item sidebar-activity-log">
                    <div class="sidebar-link-text">ACTIVITY LOG</div>
                </div>
                <div class="sidebar-content-item sidebar-logout">
                    <div class="sidebar-link-text">LOG OUT</div>
                </div>
                

            </div>
        </div>
        <div class="main-content">
            <div class="activity-log-content">
                <div class="activity-log-content-top-part">
                    <h1 class="activity-log-content-head-text">EVENT</h1>
                    <div class="activity-log-content-btn">
                        <button type="button" class="btn">Add New</button>
                    </div>
                </div>
                <div class="activity-log-content-activity-log-account-content">
                    <div class="activity-log-account-content-top-part">
                        <div class="activity-log-account-content-search-input-container">
                            <input type="text" name="" id="activity-log-account-content-search-input" placeholder="Search...">
                        </div>
                    </div>
                    <div class="activity-log-account-content-main-content">
                        <div class="activity-log-account-content-head-data">
                            <div class="activity-log-account-content-header-row-username">Username</div>
                            <div class="activity-log-account-content-header-row-date-log">Date Log</div>
                            <div class="activity-log-account-content-header-row-change"></div>
                            
                        </div>
                        <div class="activity-log-account-content-table-data">
                            <div class="activity-log-account-content-header-row-name">Instramural</div>
                            <div class="activity-log-account-content-header-row-place">Normi</div>
                            <div class="activity-log-account-content-header-row-status">Ongoing</div>
                            <div class="activity-log-account-content-header-row-date">09/01/2024</div>
                            <div class="activity-log-account-content-header-row-time">08:00 am - 05:00 pm</div>
                            <div class="activity-log-account-content-header-row-organizer">CSG</div>
                            <div class="activity-log-account-content-header-row-change">
                                <button type="button" class="user-account-edit">EDIT</button>
                                <button type="button" class="user-account-remove">REMOVE</button>
                            </div>
                        </div>
                        <div class="activity-log-account-content-table-data">
                            <div class="activity-log-account-content-header-row-name">Instramural</div>
                            <div class="activity-log-account-content-header-row-place">Normi</div>
                            <div class="activity-log-account-content-header-row-status">Ongoing</div>
                            <div class="activity-log-account-content-header-row-date">09/01/2024</div>
                            <div class="activity-log-account-content-header-row-time">08:00 am - 05:00 pm</div>
                            <div class="activity-log-account-content-header-row-organizer">CSG</div>
                            <div class="activity-log-account-content-header-row-change">
                                <button type="button" class="user-account-edit">EDIT</button>
                                <button type="button" class="user-account-remove">REMOVE</button>
                            </div>
                        </div>
                        <div class="activity-log-account-content-table-data">
                            <div class="activity-log-account-content-header-row-name">Instramural</div>
                            <div class="activity-log-account-content-header-row-place">Normi</div>
                            <div class="activity-log-account-content-header-row-status">Ongoing</div>
                            <div class="activity-log-account-content-header-row-date">09/01/2024</div>
                            <div class="activity-log-account-content-header-row-time">08:00 am - 05:00 pm</div>
                            <div class="activity-log-account-content-header-row-organizer">CSG</div>
                            <div class="activity-log-account-content-header-row-change">
                                <button type="button" class="user-account-edit">EDIT</button>
                                <button type="button" class="user-account-remove">REMOVE</button>
                            </div>
                        </div>
                        <div class="activity-log-account-content-table-data">
                            <div class="activity-log-account-content-header-row-name">Instramural</div>
                            <div class="activity-log-account-content-header-row-place">Normi</div>
                            <div class="activity-log-account-content-header-row-status">Ongoing</div>
                            <div class="activity-log-account-content-header-row-date">09/01/2024</div>
                            <div class="activity-log-account-content-header-row-time">08:00 am - 05:00 pm</div>
                            <div class="activity-log-account-content-header-row-organizer">CSG</div>
                            <div class="activity-log-account-content-header-row-change">
                                <button type="button" class="user-account-edit">EDIT</button>
                                <button type="button" class="user-account-remove">REMOVE</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
        </div>  
    </div>
</body>
</html>