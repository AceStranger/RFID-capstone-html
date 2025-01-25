<?php 
    session_start();
    if (!isset($_SESSION['user_ROLE'])) {
        header("Location: LogIn.php");
        exit();
    }
    else {
        $userRole = $_SESSION['user_ROLE'];
        if (str_contains($userRole, "officer")){
            header("Location: adminDashboardPage.php");
        } else {
            header("Location: LogInPage.html");
        }
    }


    exit();

?>