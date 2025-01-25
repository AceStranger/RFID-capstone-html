<?php 
    include "dbh.php";
    $user_ID = $_SESSION['user_ID'];
    
    $query = "SELECT * FROM user WHERE user_id = '$user_ID'";
    $result = mysqli_query($conn, $query);
    $userInfo = $result->fetch_assoc();
    $userfn = $userInfo['user_firstname'].' '.$userInfo['user_middlename'].' '.$userInfo['user_lastname'];
    $userpfp = $userInfo['user_img'];
?>

    <div class="header-content">
        <div class="header-content-logo">
            <div class="logo-container"><img src="..\pictures\NORMI Logo.png" alt="" class="logo"></div>
        </div>
        <div class="header-content-menu">
            <div class="header-content-menu-item"><a href="EventPage.php">Event</a></div>
        </div>
        <div class="side-profile-content">
            <div class="side-profile-content-name"><?php echo $userfn;?></div>
            <div class="side-profile-content-avatar">
                <img src="<?php echo $userpfp; ?>" alt="">   
            </div>
            <div class="side-profile-content-user-menu-list-container">
                <div class="user-menu-list-content">
                    <form action="adminUserAccount.php" method="POST" style="display: inline;">
                        <input type="hidden" name="user-id" value="<?php echo $user_ID; ?>" />
                        <button type="submit" name="pSubmit" id="viewProfilebtn">View Profile</button>
                    </form>
                    <a href="LogOut.php">Log out</a>
                </div>
            </div>
        </div>
    </div> 