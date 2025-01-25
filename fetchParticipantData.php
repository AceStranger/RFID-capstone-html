<?php
include "dbh.php";

header('Content-Type: application/json');

// Get the posted JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Ensure the event_id is set
if (isset($data['event_id'])) {
    $event_id = $data['event_id'];
    $event_participant = trim($data['event_participant']);

    $participants = [];
    $attendance = [];
    // Check if $event_participant contains commas and process accordingly
    $event_participants = [];
    if (str_contains($event_participant, ",")) {
        // Split the string into an array if it contains commas
        $event_participants = explode(",", $event_participant);
    } else {
        // Treat $event_participant as a single item array
        $event_participants[] = $event_participant;
    }
    
    error_log("Processing event participant: " . print_r( "all asd", true));
    foreach ($event_participants as $event_participant) {
        $part = explode("-", $event_participant);
        $programLevels = ["College", "college", "Secondary", "secondary", "Primary", "primary"];

        if (str_contains($part[0], "all") || str_contains($part[0], "All")) {
            error_log("Processing event participant: " . print_r( "All true", true));

            if (in_array($part[1], $programLevels)) {
                error_log("Processing event participant: " . print_r( "programLevels true", true));
                // Query program_id for the given program_level
                $level = ucfirst(strtolower($part[1])); // Normalize case
                $sql = "SELECT program_id, program_name FROM program WHERE program_level = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $level);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $program_id = $row['program_id'];
                    $program_name = $row['program_name'];

                    // Query students by program_id
                    $sqlStudents = "SELECT * FROM student WHERE program_id = ?";
                    $stmtStudents = $conn->prepare($sqlStudents);
                    $stmtStudents->bind_param("i", $program_id);
                    $stmtStudents->execute();
                    $resultStudents = $stmtStudents->get_result();

                    while ($student = $resultStudents->fetch_assoc()) {
                        $participants[$level][$program_name][$student['year/grade_level']][] = $student;
                    }
                }
            } else {
                error_log("Processing event participant: " . print_r( $part[1], true));
                // Handle when the second part is a program_name
                $programName = trim($part[1]);
                $sql = "SELECT program_id FROM program WHERE program_name = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $programName);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($row = $result->fetch_assoc()) {
                    $program_id = $row['program_id'];

                    // Query students by program_id
                    $sqlStudents = "SELECT * FROM student WHERE program_id = ?";
                    $stmtStudents = $conn->prepare($sqlStudents);
                    $stmtStudents->bind_param("i", $program_id);
                    $stmtStudents->execute();
                    $resultStudents = $stmtStudents->get_result();

                    while ($student = $resultStudents->fetch_assoc()) {
                        $participants[$programName][$student['year/grade_level']][$student['section']][] = $student;
                    }
                }
            }
        } else {
            error_log("Processing event participant: " . print_r( "all false", true));
            // Handle when the first part is program_name and second is year/grade_level
            $programName = trim($part[0]);
            $yearGradeLevel = trim($part[1]);
            $section = trim($part[2]) ?? null;

            $sql = "SELECT program_id FROM program WHERE program_name = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $programName);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $program_id = $row['program_id'];

                // Query students by program_id, year/grade_level, and optionally section
                $sqlStudents = "SELECT * FROM student WHERE program_id = ? AND `year/grade_level` = ?";
                if ($section) {
                    $sqlStudents .= " AND section = ?";
                }
                $stmtStudents = $conn->prepare($sqlStudents);
                if ($section) {
                    $stmtStudents->bind_param("iss", $program_id, $yearGradeLevel, $section);
                } else {
                    $stmtStudents->bind_param("is", $program_id, $yearGradeLevel);
                }
                $stmtStudents->execute();
                $resultStudents = $stmtStudents->get_result();

                while ($student = $resultStudents->fetch_assoc()) {
                    $participants[$programName][$yearGradeLevel][$section][] = $student;
                }
            }
        }
    }

    // Fetch attendance data
    $sqlAttendance = "SELECT * FROM attendance WHERE event_id = ?";
    $stmt = $conn->prepare($sqlAttendance);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $attendance[] = $row;
    }

    // Return response
    echo json_encode([
        'success' => true,
        'event_participant' => [
            'participant' => $participants,
            'attendance' => $attendance
        ]
    ]);
    // Close the database connection
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No event_id provided']);
}
?>
