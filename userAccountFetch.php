<?php
include('dbh.php');

$role = $_GET['role'] ?? 'all';
$col = $_GET['col'] ?? 'none';
$search = $_GET['search'] ?? '';

$roleCondition = ($role === 'all' || empty($role)) ? '' : "user_role LIKE '%$role%'";
$colCondition = '';
if ($col !== 'none' && !empty($col)) {
    switch ($col) {
        case 'School ID':
            $colCondition = "user_school_id LIKE '%$search%'";
            break;
        case 'First Name':
            $colCondition = "user_firstname LIKE '%$search%'";
            break;
        case 'Middle Name':
            $colCondition = "user_middlename LIKE '%$search%'";
            break;
        case 'Last Name':
            $colCondition = "user_lastname LIKE '%$search%'";
            break;
        case 'Suffix Name':
            $colCondition = "user_suffixname LIKE '%$search%'";
            break;
        case 'Phone Number':
            $colCondition = "user_phone_number LIKE '%$search%'";
            break;
    }
}

$where = [];
if ($roleCondition) $where[] = $roleCondition;
if ($colCondition) $where[] = $colCondition;

$whereClause = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

$sql = "SELECT * FROM user $whereClause";
$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

echo json_encode($data);
?>
