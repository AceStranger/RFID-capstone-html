<?php 
    session_start();
    echo $_SESSION['user_ROLE'];
    if (!isset($_SESSION['user_ROLE'])) {
        header("Location: officerLogInPage.html"); // Redirect to login if not logged in
        exit();
    }
    else {
        $userRole = $_SESSION['user_ROLE'];
        var_dump( $userRole);
        if (strpos($userRole, "officer") !== false) {
            header("Location: adminDashboardPage.php");
        } else {
            header("Location: officerLogInPage.html");
        }
    }


    exit();

?>