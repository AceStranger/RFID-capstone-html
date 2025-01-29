<?php
include "dbh.php";

// Fetch all department data
$query = "SELECT department_id, department_name FROM department ORDER BY department_id ASC";
$result = mysqli_query($conn, $query);

$departments = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $departments[] = $row;
    }
    echo json_encode(['success' => true, 'departments' => $departments]);
} else {
    echo json_encode(['success' => false, 'message' => 'No departments found.']);
}

mysqli_close($conn);
?>
