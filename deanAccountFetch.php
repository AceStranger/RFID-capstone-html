<?php
include('dbh.php');

// Retrieve filter parameters
$department = $_GET['department'] ?? '';
$search = $_GET['search'] ?? '';

// Build query with conditions
$departmentCondition = "";
$nameCondition = "";

if ($department !== "") {
    if ($department === "none") {
        $departmentCondition = "dean.department_id = ''";
    } else {
        $departmentCondition = "dean.department_id = '$department'";
    }
}

if ($search !== "") {
    $nameCondition = "(LOWER(user.user_firstname) LIKE LOWER('%$search%') OR 
                        LOWER(user.user_lastname) LIKE LOWER('%$search%') OR 
                        LOWER(user.user_middlename) LIKE LOWER('%$search%') OR
                        LOWER(user.user_suffixname) LIKE LOWER('%$search%'))";
}

// Combine conditions
$where = "WHERE user.user_role LIKE '%dean%'";

if ($departmentCondition !== "" && $nameCondition !== "") {
    $where .= " AND $departmentCondition AND $nameCondition";
} elseif ($departmentCondition !== "") {
    $where .= " AND $departmentCondition";
} elseif ($nameCondition !== "") {
    $where .= " AND $nameCondition";
}

// Query the database for users
$userQuery = "SELECT user.*, dean.*, department.*
              FROM dean
              LEFT JOIN user ON user.user_id = dean.user_id
              LEFT JOIN department ON dean.department_id = department.department_id
              $where";

$result = mysqli_query($conn, $userQuery);
$rows = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
}

echo json_encode($rows);
?>
