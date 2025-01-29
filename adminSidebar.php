<?php
include "dbh.php";
$user_ID = $_SESSION['user_ID'];
$query = "SELECT * FROM user WHERE user_id = $user_ID";
$result = mysqli_query($conn, $query);
$userInfo = $result->fetch_assoc();
$userPFP = $userInfo['user_img'];

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

if (str_contains($userInfo['user_role'], "officer")) {
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
if (str_contains($userInfo['user_role'], "dean")) {
    $sidebarContent = "
        {$sidebarItems['dashboard']}
        {$sidebarItems['report']}
        {$sidebarItems['activityLog']}
        {$sidebarItems['logout']}
    ";
}
if (str_contains($userInfo['user_role'], "admin")) {
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

<button class="sidebar-toggle-btn" onclick="toggleSidebar()">></button>
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
                    <?php echo "{$userInfo['user_firstname']} {$userInfo['user_middlename']} {$userInfo['user_lastname']}"; ?>
                </div>
                <div class="sidebar-user-role"><?php echo $userInfo['user_role']; ?></div>
            </div>
        </div>
        <div class="sidebar-content-btn">
            <div class="sidebar-content-item-list">
                <?php echo $sidebarContent; ?>
            </div>
        </div>
    </div>
</div>
<script>
    
function toggleSidebar() {
    const sidebar = document.querySelector('.main-sidebar-content');
    const mainContentContainer = document.querySelector('.main-content-container');
    const mainContent = document.querySelector('.main-content');
    const toggleButton = document.querySelector('.sidebar-toggle-btn');

    // Check if sidebar is visible or hidden
    if (sidebar.style.width === '0px' || sidebar.style.width === '') {
        sidebar.style.width = '300px';  // Default width
        mainContentContainer.style.gridTemplateColumns = '300px 1fr';
        toggleButton.textContent = '<';
        toggleButton.style.top = "5px";
        toggleButton.style.padding = "10px";
    } else {
        sidebar.style.width = '0px';  // Hide sidebar
        mainContentContainer.style.gridTemplateColumns = '0px 1fr';   // Adjust grid when sidebar is hidden
        toggleButton.textContent = '>';
        toggleButton.style.top = "50%";
        toggleButton.style.paddingInline = "1px";
        toggleButton.style.paddingBlock = "10px";
    }
}


</script>