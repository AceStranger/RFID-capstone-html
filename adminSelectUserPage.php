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
    <title>Document</title>
    <?php include "titleIcon.php" ;?>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminSelectUser.css">
    <script src="js\adminSidebar.js"></script>
    <script src="js\adminUser.js"></script>
</head>
<body>
    <div class="main-content-container">
        <?php include_once"adminSidebar.php";?>
        <div class="main-content">
            <div class="user-content">
                <div class="user-content-top-part">
                    <h1 class="user-content-head-text">USER</h1>
                    <div class="user-content-btn">
                        <form action="adminSelectUser.php" method="post">
                            <input type="hidden" name="request" value="<?php echo $_SESSION['request']?>">
                            <button type="submit" name="cancelSelectUser" class="btn-add-new-user">Cancel</button>
                        </form>
                    </div>
                </div>
                
                <div class="user-content-users-account-content">
                    <form method="get" class="users-account-content-form" id="users-account-content-form" enctype="multipart/form-data">
                        <div class="users-account-content-top-part">
                            <div class="users-account-content-search-input-container">
                                <label for="users-account-content-filter-select-col">Column :</label>
                                <select name="users-account-content-filter-select-col" id="users-account-content-filter-select-col">
                                    <option value="none" selected>None</option>
                                    <option value="School ID">School ID</option>
                                    <option value="First Name">First Name</option>
                                    <option value="Middle Name">Middle Name</option>
                                    <option value="Last Name">Last Name</option>
                                    <option value="Suffix Name">Suffix Name</option>
                                    <option value="Phone Number">Phone Number</option>
                                </select>
                                <input type="text" name="users-account-content-search-input" id="users-account-content-search-input" placeholder="Search...">
                                <input type="submit" name="filterSubmit" id="filterSubmit" value="Search">
                            </div>
                        </div>
                    </form>
                    <script>
                        
                        const selectCol = document.getElementById('users-account-content-filter-select-col');
                        const searchInput = document.getElementById('users-account-content-search-input');
                        const formFilter = document.getElementById('users-account-content-form');
                        const FormFilterSubmit = document.getElementById('filterSubmit');
                        
                        selectCol.addEventListener('change', FormFilterSubmit.click);
                        searchInput.addEventListener('change', FormFilterSubmit.click);
                    </script>
                    <div class="users-account-content-main-content">
                        <table class="users-account-content-table">
                            <thead class="users-account-content-table-head">
                                <tr class="users-account-content-table-row">
                                    <th class="users-account-content-data-school-id">School ID</th>
                                    <th class="users-account-content-data-fname">First Name</th>
                                    <th class="users-account-content-data-mname">Middle Name</th>
                                    <th class="users-account-content-data-lname">Last Name</th>
                                    <th class="users-account-content-data-suffix">Suffix Name</th>
                                    <th class="users-account-content-data-pnumber">Phone Number</th>
                                    <th class="users-account-content-data-role">Role</th>
                                    <th class="users-account-content-data-btn-select"></th>
                                </tr>
                            </thead>
                            <tbody class="users-account-content-table-body">
                    <?php 
                        $col = @$_GET['users-account-content-filter-select-col'];
                        $searchInput = mysqli_real_escape_string($conn, @$_GET['users-account-content-search-input']);

                        // Handle column selection and dynamic query filtering
                        $where = ""; // Initialize WHERE clause
                        if ($col !== "" && $searchInput !== "") {
                            $col = mysqli_real_escape_string($conn, $col);
                            if ($col === 'School ID') {
                                $sID = number_format(@$_GET['users-account-content-search-input']);
                                $sID = str_replace(',', '', $sID); 
                                $where = "user_school_id = $sID";
                            } elseif ($col === 'First Name') {
                                $where = "LOWER(user_firstname) = LOWER('$searchInput')";
                            } elseif ($col === 'Middle Name') {
                                $where = "LOWER(user_middlename) = LOWER('$searchInput')";
                            } elseif ($col === 'Last Name') {
                                $where = "LOWER(user_lastname) = LOWER('$searchInput')";
                            } elseif ($col === 'Suffix Name') {
                                $where = "LOWER(user_suffixname) = LOWER('$searchInput')";
                            } elseif ($col === 'Phone Number') {
                                $pNum = number_format(@$_GET['users-account-content-search-input']);
                                $pNum = str_replace(',', '', $pNum); 
                                $where = "user_phone_number = $pNum";
                            }
                        }

                        // Build base query based on request type
                        $request = $_SESSION['request'];
                        $selectQuery = "";
                        if (!empty($request)) {
                            switch ($request) {
                                case 'RFID Selecting user':
                                    $selectQuery = "SELECT * FROM user 
                                        WHERE user.user_id NOT IN (SELECT user_id FROM rfid);
                                    ";
                                    break;
                                case 'Dean Selecting user':
                                    $selectQuery = "SELECT * FROM user WHERE LOWER(user_role) NOT LIKE LOWER('%dean%')";
                                    break;
                                case 'Officer Selecting user':
                                    $selectQuery = "SELECT * FROM user WHERE LOWER(user_role) NOT LIKE LOWER('%officer%')";
                                    break;
                                case 'Student Selecting user':
                                    $selectQuery = "SELECT * FROM user WHERE LOWER(user_role) NOT LIKE LOWER('%student%')";
                                    break;
                                default:
                                    $selectQuery = "SELECT * FROM user";
                                    break;
                            }
                        }


                        if ($where !== "") {
                            if (strpos($selectQuery, 'WHERE') !== false) {
                                $selectQuery .= " AND $where";
                            } else {
                                $selectQuery .= " WHERE $where";
                            }
                        }
                        

                        $userQuery = $selectQuery;  // Final query with dynamic filtering

                        // Execute the query
                        $result = mysqli_query($conn, $userQuery);
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                echo '
                                
                                <tr class="users-account-content-table-row">
                                    <form action="adminSelectUser.php" method="post" class="users-account-content-data-form">
                                        <input type="hidden" name="user-id" id="user-id" value="'.$row["user_id"].'">
                                        <input type="hidden" name="request" id="request" value="'.$request.'">

                                        <td class="users-account-content-data-school-id">'. $row['user_school_id'] .'</td>
                                        <td class="users-account-content-data-fname">'. $row['user_firstname'] .'</td>
                                        <td class="users-account-content-data-mname">'. $row['user_middlename'] .'</td>
                                        <td class="users-account-content-data-lname">'. $row['user_lastname'] .'</td>
                                        <td class="users-account-content-data-suffix">'. $row['user_suffixname'] .'</td>
                                        <td class="users-account-content-data-pnumber">'. $row['user_phone_number'] .'</td>
                                        <td class="users-account-content-data-role">'. $row['user_role'] .'</td>
                                        <td class="users-account-content-data-btn-select">
                                            <button type="submit" name="selectSubmit" class="user-account-select">select</button>
                                        </td>

                                    </form>
                                </tr>
                                
                                '; 
                            }
                        }
                    
                    ?>

                
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                
            </div>
            
        </div>  
        
    </div>

    <script>
    </script>
</body>
</html>