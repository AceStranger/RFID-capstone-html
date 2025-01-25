<?php
include "dbh.php";

// Fetch departments
$sql = "SELECT department_id, department_name FROM department";
$result = $conn->query($sql);

$departments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
}

echo json_encode($departments);
$conn->close();
?>
