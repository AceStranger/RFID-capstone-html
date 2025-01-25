<?php 
    session_start();
    if (!isset($_SESSION['user_ROLE'])) {
        header("Location: LogIn.php");
        exit();
    }
    else {
        $userRole = $_SESSION['user_ROLE'];
        if (str_contains($userRole, "student")){
            header("Location: EventPage.php");
        } else {
            header("Location: LogInPage.html");
        }
    }


    exit();

?>