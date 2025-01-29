<?php
header('Content-Type: application/json');

// Database connection
include 'dbh.php';

// Base query
$query = "SELECT * FROM organization";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Output JSON
echo json_encode($data);
?>
