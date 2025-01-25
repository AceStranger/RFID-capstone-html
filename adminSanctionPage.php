<?php
session_start();
include "dbh.php";
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
    <title>Sanction</title>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminSanction.css">
    <script src="js\adminSidebar.js"></script>
</head>
<body>
    <div class="main-content-container">
        <?php include_once"adminSidebar.php";?>
        <div class="main-content">
            <div class="sanction-content">
                <div class="sanction-content-top-part">
                    <h1 class="sanction-content-head-text">SANCTION</h1>
                    <div class="sanction-content-btn">
                        <button type="button" class="btn">Add New</button>
                    </div>
                </div>
                <div class="sanction-content-sanction-account-content">
                    <div class="sanction-account-content-top-part">
                        <div class="sanction-account-content-search-input-container">
                            <input type="text" name="" id="sanction-account-content-search-input" placeholder="Search...">
                        </div>
                    </div>
                    <div class="sanction-account-content-main-content">
                        <div class="sanction-account-content-head-data">
                            <div class="sanction-account-content-header-row-fullname">Name</div>
                            <div class="sanction-account-content-header-row-nOfAbsence">No. of Absence</div>
                            <div class="sanction-account-content-header-row-total-fines">Total Fines</div>
                            <div class="sanction-account-content-header-row-payment">Payment</div>
                            <div class="sanction-account-content-header-row-payment-status">Payment Status</div>
                            <div class="sanction-account-content-header-row-change"></div>
                            
                        </div>
                        <div class="sanction-account-content-table-data">
                            <div class="sanction-account-content-header-row-fullname">Klein Moretti</div>
                            <div class="sanction-account-content-header-row-nOfAbsence">5</div>
                            <div class="sanction-account-content-header-row-total-fines">₱ 500.00</div>
                            <div class="sanction-account-content-header-row-payment">₱ 450.00</div>
                            <div class="sanction-account-content-header-row-payment-status">Partial Payment</div>
                            <div class="sanction-account-content-header-row-change">
                                <button type="button" class="user-account-edit">EDIT</button>
                                <button type="button" class="user-account-remove">REMOVE</button>
                            </div>
                        </div>
                        <div class="sanction-account-content-table-data"> 
                            <div class="sanction-account-content-header-row-fullname">Benson Moretti</div>
                            <div class="sanction-account-content-header-row-nOfAbsence">3</div>
                            <div class="sanction-account-content-header-row-total-fines">₱ 300.00</div>
                            <div class="sanction-account-content-header-row-payment">₱ 300.00</div>
                            <div class="sanction-account-content-header-row-payment-status">Paid</div>
                            <div class="sanction-account-content-header-row-change">
                                <button type="button" class="user-account-edit">EDIT</button>
                                <button type="button" class="user-account-remove">REMOVE</button>
                            </div>
                        </div>
                        <div class="sanction-account-content-table-data"> 
                            <div class="sanction-account-content-header-row-fullname">Dunn Smith</div>
                            <div class="sanction-account-content-header-row-nOfAbsence">2</div>
                            <div class="sanction-account-content-header-row-total-fines">₱ 200.00</div>
                            <div class="sanction-account-content-header-row-payment">₱ 200.00</div>
                            <div class="sanction-account-content-header-row-payment-status">Paid</div>
                            <div class="sanction-account-content-header-row-change">
                                <button type="button" class="user-account-edit">EDIT</button>
                                <button type="button" class="user-account-remove">REMOVE</button>
                            </div>
                        </div>
                        <div class="sanction-account-content-table-data"> 
                            <div class="sanction-account-content-header-row-fullname">Rosselle Gustav</div>
                            <div class="sanction-account-content-header-row-nOfAbsence">5</div>
                            <div class="sanction-account-content-header-row-total-fines">₱ 500.00</div>
                            <div class="sanction-account-content-header-row-payment">₱ 500.00</div>
                            <div class="sanction-account-content-header-row-payment-status">Paid</div>
                            <div class="sanction-account-content-header-row-change">
                                <button type="button" class="user-account-edit">EDIT</button>
                                <button type="button" class="user-account-remove">REMOVE</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
        </div>  
    </div>
</body>
</html>