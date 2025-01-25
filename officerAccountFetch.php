<?php
include 'dbh.php';  // Include the database connection file

// Get parameters from the query string
$organization = $_GET['organization'] ?? '';
$column = $_GET['column'] ?? '';
$search = $_GET['search'] ?? '';

// Filter by organization
$organizationCondition = '';
if ($organization !== "") {
    if ($organization === 'none') {
        $organizationCondition = "officer.organization_id IS NULL OR officer.organization_id = ''";
    } else {
        $organizationCondition = "officer.organization_id = '$organization'";
    }
}

// Filter by column and search text
$colCondition = '';
if ($column === 'name' && !empty($search)) {
    $colCondition = "(LOWER(user.user_firstname) LIKE LOWER('%$search%') OR LOWER(user.user_lastname) LIKE LOWER('%$search%'))";
} elseif ($column === 'position' && !empty($search)) {
    $colCondition = "LOWER(officer.officer_position) LIKE LOWER('%$search%')";
}

// Combine conditions
$whereConditions = ["user.user_role LIKE '%officer%'"];
if ($organizationCondition) {
    $whereConditions[] = $organizationCondition;
}
if ($colCondition) {
    $whereConditions[] = $colCondition;
}

$whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Query to get officer data
$officerQuery = "SELECT user.*, officer.*, organization.organization_name
                FROM user
                LEFT JOIN officer ON user.user_id = officer.user_id
                LEFT JOIN organization ON officer.organization_id = organization.organization_id
                $whereClause";
$result = mysqli_query($conn, $officerQuery);

// Fetch data and return it as JSON
$data = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

echo json_encode($data);
?>
