<?php
session_start();
include "dbh.php";
if(@!isset($_SESSION['user_ID']) || $_SESSION['user_ID'] === null) {
    header("Location: LogInPage.html");
    exit();
}
$user_ID = @$_SESSION['user_ID'];
$query = "SELECT * FROM user WHERE user_id = $user_ID";
$result = mysqli_query($conn, $query);
$userInfo = $result->fetch_assoc();




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event</title>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminRFIDAssign.css">
    <script src="js\adminSidebar.js"></script>
    <script src="js\jquery-3.7.1.js"></script>
</head>
<body>
    <div class="main-content-container">
        <?php include_once"adminSidebar.php";?>
        <div class="main-content">
            <div class="rfid-assign-data-content">
                <div class="rfid-assign-data-content-top-part">
                    <h1 class="rfid-assign-data-content-head-text">RFID</h1>
                </div>
                <?php 
                    $usersID = "";
                    $fullName = "";
                    $usersSchoolID = "";
                    $profilePic = "";
                    if(@isset($_SESSION['UserSelected']) && $_SESSION['UserSelected'] === true){
                        // Check if USERSID is set in session
                        if (@!isset($_SESSION['USERSID']) && $_SESSION['USERSID'] < 1) {
                            $_SESSION['UserSelected'] = false;
                            echo "<script>alert('You have not selected a user!');</script>";
                            header("Location: adminSelectUser.php"); // Redirect to select user page if no user is selected
                            exit();
                        }

                        $userID = $_SESSION['USERSID']; // Get the selected user ID from session

                        // Fetch user details from the database
                        $userQuery = "SELECT * FROM user WHERE user_id = '$userID'";
                        $userResult = mysqli_query($conn, $userQuery);
                        $userData = mysqli_fetch_assoc($userResult);
                    

                        // $_SESSION['USERSID'] = null;
                        // unset($_SESSION['USERSID']);
                    
                        if ($userData) {
                            // Assign the fetched data to variables
                            $usersID = $userData['user_id'];
                            $fullName = $userData['user_firstname'] . " " . $userData['user_middlename'] . " " . $userData['user_lastname'] . " " . $userData['user_suffixname']; 
                            $usersSchoolID = $userData['user_school_id'];
                            $profilePic = $userData['user_img']; 
                        } else {
                            echo "User not found.";
                            exit();
                        }
                        $addNewFormDisplay = 'style="display:flex;"';
                    }
                
                ?>
                <div class="rfid-assign-data-content-main-content">
                    <div class="rfid-assign-data-main-sub-content">
                        <form action="adminRFIDAssignHandler.php" method="post" class="rfid-assign-data-content-form">
                            <input type="hidden" name="users-id" value="<?php echo htmlspecialchars($usersID); ?>">
                            <div class="form-content-row">
                                <label for="">Name:</label>
                                <input type="text" disabled name="" id="" value="<?php echo $fullName;    ?>">
                            </div>
                            <div class="form-content-row">
                                <label for="">School ID:</label>
                                <input type="text" disabled name="" id="" value="<?php echo $usersSchoolID; ?>">
                            </div>
                            <div class="form-content-row">
                                <label for="">Image:</label>
                                <img src="<?php echo $profilePic; ?>" alt="" srcset="">
                            </div>

                            <div class="form-content-row">
                                <label for="">RFID Tag:</label>
                                <input type="text" name="rfid-tag" id="rfid-tag" value="">
                            </div>
                            <div class="form-content-row">
                                <label for="">Assigning Status:</label>
                                <div class="assigning-status">Assigning RFID Tag has Failed!</div>
                            </div>

                            <div class="form-content-btn">
                                <button id="connectButton" type="button">Scan</button>
                                <button type="submmit" name="rfidAssign">Assign</button>
                            </div>
                            


                        </form>
<script>
// Function to connect to the serial port and read data
async function connectToSerialPort() {
    try {
        // Request a port and open a connection
        const port = await navigator.serial.requestPort();
        await port.open({ baudRate: 9600 }); // Set to your desired baud rate
        
        // Set up the text decoder to read incoming data
        const textDecoder = new TextDecoderStream();
        const readableStreamClosed = port.readable.pipeTo(textDecoder.writable);
        const reader = textDecoder.readable.getReader();

        let buffer = ""; // Initialize an empty buffer to accumulate incoming data

        console.log("Connected to the serial port.");

        // Continuously read data from the serial port
        while (true) {
            const { value, done } = await reader.read();
            if (done) {
                // Allow the serial port to be closed
                reader.releaseLock();
                break;
            }
            if (value) {
                // Append the new data to the buffer
                buffer += value;

                // Split the buffer by new lines
                const lines = buffer.split("\n");

                // Process each line
                for (let i = 0; i < lines.length; i++) {
                    const line = lines[i].trim();

                    // Check if the line contains "Card UID:"
                    if (line.startsWith("Card UID:")) {
                        // Extract and display the UID in the RFID Tag field
                        const uid = line.replace("Card UID:", "").trim();
                        document.getElementById("rfid-tag").value = uid;
                        console.log("UID Detected:", uid); // Log the UID for verification
                    }
                }

                // If there is a partial line at the end, keep it in the buffer for the next iteration
                buffer = lines[lines.length - 1]; 
            }
        }
    } catch (error) {
        console.error("Error:", error);
    }
}

// Add an event listener to a button to trigger the serial connection
document.getElementById("connectButton").addEventListener("click", function () {
    connectToSerialPort();
});

</script>
                    </div>
                </div>
            </div>
        </div>  
    </div>
    

    <script>
    </script>
</body>
</html>