<?php 
session_start();

// Check if user role is set
if (!isset($_SESSION['user_ROLE'])) {
    header("Location: LogIn.php");
    exit();
}

$userRole = $_SESSION['user_ROLE'];

// Check if the user is an admin
if (strpos($userRole, "admin") !== false) {
    header("Location: adminDashboardPage.php");
    exit();
} else {
    header("Location: adminLogIn.html?#message=User is not a Administrator"); // Ensure the correct file is used
    exit();
}
?>
