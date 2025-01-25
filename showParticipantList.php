<?php 
session_start();
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $eventID = $_POST['event-id'];
    $eventParticipants = $_POST['event-participants'];
    $_SESSION['event-id'] = $eventID;
    $_SESSION['event-participants'] = $eventParticipants;
    header("Location: adminEventParticipantAttendance.php");
}


?>