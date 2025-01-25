<?php
include "dbh.php";

header('Content-Type: application/json'); // Ensure the content is returned as JSON

$sql = "
    SELECT 
        program_id, 
        program_name, 
        program_level,
        program_year_grade_level, 
        section 
    FROM 
        program
        ";

$result = $conn->query($sql);

$programs = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $programId = $row['program_id'];
        if (!isset($programs[$programId])) {
            $programs[$programId] = [
                "program_id" => $row['program_id'],
                "program_level" => $row['program_level'],
                "program_name" => $row['program_name'],
                "program_year_grade_levels" => [],
                "program_sections" => []
            ];
        }
        // Split the program_year_grade_level by commas and add to the array
        $yearLevels = array_map('trim', explode(',', $row['program_year_grade_level']));
        foreach ($yearLevels as $yearLevel) {
            if (!in_array($yearLevel, $programs[$programId]['program_year_grade_levels'])) {
                $programs[$programId]['program_year_grade_levels'][] = $yearLevel;
            }
        }

        // Split the sections by commas and add to the array
        $sections = array_map('trim', explode(',', $row['section']));
        foreach ($sections as $section) {
            if (!in_array($section, $programs[$programId]['program_sections'])) {
                $programs[$programId]['program_sections'][] = $section;
            }
        }
    }

    echo json_encode(["success" => true, "programs" => array_values($programs)]);
} else {
    echo json_encode(["success" => false, "message" => "No programs found."]);
}

$conn->close();
?>
