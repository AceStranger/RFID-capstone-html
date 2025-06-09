<?php
include "dbh.php";
$user_ID = $_SESSION['user_ID'];
$query = $conn->prepare("SELECT * FROM user WHERE user_id = ? LIMIT 1");
$query->bind_param("i", $user_ID);
$query->execute();
$result = $query->get_result();
$userInfo = $result->fetch_assoc();
$userPFP = (!empty($userInfo['user_img'])) ? $userInfo['user_img'] : 'pictures\pfp avatar.png';
$userRole = (!empty($userInfo['user_role'])) ? $userInfo['user_role'] : '';
$userFirstname = (!empty(trim($userInfo['user_firstname']))) ? $userInfo['user_firstname'] . " "  : '';
$userMiddlename = (!empty(trim($userInfo['user_middlename']))) ? $userInfo['user_middlename'] . " "  : '';
$userLastname = (!empty(trim($userInfo['user_lastname']))) ? $userInfo['user_lastname'] . " "  : '';
$userSuffixname = !empty(trim($userInfo['user_suffixname'])) ? ", " . $userInfo['user_suffixname'] : '';

error_log("UserRole: ".$userRole);
$sidebarItems = [
    'dashboard' => "<div class='sidebar-content-item sidebar-dashboard'><div class='sidebar-link-text'>DASHBOARD</div></div>",
    'users' => "<div class='sidebar-content-item sidebar-user'><div class='sidebar-link-text'>USERS</div></div>",
    'students' => "<div class='sidebar-content-item sidebar-student'><div class='sidebar-link-text'>STUDENTS</div></div>",
    'officers' => "<div class='sidebar-content-item sidebar-officer'><div class='sidebar-link-text'>OFFICERS</div></div>",
    'deans' => "<div class='sidebar-content-item sidebar-dean'><div class='sidebar-link-text'>DEANS</div></div>",
    'events' => "<div class='sidebar-content-item sidebar-event'><div class='sidebar-link-text'>EVENTS</div></div>",
    'organizations' => "<div class='sidebar-content-item sidebar-organization'><div class='sidebar-link-text'>ORGANIZATIONS</div></div>",
    'programs' => "<div class='sidebar-content-item sidebar-program'><div class='sidebar-link-text'>PROGRAMS</div></div>",
    'departments' => "<div class='sidebar-content-item sidebar-department'><div class='sidebar-link-text'>DEPARTMENTS</div></div>",
    'organizationsData' => "<div class='sidebar-content-item sidebar-organizationData'><div class='sidebar-link-text'>ORGANIZATION</div></div>",
    'sanctions' => "<div class='sidebar-content-item sidebar-sanction'><div class='sidebar-link-text'>SANCTIONS</div></div>",
    'report' => "<div class='sidebar-content-item sidebar-report'><div class='sidebar-link-text'>REPORT</div></div>",
    'activityLog' => "<div class='sidebar-content-item sidebar-activityLog'><div class='sidebar-link-text'>ACTIVITY LOG</div></div>",
    'logout' => "<div class='sidebar-content-item sidebar-logout'><div class='sidebar-link-text'>LOG OUT</div></div>",
];

if (str_contains($userRole, "officer")) {
    $sidebarContent = implode('', $sidebarItems);
    $sidebarContent = "
        {$sidebarItems['dashboard']}
        {$sidebarItems['events']}
        {$sidebarItems['organizationsData']}
        {$sidebarItems['report']}
        {$sidebarItems['activityLog']}
        {$sidebarItems['logout']}
    ";
    // $sidebarContent = "
    //     {$sidebarItems['dashboard']}
    //     {$sidebarItems['events']}
    //     {$sidebarItems['organizationsData']}
    //     {$sidebarItems['sanctions']}
    //     {$sidebarItems['report']}
    //     {$sidebarItems['activityLog']}
    //     {$sidebarItems['logout']}
    // ";
}
if (str_contains($userRole, "dean")) {
    $sidebarContent = "
        {$sidebarItems['dashboard']}
        {$sidebarItems['report']}
        {$sidebarItems['activityLog']}
        {$sidebarItems['logout']}
    ";
}
if (str_contains($userRole, "admin")) {
    $sidebarContent = "
        {$sidebarItems['dashboard']}
        {$sidebarItems['users']}
        {$sidebarItems['students']}
        {$sidebarItems['officers']}
        {$sidebarItems['deans']}
        {$sidebarItems['organizations']}
        {$sidebarItems['programs']}
        {$sidebarItems['departments']}
        {$sidebarItems['activityLog']}
        {$sidebarItems['logout']}
    ";
}
?>

<button class="sidebar-toggle-btn"><</button> 
<div class="main-sidebar-content">  
    <div class="sidebar-content">
        <div class="sidebar-content-header">
            <div class="logo-container"><img src="..\pictures\NORMI Logo.png" alt="" class="logo"></div>
        </div>
        <div class="sidebar-content-user">
            <div class="sidebar-user-avatar">
                <?php echo "<img src='$userPFP' alt='' class=''>"; ?>
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">
                    <?php echo htmlspecialchars($userFirstname) . htmlspecialchars($userMiddlename) . htmlspecialchars($userLastname) . htmlspecialchars($userSuffixname); ?>
                </div>
                <div class="sidebar-user-role"><?php echo $userRole; ?></div>
            </div>
        </div>
        <div class="sidebar-content-btn">
            <div class="sidebar-content-item-list">
                <?php echo $sidebarContent; ?>
            </div>
        </div>
    </div>
</div>