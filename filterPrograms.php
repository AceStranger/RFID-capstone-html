<?php
include "dbh.php";

// Decode the incoming JSON payload
$request = json_decode(file_get_contents('php://input'), true);

$filterColumn = $request['filterColumn'] ?? '';
$searchInput = $request['searchInput'] ?? '';

// Validate filter column to prevent SQL injection
$validColumns = ['program_name', 'department_name'];
if (!in_array($filterColumn, $validColumns)) {
    echo json_encode([]);
    exit();
}

// Build query with filter
$searchInput = "%" . $conn->real_escape_string($searchInput) . "%";
$query = "SELECT 
            program.program_id, 
            program.program_name, 
            program.program_level, 
            program.program_year_grade_level, 
            program.section, 
            CASE 
                WHEN program.department_id = 0 THEN NULL
                ELSE department.department_name
            END AS department_name
          FROM program 
          LEFT JOIN department 
          ON program.department_id = department.department_id
          WHERE $filterColumn LIKE ?
          ORDER BY program.department_id ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $searchInput);
$stmt->execute();
$result = $stmt->get_result();

// Fetch and return data as JSON
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
