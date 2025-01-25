<?php
include "dbh.php"; // Assuming this file contains the database connection

// SQL query to fetch organization details
$sql = "SELECT 
            organization_id, 
            organization_name, 
            organization_responsibility, 
            organization_list_of_position 
        FROM 
            organization";

$result = $conn->query($sql);

// Initialize an array to store organization data
$organizations = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Add each organization data to the array
        $organizations[] = [
            'organization_id' => $row['organization_id'],
            'organization_name' => $row['organization_name'],
            'organization_responsibility' => $row['organization_responsibility'],
            'organization_list_of_position' => $row['organization_list_of_position']
        ];
    }
}

// Output the organization data as a JSON response
echo json_encode($organizations);

// Close the database connection
$conn->close();
?>
