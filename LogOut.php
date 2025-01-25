<?php
     session_start();
     // Clear specific session variables
     $_SESSION['user_ID'] = ''; 
     // Unset session variables
     unset($_SESSION['user_ID']); 
     session_unset();
     // Destroy the session
     session_destroy();
     // Redirect to the login page or home page
     header("Location: LogInPage.html");
exit;
?>