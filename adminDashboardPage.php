
<?php

session_start();
include "dbh.php";

if(@!isset($_SESSION['user_ID']) || $_SESSION['user_ID'] === null) {
    header("Location: LogInPage.html");
    exit();
}

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
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/adminBody.css"> 
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminDashboard.css">
    <script src="js\adminSidebar.js"></script>
</head>
<body>
    <div class="main-content-container">
        <?php include_once"adminSidebar.php";?>
        <div class="main-content">
            <div class="dashboard-content">
            <div class="dasboard-content-main-content">
                <h2>Posted Events</h2>
                <div class="main-content-posted-event">
                    <div class="posted-event-content-container">
                        <div class="posted-event-table-content-subcontainer">
                            <table class='posted-event-table'>
                                <thead>
                                    <tr>
                                        <th>Event Name</th>
                                        <th>Date</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT * FROM event ORDER BY date_added DESC";
                                    $result = mysqli_query($conn, $query);

                                    if (!$result) {
                                        die("Query failed: " . mysqli_error($conn));
                                    }

                                    // Check if there are any events
                                    if (mysqli_num_rows($result) > 0) {

                                        // Loop through each event
                                        while ($event = mysqli_fetch_assoc($result)) {
                                            $eventName = htmlspecialchars($event['event_name']);
                                            $eventDate = htmlspecialchars($event['event_date']);
                                            $eventTimeStart = htmlspecialchars($event['event_time_start']);
                                            $eventTimeEnd = htmlspecialchars($event['event_time_end']);
                                            $eventPlace = htmlspecialchars($event['event_place']);

                                            echo "
                                                <tr class='posted-event-content-row'>
                                                    <td>$eventName</td>
                                                    <td>$eventDate</td>
                                                    <td>$eventTimeStart</td>
                                                    <td>$eventTimeEnd</td>
                                                    <td>$eventPlace</td>
                                                </tr>
                                            ";
                                        }

                                    } else {
                                        echo "<p>No events have been posted yet.</p>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-content-aside-content">
                <!-- Today's Event -->
                <div class="aside-content-today-event-content">
                    <span>Today's Event</span>
                    <div class="today-event-content-container">
                        <div class="today-event-content-subcontainer">
                            <table class='today-event-table'>
                                <thead>
                                    <tr>
                                        <th>Event Name</th>
                                        <th>Location</th>
                                        <th>Attendees</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Fetch events happening today
                                    $query = "SELECT * FROM event WHERE event_date = CURDATE() ORDER BY `date_added`DESC";
                                    $result = mysqli_query($conn, $query);

                                    if (!$result) {
                                        die("Query failed: " . mysqli_error($conn));
                                    }

                                    // Check if there are any events today
                                    if (mysqli_num_rows($result) > 0) {

                                        // Loop through each event happening today
                                        while ($event = mysqli_fetch_assoc($result)) {
                                            $eventName = htmlspecialchars($event['event_name']);
                                            $eventPlace = htmlspecialchars($event['event_place']);
                                            $attendeeCount = htmlspecialchars($event['event_participant']); // Assuming this stores attendee count

                                            echo "
                                                <tr>
                                                    <td>$eventName</td>
                                                    <td>$eventPlace</td>
                                                    <td>$attendeeCount</td>
                                                </tr>
                                            ";
                                        }
                                    } else {
                                        echo "<td colspan='3'>No events are scheduled for today.</td>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Event -->
                <div class="aside-content-upcoming-event-content">
                    <span>Upcoming Event</span>
                    <div class="upcoming-event-content-container">
                        <div class="upcoming-event-content-subcontainer">
                            <table class='upcoming-event-table'>
                                <thead>
                                    <tr>
                                        <th>Event Name</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        // Fetch upcoming events where event_date is after today
                                        $query = "SELECT * FROM event WHERE event_date > CURDATE() ORDER BY event_date ASC";
                                        $result = mysqli_query($conn, $query);

                                        if (!$result) {
                                            die("Query failed: " . mysqli_error($conn));
                                        }

                                        // Check if there are any upcoming events
                                        if (mysqli_num_rows($result) > 0) {

                                            // Loop through each upcoming event
                                            while ($event = mysqli_fetch_assoc($result)) {
                                                $eventName = htmlspecialchars($event['event_name']);
                                                $eventDate = htmlspecialchars(date("m/d/Y", strtotime($event['event_date'])));
                                                $eventTimeStart = htmlspecialchars(date("h:i a", strtotime($event['event_time_start'])));
                                                $eventTimeEnd = htmlspecialchars(date("h:i a", strtotime($event['event_time_end'])));
                                                $eventPlace = htmlspecialchars($event['event_place']);

                                                echo "
                                                    <tr>
                                                        <td>$eventName</td>
                                                        <td>$eventDate</td>
                                                        <td>$eventTimeStart - $eventTimeEnd</td>
                                                        <td>$eventPlace</td>
                                                    </tr>
                                                ";
                                            }

                                            echo "";
                                        } else {
                                            echo "<td colspan='4'>No upcoming events scheduled.</td>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div> 
    </div>
</body>
</html>