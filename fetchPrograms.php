<?php
include "dbh.php";

$departmentId = isset($_GET['department_id']) ? intval($_GET['department_id']) : 0;

$sql = "
    SELECT 
        program_id, 
        program_name, 
        program_year_grade_level, 
        section 
    FROM 
        program 
    WHERE 
        department_id = $departmentId";

$result = $conn->query($sql);

$programs = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $programId = $row['program_id'];
        if (!isset($programs[$programId])) {
            $programs[$programId] = [
                "program_id" => $row['program_id'],
                "program_name" => $row['program_name'],
                "program_year_grade_levels" => [],
                "program_sections" => []
            ];
        }
        // Add year grade levels and sections to respective arrays
        if (!in_array($row['program_year_grade_level'], $programs[$programId]['program_year_grade_levels'])) {
            $programs[$programId]['program_year_grade_levels'][] = $row['program_year_grade_level'];
        }
        if (!in_array($row['section'], $programs[$programId]['program_sections'])) {
            $programs[$programId]['program_sections'][] = $row['section'];
        }
    }
}

echo json_encode(array_values($programs));
$conn->close();
?>
