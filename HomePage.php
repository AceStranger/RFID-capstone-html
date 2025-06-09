<?php
session_start();
include "dbh.php";
if(isset($_SESSION['user_ID'])){
    $user_ID = $_SESSION['user_ID'];
    $query = "SELECT * FROM user WHERE user_id = $user_ID";
    $result = mysqli_query($conn, $query);
    
    $userInfo = mysqli_fetch_assoc($result);
    $userfn = $userInfo['user_firstname'].' '.$userInfo['user_middlename'].' '.$userInfo['user_lastname'];
    $userpfp = $userInfo['user_img'];
    $userFormattedImage = '<img src="data:image/jpeg;base64,'.base64_encode($userpfp).'" alt="">';

} else{
    header("Location: LogInPage.html");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <?php include "titleIcon.php" ;?>
    <link rel="stylesheet" href="css/headerStyle.css"> 
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/event.css">
    <script defer src="js/jquery-3.7.1.js"></script> 
    <script defer src="js/usermenu.js"></script> 
    </head>
    <body>
    <?php include_once"header.php";?>
    <div class="home-content">
        <div class="home-content-event">
            <div class="event-content-bg-picture"></div>
            <div class="event-content-WH">
                <div class="event-content-when">When</div>
                <div class="event-content-where">Where</div>
            </div>
            <div class="event-content-eventName">
                <div class="event-content-event-name">Event Name</div>
            </div>
        </div>
    </div>
    <?php 
        include_once("footer.php");
    ?>


</body>
</html>